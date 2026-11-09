<?php
/**
 * Punto de entrada de la aplicación (equivale a server.js).
 * Todas las peticiones pasan por acá: index.php -> routes -> controllers -> models -> views
 */
declare(strict_types=1);

define('ROOT_PATH', __DIR__);

// Autocarga de clases (core, controllers, models)
spl_autoload_register(function (string $clase): void {
    foreach (['core', 'controllers', 'models'] as $carpeta) {
        $archivo = ROOT_PATH . "/$carpeta/$clase.php";
        if (is_file($archivo)) {
            require $archivo;
            return;
        }
    }
});

Env::cargar(ROOT_PATH . '/.env');
require ROOT_PATH . '/config/config.php';
require ROOT_PATH . '/config/db.php';

// Sesión segura
ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

// Manejo global de errores
set_exception_handler(function (Throwable $error): void {
    error_log('Error: ' . $error->getMessage());
    http_response_code(500);
    $esApi = str_contains($_SERVER['REQUEST_URI'] ?? '', '/index.php/api/');
    $mensaje = 'No se pudo completar la operación. Revisá la conexión con la base de datos.';
    if (APP_DEBUG) {
        $mensaje .= ' [Detalle: ' . $error->getMessage() . ']';
    }
    if ($esApi) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => $mensaje], JSON_UNESCAPED_UNICODE);
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo $mensaje;
    }
});

// Rutas
$router = new Router();
require ROOT_PATH . '/routes/web.php';
$router->despachar($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
