<?php
/**
 * Controlador de cartas/jugadores: entrega las cartas al juego en JSON.
 */
class JugadorController extends Controller
{
    // GET /api/cartas
    public function cartas(): void
    {
        $jugadores = new Jugador();
        $this->json($jugadores->agrupadosPorDivision());
    }
}
