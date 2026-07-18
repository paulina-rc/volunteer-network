<?php
/**
 * General configuration for Enlaza.
 * Adjust these values for the local environment (XAMPP/Laragon) or hosting.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'enlaza');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('BASE_URL', '/enlaza/public/');

define('MAX_SLOTS_DEFAULT', 20);

// Valid system roles (RN01)
define('ROLE_VOLUNTEER', 'volunteer');
define('ROLE_ORGANIZATION', 'organization');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
