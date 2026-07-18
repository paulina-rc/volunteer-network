<?php
$statusLabels = ['pending' => 'Pendiente', 'accepted' => 'Aceptada', 'rejected' => 'Rechazada'];
$statusColors = [
    'pending' => ['bg' => '#E9A227', 'text' => '#4a3106'],
    'accepted' => ['bg' => '#A8C9A1', 'text' => '#0B3945'],
    'rejected' => ['bg' => '#F1EEE6', 'text' => '#8a8a85'],
];

// TODO (Paso 6 del plan de desarrollo, RF08, CU06): reemplazar por
// EnrollmentModel::listByOpportunity() con las inscripciones reales, y
// EnrollmentModel::updateStatus() al aceptar/rechazar.
$people = [
    ['name' => 'Ana Rodríguez', 'email' => 'ana.rodriguez@correo.com', 'initials' => 'AR', 'enrolledOn' => '10 ago', 'status' => 'accepted'],
    ['name' => 'Luis Vargas', 'email' => 'luis.vargas@correo.com', 'initials' => 'LV', 'enrolledOn' => '11 ago', 'status' => 'pending'],
    ['name' => 'Kimberly Solano', 'email' => 'kimberly.solano@correo.com', 'initials' => 'KS', 'enrolledOn' => '12 ago', 'status' => 'pending'],
    ['name' => 'Josué Alfaro', 'email' => 'josue.alfaro@correo.com', 'initials' => 'JA', 'enrolledOn' => '13 ago', 'status' => 'rejected'],
    ['name' => 'Melany Rojas', 'email' => 'melany.rojas@correo.com', 'initials' => 'MR', 'enrolledOn' => '13 ago', 'status' => 'accepted'],
];
$totalCount = count($people);
$pendingCount = count(array_filter($people, fn ($p) => $p['status'] === 'pending'));
$acceptedCount = count(array_filter($people, fn ($p) => $p['status'] === 'accepted'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestionar inscripciones · Enlaza</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/general-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php $headerVariant = 'minimal'; $backLinkHref = BASE_URL . '?action=view_organization_profile'; $backLinkLabel = 'Volver a mi perfil'; require __DIR__ . '/partials/header.php'; ?>

<section class="narrow-page" style="max-width:1000px">
    <h1 class="page-heading">Gestionar inscripciones</h1>
    <p class="page-subtext">Jornada de reforestación río San Carlos · 24 de agosto, 2026</p>

    <div style="display:flex;gap:10px;margin-bottom:28px">
        <button type="button" class="pill-filter pill-filter--sm pill-filter--active" data-enrollment-filter="all">Todas (<span data-count="all"><?= $totalCount ?></span>)</button>
        <button type="button" class="pill-filter pill-filter--sm" data-enrollment-filter="pending">Pendientes (<span data-count="pending"><?= $pendingCount ?></span>)</button>
        <button type="button" class="pill-filter pill-filter--sm" data-enrollment-filter="accepted">Aceptadas (<span data-count="accepted"><?= $acceptedCount ?></span>)</button>
    </div>

    <div style="display:flex;flex-direction:column;gap:14px" data-enrollment-list>
        <?php foreach ($people as $person): ?>
            <div class="enrollment-row" data-enrollment-row data-status="<?= $person['status'] ?>">
                <div class="enrollment-row__avatar"><?= htmlspecialchars($person['initials']) ?></div>
                <div class="enrollment-row__info">
                    <div class="enrollment-row__name"><?= htmlspecialchars($person['name']) ?></div>
                    <div class="enrollment-row__contact"><?= htmlspecialchars($person['email']) ?> · Inscrita el <?= htmlspecialchars($person['enrolledOn']) ?></div>
                </div>
                <div class="enrollment-row__status" data-status-badge style="background:<?= $statusColors[$person['status']]['bg'] ?>;color:<?= $statusColors[$person['status']]['text'] ?>"><?= htmlspecialchars($statusLabels[$person['status']]) ?></div>
                <div class="enrollment-row__actions" data-status-actions style="<?= $person['status'] !== 'pending' ? 'display:none' : '' ?>">
                    <button type="button" class="btn btn--sm" style="background:var(--color-primary);color:#fff;border:none" data-accept-button>Aceptar</button>
                    <button type="button" class="btn btn--sm" style="border-color:var(--color-border-strong);color:#6d6d68;background:transparent" data-reject-button>Rechazar</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="empty-state" data-enrollment-empty style="display:none">
        <p>No hay inscripciones en esta categoría.</p>
    </div>
</section>

<script>
    var ENROLLMENT_STATUS_LABELS = <?= json_encode($statusLabels, JSON_UNESCAPED_UNICODE) ?>;
    var ENROLLMENT_STATUS_COLORS = <?= json_encode($statusColors) ?>;
</script>
<script src="<?= BASE_URL ?>assets/js/manage-enrollments.js"></script>
</body>
</html>
