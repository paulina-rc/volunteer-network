<?php
require_once __DIR__ . '/../helpers/view.php';

class OpportunityController
{
    /**
     * Home page: featured opportunities, the category grid with live counts and
     * the platform counters (RF06 entry point).
     */
    public function home(): void
    {
        $opportunityModel = new OpportunityModel();

        $categories = (new CategoryModel())->getAllWithCounts();
        $featuredOpportunities = $opportunityModel->getFeatured(3);
        $platformStats = $opportunityModel->getPlatformStats();

        require __DIR__ . '/../views/home.php';
    }

    /**
     * Opportunity search and filtering (RF06).
     * Every filter is read from the query string, so a filtered result is a
     * shareable URL and the form can render itself back in the same state.
     */
    public function search(): void
    {
        $categories = (new CategoryModel())->getAll();

        // Selecting no category means "do not filter by category", so the empty
        // list and a missing parameter behave the same way. The view checks
        // exactly the boxes in $selectedCategories, or all of them when it is
        // empty, which keeps the form and the results in agreement.
        $selectedCategories = array_values(array_filter(
            array_map('intval', (array) ($_GET['categories'] ?? [])),
            static fn (int $id): bool => $id > 0
        ));

        $filters = [
            'text'       => trim((string) ($_GET['text'] ?? '')),
            'location'   => trim((string) ($_GET['location'] ?? '')),
            'date'       => (string) ($_GET['date'] ?? ''),
            'sort'       => (string) ($_GET['sort'] ?? 'recent'),
            'categories' => $selectedCategories,
        ];

        $opportunities = (new OpportunityModel())->searchWithFilters($filters);

        // Tells the empty state whether to offer "ver todas" or simply say that
        // nothing is published yet.
        $hasActiveFilters = $filters['text'] !== ''
            || $filters['location'] !== ''
            || $filters['date'] !== ''
            || $selectedCategories !== [];

        require __DIR__ . '/../views/opportunity_list.php';
    }

    /**
     * Opportunity detail. An unknown or malformed id renders the 404 screen
     * instead of a blank page.
     */
    public function view(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $opportunityModel = new OpportunityModel();
        $opportunity = $id > 0 ? $opportunityModel->findById($id) : null;

        if ($opportunity === null) {
            $this->renderNotFound(
                'Oportunidad no encontrada',
                'La oportunidad que buscás no existe o fue eliminada por la organización.'
            );
            return;
        }

        $requirements = requirementLines($opportunity['requirements'] ?? null);
        $skills = $opportunityModel->getSkills($id);

        // Lets the panel show "ya estás inscrita" and its current state instead
        // of a button that would only be rejected (RF11).
        $currentEnrollment = null;
        if (isVolunteer() && isset($_SESSION['volunteer_id'])) {
            $enrollmentModel = new EnrollmentModel();
            foreach ($enrollmentModel->getByVolunteer((int) $_SESSION['volunteer_id']) as $enrollment) {
                if ((int) $enrollment['opportunity_id'] === $id) {
                    $currentEnrollment = $enrollment;
                    break;
                }
            }
        }

        require __DIR__ . '/../views/opportunity_detail.php';
    }

    /**
     * RF05 / RN02 / RN03 — publish a new opportunity.
     */
    public function publish(): void
    {
        requireRole(ROLE_ORGANIZATION);
        $organizationId = $this->currentOrganizationId();

        if (!$this->isPost()) {
            $this->renderForm(null, $this->emptyFormData(), []);
            return;
        }

        if (!verifyCsrf()) {
            flash('error', 'No pudimos guardar la oportunidad. Probá de nuevo.');
            redirectTo('publish_opportunity');
        }

        $isDraft = ($_POST['submit_action'] ?? '') === 'draft';
        $data = $this->readFormData();
        $errors = $this->validate($data, $isDraft);

        // RN02 — an incomplete profile may keep drafts, but may not publish.
        if (!$isDraft && !$this->organizationCanPublish($organizationId)) {
            flash('error', 'Antes de publicar tenés que completar la descripción, la ubicación y el contacto de tu organización (RN02).');
            redirectTo('view_organization_profile');
        }

        if ($errors !== []) {
            $this->renderForm(null, $data, $errors);
            return;
        }

        $newId = (new OpportunityModel())->create([
            'organization_id' => $organizationId,
            'category_id'     => $data['category_id'],
            'title'           => $data['title'],
            'description'     => $data['description'],
            'requirements'    => $data['requirements'],
            'location'        => $data['location'],
            'activity_date'   => $data['activity_date'],
            'time'            => $data['time'],
            'total_slots'     => $data['total_slots'],
            'status'          => $isDraft ? 'draft' : 'active',
        ], $data['skills']);

        flash('success', $isDraft
            ? 'Guardamos la oportunidad como borrador. No es visible para las personas voluntarias hasta que la publiques.'
            : 'Publicaste la oportunidad. Ya aparece en el buscador.');
        redirectTo('view_opportunity', ['id' => $newId]);
    }

    /**
     * RN09 — edit an opportunity that belongs to the organization in session.
     */
    public function edit(): void
    {
        requireRole(ROLE_ORGANIZATION);

        $opportunityModel = new OpportunityModel();
        $id = (int) ($_POST['opportunity_id'] ?? $_GET['id'] ?? 0);
        $opportunity = $this->loadOwnedOpportunity($id);

        if (!$this->isPost()) {
            $data = $this->readFormData($opportunity);
            $data['skills'] = array_map('intval', array_column($opportunityModel->getSkills($id), 'id'));
            $this->renderForm($opportunity, $data, []);
            return;
        }

        if (!verifyCsrf()) {
            flash('error', 'No pudimos guardar los cambios. Probá de nuevo.');
            redirectTo('edit_opportunity', ['id' => $id]);
        }

        $isDraft = ($_POST['submit_action'] ?? '') === 'draft';
        $data = $this->readFormData();
        $errors = $this->validate($data, $isDraft);

        if (!$isDraft && $opportunity['status'] === 'draft'
            && !$this->organizationCanPublish($this->currentOrganizationId())) {
            flash('error', 'Antes de publicar tenés que completar la descripción, la ubicación y el contacto de tu organización (RN02).');
            redirectTo('view_organization_profile');
        }

        if ($errors !== []) {
            $this->renderForm($opportunity, $data, $errors);
            return;
        }

        // A published opportunity keeps its current state unless it was a draft
        // being published now, or the editor explicitly saved it as a draft.
        $status = $isDraft ? 'draft' : ($opportunity['status'] === 'draft' ? 'active' : $opportunity['status']);

        $opportunityModel->update($id, [
            'category_id'   => $data['category_id'],
            'title'         => $data['title'],
            'description'   => $data['description'],
            'requirements'  => $data['requirements'],
            'location'      => $data['location'],
            'activity_date' => $data['activity_date'],
            'time'          => $data['time'],
            'total_slots'   => $data['total_slots'],
            'status'        => $status,
        ], $data['skills']);

        flash('success', 'Actualizamos la oportunidad.');
        redirectTo('view_opportunity', ['id' => $id]);
    }

    /**
     * RN09 — the organization closes or reopens an opportunity by hand.
     */
    public function close(): void
    {
        requireRole(ROLE_ORGANIZATION);

        $id = (int) ($_POST['opportunity_id'] ?? 0);

        if (!$this->isPost() || !verifyCsrf()) {
            flash('error', 'No pudimos cambiar el estado de la oportunidad. Probá de nuevo.');
            redirectTo('view_organization_profile');
        }

        $opportunity = $this->loadOwnedOpportunity($id);
        $newStatus = ($_POST['new_status'] ?? 'closed') === 'active' ? 'active' : 'closed';

        if ($newStatus === 'active') {
            if ((int) $opportunity['available_slots'] <= 0) {
                flash('error', 'No se puede reabrir: la oportunidad no tiene cupos disponibles.');
                redirectTo('view_organization_profile');
            }
            if ($opportunity['activity_date'] < date('Y-m-d')) {
                flash('error', 'No se puede reabrir: la fecha de la actividad ya pasó.');
                redirectTo('view_organization_profile');
            }
        }

        (new OpportunityModel())->setStatus($id, $newStatus);

        flash('success', $newStatus === 'closed'
            ? 'Cerraste la oportunidad. Ya no aparece en el buscador ni admite inscripciones.'
            : 'Reabriste la oportunidad. Vuelve a aparecer en el buscador.');
        redirectTo('view_organization_profile');
    }

    // ------------------------------------------------------------
    // Form handling for publish / edit
    // ------------------------------------------------------------

    /**
     * Reads the opportunity form, falling back to an existing row when first
     * rendering the edit screen.
     */
    private function readFormData(?array $opportunity = null): array
    {
        if ($opportunity !== null) {
            return [
                'title'         => (string) $opportunity['title'],
                'category_id'   => (int) $opportunity['category_id'],
                'total_slots'   => (int) $opportunity['total_slots'],
                'description'   => (string) $opportunity['description'],
                'requirements'  => (string) $opportunity['requirements'],
                'location'      => (string) $opportunity['location'],
                'activity_date' => (string) $opportunity['activity_date'],
                'time'          => (string) $opportunity['time'],
                'skills'        => [],
            ];
        }

        return [
            'title'         => trim((string) ($_POST['title'] ?? '')),
            'category_id'   => (int) ($_POST['category_id'] ?? 0),
            'total_slots'   => (int) ($_POST['total_slots'] ?? 0),
            'description'   => trim((string) ($_POST['description'] ?? '')),
            'requirements'  => trim((string) ($_POST['requirements'] ?? '')),
            'location'      => trim((string) ($_POST['location'] ?? '')),
            'activity_date' => trim((string) ($_POST['activity_date'] ?? '')),
            'time'          => trim((string) ($_POST['time'] ?? '')),
            'skills'        => array_values(array_filter(
                array_map('intval', (array) ($_POST['skills'] ?? [])),
                static fn (int $skillId): bool => $skillId > 0
            )),
        ];
    }

    private function emptyFormData(): array
    {
        return [
            'title' => '', 'category_id' => 0, 'total_slots' => '', 'description' => '',
            'requirements' => '', 'location' => '', 'activity_date' => '', 'time' => '',
            'skills' => [],
        ];
    }

    /**
     * RN03 — an opportunity needs a title, description, skills, location, date
     * and slots. Returns field name => message, so each error can be printed
     * under the field it belongs to.
     */
    private function validate(array $data, bool $isDraft): array
    {
        $errors = [];

        if ($data['title'] === '') {
            $errors['title'] = 'El título es obligatorio.';
        } elseif (mb_strlen($data['title']) > 150) {
            $errors['title'] = 'El título no puede superar los 150 caracteres.';
        }

        if ($data['description'] === '') {
            $errors['description'] = 'Contá de qué trata la actividad.';
        }

        if ($data['location'] === '') {
            $errors['location'] = 'Indicá dónde se realiza la actividad.';
        }

        if ($data['category_id'] <= 0) {
            $errors['category_id'] = 'Elegí una categoría.';
        }

        if ($data['activity_date'] === '') {
            $errors['activity_date'] = 'Indicá la fecha de la actividad.';
        } elseif (!$this->isValidDate($data['activity_date'])) {
            $errors['activity_date'] = 'La fecha no tiene un formato válido.';
        } elseif (!$isDraft && $data['activity_date'] < date('Y-m-d')) {
            // A draft may hold a past date while it is being prepared.
            $errors['activity_date'] = 'La fecha no puede ser anterior a hoy. Guardalo como borrador si todavía no está definida.';
        }

        if ($data['total_slots'] <= 0) {
            $errors['total_slots'] = 'Los cupos tienen que ser un número mayor que cero.';
        }

        if ($data['skills'] === []) {
            $errors['skills'] = 'Marcá al menos una habilidad requerida.';
        }

        return $errors;
    }

    private function isValidDate(string $date): bool
    {
        $parsed = DateTimeImmutable::createFromFormat('Y-m-d', $date);

        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }

    private function renderForm(?array $opportunity, array $formData, array $formErrors): void
    {
        $categories = (new CategoryModel())->getAll();
        $allSkills = (new SkillModel())->getAll();
        $isEditing = $opportunity !== null;
        $profileComplete = (new OrganizationModel())
            ->isProfileComplete((new OrganizationModel())->findById($this->currentOrganizationId()) ?? []);

        require __DIR__ . '/../views/opportunity_publish.php';
    }

    /**
     * Loads an opportunity and refuses it unless it belongs to the organization
     * in session. Never returns when the check fails.
     */
    private function loadOwnedOpportunity(int $id): array
    {
        $opportunity = $id > 0 ? (new OpportunityModel())->findById($id) : null;

        if ($opportunity === null) {
            flash('error', 'Esa oportunidad no existe.');
            redirectTo('view_organization_profile');
        }

        if ((int) $opportunity['organization_id'] !== $this->currentOrganizationId()) {
            flash('error', 'Esa oportunidad no pertenece a tu organización.');
            redirectTo('view_organization_profile');
        }

        return $opportunity;
    }

    private function organizationCanPublish(int $organizationId): bool
    {
        $organizationModel = new OrganizationModel();
        $organization = $organizationModel->findById($organizationId);

        return $organization !== null && $organizationModel->isProfileComplete($organization);
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

    private function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    /**
     * Sends a 404 and renders the shared error screen.
     */
    private function renderNotFound(string $title, string $message): void
    {
        http_response_code(404);
        $errorTitle = $title;
        $errorMessage = $message;
        require __DIR__ . '/../views/error_404.php';
    }
}
