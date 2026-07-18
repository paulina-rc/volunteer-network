<?php
require_once __DIR__ . '/../helpers/view.php';

class OrganizationController
{
    public function viewProfile(): void
    {
        require __DIR__ . '/../views/organization_profile.php';
    }

    public function saveProfile(): void
    {
        renderPending('Guardar perfil de organización', 'Se implementa en el Paso 3 del plan de desarrollo (RF04, RN02).');
    }
}
