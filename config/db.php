<?php
/**
 * Conexión a la base de datos (equivale a config/db.js).
 * Los datos se leen desde el archivo .env
 */
class Database
{
    private static ?mysqli $conexion = null;

    public static function conexion(): mysqli
    {
        if (self::$conexion === null) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            self::$conexion = new mysqli(
                Env::get('DB_HOST', 'localhost'),
                Env::get('DB_USER', 'root'),
                Env::get('DB_PASS', ''),
                Env::get('DB_NAME', 'juego_cartas'),
                (int)Env::get('DB_PORT', '3306')
            );
            self::$conexion->set_charset('utf8mb4');
        }
        return self::$conexion;
    }
}
