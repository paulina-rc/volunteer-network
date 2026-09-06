<?php
require_once __DIR__ . '/../helpers/view.php';

class EnrollmentController
{
    public function manage(): void
    {
        require __DIR__ . '/../views/enrollment_management.php';
    }

    /**
     * RF07 / CU05. The insert itself lands in Step 6, but the RN04 guard runs
     * here already, using the same acceptsEnrollments() the card and the detail
     * screen use — so a volunteer cannot reach this action for an opportunity
     * whose button they were never shown.
     */
    public function enroll(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $opportunity = $id > 0 ? (new OpportunityModel())->findById($id) : null;

        if ($opportunity === null) {
            flash('error', 'La oportunidad que intentás abrir no existe.');
            redirectTo('search_opportunities');
        }

        if (!acceptsEnrollments($opportunity)) {
            flash('warning', enrollmentBlockedReason($opportunity));
            redirectTo('view_opportunity', ['id' => $id]);
        }

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
