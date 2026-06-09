<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resultado del backup</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 32px; max-width: 760px; margin: 0 auto; }
        pre { background: #f4f4f4; padding: 16px; overflow: auto; }
        a, button { display: inline-block; margin-top: 16px; }
    </style>
</head>
<body>
    <h1>Proceso completado</h1>
    <p><strong>Backup:</strong> {{ $backupPath }}</p>
    <p><strong>Exit code:</strong> {{ $exitCode }}</p>
    <h2>Salida</h2>
    <pre>{{ $output }}</pre>
    <a href="{{ url('/backup/database') }}">Volver</a>
</body>
</html>
