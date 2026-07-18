<?php
require_once __DIR__ . '/../helpers/vista.php';

class VoluntarioController
{
    public function verPerfil(): void
    {
        renderPendiente('Perfil de voluntario', 'Se implementa en el Paso 3 del plan de desarrollo (RF03).');
    }

    public function guardarPerfil(): void
    {
        renderPendiente('Guardar perfil de voluntario', 'Se implementa en el Paso 3 del plan de desarrollo (RF03).');
    }

    public function recomendaciones(): void
    {
        renderPendiente('Recomendado para vos', 'Se implementa en el Paso 7 del plan de desarrollo (RF10, RN08).');
    }
}
