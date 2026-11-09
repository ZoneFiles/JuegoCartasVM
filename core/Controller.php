<?php
/**
 * Controlador base: utilidades comunes (JSON, vistas, sesión, entradas).
 */
abstract class Controller
{
    protected function json(array $datos, int $estado = 200): never
    {
        http_response_code($estado);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function vista(string $nombre, array $datos = []): void
    {
        extract($datos, EXTR_SKIP);
        require VIEWS_PATH . '/' . $nombre . '.php';
    }

    protected function redirigir(string $ruta): never
    {
        header('Location: ' . url($ruta));
        exit;
    }

    protected function autenticado(): bool
    {
        return isset($_SESSION['usuario_id']);
    }

    /** ¿El usuario logueado tiene rol 'admin'? (se verifica en la BD en cada petición) */
    protected function esAdmin(): bool
    {
        return $this->autenticado()
            && (new Usuario())->rolDe((int)$_SESSION['usuario_id']) === Usuario::ROL_ADMIN;
    }

    /** Lee un campo POST de texto, recortado. */
    protected function post(string $campo): string
    {
        return isset($_POST[$campo]) && is_string($_POST[$campo]) ? trim($_POST[$campo]) : '';
    }
}
