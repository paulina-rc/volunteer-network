<?php
$volunteerName = 'Ana Rodríguez';
$volunteerLocation = 'Ciudad Quesada, San Carlos';
$aboutMe = 'Estudiante de bachillerato en el Colegio Agropecuario de San Carlos, me interesa el trabajo ambiental y comunitario.';

if (($_SESSION['role'] ?? null) === ROLE_VOLUNTEER) {
    $volunteer = (new VolunteerModel())->findByUserId((int) $_SESSION['user_id']);
    if ($volunteer !== null) {
        $volunteerName = $volunteer['full_name'];
        $volunteerLocation = $volunteer['location'] ?: 'Ubicación no indicada aún';
        $aboutMe = $volunteer['about_me'] ?: $aboutMe;
    }
}

$nameParts = preg_split('/\s+/', trim($volunteerName));
$volunteerInitials = strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[count($nameParts) > 1 ? 1 : 0], 0, 1));

// TODO (Paso 6 del plan de desarrollo, RF08): reemplazar por
// EnrollmentModel::listByVolunteer() con las inscripciones reales.
$enrollments = [
    ['tag' => 'Ambiental', 'title' => 'Jornada de reforestación río San Carlos', 'org' => 'Fundación Verde Norte', 'date' => '24 ago 2026', 'status' => 'Aceptada', 'statusBg' => '#A8C9A1', 'statusColor' => '#0B3945', 'photoAlt' => 'reforestación'],
    ['tag' => 'Cultural', 'title' => 'Rescate de tradiciones orales boyeras', 'org' => 'Casa de la Cultura Zarcero', 'date' => '11 oct 2026', 'status' => 'Pendiente', 'statusBg' => '#E9A227', 'statusColor' => '#4a3106', 'photoAlt' => 'tradiciones boyeras'],
    ['tag' => 'Salud', 'title' => 'Feria de salud comunitaria', 'org' => 'Cruz Roja — sede local', 'date' => '14 sept 2026', 'status' => 'Completada', 'statusBg' => '#0F4C5C', 'statusColor' => '#fff', 'photoAlt' => 'feria de salud'],
    ['tag' => 'Educativo', 'title' => 'Tutorías de matemáticas para primaria', 'org' => 'Asociación Aprender Juntos', 'date' => '2 sept 2026', 'status' => 'Rechazada', 'statusBg' => '#F1EEE6', 'statusColor' => '#8a8a85', 'photoAlt' => 'tutorías'],
];

// TODO (Paso 3 del plan de desarrollo, RF03): reemplazar por las categorías
// de interés reales guardadas en voluntario_interes / VolunteerModel::saveInterests().
$interestCategories = [
    ['name' => 'Ambiental', 'active' => true],
    ['name' => 'Educativo', 'active' => true],
    ['name' => 'Comunitario', 'active' => false],
    ['name' => 'Salud', 'active' => false],
    ['name' => 'Cultural', 'active' => false],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi perfil · Enlaza</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/general-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>

<section class="profile-hero">
    <div class="container profile-hero__top">
        <div class="profile-hero__avatar"><?= htmlspecialchars($volunteerInitials) ?></div>
        <div class="profile-hero__info">
            <h1 class="profile-hero__name"><?= htmlspecialchars($volunteerName) ?></h1>
            <div class="profile-hero__meta">
                <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($volunteerLocation) ?></span>
                <span><i class="fa-regular fa-calendar"></i> Voluntaria desde marzo 2025</span>
            </div>
        </div>
        <div class="profile-hero__actions">
            <button type="button" class="btn btn--secondary btn--outline-light">Editar perfil</button>
        </div>
    </div>
    <div class="container profile-hero__stats">
        <div class="profile-stat"><div class="profile-stat__number">36</div><div class="profile-stat__label">Horas acumuladas</div></div>
        <div class="profile-stat"><div class="profile-stat__number">7</div><div class="profile-stat__label">Actividades completadas</div></div>
        <div class="profile-stat"><div class="profile-stat__number">4</div><div class="profile-stat__label">Organizaciones apoyadas</div></div>
    </div>
    <div style="height:40px"></div>
</section>

<section class="page-section" style="padding:36px 0 76px">
    <div class="container">
        <div style="display:flex;gap:10px;margin-bottom:32px">
            <button type="button" class="pill-filter pill-filter--active" data-profile-tab="enrollments">Mis inscripciones</button>
            <button type="button" class="pill-filter" data-profile-tab="skills">Habilidades e intereses</button>
        </div>

        <div data-profile-panel="enrollments" style="display:flex;flex-direction:column;gap:16px">
            <?php foreach ($enrollments as $enrollment): ?>
                <div class="enrollment-row">
                    <div class="enrollment-row__photo"><img src="<?= BASE_URL ?>assets/img/placeholder.jpg" alt="<?= htmlspecialchars($enrollment['photoAlt']) ?>"></div>
                    <div class="enrollment-row__info">
                        <div class="eyebrow-label" style="margin-bottom:0"><?= htmlspecialchars($enrollment['tag']) ?></div>
                        <h3 class="enrollment-row__title"><?= htmlspecialchars($enrollment['title']) ?></h3>
                        <div class="enrollment-row__meta"><?= htmlspecialchars($enrollment['org']) ?> · <?= htmlspecialchars($enrollment['date']) ?></div>
                    </div>
                    <div class="enrollment-row__status" style="background:<?= $enrollment['statusBg'] ?>;color:<?= $enrollment['statusColor'] ?>"><?= htmlspecialchars($enrollment['status']) ?></div>
                    <a href="<?= BASE_URL ?>?action=view_opportunity" style="font-size:13px;font-weight:600;color:var(--color-primary)">Ver detalle</a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="panel" data-profile-panel="skills" style="display:none;padding:28px;max-width:640px">
            <!-- TODO (Paso 3 del plan de desarrollo, RF03): conectar este formulario a
                 ?action=save_volunteer_profile (VolunteerController::saveProfile). -->
            <div class="form-group">
                <label for="profile-about-me">Sobre mí</label>
                <textarea id="profile-about-me" rows="3"><?= htmlspecialchars($aboutMe) ?></textarea>
            </div>
            <div class="form-group">
                <label>Categorías de interés</label>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <?php foreach ($interestCategories as $category): ?>
                        <span class="chip<?= $category['active'] ? ' chip--active' : '' ?>"><?= htmlspecialchars($category['name']) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="profile-availability">Disponibilidad</label>
                <select id="profile-availability">
                    <option>Fines de semana</option>
                    <option>Entre semana por la tarde</option>
                    <option>Flexible</option>
                </select>
            </div>
            <button type="button" class="btn btn--primary">Guardar cambios</button>
        </div>
    </div>
</section>

<script src="<?= BASE_URL ?>assets/js/volunteer-profile.js"></script>
</body>
</html>
