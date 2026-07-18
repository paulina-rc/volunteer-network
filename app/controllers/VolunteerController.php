<?php
require_once __DIR__ . '/../helpers/view.php';

class VolunteerController
{
    public function viewProfile(): void
    {
        require __DIR__ . '/../views/volunteer_profile.php';
    }

    public function saveProfile(): void
    {
        renderPending('Guardar perfil de voluntario', 'Se implementa en el Paso 3 del plan de desarrollo (RF03).');
    }

    public function recommendations(): void
    {
        renderPending('Recomendado para vos', 'Se implementa en el Paso 7 del plan de desarrollo (RF10, RN08).');
    }
}
