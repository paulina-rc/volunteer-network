<?php
require_once __DIR__ . '/../helpers/view.php';

class VolunteerController
{
    /**
     * RF03 / RF10 / RF11 — the volunteer's own profile: stats, enrollments,
     * skills and interests, and the recommendations panel.
     */
    public function viewProfile(): void
    {
        requireRole(ROLE_VOLUNTEER);
        // ?panel= lets the "Editar perfil" button and the empty states link
        // straight to the tab they mean.
        $this->renderProfile((string) ($_GET['panel'] ?? 'enrollments'));
    }

    /**
     * RF10 — same screen, opened straight on the recommendations tab.
     */
    public function recommendations(): void
    {
        requireRole(ROLE_VOLUNTEER);
        $this->renderProfile('recommendations');
    }

    /**
     * RF03 — saves the editable profile plus the skill and interest checkboxes.
     */
    public function saveProfile(): void
    {
        requireRole(ROLE_VOLUNTEER);

        if (!$this->isPost() || !verifyCsrf()) {
            flash('error', 'No pudimos guardar los cambios. Probá de nuevo.');
            redirectTo('view_volunteer_profile');
        }

        $volunteerModel = new VolunteerModel();
        $volunteer = $volunteerModel->findByUserId((int) $_SESSION['user_id']);

        if ($volunteer === null) {
            flash('error', 'No encontramos tu perfil de voluntario.');
            redirectTo('home');
        }

        $data = [
            'full_name'    => trim((string) ($_POST['full_name'] ?? '')),
            'location'     => trim((string) ($_POST['location'] ?? '')),
            'availability' => trim((string) ($_POST['availability'] ?? '')),
            'about_me'     => trim((string) ($_POST['about_me'] ?? '')),
        ];

        $errors = [];
        if ($data['full_name'] === '') {
            $errors['full_name'] = 'El nombre completo es obligatorio.';
        } elseif (mb_strlen($data['full_name']) > 150) {
            $errors['full_name'] = 'El nombre no puede superar los 150 caracteres.';
        }
        if (mb_strlen($data['location']) > 150) {
            $errors['location'] = 'La ubicación no puede superar los 150 caracteres.';
        }

        if ($errors !== []) {
            // Re-render with what the volunteer typed and the message per field.
            $this->renderProfile('skills', $errors, $data);
            return;
        }

        $volunteerId = (int) $volunteer['id'];
        $volunteerModel->updateProfile($volunteerId, $data);
        $volunteerModel->saveSkills($volunteerId, (array) ($_POST['skills'] ?? []));
        $volunteerModel->saveInterests($volunteerId, (array) ($_POST['interests'] ?? []));

        flash('success', 'Tu perfil quedó actualizado.');
        redirectTo('view_volunteer_profile');
    }

    /**
     * Loads everything the profile screen shows and renders it.
     *
     * @param string $activePanel   'enrollments' | 'skills' | 'recommendations'
     * @param array  $formErrors    field name => message, after a failed save
     * @param array  $formData      what the volunteer typed, to refill the form
     */
    private function renderProfile(string $activePanel, array $formErrors = [], array $formData = []): void
    {
        $volunteerModel = new VolunteerModel();
        $volunteer = $volunteerModel->findByUserId((int) $_SESSION['user_id']);

        if ($volunteer === null) {
            flash('error', 'No encontramos tu perfil de voluntario.');
            redirectTo('home');
        }

        $volunteerId = (int) $volunteer['id'];

        $stats = $volunteerModel->getStats($volunteerId);
        $enrollments = (new EnrollmentModel())->getByVolunteer($volunteerId);
        $volunteerSkills = $volunteerModel->getSkills($volunteerId);
        $volunteerInterests = $volunteerModel->getInterests($volunteerId);
        $allSkills = (new SkillModel())->getAll();
        $allCategories = (new CategoryModel())->getAll();

        $selectedSkillIds = array_map('intval', array_column($volunteerSkills, 'id'));
        $selectedInterestIds = array_map('intval', array_column($volunteerInterests, 'id'));

        // RN08 — recommendations only make sense once there is something to
        // match on; otherwise the screen invites the volunteer to fill the
        // profile instead of showing an empty section.
        $hasProfileData = $selectedSkillIds !== [] || $selectedInterestIds !== [];
        $recommendations = $hasProfileData
            ? (new OpportunityModel())->getRecommendationsFor($volunteerId, (string) ($volunteer['location'] ?? ''), 3)
            : [];

        require __DIR__ . '/../views/volunteer_profile.php';
    }

    private function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }
}
