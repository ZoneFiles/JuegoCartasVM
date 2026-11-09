<?php
/**
 * Modelo Usuario: consultas SQL de las tablas `usuarios` y `roles`.
 * Roles: 'admin' (puede editar las cartas) y 'jugador' (solo juega).
 */
class Usuario
{
    public const ROL_ADMIN = 'admin';
    public const ROL_JUGADOR = 'jugador';

    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::conexion();
        // Solo crea las tablas si la base está vacía (instalación nueva)
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS roles (
                id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(20) NOT NULL UNIQUE,
                descripcion VARCHAR(100) NOT NULL DEFAULT ''
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        $this->db->query(
            "INSERT IGNORE INTO roles (id, nombre, descripcion) VALUES
                (1, 'admin', 'Puede crear, editar y borrar cartas'),
                (2, 'jugador', 'Solo puede jugar')"
        );
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS usuarios (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                usuario VARCHAR(30) NOT NULL UNIQUE,
                email VARCHAR(254) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                rol_id TINYINT UNSIGNED NOT NULL DEFAULT 2,
                creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id) REFERENCES roles (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }

    /** INSERT: todo usuario nuevo se registra como 'jugador'. Devuelve el id creado. */
    public function crear(string $usuario, string $email, string $passwordHash): int
    {
        $q = $this->db->prepare(
            "INSERT INTO usuarios (usuario, email, password_hash, rol_id)
             VALUES (?, ?, ?, (SELECT id FROM roles WHERE nombre = 'jugador'))"
        );
        $q->bind_param('sss', $usuario, $email, $passwordHash);
        $q->execute();
        $id = $this->db->insert_id;
        $q->close();
        return $id;
    }

    /** SELECT con JOIN a roles, por nombre de usuario o correo. */
    public function buscarPorIdentificador(string $identificador): ?array
    {
        $q = $this->db->prepare(
            'SELECT u.id, u.usuario, u.password_hash, r.nombre AS rol
             FROM usuarios u
             INNER JOIN roles r ON r.id = u.rol_id
             WHERE u.usuario = ? OR u.email = ? LIMIT 1'
        );
        $q->bind_param('ss', $identificador, $identificador);
        $q->execute();
        $fila = $q->get_result()->fetch_assoc();
        $q->close();
        return $fila ?: null;
    }

    /** Rol actual de un usuario (se consulta siempre a la BD, no a la sesión). */
    public function rolDe(int $id): ?string
    {
        $q = $this->db->prepare(
            'SELECT r.nombre FROM usuarios u INNER JOIN roles r ON r.id = u.rol_id WHERE u.id = ?'
        );
        $q->bind_param('i', $id);
        $q->execute();
        $fila = $q->get_result()->fetch_assoc();
        $q->close();
        return $fila['nombre'] ?? null;
    }
}
