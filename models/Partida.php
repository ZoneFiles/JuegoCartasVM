<?php
/**
 * Modelo Partida: consultas SQL de la tabla `historial_partidas`.
 */
class Partida
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::conexion();
    }

    /** INSERT de una partida terminada. */
    public function guardar(string $ganador, string $perdedor, int $p1, int $p2): void
    {
        $q = $this->db->prepare('INSERT INTO historial_partidas (ganador, perdedor, puntaje_j1, puntaje_j2) VALUES (?, ?, ?, ?)');
        $q->bind_param('ssii', $ganador, $perdedor, $p1, $p2);
        $q->execute();
        $q->close();
    }

    /** SELECT de las últimas N partidas. */
    public function ultimas(int $limite = 10): array
    {
        $q = $this->db->prepare('SELECT * FROM historial_partidas ORDER BY id DESC LIMIT ?');
        $q->bind_param('i', $limite);
        $q->execute();
        $filas = $q->get_result()->fetch_all(MYSQLI_ASSOC);
        $q->close();
        return $filas;
    }
}
