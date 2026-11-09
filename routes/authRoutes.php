<?php
/** Endpoints de autenticación. */
$router->get('/api/auth/status',    [AuthController::class, 'status']);
$router->post('/api/auth/register', [AuthController::class, 'register']);
$router->post('/api/auth/login',    [AuthController::class, 'login']);
$router->post('/api/auth/logout',   [AuthController::class, 'logout']);
