<?php
/** Endpoints de cartas/jugadores. */
$router->get('/api/cartas', [JugadorController::class, 'cartas']);
