<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class DatabaseBackup
{
    public function run(?string $customPath = null): string
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            throw new RuntimeException("Database backup only supports mysql/mariadb, current driver: {$driver}.");
        }

        $path = $customPath ?: storage_path('app/backups/db-backup-'.now()->format('Ymd-His').'.sql');
        $directory = dirname($path);
        File::ensureDirectoryExists($directory);

        $pdo = $connection->getPdo();
        $database = $connection->getDatabaseName();
        $tables = $connection->select('SHOW FULL TABLES WHERE Table_type = ?', ['BASE TABLE']);
        $tableKey = 'Tables_in_'.$database;

        $sql = [];
        $sql[] = '-- Database backup generated at '.now()->toDateTimeString();
        $sql[] = 'SET FOREIGN_KEY_CHECKS=0;';
        $sql[] = '';

        foreach ($tables as $tableRow) {
            $table = $tableRow->{$tableKey};

            $create = $connection->selectOne('SHOW CREATE TABLE `'.$table.'`');
            $createSql = $create->{'Create Table'} ?? null;
            if (! $createSql) {
                continue;
            }

            $sql[] = 'DROP TABLE IF EXISTS `'.$table.'`;';
            $sql[] = $createSql.';';
            $sql[] = '';

            $rows = $connection->select('SELECT * FROM `'.$table.'`');
            if (empty($rows)) {
                continue;
            }

            $columns = array_keys((array) $rows[0]);
            $quotedColumns = array_map(fn ($column) => '`'.$column.'`', $columns);

            foreach ($rows as $row) {
                $values = [];
                foreach ($columns as $column) {
                    $value = $row->{$column};

                    if ($value === null) {
                        $values[] = 'NULL';
                        continue;
                    }

                    if (is_bool($value)) {
                        $values[] = $value ? '1' : '0';
                        continue;
                    }

                    if (is_int($value) || is_float($value)) {
                        $values[] = (string) $value;
                        continue;
                    }

                    if (is_resource($value)) {
                        $value = stream_get_contents($value);
                    }

                    $values[] = $pdo->quote((string) $value);
                }

                $sql[] = 'INSERT INTO `'.$table.'` ('.implode(', ', $quotedColumns).') VALUES ('.implode(', ', $values).');';
            }

            $sql[] = '';
        }

        $sql[] = 'SET FOREIGN_KEY_CHECKS=1;';

        File::put($path, implode(PHP_EOL, $sql).PHP_EOL);

        return $path;
    }
}
