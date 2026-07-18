<?php
require_once __DIR__ . '/../helpers/vista.php';

class UsuarioController
{
    public function mostrarRegistro(): void
    {
        renderPendiente('Registro e inicio de sesión', 'Se implementa en el Paso 2 del plan de desarrollo (RF01, RF02, RN01).');
    }

    public function registrar(): void
    {
        renderPendiente('Registrar usuario', 'Se implementa en el Paso 2 del plan de desarrollo (CU01).');
    }

    public function iniciarSesion(): void
    {
        renderPendiente('Iniciar sesión', 'Se implementa en el Paso 2 del plan de desarrollo (CU02).');
    }

    public function cerrarSesion(): void
    {
        $_SESSION = [];
        session_destroy();
        header('Location: ' . BASE_URL);
        exit;
    }
}
