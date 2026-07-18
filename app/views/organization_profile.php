<?php
$activeNav = 'organizations';

$organizationName = 'Fundación Verde Norte';
$organizationDescription = 'Trabajamos en restauración de bosques y educación ambiental en la Zona Norte desde 2011. Coordinamos jornadas de reforestación, viveros comunitarios y talleres para escuelas de la región.';
$organizationLocation = 'San Carlos, Alajuela';

if (($_SESSION['role'] ?? null) === ROLE_ORGANIZATION) {
    $organization = (new OrganizationModel())->findByUserId((int) $_SESSION['user_id']);
    if ($organization !== null) {
        $organizationName = $organization['name'];
        $organizationDescription = $organization['description'] ?: $organizationDescription;
        $organizationLocation = $organization['location'] ?: 'Ubicación no indicada aún';
    }
}

$nameParts = preg_split('/\s+/', trim($organizationName));
$organizationInitials = strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[count($nameParts) > 1 ? 1 : 0], 0, 1));

// TODO (Paso 4 del plan de desarrollo, RF05): reemplazar por
// OpportunityModel::searchWithFilters(['organization_id' => ...]) con las
// oportunidades reales de esta organización.
$publishedOpportunities = [
    ['tag' => 'Ambiental', 'title' => 'Jornada de reforestación río San Carlos', 'enrolled' => 12, 'slots' => 20, 'status' => 'Activa', 'statusBg' => '#A8C9A1', 'statusColor' => '#0B3945', 'photoAlt' => 'reforestación'],
    ['tag' => 'Ambiental', 'title' => 'Vivero comunitario — mantenimiento', 'enrolled' => 5, 'slots' => 10, 'status' => 'Activa', 'statusBg' => '#A8C9A1', 'statusColor' => '#0B3945', 'photoAlt' => 'vivero forestal'],
    ['tag' => 'Ambiental', 'title' => 'Taller de reciclaje en escuela rural', 'enrolled' => 18, 'slots' => 18, 'status' => 'Cerrada', 'statusBg' => '#F1EEE6', 'statusColor' => '#8a8a85', 'photoAlt' => 'taller de reciclaje escolar'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($organizationName) ?> · Enlaza</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/general-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>

<section class="org-cover">
    <img src="<?= BASE_URL ?>assets/img/placeholder.jpg" alt="foto: equipo de la fundación trabajando en vivero forestal">
</section>

<section class="org-header-card">
    <div class="container org-header-card__inner">
        <div class="profile-hero__avatar profile-hero__avatar--square"><?= htmlspecialchars($organizationInitials) ?></div>
        <div class="profile-hero__info" style="padding-bottom:6px">
            <h1 class="profile-hero__name profile-hero__name--dark"><?= htmlspecialchars($organizationName) ?></h1>
            <div class="profile-hero__meta profile-hero__meta--dark">
                <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($organizationLocation) ?></span>
                <span><i class="fa-solid fa-leaf"></i> Ambiental</span>
                <span><i class="fa-regular fa-calendar"></i> Aliada desde 2019</span>
            </div>
        </div>
        <div style="padding-bottom:6px">
            <button type="button" class="btn btn--secondary">Editar perfil</button>
        </div>
    </div>
</section>

<section style="padding:0 0 76px">
    <div class="container">
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:40px;margin:44px 0">
            <div>
                <h2 class="detail-section-title">Sobre la organización</h2>
                <p class="detail-section-text" style="margin-bottom:0"><?= htmlspecialchars($organizationDescription) ?></p>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div class="org-stat"><div class="org-stat__number">9</div><div class="org-stat__label">Oportunidades publicadas</div></div>
                <div class="org-stat"><div class="org-stat__number">142</div><div class="org-stat__label">Voluntarios apoyados</div></div>
            </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
            <h2 class="section-intro__title" style="margin:0;font-size:20px">Oportunidades publicadas</h2>
            <a href="<?= BASE_URL ?>?action=manage_enrollments" style="font-size:14px;font-weight:600;color:var(--color-primary)">Gestionar inscripciones <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="grid-3">
            <?php foreach ($publishedOpportunities as $opportunity): ?>
                <div class="opportunity-card opportunity-card--compact">
                    <div class="opportunity-card__photo">
                        <img src="<?= BASE_URL ?>assets/img/placeholder.jpg" alt="<?= htmlspecialchars($opportunity['photoAlt']) ?>">
                        <span class="opportunity-card__badge" style="background:<?= $opportunity['statusBg'] ?>;color:<?= $opportunity['statusColor'] ?>"><?= htmlspecialchars($opportunity['status']) ?></span>
                    </div>
                    <div class="opportunity-card__body">
                        <div class="eyebrow-label"><?= htmlspecialchars($opportunity['tag']) ?></div>
                        <h3 class="opportunity-card__title"><?= htmlspecialchars($opportunity['title']) ?></h3>
                        <div style="font-size:12px;color:#6d6d68;margin-bottom:14px"><?= (int) $opportunity['enrolled'] ?> inscritos · <?= (int) $opportunity['slots'] ?> cupos</div>
                        <a href="<?= BASE_URL ?>?action=view_opportunity" class="btn btn--secondary btn--full-width">Ver / editar</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
</body>
</html>
