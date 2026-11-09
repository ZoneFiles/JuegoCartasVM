<?php
/** Rutas del panel de administración (CRUD de cartas). */
$router->get('/admin',            [AdminController::class, 'index']);
$router->post('/admin/guardar',   [AdminController::class, 'guardar']);
$router->post('/admin/eliminar',  [AdminController::class, 'eliminar']);
