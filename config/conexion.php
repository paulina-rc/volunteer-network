<?php
require_once __DIR__ . '/config.php';

/**
 * Devuelve una única instancia de conexión PDO (patrón singleton simple).
 */
function obtenerConexion(): PDO
{
    static $conexion = null;

    if ($conexion === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $conexion = new PDO($dsn, DB_USER, DB_PASS, $opciones);
        } catch (PDOException $excepcion) {
            error_log('Error de conexión a la base de datos: ' . $excepcion->getMessage());
            http_response_code(500);
            die('No se pudo conectar a la base de datos. Revisá la configuración en /config/config.php.');
        }
    }

    return $conexion;
}
