<?php
require_once __DIR__ . '/../helpers/view.php';

class UserController
{
    public function showRegister(): void
    {
        $tab = $_GET['tab'] ?? 'login';
        $this->render($tab === 'register' ? 'register' : 'login');
    }

    public function register(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->render('register');
            return;
        }

        if (!verifyCsrf()) {
            $this->render('register', [], ['No pudimos procesar el formulario. Volvé a intentarlo.']);
            return;
        }

        $role = trim($_POST['role'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $organizationName = trim($_POST['organization_name'] ?? '');

        $registerData = [
            'role'              => $role,
            'email'             => $email,
            'full_name'         => $fullName,
            'organization_name' => $organizationName,
        ];

        $errors = $this->validateRegistration($role, $email, $password, $confirmPassword, $fullName, $organizationName);

        if (empty($errors)) {
            $userModel = new UserModel();
            if ($userModel->findByEmail($email) !== null) {
                $errors[] = 'Ese correo ya está registrado. Iniciá sesión o usá otro correo.';
            }
        }

        if (!empty($errors)) {
            $this->render('register', [], $errors, $registerData);
            return;
        }

        $connection = getConnection();
        $connection->beginTransaction();

        try {
            $userModel = new UserModel();
            $userId = $userModel->createUser($email, password_hash($password, PASSWORD_DEFAULT), $role);

            if ($role === ROLE_VOLUNTEER) {
                (new VolunteerModel())->createVolunteer($userId, $fullName);
            } else {
                (new OrganizationModel())->createOrganization($userId, $organizationName);
            }

            $connection->commit();
        } catch (PDOException $exception) {
            $connection->rollBack();
            error_log('Error registering user: ' . $exception->getMessage());
            $this->render('register', [], ['No se pudo completar el registro. Probá de nuevo en unos minutos.'], $registerData);
            return;
        }

        $this->render('login', [], [], [], $email, 'Cuenta creada correctamente. Iniciá sesión para continuar.');
    }

    public function login(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->render();
            return;
        }

        if (!verifyCsrf()) {
            $this->render('login', ['No pudimos procesar el formulario. Volvé a intentarlo.']);
            return;
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $errors = [];

        if ($email === '' || $password === '') {
            $errors[] = 'Ingresá tu correo y tu contraseña.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo no tiene un formato válido.';
        }

        $user = null;
        if (empty($errors)) {
            $user = (new UserModel())->verifyCredentials($email, $password);
            if ($user === null) {
                $errors[] = 'Correo o contraseña incorrectos.';
            }
        }

        if (!empty($errors)) {
            $this->render('login', $errors, [], [], $email);
            return;
        }

        $userId = (int) $user['id'];
        $role = $user['role'];

        $profile = $role === ROLE_VOLUNTEER
            ? (new VolunteerModel())->findByUserId($userId)
            : (new OrganizationModel())->findByUserId($userId);

        $this->createSession($userId, $role, (int) $profile['id']);
        $this->redirectByRole($role);
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        header('Location: ' . BASE_URL);
        exit;
    }

    /**
     * Validates the registration form data (RNF03) before touching the database.
     */
    private function validateRegistration(
        string $role,
        string $email,
        string $password,
        string $confirmPassword,
        string $fullName,
        string $organizationName
    ): array {
        $errors = [];

        if ($role !== ROLE_VOLUNTEER && $role !== ROLE_ORGANIZATION) {
            $errors[] = 'Seleccioná un tipo de cuenta válido.';
        }

        if ($email === '') {
            $errors[] = 'El correo es obligatorio.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
            $errors[] = 'El correo no tiene un formato válido.';
        }

        if ($password === '') {
            $errors[] = 'La contraseña es obligatoria.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        } elseif ($password !== $confirmPassword) {
            $errors[] = 'Las contraseñas no coinciden.';
        }

        if ($role === ROLE_VOLUNTEER) {
            if ($fullName === '') {
                $errors[] = 'El nombre completo es obligatorio.';
            } elseif (strlen($fullName) > 150) {
                $errors[] = 'El nombre completo no puede superar los 150 caracteres.';
            }
        }

        if ($role === ROLE_ORGANIZATION) {
            if ($organizationName === '') {
                $errors[] = 'El nombre de la organización es obligatorio.';
            } elseif (strlen($organizationName) > 150) {
                $errors[] = 'El nombre de la organización no puede superar los 150 caracteres.';
            }
        }

        return $errors;
    }

    private function createSession(int $userId, string $role, int $profileId): void
    {
        // New session id the moment the privileges change, so a session id
        // captured before login cannot be reused afterwards. Session data
        // (including the CSRF token) survives the regeneration.
        session_regenerate_id(true);

        $_SESSION['user_id'] = $userId;
        $_SESSION['role'] = $role;

        if ($role === ROLE_VOLUNTEER) {
            $_SESSION['volunteer_id'] = $profileId;
        } else {
            $_SESSION['organization_id'] = $profileId;
        }
    }

    private function redirectByRole(string $role): void
    {
        $destination = $role === ROLE_VOLUNTEER ? 'view_volunteer_profile' : 'view_organization_profile';
        header('Location: ' . BASE_URL . '?action=' . $destination);
        exit;
    }

    private function render(
        string $activeTab = 'login',
        array $loginErrors = [],
        array $registerErrors = [],
        array $registerData = [],
        string $loginEmail = '',
        string $successMessage = ''
    ): void {
        require __DIR__ . '/../views/register.php';
    }
}
