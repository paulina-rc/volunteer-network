<?php
require_once __DIR__ . '/../helpers/view.php';

class EnrollmentController
{
    public function manage(): void
    {
        renderPending('Gestionar inscripciones', 'Se implementa en el Paso 6 del plan de desarrollo (RF08, CU06).');
    }

    public function enroll(): void
    {
        renderPending('Inscribirse a una oportunidad', 'Se implementa en el Paso 6 del plan de desarrollo (RF07, RN04, CU05).');
    }

    public function accept(): void
    {
        renderPending('Aceptar inscripción', 'Se implementa en el Paso 6 del plan de desarrollo (RN05, RN06).');
    }

    public function reject(): void
    {
        renderPending('Rechazar inscripción', 'Se implementa en el Paso 6 del plan de desarrollo (RN06).');
    }
}
