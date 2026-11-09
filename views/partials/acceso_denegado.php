<?php /** Vista 403: el usuario no tiene rol admin. */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <base href="<?= e(ASSETS_URL) ?>/">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso denegado</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body style="display:flex;align-items:center;justify-content:center;">
    <div class="card-panel" style="text-align:center;max-width:420px;">
        <h3>Acceso denegado</h3>
        <p style="margin-bottom:18px;">Solo los administradores pueden editar las cartas.</p>
        <a href="<?= e(url()) ?>" class="btn-back">Volver al juego</a>
    </div>
</body>
</html>
