<?php
require_once __DIR__ . '/../helpers/view.php';

class EnrollmentController
{
    /**
     * Enrollment states an organization can filter by on the management screen.
     */
    private const FILTERABLE_STATUSES = ['pending', 'accepted', 'rejected', 'completed'];

    /**
     * RF07 / CU05 — a volunteer enrolls in an opportunity.
     *
     * Every rejection path leaves a flash message and sends the volunteer back,
     * so they always learn why nothing happened.
     */
    public function enroll(): void
    {
        requireRole(ROLE_VOLUNTEER);

        $opportunityId = (int) ($_POST['opportunity_id'] ?? 0);

        if (!$this->isPost() || !verifyCsrf()) {
            flash('error', 'No pudimos procesar tu inscripción. Probá de nuevo.');
            redirectTo('view_opportunity', ['id' => $opportunityId]);
        }

        $opportunityModel = new OpportunityModel();
        $opportunity = $opportunityId > 0 ? $opportunityModel->findById($opportunityId) : null;

        if ($opportunity === null) {
            flash('error', 'La oportunidad que intentás abrir no existe.');
            redirectTo('search_opportunities');
        }

        $volunteerId = $this->currentVolunteerId();
        $enrollmentModel = new EnrollmentModel();

        if ($enrollmentModel->existsFor($opportunityId, $volunteerId)) {
            flash('warning', 'Ya estabas inscrita en esta oportunidad. Podés seguir su estado desde tu perfil.');
            redirectTo('view_opportunity', ['id' => $opportunityId]);
        }

        // RN04 — the same check the card and the detail screen use.
        if (!acceptsEnrollments($opportunity)) {
            flash('warning', enrollmentBlockedReason($opportunity));
            redirectTo('view_opportunity', ['id' => $opportunityId]);
        }

        // RN06 — the model always inserts as 'pending'.
        $enrollmentModel->create($opportunityId, $volunteerId);
        $opportunityModel->syncAvailableSlots($opportunityId);

        flash('success', 'Tu inscripción quedó registrada como PENDIENTE. La organización la va a revisar y te va a avisar cuando la acepte o la rechace.');
        redirectTo('view_opportunity', ['id' => $opportunityId]);
    }

    /**
     * RF08 / CU06 — the organization reviews the enrollments it received.
     *
     * With ?opportunity_id= it shows one opportunity (after checking the
     * opportunity really belongs to the organization in session, RN07);
     * without it, every enrollment across all of them. ?status= filters the
     * list, while the tab counts always describe the unfiltered set.
     */
    public function manage(): void
    {
        requireRole(ROLE_ORGANIZATION);

        $organizationId = $this->currentOrganizationId();
        $opportunityModel = new OpportunityModel();
        $enrollmentModel = new EnrollmentModel();

        $opportunityId = (int) ($_GET['opportunity_id'] ?? 0);
        $selectedOpportunity = null;

        if ($opportunityId > 0) {
            $selectedOpportunity = $opportunityModel->findById($opportunityId);

            if ($selectedOpportunity === null
                || (int) $selectedOpportunity['organization_id'] !== $organizationId) {
                flash('error', 'Esa oportunidad no pertenece a tu organización.');
                redirectTo('manage_enrollments');
            }

            $enrollments = $enrollmentModel->getByOpportunity($opportunityId);
        } else {
            $enrollments = $enrollmentModel->getByOrganization($organizationId);
        }

        // Counted before filtering, so the tabs keep showing the totals.
        $counts = $enrollmentModel->countByStatus($enrollments);

        $statusFilter = (string) ($_GET['status'] ?? 'all');
        if (!in_array($statusFilter, self::FILTERABLE_STATUSES, true)) {
            $statusFilter = 'all';
        }

        $visibleEnrollments = $statusFilter === 'all'
            ? $enrollments
            : array_values(array_filter(
                $enrollments,
                static fn (array $enrollment): bool => $enrollment['status'] === $statusFilter
            ));

        // Feeds the opportunity picker at the top of the screen.
        $organizationOpportunities = $opportunityModel->getByOrganization($organizationId);

        require __DIR__ . '/../views/enrollment_management.php';
    }

    /**
     * RN06 — the organization accepts an enrollment.
     * RN05 — if that fills the last slot, syncAvailableSlots() closes the
     * opportunity and the volunteer is told so explicitly.
     */
    public function accept(): void
    {
        $enrollment = $this->loadOwnedEnrollment();
        $opportunityId = (int) $enrollment['opportunity_id'];

        if ($enrollment['status'] === 'accepted') {
            flash('warning', 'Esa inscripción ya estaba aceptada.');
            $this->backToManagement($opportunityId);
        }

        // RN04/RN05 — never hand out a slot that does not exist.
        if ((int) $enrollment['available_slots'] <= 0) {
            flash('error', 'No quedan cupos disponibles en esta oportunidad, así que no se puede aceptar la inscripción.');
            $this->backToManagement($opportunityId);
        }

        (new EnrollmentModel())->setStatus((int) $enrollment['id'], 'accepted');

        flash('success', 'Aceptaste la inscripción de ' . $enrollment['volunteer_name'] . '.');
        $this->syncAndReport($opportunityId);
        $this->backToManagement($opportunityId);
    }

    /**
     * RN06 — the organization rejects an enrollment. Rejecting an accepted one
     * frees its slot again, which syncAvailableSlots() picks up.
     */
    public function reject(): void
    {
        $enrollment = $this->loadOwnedEnrollment();
        $opportunityId = (int) $enrollment['opportunity_id'];

        if ($enrollment['status'] === 'rejected') {
            flash('warning', 'Esa inscripción ya estaba rechazada.');
            $this->backToManagement($opportunityId);
        }

        (new EnrollmentModel())->setStatus((int) $enrollment['id'], 'rejected');

        flash('success', 'Rechazaste la inscripción de ' . $enrollment['volunteer_name'] . '.');
        $this->syncAndReport($opportunityId);
        $this->backToManagement($opportunityId);
    }

    /**
     * Marks an accepted enrollment as completed once the activity has taken
     * place. Completed enrollments keep consuming their slot, which is why the
     * counters treat 'accepted' and 'completed' the same way.
     */
    public function complete(): void
    {
        $enrollment = $this->loadOwnedEnrollment();
        $opportunityId = (int) $enrollment['opportunity_id'];

        if ($enrollment['status'] !== 'accepted') {
            flash('error', 'Solo se puede marcar como completada una inscripción que ya fue aceptada.');
            $this->backToManagement($opportunityId);
        }

        if ($enrollment['activity_date'] >= date('Y-m-d')) {
            flash('error', 'Todavía no se puede marcar como completada: la actividad no ha ocurrido.');
            $this->backToManagement($opportunityId);
        }

        (new EnrollmentModel())->setStatus((int) $enrollment['id'], 'completed');
        (new OpportunityModel())->syncAvailableSlots($opportunityId);

        flash('success', 'Marcaste como completada la participación de ' . $enrollment['volunteer_name'] . '.');
        $this->backToManagement($opportunityId);
    }

    // ------------------------------------------------------------
    // Shared guards and helpers
    // ------------------------------------------------------------

    /**
     * Shared guard for accept / reject / complete: organization role, POST,
     * CSRF, and the enrollment must belong to one of this organization's own
     * opportunities (RN07). Never returns when any of those fails.
     */
    private function loadOwnedEnrollment(): array
    {
        requireRole(ROLE_ORGANIZATION);

        if (!$this->isPost() || !verifyCsrf()) {
            flash('error', 'No pudimos procesar la acción. Probá de nuevo.');
            redirectTo('manage_enrollments');
        }

        $enrollmentId = (int) ($_POST['enrollment_id'] ?? 0);
        $enrollment = $enrollmentId > 0 ? (new EnrollmentModel())->findById($enrollmentId) : null;

        if ($enrollment === null) {
            flash('error', 'Esa inscripción no existe.');
            redirectTo('manage_enrollments');
        }

        if ((int) $enrollment['organization_id'] !== $this->currentOrganizationId()) {
            flash('error', 'Esa inscripción no pertenece a tu organización.');
            redirectTo('manage_enrollments');
        }

        return $enrollment;
    }

    /**
     * Recomputes the slot counter and, when the opportunity just filled up,
     * says so — this is what makes RN05 visible on screen instead of only in
     * the database.
     */
    private function syncAndReport(int $opportunityId): void
    {
        $isFull = (new OpportunityModel())->syncAvailableSlots($opportunityId);

        if ($isFull) {
            flash('warning', 'Se llenaron los cupos, así que la oportunidad pasó automáticamente a Cerrada.');
        }
    }

    private function backToManagement(int $opportunityId): void
    {
        redirectTo('manage_enrollments', ['opportunity_id' => $opportunityId]);
    }

    private function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    /**
     * The profile id is put in the session at login; it is looked up again only
     * as a fallback for sessions created before that.
     */
    private function currentVolunteerId(): int
    {
        if (isset($_SESSION['volunteer_id'])) {
            return (int) $_SESSION['volunteer_id'];
        }

        $volunteer = (new VolunteerModel())->findByUserId((int) $_SESSION['user_id']);
        $_SESSION['volunteer_id'] = (int) $volunteer['id'];

        return (int) $volunteer['id'];
    }

    private function currentOrganizationId(): int
    {
        if (isset($_SESSION['organization_id'])) {
            return (int) $_SESSION['organization_id'];
        }

        $organization = (new OrganizationModel())->findByUserId((int) $_SESSION['user_id']);
        $_SESSION['organization_id'] = (int) $organization['id'];

        return (int) $organization['id'];
    }
}
