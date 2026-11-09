<?php
/**
 * Modelo Jugador: consultas SQL (SELECT / INSERT / UPDATE / DELETE) de la tabla `jugadores`.
 */
class Jugador
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::conexion();
    }

    public function todos(): array
    {
        return $this->db->query('SELECT * FROM jugadores ORDER BY id')->fetch_all(MYSQLI_ASSOC);
    }

    public function buscar(int $id): ?array
    {
        $q = $this->db->prepare('SELECT * FROM jugadores WHERE id = ?');
        $q->bind_param('i', $id);
        $q->execute();
        $fila = $q->get_result()->fetch_assoc();
        $q->close();
        return $fila ?: null;
    }

    /**
     * Devuelve las cartas agrupadas por división y equipo,
     * con el formato que consume el juego (js/script.js).
     */
    public function agrupadosPorDivision(): array
    {
        $divisiones = ['primera' => [], 'segunda' => []];

        foreach ($this->todos() as $fila) {
            $div = $fila['division'] ?: 'primera';
            $eq = $fila['equipo'];

            if (!isset($divisiones[$div][$eq])) {
                $divisiones[$div][$eq] = [
                    'escudo' => $fila['escudo_equipo'] ?: 'img/logo.png',
                    'jugadores' => [],
                ];
            }

            $divisiones[$div][$eq]['jugadores'][] = [
                'nombre'   => $fila['nombre'],
                'ataque'   => (int)$fila['ataque'],
                'defensa'  => (int)$fila['defensa'],
                'control'  => (int)$fila['control'],
                'cartaImg' => $fila['carta_img'],
                'equipo'   => $eq,
            ];
        }
        return $divisiones;
    }

    public function crear(array $d): void
    {
        $q = $this->db->prepare(
            'INSERT INTO jugadores (nombre, ataque, defensa, control, carta_img, equipo, division, escudo_equipo)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $q->bind_param('siiissss', $d['nombre'], $d['ataque'], $d['defensa'], $d['control'],
            $d['carta_img'], $d['equipo'], $d['division'], $d['escudo_equipo']);
        $q->execute();
        $q->close();
    }

    public function actualizar(int $id, array $d): void
    {
        $q = $this->db->prepare(
            'UPDATE jugadores SET nombre=?, ataque=?, defensa=?, control=?, carta_img=?, equipo=?, division=?, escudo_equipo=?
             WHERE id=?'
        );
        $q->bind_param('siiissssi', $d['nombre'], $d['ataque'], $d['defensa'], $d['control'],
            $d['carta_img'], $d['equipo'], $d['division'], $d['escudo_equipo'], $id);
        $q->execute();
        $q->close();
    }

    public function eliminar(int $id): void
    {
        $q = $this->db->prepare('DELETE FROM jugadores WHERE id = ?');
        $q->bind_param('i', $id);
        $q->execute();
        $q->close();
    }
}
