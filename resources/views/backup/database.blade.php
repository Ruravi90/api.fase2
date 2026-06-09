<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Backup de base de datos</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 32px; max-width: 560px; margin: 0 auto; }
        label { display: block; margin-bottom: 8px; }
        input { width: 100%; padding: 10px; margin-bottom: 16px; box-sizing: border-box; }
        button { padding: 10px 16px; }
    </style>
</head>
<body>
    <h1>Backup de base de datos</h1>
    <form method="POST" action="{{ url('/backup/database') }}">
        <label for="key">Clave de backup</label>
        <input type="password" id="key" name="key" autocomplete="off" required>
        <button type="submit">Generar y descargar .sql</button>
    </form>
    <form method="POST" action="{{ url('/backup/database/migrate') }}">
        <label for="key-migrate">Clave de backup</label>
        <input type="password" id="key-migrate" name="key" autocomplete="off" required>
        <button type="submit">Backup + ejecutar migraciones pendientes</button>
    </form>
</body>
</html>
