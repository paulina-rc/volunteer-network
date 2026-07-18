<?php
require_once __DIR__ . '/../helpers/vista.php';

class OrganizacionController
{
    public function verPerfil(): void
    {
        renderPendiente('Perfil de organización', 'Se implementa en el Paso 3 del plan de desarrollo (RF04, RN02).');
    }

    public function guardarPerfil(): void
    {
        renderPendiente('Guardar perfil de organización', 'Se implementa en el Paso 3 del plan de desarrollo (RF04, RN02).');
    }
}
