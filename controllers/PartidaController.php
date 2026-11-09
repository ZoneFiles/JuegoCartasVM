<?php
/**
 * Controlador de partidas: guardar resultado y mostrar el historial.
 */
class PartidaController extends Controller
{
    // POST /api/partidas
    public function guardar(): void
    {
        if (!$this->autenticado()) {
            $this->json(['error' => 'Necesitás iniciar sesión para guardar una partida.'], 401);
        }

        $ganador = $this->post('ganador');
        $perdedor = $this->post('perdedor');
        $p1 = filter_var($_POST['p1'] ?? null, FILTER_VALIDATE_INT);
        $p2 = filter_var($_POST['p2'] ?? null, FILTER_VALIDATE_INT);

        if ($ganador === '' || $perdedor === '' || $p1 === false || $p2 === false) {
            $this->json(['error' => 'Los datos de la partida no son válidos.'], 400);
        }

        (new Partida())->guardar($ganador, $perdedor, $p1, $p2);
        $this->json(['ok' => true]);
    }

    // GET /api/ranking  (devuelve un fragmento HTML con la tabla)
    public function ranking(): void
    {
        $partidas = (new Partida())->ultimas(10);
        header('Content-Type: text/html; charset=utf-8');
        $this->vista('partials/ranking_tabla', ['partidas' => $partidas]);
    }
}
