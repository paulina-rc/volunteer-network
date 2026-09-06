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

    public function publish(): void
    {
        $categories = (new CategoryModel())->getAll();
        require __DIR__ . '/../views/opportunity_publish.php';
    }

    public function edit(): void
    {
        renderPending('Editar oportunidad', 'Se implementa en el Paso 4 del plan de desarrollo (RN09).');
    }

    public function close(): void
    {
        renderPending('Cerrar oportunidad', 'Se implementa en el Paso 4 del plan de desarrollo (RN05, RN09).');
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
