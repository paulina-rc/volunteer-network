<?php
require_once __DIR__ . '/../helpers/view.php';

class OrganizationController
{
    /**
     * RF04 — organization profile.
     *
     * With ?id= it shows any organization's public profile. Without it, the
     * profile of the organization in session, which additionally gets the
     * management actions and the RN02 warning.
     */
    public function viewProfile(): void
    {
        $organizationModel = new OrganizationModel();
        $requestedId = (int) ($_GET['id'] ?? 0);

        if ($requestedId > 0) {
            $organization = $organizationModel->findById($requestedId);

            if ($organization === null) {
                http_response_code(404);
                $errorTitle = 'Organización no encontrada';
                $errorMessage = 'La organización que buscás no existe o cambió de nombre.';
                require __DIR__ . '/../views/error_404.php';
                return;
            }

            // The owner opening their own id still gets the private view.
            $isOwner = isOrganization()
                && (int) ($_SESSION['organization_id'] ?? 0) === (int) $organization['id'];
        } else {
            requireRole(ROLE_ORGANIZATION);
            $organization = $organizationModel->findByUserId((int) $_SESSION['user_id']);

            if ($organization === null) {
                flash('error', 'No encontramos el perfil de tu organización.');
                redirectTo('home');
            }

            $isOwner = true;
        }

        $this->renderProfile($organization, $isOwner);
    }

    /**
     * RF04 / RN02 — saves the profile and keeps profile_complete in sync, which
     * is the flag that decides whether this organization may publish.
     */
    public function saveProfile(): void
    {
        requireRole(ROLE_ORGANIZATION);

        if (!$this->isPost() || !verifyCsrf()) {
            flash('error', 'No pudimos guardar los cambios. Probá de nuevo.');
            redirectTo('view_organization_profile');
        }

        $organizationModel = new OrganizationModel();
        $organization = $organizationModel->findByUserId((int) $_SESSION['user_id']);

        if ($organization === null) {
            flash('error', 'No encontramos el perfil de tu organización.');
            redirectTo('home');
        }

        $data = [
            'name'         => trim((string) ($_POST['name'] ?? '')),
            'category_id'  => (int) ($_POST['category_id'] ?? 0),
            'description'  => trim((string) ($_POST['description'] ?? '')),
            'location'     => trim((string) ($_POST['location'] ?? '')),
            'contact'      => trim((string) ($_POST['contact'] ?? '')),
            'founded_year' => (int) ($_POST['founded_year'] ?? 0),
        ];

        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'El nombre de la organización es obligatorio.';
        } elseif (mb_strlen($data['name']) > 150) {
            $errors['name'] = 'El nombre no puede superar los 150 caracteres.';
        }
        if ($data['founded_year'] !== 0
            && ($data['founded_year'] < 1800 || $data['founded_year'] > (int) date('Y'))) {
            $errors['founded_year'] = 'Indicá un año entre 1800 y ' . date('Y') . '.';
        }

        if ($errors !== []) {
            $this->renderProfile(array_merge($organization, $data), true, $errors);
            return;
        }

        $organizationModel->updateProfile((int) $organization['id'], $data);

        if ($organizationModel->isProfileComplete($data)) {
            flash('success', 'El perfil quedó actualizado. Ya podés publicar oportunidades.');
        } else {
            // RN02 — say plainly what is still missing and why it matters.
            flash('warning', 'Guardamos los cambios, pero todavía falta descripción, ubicación o contacto, así que aún no podés publicar oportunidades.');
        }

        redirectTo('view_organization_profile');
    }

    /**
     * Loads the opportunities and stats the profile shows and renders it.
     */
    private function renderProfile(array $organization, bool $isOwner, array $formErrors = []): void
    {
        $organizationModel = new OrganizationModel();
        $organizationId = (int) $organization['id'];

        $stats = $organizationModel->getStats($organizationId);
        $opportunities = (new OpportunityModel())->getByOrganization($organizationId);

        // Visitors never see drafts; only the owner does (RN07).
        if (!$isOwner) {
            $opportunities = array_values(array_filter(
                $opportunities,
                static fn (array $opportunity): bool => $opportunity['status'] !== 'draft'
            ));
        }

        $profileComplete = $organizationModel->isProfileComplete($organization);
        $pendingCount = $isOwner ? (new EnrollmentModel())->countPending($organizationId) : 0;
        $categories = (new CategoryModel())->getAll();

        require __DIR__ . '/../views/organization_profile.php';
    }

    private function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }
}
