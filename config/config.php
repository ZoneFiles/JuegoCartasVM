<?php
/**
 * Configuración global: constantes de rutas/URLs y funciones auxiliares.
 */
define('VIEWS_PATH', ROOT_PATH . '/views');
define('APP_DEBUG', filter_var(Env::get('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN));

// URL base calculada automáticamente (funciona en cualquier carpeta de WAMP)
$carpeta = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
define('BASE_PATH', $carpeta);
define('APP_URL', BASE_PATH . '/index.php');   // rutas de la aplicación
define('ASSETS_URL', BASE_PATH . '/public');   // css / js / img / audio

/** Escapa texto para imprimirlo en HTML. */
function e($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

/** URL de una ruta de la aplicación. Ej: url('admin') */
function url(string $ruta = ''): string
{
    $ruta = trim($ruta, '/');
    return $ruta === '' ? APP_URL : APP_URL . '/' . $ruta;
}

/** Token CSRF de la sesión. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valido(?string $token): bool
{
    return is_string($token) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}
