<?php
/**
 * Archivo central de rutas: registra la página principal y carga
 * los archivos de rutas de cada módulo.
 */
$router->get('/', [JuegoController::class, 'index']);

require __DIR__ . '/authRoutes.php';
require __DIR__ . '/jugadorRoutes.php';
require __DIR__ . '/partidaRoutes.php';
require __DIR__ . '/adminRoutes.php';
