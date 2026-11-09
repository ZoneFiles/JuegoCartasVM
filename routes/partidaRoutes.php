<?php
/** Endpoints de partidas y ranking. */
$router->post('/api/partidas', [PartidaController::class, 'guardar']);
$router->get('/api/ranking',   [PartidaController::class, 'ranking']);
