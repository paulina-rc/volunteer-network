<?php
/**
 * Configuración general de Enlaza.
 * Ajustar estos valores según el entorno local (XAMPP/Laragon) o el hosting.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'enlaza');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('BASE_URL', '/enlaza/public/');

define('MAX_CUPOS_DEFAULT', 20);

// Roles válidos del sistema (RN01)
define('ROL_VOLUNTARIO', 'voluntario');
define('ROL_ORGANIZACION', 'organizacion');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
