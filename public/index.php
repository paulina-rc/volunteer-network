<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../app/helpers/functions.php';

spl_autoload_register(function ($class) {
    $possiblePaths = [
        __DIR__ . '/../app/controllers/' . $class . '.php',
        __DIR__ . '/../app/models/' . $class . '.php',
    ];
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Route map: ?action=... => [Controller, method]
// See section 8.2 (Main routes) of the technical documentation.
$routes = [
    'home'                          => ['OpportunityController', 'home'],

    'register'                      => ['UserController', 'showRegister'],
    'register_user'                 => ['UserController', 'register'],
    'login'                         => ['UserController', 'login'],
    'logout'                        => ['UserController', 'logout'],

    'view_volunteer_profile'        => ['VolunteerController', 'viewProfile'],
    'save_volunteer_profile'        => ['VolunteerController', 'saveProfile'],
    'recommendations'               => ['VolunteerController', 'recommendations'],

    'view_organization_profile'     => ['OrganizationController', 'viewProfile'],
    'save_organization_profile'     => ['OrganizationController', 'saveProfile'],

    'search_opportunities'          => ['OpportunityController', 'search'],
    'view_opportunity'              => ['OpportunityController', 'view'],
    'publish_opportunity'           => ['OpportunityController', 'publish'],
    'edit_opportunity'              => ['OpportunityController', 'edit'],
    'close_opportunity'             => ['OpportunityController', 'close'],

    'manage_enrollments'            => ['EnrollmentController', 'manage'],
    'enroll'                        => ['EnrollmentController', 'enroll'],
    'accept_enrollment'             => ['EnrollmentController', 'accept'],
    'reject_enrollment'             => ['EnrollmentController', 'reject'],
    'complete_enrollment'           => ['EnrollmentController', 'complete'],
];

$action = $_GET['action'] ?? 'home';

if (!array_key_exists($action, $routes)) {
    http_response_code(404);
    echo '<h1 style="font-family:sans-serif;color:#0F4C5C">404 — Ruta no encontrada</h1>';
    echo '<p style="font-family:sans-serif">La acción "' . htmlspecialchars($action) . '" no existe.</p>';
    exit;
}

[$controllerName, $methodName] = $routes[$action];

if (!class_exists($controllerName)) {
    http_response_code(500);
    die('Controlador no encontrado: ' . htmlspecialchars($controllerName));
}

$controller = new $controllerName();

if (!method_exists($controller, $methodName)) {
    http_response_code(500);
    die('Método no encontrado: ' . htmlspecialchars($controllerName) . '::' . htmlspecialchars($methodName));
}

$controller->$methodName();
