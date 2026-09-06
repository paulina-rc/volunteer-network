<?php
$activeNav = 'opportunities';

// $opportunity, $requirements and $skills come from OpportunityController::view().
$totalSlots = (int) $opportunity['total_slots'];
$takenSlots = (int) $opportunity['taken_slots'];
$availableSlots = (int) $opportunity['available_slots'];

// The bar fills with the slots already taken over the total (RN05).
$occupiedPercent = $totalSlots > 0
    ? (int) round(min($takenSlots, $totalSlots) / $totalSlots * 100)
    : 0;

$isOpen = acceptsEnrollments($opportunity);
$blockedReason = enrollmentBlockedReason($opportunity);

$organizationMeta = $opportunity['org_location'] ?? '';
if (!empty($opportunity['founded_year'])) {
    $organizationMeta = trim($organizationMeta . ' · Aliada desde ' . (int) $opportunity['founded_year'], ' ·');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($opportunity['title']) ?> · Enlaza</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/general-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>

<div class="container" style="padding-top:20px">
    <a href="<?= e(actionUrl('search_opportunities')) ?>" class="breadcrumb-link"><i class="fa-solid fa-arrow-left"></i> Volver a oportunidades</a>
</div>

<section class="container detail-layout">
    <div>
        <div class="detail-photo">
            <img src="<?= BASE_URL ?>assets/img/placeholder.jpg" alt="foto: <?= e($opportunity['title']) ?>">
        </div>
        <div class="eyebrow-label"><?= e($opportunity['category_name']) ?></div>
        <h1 class="detail-title"><?= e($opportunity['title']) ?></h1>
        <div class="detail-meta">
            <span class="detail-meta__item"><i class="fa-regular fa-calendar"></i> <?= e(formatDate($opportunity['activity_date'])) ?></span>
            <?php if (!empty($opportunity['time'])): ?>
                <span class="detail-meta__item"><i class="fa-regular fa-clock"></i> <?= e($opportunity['time']) ?></span>
            <?php endif; ?>
            <span class="detail-meta__item"><i class="fa-solid fa-location-dot"></i> <?= e($opportunity['location']) ?></span>
        </div>

        <h2 class="detail-section-title">Descripción</h2>
        <p class="detail-section-text"><?= nl2br(e($opportunity['description'])) ?></p>

        <?php if ($requirements !== []): ?>
            <h2 class="detail-section-title">Requisitos</h2>
            <ul class="detail-requirements">
                <?php foreach ($requirements as $requirement): ?>
                    <li><?= e($requirement) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($skills !== []): ?>
            <h2 class="detail-section-title">Habilidades requeridas</h2>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                <?php foreach ($skills as $skill): ?>
                    <span class="chip chip--active"><?= e($skill['name']) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h2 class="detail-section-title">Organiza</h2>
        <div class="org-card">
            <div class="org-card__logo"><?= e(initials($opportunity['organization_name'])) ?></div>
            <div class="org-card__info">
                <div class="org-card__name"><?= e($opportunity['organization_name']) ?></div>
                <div class="org-card__meta"><?= e($organizationMeta) ?></div>
            </div>
            <a href="<?= e(actionUrl('view_organization_profile', ['id' => (int) $opportunity['org_id']])) ?>" class="org-card__link">Ver perfil</a>
        </div>
    </div>

    <div class="panel enrollment-panel">
        <div data-enrollment-form>
            <div class="enrollment-panel__slots-row">
                <span>Cupos disponibles</span>
                <strong><?= $availableSlots ?> de <?= $totalSlots ?></strong>
            </div>
            <div class="enrollment-panel__bar">
                <div class="enrollment-panel__bar-fill" style="width:<?= $occupiedPercent ?>%"></div>
            </div>
            <p style="font-size:12px;color:#6d6d68;margin:0 0 14px"><?= $takenSlots ?> de <?= $totalSlots ?> cupos ya ocupados</p>

            <?php if ($isOpen): ?>
                <!-- TODO (Paso 6 del plan de desarrollo, RF07, RN04): conectar este botón a
                     ?action=enroll (EnrollmentController::enroll) en vez de un toggle visual local. -->
                <button type="button" class="btn btn--primary btn--full-width" style="margin-bottom:12px" data-enroll-button>Inscribirme a esta oportunidad</button>
                <button type="button" class="btn btn--secondary btn--full-width">Guardar para después</button>
            <?php else: ?>
                <button type="button" class="btn btn--primary btn--full-width" style="margin-bottom:12px" disabled>Inscripciones cerradas</button>
                <p style="font-size:13px;color:#8a8a85;text-align:center;margin:0"><?= e($blockedReason) ?></p>
            <?php endif; ?>
        </div>
        <div class="enrollment-panel__success" data-enrollment-success style="display:none">
            <i class="fa-solid fa-circle-check"></i>
            <h3>¡Listo, quedaste inscrito!</h3>
            <p>La organización revisará tu inscripción y te avisará por correo.</p>
            <a href="<?= e(actionUrl('view_volunteer_profile')) ?>" class="btn btn--secondary btn--full-width">Ver mis inscripciones</a>
        </div>
    </div>
</section>

<script src="<?= BASE_URL ?>assets/js/opportunity-detail.js"></script>
</body>
</html>
