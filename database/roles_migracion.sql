-- =====================================================================
-- MIGRACIÓN: agrega ROLES a una base `juego_cartas` que YA existe.
-- Ejecutar UNA sola vez (phpMyAdmin > base juego_cartas > pestaña SQL).
-- Si instalás desde cero, no lo necesitás: JuegoFutbol.sql ya los incluye.
-- =====================================================================
USE `juego_cartas`;

-- 1) Tabla de roles
CREATE TABLE IF NOT EXISTS roles (
  id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(20) NOT NULL UNIQUE,
  descripcion VARCHAR(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO roles (id, nombre, descripcion) VALUES
  (1, 'admin',   'Puede crear, editar y borrar cartas'),
  (2, 'jugador', 'Solo puede jugar');

-- 2) Columna de rol en usuarios (por defecto todos son 'jugador')
ALTER TABLE usuarios
  ADD COLUMN rol_id TINYINT UNSIGNED NOT NULL DEFAULT 2 AFTER password_hash,
  ADD CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id) REFERENCES roles (id);

-- 3) Dar rol de administrador a quien corresponda (cambiá el nombre de usuario)
UPDATE usuarios SET rol_id = (SELECT id FROM roles WHERE nombre = 'admin')
WHERE usuario = 'hola';

-- 4) Consulta para verificar usuarios y roles
SELECT u.id, u.usuario, u.email, r.nombre AS rol
FROM usuarios u
INNER JOIN roles r ON r.id = u.rol_id
ORDER BY u.id;

-- Para promover o quitar permisos más adelante:
--   UPDATE usuarios SET rol_id = 1 WHERE usuario = 'otro_usuario';   -- hacer admin
--   UPDATE usuarios SET rol_id = 2 WHERE usuario = 'otro_usuario';   -- volver a jugador
