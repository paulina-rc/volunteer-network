<?php
/**
 * General configuration for Enlaza.
 * Adjust these values for the local environment (XAMPP/Laragon) or hosting.
 */

/**
 * Errors are logged, never printed. A PHP notice rendered inside the page
 * would leak file paths and query fragments to the visitor (RNF02).
 * While developing, flip DISPLAY_ERRORS to true to see them on screen again.
 */
define('DISPLAY_ERRORS', false);

error_reporting(E_ALL);
ini_set('display_errors', DISPLAY_ERRORS ? '1' : '0');
ini_set('display_startup_errors', DISPLAY_ERRORS ? '1' : '0');
ini_set('log_errors', '1');

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
