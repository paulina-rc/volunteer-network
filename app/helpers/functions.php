<?php
/**
 * Shared view helpers for Enlaza.
 *
 * Loaded from public/index.php on every request, right after config.php and
 * connection.php, so every controller and view can rely on these functions
 * without requiring anything else.
 *
 * Visible strings returned by these helpers (flash messages, status labels,
 * month names) are in Spanish on purpose — they reach the end user directly
 * (see CLAUDE.md, section 5).
 */

// ------------------------------------------------------------
// Output escaping
// ------------------------------------------------------------

/**
 * Escapes a value for safe output inside HTML (RNF02).
 * Null becomes an empty string so views can print optional columns directly.
 */
function e(?string $text): string
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

// ------------------------------------------------------------
// URLs and redirects
// ------------------------------------------------------------

/**
 * Builds an internal URL for a router action.
 * actionUrl('view_opportunity', ['id' => 3])
 *   => /enlaza/public/index.php?action=view_opportunity&id=3
 */
function actionUrl(string $action, array $params = []): string
{
    return BASE_URL . 'index.php?' . http_build_query(array_merge(['action' => $action], $params));
}

/**
 * Sends a redirect to a router action and stops execution.
 */
function redirectTo(string $action, array $params = []): void
{
    header('Location: ' . actionUrl($action, $params));
    exit;
}

// ------------------------------------------------------------
// Session and roles
// ------------------------------------------------------------

/**
 * Returns the logged-in user as stored in the session, or null when nobody is
 * logged in. Reads only the session — it does not hit the database.
 *
 * Shape: ['id' => int, 'role' => string, 'volunteer_id' => ?int, 'organization_id' => ?int]
 */
function currentUser(): ?array
{
    if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
        return null;
    }

    return [
        'id'              => (int) $_SESSION['user_id'],
        'role'            => (string) $_SESSION['role'],
        'volunteer_id'    => isset($_SESSION['volunteer_id']) ? (int) $_SESSION['volunteer_id'] : null,
        'organization_id' => isset($_SESSION['organization_id']) ? (int) $_SESSION['organization_id'] : null,
    ];
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function isVolunteer(): bool
{
    return ($_SESSION['role'] ?? null) === ROLE_VOLUNTEER;
}

function isOrganization(): bool
{
    return ($_SESSION['role'] ?? null) === ROLE_ORGANIZATION;
}

/**
 * Guard for actions that need any logged-in user.
 * Sends the visitor to the login tab with a warning.
 */
function requireLogin(): void
{
    if (isLoggedIn()) {
        return;
    }

    flash('warning', 'Iniciá sesión para continuar.');
    redirectTo('register', ['tab' => 'login']);
}

/**
 * Guard for actions restricted to one role (RN01).
 * Expects ROLE_VOLUNTEER or ROLE_ORGANIZATION.
 */
function requireRole(string $role): void
{
    requireLogin();

    if (($_SESSION['role'] ?? null) === $role) {
        return;
    }

    $message = $role === ROLE_ORGANIZATION
        ? 'Esta sección es solo para organizaciones.'
        : 'Esta sección es solo para personas voluntarias.';

    flash('error', $message);
    redirectTo('home');
}

// ------------------------------------------------------------
// Flash messages
// ------------------------------------------------------------

/**
 * Queues a message to be shown on the next rendered screen.
 *
 * @param string $type 'success', 'warning' or 'error'
 */
function flash(string $type, string $text): void
{
    if (!in_array($type, ['success', 'warning', 'error'], true)) {
        $type = 'success';
    }

    $_SESSION['flash'][] = ['type' => $type, 'text' => $text];
}

/**
 * Returns every queued message and empties the queue, so the same message is
 * never shown twice. Each entry is ['type' => ..., 'text' => ...].
 */
function getFlash(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return $messages;
}

// ------------------------------------------------------------
// CSRF protection
// ------------------------------------------------------------

/**
 * Returns the session's CSRF token, creating it on first use.
 */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Hidden input to drop inside every POST form.
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

/**
 * Checks the token sent by a POST form against the one in the session.
 * Uses hash_equals so the comparison does not leak timing information.
 */
function verifyCsrf(): bool
{
    $submitted = $_POST['csrf_token'] ?? '';
    $expected = $_SESSION['csrf_token'] ?? '';

    if (!is_string($submitted) || $expected === '') {
        return false;
    }

    return hash_equals($expected, $submitted);
}

// ------------------------------------------------------------
// Formatting
// ------------------------------------------------------------

/**
 * Formats a MySQL DATE / DATETIME in Spanish.
 *
 *   formatDate('2026-09-24')        => "24 de setiembre, 2026"
 *   formatDate('2026-09-24', false) => "24 set 2026"
 *
 * Month names are written by hand instead of using setlocale(), which depends
 * on locales that may not be installed on the server. Returns '' for null or
 * unparseable input, so views can print the result directly.
 */
function formatDate(?string $date, bool $long = true): string
{
    if ($date === null || trim($date) === '') {
        return '';
    }

    try {
        $parsed = new DateTimeImmutable($date);
    } catch (Exception $exception) {
        return '';
    }

    $longMonths = [
        1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'setiembre', 'octubre', 'noviembre', 'diciembre',
    ];
    $shortMonths = [
        1 => 'ene', 'feb', 'mar', 'abr', 'may', 'jun',
        'jul', 'ago', 'set', 'oct', 'nov', 'dic',
    ];

    $day = (int) $parsed->format('j');
    $month = (int) $parsed->format('n');
    $year = $parsed->format('Y');

    return $long
        ? $day . ' de ' . $longMonths[$month] . ', ' . $year
        : $day . ' ' . $shortMonths[$month] . ' ' . $year;
}

/**
 * Splits the requirements column into the lines the detail screen lists.
 * Blank lines and stray whitespace are dropped, so an empty column yields [].
 *
 * @return string[]
 */
function requirementLines(?string $requirements): array
{
    if ($requirements === null || trim($requirements) === '') {
        return [];
    }

    $lines = preg_split('/\r\n|\r|\n/', $requirements);

    return array_values(array_filter(
        array_map('trim', $lines),
        static fn (string $line): bool => $line !== ''
    ));
}

// ------------------------------------------------------------
// Business rules shared by views and controllers
// ------------------------------------------------------------

/**
 * RN04 — whether an opportunity can still take enrollments.
 *
 * The single definition of the rule: it must be open, its date must not have
 * passed, and it must have at least one free slot. Used by the opportunity
 * card, the detail screen and EnrollmentController, so the button a volunteer
 * sees and the check the server makes can never disagree.
 *
 * Takes a row as returned by OpportunityModel.
 */
function acceptsEnrollments(array $opportunity): bool
{
    return ($opportunity['status'] ?? '') === 'active'
        && ($opportunity['activity_date'] ?? '') >= date('Y-m-d')
        && (int) ($opportunity['available_slots'] ?? 0) > 0;
}

/**
 * Why an opportunity is not taking enrollments, for the message next to a
 * disabled button. Returns '' when it is in fact open.
 */
function enrollmentBlockedReason(array $opportunity): string
{
    if (acceptsEnrollments($opportunity)) {
        return '';
    }

    if (($opportunity['status'] ?? '') === 'draft') {
        return 'Esta oportunidad todavía es un borrador.';
    }

    if (($opportunity['activity_date'] ?? '') < date('Y-m-d')) {
        return 'La fecha de esta actividad ya pasó.';
    }

    if ((int) ($opportunity['available_slots'] ?? 0) <= 0) {
        return 'Esta oportunidad ya no tiene cupos disponibles.';
    }

    return 'Esta oportunidad está cerrada.';
}

/**
 * RN08 — why an opportunity was recommended, in the priority order the rule
 * defines: skills first, then interests, then location. Takes a row from
 * OpportunityModel::getRecommendationsFor(), which carries the three counters.
 *
 * Showing this next to each suggestion is what makes the rule visible instead
 * of the list looking arbitrary.
 */
function recommendationReason(array $opportunity): string
{
    $skillMatches = (int) ($opportunity['skill_matches'] ?? 0);

    if ($skillMatches > 0) {
        return $skillMatches === 1
            ? 'Coincide con 1 habilidad tuya'
            : 'Coincide con ' . $skillMatches . ' habilidades tuyas';
    }

    if ((int) ($opportunity['interest_match'] ?? 0) > 0) {
        return 'Es del área de ' . ($opportunity['category_name'] ?? '') . ', que te interesa';
    }

    if ((int) ($opportunity['location_match'] ?? 0) > 0) {
        return 'Se realiza cerca de tu ubicación';
    }

    return '';
}

/**
 * Builds the avatar initials for a person or organization name:
 * "Ana Rodríguez" => "AR", "Fundación Verde Norte" => "FV", "Enlaza" => "EN".
 */
function initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY);

    if ($parts === false || $parts === []) {
        return '';
    }

    $first = mb_substr($parts[0], 0, 1);
    $second = count($parts) > 1
        ? mb_substr($parts[1], 0, 1)
        : mb_substr($parts[0], 1, 1);

    return mb_strtoupper($first . $second);
}

/**
 * Spanish label for an enrollment status (pending/accepted/rejected/completed)
 * or an opportunity status (active/closed/draft). Unknown values are returned
 * unchanged so nothing silently disappears from the screen.
 */
function statusLabel(string $status): string
{
    $labels = [
        'pending'   => 'Pendiente',
        'accepted'  => 'Aceptada',
        'rejected'  => 'Rechazada',
        'completed' => 'Completada',
        'active'    => 'Activa',
        'closed'    => 'Cerrada',
        'draft'     => 'Borrador',
    ];

    return $labels[$status] ?? $status;
}
