<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/conexion.php';

spl_autoload_register(function ($clase) {
    $posiblesRutas = [
        __DIR__ . '/../app/controllers/' . $clase . '.php',
        __DIR__ . '/../app/models/' . $clase . '.php',
    ];
    foreach ($posiblesRutas as $ruta) {
        if (file_exists($ruta)) {
            require_once $ruta;
            return;
        }
    }
});

// Mapa de rutas: ?accion=... => [Controlador, método]
// Ver sección 8.2 (Rutas principales) de la documentación técnica.
$rutas = [
    'home'                          => ['OportunidadController', 'home'],

    'registro'                      => ['UsuarioController', 'mostrarRegistro'],
    'registrar_usuario'             => ['UsuarioController', 'registrar'],
    'iniciar_sesion'                => ['UsuarioController', 'iniciarSesion'],
    'cerrar_sesion'                 => ['UsuarioController', 'cerrarSesion'],

    'ver_perfil_voluntario'         => ['VoluntarioController', 'verPerfil'],
    'guardar_perfil_voluntario'     => ['VoluntarioController', 'guardarPerfil'],
    'recomendaciones'               => ['VoluntarioController', 'recomendaciones'],

    'ver_perfil_organizacion'       => ['OrganizacionController', 'verPerfil'],
    'guardar_perfil_organizacion'   => ['OrganizacionController', 'guardarPerfil'],

    'buscar_oportunidades'          => ['OportunidadController', 'buscar'],
    'ver_oportunidad'               => ['OportunidadController', 'ver'],
    'publicar_oportunidad'          => ['OportunidadController', 'publicar'],
    'editar_oportunidad'            => ['OportunidadController', 'editar'],
    'cerrar_oportunidad'            => ['OportunidadController', 'cerrar'],

    'gestionar_inscripciones'       => ['InscripcionController', 'gestionar'],
    'inscribirse'                   => ['InscripcionController', 'inscribirse'],
    'aceptar_inscripcion'           => ['InscripcionController', 'aceptar'],
    'rechazar_inscripcion'          => ['InscripcionController', 'rechazar'],
];

$accion = $_GET['accion'] ?? 'home';

if (!array_key_exists($accion, $rutas)) {
    http_response_code(404);
    echo '<h1 style="font-family:sans-serif;color:#0F4C5C">404 — Ruta no encontrada</h1>';
    echo '<p style="font-family:sans-serif">La acción "' . htmlspecialchars($accion) . '" no existe.</p>';
    exit;
}

[$nombreControlador, $metodo] = $rutas[$accion];

if (!class_exists($nombreControlador)) {
    http_response_code(500);
    die('Controlador no encontrado: ' . htmlspecialchars($nombreControlador));
}

$controlador = new $nombreControlador();

if (!method_exists($controlador, $metodo)) {
    http_response_code(500);
    die('Método no encontrado: ' . htmlspecialchars($nombreControlador) . '::' . htmlspecialchars($metodo));
}

$controlador->$metodo();
