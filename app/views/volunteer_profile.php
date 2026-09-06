<?php
/**
 * Volunteer profile (RF03, RF10, RF11).
 *
 * From VolunteerController::renderProfile():
 * $volunteer, $stats, $enrollments, $volunteerSkills, $volunteerInterests,
 * $allSkills, $allCategories, $selectedSkillIds, $selectedInterestIds,
 * $recommendations, $hasProfileData, $activePanel, $formErrors, $formData
 */
$statusColors = [
    'pending'   => ['bg' => '#E9A227', 'text' => '#4a3106'],
    'accepted'  => ['bg' => '#A8C9A1', 'text' => '#0B3945'],
    'rejected'  => ['bg' => '#F1EEE6', 'text' => '#8a8a85'],
    'completed' => ['bg' => '#0F4C5C', 'text' => '#fff'],
];

// After a failed save the form shows what was typed; otherwise the stored row.
$value = static function (string $field) use ($formData, $volunteer) {
    return $formData[$field] ?? $volunteer[$field] ?? '';
};

$availabilityOptions = [
    'Fines de semana',
    'Entre semana por la mañana',
    'Entre semana por la tarde',
    'Sábados',
    'Horario flexible',
];
$currentAvailability = (string) $value('availability');

$panels = ['enrollments', 'recommendations', 'skills'];
$activePanel = in_array($activePanel, $panels, true) ? $activePanel : 'enrollments';
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
        <div class="profile-hero__avatar"><?= e(initials($volunteer['full_name'])) ?></div>
        <div class="profile-hero__info">
            <h1 class="profile-hero__name"><?= e($volunteer['full_name']) ?></h1>
            <div class="profile-hero__meta">
                <span><i class="fa-solid fa-location-dot"></i> <?= e($volunteer['location'] ?: 'Ubicación no indicada aún') ?></span>
                <span><i class="fa-regular fa-calendar"></i> Voluntaria desde <?= e(formatDate($volunteer['created_at'], false)) ?></span>
            </div>
        </div>
        <div class="profile-hero__actions">
            <a href="<?= e(actionUrl('view_volunteer_profile', ['panel' => 'skills'])) ?>#editar" class="btn btn--secondary btn--outline-light">Editar perfil</a>
        </div>
    </div>
    <div class="container profile-hero__stats">
        <div class="profile-stat"><div class="profile-stat__number"><?= (int) $stats['hours'] ?></div><div class="profile-stat__label">Horas acumuladas (estimadas)</div></div>
        <div class="profile-stat"><div class="profile-stat__number"><?= (int) $stats['completed'] ?></div><div class="profile-stat__label">Actividades completadas</div></div>
        <div class="profile-stat"><div class="profile-stat__number"><?= (int) $stats['organizations'] ?></div><div class="profile-stat__label">Organizaciones apoyadas</div></div>
    </div>
    <div style="height:40px"></div>
</section>

<section class="page-section" style="padding:36px 0 76px">
    <div class="container">
        <div class="profile-tabs">
            <button type="button" class="pill-filter<?= $activePanel === 'enrollments' ? ' pill-filter--active' : '' ?>" data-profile-tab="enrollments">Mis inscripciones (<?= count($enrollments) ?>)</button>
            <button type="button" class="pill-filter<?= $activePanel === 'recommendations' ? ' pill-filter--active' : '' ?>" data-profile-tab="recommendations">Recomendado para vos</button>
            <button type="button" class="pill-filter<?= $activePanel === 'skills' ? ' pill-filter--active' : '' ?>" data-profile-tab="skills">Habilidades e intereses</button>
        </div>

        <!-- ============ Mis inscripciones (RF11) ============ -->
        <div data-profile-panel="enrollments" style="<?= $activePanel === 'enrollments' ? 'display:flex' : 'display:none' ?>;flex-direction:column;gap:16px">
            <?php if ($enrollments === []): ?>
                <div class="empty-state">
                    <i class="fa-regular fa-calendar"></i>
                    <p>Todavía no te has inscrito en ninguna oportunidad.</p>
                    <a href="<?= e(actionUrl('search_opportunities')) ?>" class="btn btn--primary">Explorar oportunidades</a>
                </div>
            <?php endif; ?>
            <?php foreach ($enrollments as $enrollment): ?>
                <?php $colors = $statusColors[$enrollment['status']] ?? $statusColors['rejected']; ?>
                <div class="enrollment-row">
                    <div class="enrollment-row__photo"><img src="<?= BASE_URL ?>assets/img/placeholder.jpg" alt="foto: <?= e($enrollment['title']) ?>"></div>
                    <div class="enrollment-row__info">
                        <div class="eyebrow-label" style="margin-bottom:0"><?= e($enrollment['category_name']) ?></div>
                        <h3 class="enrollment-row__title"><?= e($enrollment['title']) ?></h3>
                        <div class="enrollment-row__meta"><?= e($enrollment['organization_name']) ?> · <?= e(formatDate($enrollment['activity_date'], false)) ?></div>
                    </div>
                    <div class="enrollment-row__status" style="background:<?= $colors['bg'] ?>;color:<?= $colors['text'] ?>"><?= e(statusLabel($enrollment['status'])) ?></div>
                    <a href="<?= e(actionUrl('view_opportunity', ['id' => (int) $enrollment['opportunity_id']])) ?>" class="enrollment-row__link">Ver detalle</a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- ============ Recomendado para vos (RF10, RN08) ============ -->
        <div data-profile-panel="recommendations" style="<?= $activePanel === 'recommendations' ? 'display:block' : 'display:none' ?>">
            <?php if (!$hasProfileData): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <p>Todavía no podemos recomendarte nada. Marcá tus habilidades y las categorías que te interesan, y armamos sugerencias hechas a tu medida.</p>
                    <a href="<?= e(actionUrl('view_volunteer_profile', ['panel' => 'skills'])) ?>" class="btn btn--primary">Completar mi perfil</a>
                </div>
            <?php elseif ($recommendations === []): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <p>Por ahora no hay oportunidades abiertas que coincidan con tu perfil. Probá explorando todas las publicadas.</p>
                    <a href="<?= e(actionUrl('search_opportunities')) ?>" class="btn btn--secondary">Ver todas las oportunidades</a>
                </div>
            <?php else: ?>
                <p class="page-subtext">Ordenadas por afinidad: primero tus habilidades, después tus intereses y por último la cercanía (RN08).</p>
                <div class="grid-3">
                    <?php foreach ($recommendations as $cardOpportunity): ?>
                        <div class="recommendation">
                            <?php $cardCompact = true; require __DIR__ . '/partials/opportunity_card.php'; ?>
                            <p class="recommendation__reason">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                                <?= e(recommendationReason($cardOpportunity)) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ============ Habilidades e intereses (RF03) ============ -->
        <div class="panel" data-profile-panel="skills" id="editar" style="<?= $activePanel === 'skills' ? 'display:block' : 'display:none' ?>;padding:28px;max-width:640px">
            <form method="post" action="<?= e(actionUrl('save_volunteer_profile')) ?>">
                <?= csrfField() ?>

                <div class="form-group">
                    <label for="profile-full-name">Nombre completo</label>
                    <input type="text" id="profile-full-name" name="full_name" maxlength="150" value="<?= e($value('full_name')) ?>">
                    <?php if (isset($formErrors['full_name'])): ?>
                        <p class="field-error"><?= e($formErrors['full_name']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="profile-location">Ubicación</label>
                    <input type="text" id="profile-location" name="location" maxlength="150" value="<?= e($value('location')) ?>" placeholder="Ej. Ciudad Quesada, Alajuela">
                    <?php if (isset($formErrors['location'])): ?>
                        <p class="field-error"><?= e($formErrors['location']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="profile-about-me">Sobre mí</label>
                    <textarea id="profile-about-me" name="about_me" rows="3"><?= e($value('about_me')) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="profile-availability">Disponibilidad</label>
                    <select id="profile-availability" name="availability">
                        <option value="">Sin indicar</option>
                        <?php foreach ($availabilityOptions as $option): ?>
                            <option value="<?= e($option) ?>" <?= $currentAvailability === $option ? 'selected' : '' ?>><?= e($option) ?></option>
                        <?php endforeach; ?>
                        <?php if ($currentAvailability !== '' && !in_array($currentAvailability, $availabilityOptions, true)): ?>
                            <option value="<?= e($currentAvailability) ?>" selected><?= e($currentAvailability) ?></option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Mis habilidades</label>
                    <div class="checkbox-grid">
                        <?php foreach ($allSkills as $skill): ?>
                            <label class="search-sidebar__checkbox">
                                <input type="checkbox" name="skills[]" value="<?= (int) $skill['id'] ?>"
                                       <?= in_array((int) $skill['id'], $selectedSkillIds, true) ? 'checked' : '' ?>>
                                <?= e($skill['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Categorías de interés</label>
                    <div class="checkbox-grid">
                        <?php foreach ($allCategories as $category): ?>
                            <label class="search-sidebar__checkbox">
                                <input type="checkbox" name="interests[]" value="<?= (int) $category['id'] ?>"
                                       <?= in_array((int) $category['id'], $selectedInterestIds, true) ? 'checked' : '' ?>>
                                <?= e($category['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn--primary">Guardar cambios</button>
            </form>
        </div>
    </div>
</section>

<script src="<?= BASE_URL ?>assets/js/volunteer-profile.js"></script>
</body>
</html>
