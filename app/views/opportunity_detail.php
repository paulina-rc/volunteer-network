<?php
$activeNav = 'opportunities';

// TODO (Pasos 4-5 del plan de desarrollo): reemplazar por
// OpportunityModel::findById($_GET['id']) con los datos reales de la
// oportunidad en vez de este ejemplo fijo.
$opportunity = [
    'tag' => 'Ambiental',
    'title' => 'Jornada de reforestación río San Carlos',
    'date' => '24 de agosto, 2026',
    'time' => '7:00 a. m. – 12:00 m.',
    'location' => 'San Carlos, Alajuela',
    'description' => 'Sembramos especies nativas en la ribera del río San Carlos junto a la comunidad de Aguas Zarcas, para restaurar el bosque de galería y proteger la fuente de agua. La actividad incluye una breve charla sobre las especies a sembrar y el cierre con un refrigerio para los participantes.',
    'requirements' => ['Ropa de manga larga y botas cerradas', 'Disponibilidad de 5 horas', 'No se requiere experiencia previa'],
    'photoAlt' => 'foto: voluntarios sembrando árboles en la ribera del río San Carlos, luz de mañana',
    'orgName' => 'Fundación Verde Norte',
    'orgInitials' => 'FV',
    'orgMeta' => 'San Carlos, Alajuela · Aliada desde 2019',
    'slotsAvailable' => 8,
    'slotsTotal' => 20,
];
$occupiedPercent = (int) round((($opportunity['slotsTotal'] - $opportunity['slotsAvailable']) / $opportunity['slotsTotal']) * 100);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($opportunity['title']) ?> · Enlaza</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/general-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>

<div class="container" style="padding-top:20px">
    <a href="<?= BASE_URL ?>?action=search_opportunities" class="breadcrumb-link"><i class="fa-solid fa-arrow-left"></i> Volver a oportunidades</a>
</div>

<section class="container detail-layout">
    <div>
        <div class="detail-photo">
            <img src="<?= BASE_URL ?>assets/img/placeholder.jpg" alt="<?= htmlspecialchars($opportunity['photoAlt']) ?>">
        </div>
        <div class="eyebrow-label"><?= htmlspecialchars($opportunity['tag']) ?></div>
        <h1 class="detail-title"><?= htmlspecialchars($opportunity['title']) ?></h1>
        <div class="detail-meta">
            <span class="detail-meta__item"><i class="fa-regular fa-calendar"></i> <?= htmlspecialchars($opportunity['date']) ?></span>
            <span class="detail-meta__item"><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($opportunity['time']) ?></span>
            <span class="detail-meta__item"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($opportunity['location']) ?></span>
        </div>

        <h2 class="detail-section-title">Descripción</h2>
        <p class="detail-section-text"><?= htmlspecialchars($opportunity['description']) ?></p>

        <h2 class="detail-section-title">Requisitos</h2>
        <ul class="detail-requirements">
            <?php foreach ($opportunity['requirements'] as $requirement): ?>
                <li><?= htmlspecialchars($requirement) ?></li>
            <?php endforeach; ?>
        </ul>

        <h2 class="detail-section-title">Organiza</h2>
        <div class="org-card">
            <div class="org-card__logo"><?= htmlspecialchars($opportunity['orgInitials']) ?></div>
            <div class="org-card__info">
                <div class="org-card__name"><?= htmlspecialchars($opportunity['orgName']) ?></div>
                <div class="org-card__meta"><?= htmlspecialchars($opportunity['orgMeta']) ?></div>
            </div>
            <a href="<?= BASE_URL ?>?action=view_organization_profile" class="org-card__link">Ver perfil</a>
        </div>
    </div>

    <div class="panel enrollment-panel">
        <div data-enrollment-form>
            <div class="enrollment-panel__slots-row">
                <span>Cupos disponibles</span>
                <strong><?= (int) $opportunity['slotsAvailable'] ?> de <?= (int) $opportunity['slotsTotal'] ?></strong>
            </div>
            <div class="enrollment-panel__bar">
                <div class="enrollment-panel__bar-fill" style="width:<?= $occupiedPercent ?>%"></div>
            </div>
            <!-- TODO (Paso 6 del plan de desarrollo, RF07, RN04): conectar este botón a
                 ?action=enroll (EnrollmentController::enroll) en vez de un toggle visual local. -->
            <button type="button" class="btn btn--primary btn--full-width" style="margin-bottom:12px" data-enroll-button>Inscribirme a esta oportunidad</button>
            <button type="button" class="btn btn--secondary btn--full-width">Guardar para después</button>
        </div>
        <div class="enrollment-panel__success" data-enrollment-success style="display:none">
            <i class="fa-solid fa-circle-check"></i>
            <h3>¡Listo, quedaste inscrito!</h3>
            <p>La organización revisará tu inscripción y te avisará por correo.</p>
            <a href="<?= BASE_URL ?>?action=view_volunteer_profile" class="btn btn--secondary btn--full-width">Ver mis inscripciones</a>
        </div>
    </div>
</section>

<script src="<?= BASE_URL ?>assets/js/opportunity-detail.js"></script>
</body>
</html>
