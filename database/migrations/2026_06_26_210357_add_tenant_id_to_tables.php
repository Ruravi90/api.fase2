<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tables = [
        'users', 'cat_references', 'clients', 'providers', 'address', 'cat_products',
        'cat_packages', 'cat_services', 'creditors', 'product_inventory', 'pills_inventory',
        'cat_pills', 'schedule', 'tasks', 'sales', 'payments', 'departments', 'selling_elements',
        'type_sales', 'purchases', 'jobs', 'packages', 'cat_expenses', 'complements_package',
        'cat_concept', 'additionals', 'history_balance', 'whatsapp_sessions',
        'chat_conversations', 'chat_messages', 'turn_queues', 'clinical_notes', 'medical_records'
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'tenant_id')) {
                        $table->unsignedBigInteger('tenant_id')->nullable()->index();
                    }
                });
                // Assign existing records to tenant 1
                DB::table($tableName)->update(['tenant_id' => 1]);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'tenant_id')) {
                        $table->dropColumn('tenant_id');
                    }
                });
            }
        }
    }
};
