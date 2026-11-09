<?php
/**
 * Lector simple de archivos .env (equivalente a dotenv en Node).
 */
class Env
{
    private static array $variables = [];

    public static function cargar(string $archivo): void
    {
        if (!is_readable($archivo)) {
            return;
        }
        foreach (file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
            $linea = trim($linea);
            if ($linea === '' || $linea[0] === '#' || !str_contains($linea, '=')) {
                continue;
            }
            [$clave, $valor] = explode('=', $linea, 2);
            self::$variables[trim($clave)] = trim(trim($valor), "\"'");
        }
    }

    public static function get(string $clave, ?string $porDefecto = null): ?string
    {
        return self::$variables[$clave] ?? $porDefecto;
    }
}
