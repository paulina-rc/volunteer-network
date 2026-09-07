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

/**
 * Percent-encodes every segment of a URL path, leaving the slashes alone.
 *
 * It decodes first, so running it on an already-encoded path is harmless:
 * "Red%20de%20voluntariados" and "Red de voluntariados" both come out as
 * "Red%20de%20voluntariados" instead of the double-encoded "Red%2520de...".
 */
function encodeUrlPath(string $path): string
{
    $segments = array_map(
        static fn (string $segment): string => rawurlencode(rawurldecode($segment)),
        explode('/', $path)
    );

    return implode('/', $segments);
}

/**
 * Works out the public base path of this installation from the request, so the
 * project folder can be named anything — including a name with spaces, like
 * "Red de voluntariados" — without editing this file.
 *
 * Two strategies, in order:
 *   1. everything up to and including "/public/" in the request path, which
 *      covers http://localhost/Red%20de%20voluntariados/public/index.php;
 *   2. the directory of the running script, which covers the case where the
 *      document root is already the public/ folder (base path "/").
 *
 * The result always ends in "/" and is safe to drop straight into an href.
 */
function detectBaseUrl(): string
{
    $requestPath = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');

    if (preg_match('#^(.*?/public/)#i', $requestPath, $matches) === 1) {
        return encodeUrlPath($matches[1]);
    }

    // Reached only when the URL has no /public/ segment. On the CLI (tests,
    // maintenance scripts) there is no base path to speak of.
    if (PHP_SAPI === 'cli' && $requestPath === '') {
        return '/';
    }

    $scriptDirectory = str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php')));

    if ($scriptDirectory === '' || $scriptDirectory === '.' || $scriptDirectory === '/') {
        return '/';
    }

    return encodeUrlPath(rtrim($scriptDirectory, '/')) . '/';
}

/**
 * Public base path, ending in "/". Detected automatically; set it by hand only
 * if this install sits behind a rewrite that hides the real path.
 */
define('BASE_URL', detectBaseUrl());

define('MAX_SLOTS_DEFAULT', 20);

// Valid system roles (RN01)
define('ROLE_VOLUNTEER', 'volunteer');
define('ROLE_ORGANIZATION', 'organization');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
