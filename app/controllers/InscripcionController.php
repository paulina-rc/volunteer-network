<?php
require_once __DIR__ . '/../helpers/vista.php';

class InscripcionController
{
    public function gestionar(): void
    {
        renderPendiente('Gestionar inscripciones', 'Se implementa en el Paso 6 del plan de desarrollo (RF08, CU06).');
    }

    public function inscribirse(): void
    {
        renderPendiente('Inscribirse a una oportunidad', 'Se implementa en el Paso 6 del plan de desarrollo (RF07, RN04, CU05).');
    }

    public function aceptar(): void
    {
        renderPendiente('Aceptar inscripción', 'Se implementa en el Paso 6 del plan de desarrollo (RN05, RN06).');
    }

    public function rechazar(): void
    {
        renderPendiente('Rechazar inscripción', 'Se implementa en el Paso 6 del plan de desarrollo (RN06).');
    }
}
