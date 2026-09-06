<?php
/**
 * Organization profile (RF04, RN02).
 *
 * From OrganizationController::renderProfile():
 * $organization, $isOwner, $stats, $opportunities, $profileComplete,
 * $pendingCount, $categories, $formErrors
 */
$activeNav = 'organizations';

$statusColors = [
    'active' => ['bg' => '#A8C9A1', 'text' => '#0B3945'],
    'closed' => ['bg' => '#F1EEE6', 'text' => '#8a8a85'],
    'draft'  => ['bg' => '#E9A227', 'text' => '#4a3106'],
];

$organizationMeta = [];
if (!empty($organization['location'])) {
    $organizationMeta[] = ['icon' => 'fa-location-dot', 'text' => $organization['location']];
}
if (!empty($organization['category_name'])) {
    $organizationMeta[] = ['icon' => 'fa-layer-group', 'text' => $organization['category_name']];
}
if (!empty($organization['founded_year'])) {
    $organizationMeta[] = ['icon' => 'fa-calendar', 'text' => 'Aliada desde ' . (int) $organization['founded_year']];
}

$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($organization['name']) ?> · Enlaza</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/general-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>

<section class="org-cover">
    <img src="<?= BASE_URL ?>assets/img/placeholder.jpg" alt="foto de portada de <?= e($organization['name']) ?>">
</section>

<section class="org-header-card">
    <div class="container org-header-card__inner">
        <div class="profile-hero__avatar profile-hero__avatar--square"><?= e(initials($organization['name'])) ?></div>
        <div class="profile-hero__info" style="padding-bottom:6px">
            <h1 class="profile-hero__name profile-hero__name--dark"><?= e($organization['name']) ?></h1>
            <div class="profile-hero__meta profile-hero__meta--dark">
                <?php foreach ($organizationMeta as $meta): ?>
                    <span><i class="fa-solid <?= e($meta['icon']) ?>"></i> <?= e($meta['text']) ?></span>
                <?php endforeach; ?>
                <?php if ($organizationMeta === []): ?>
                    <span>Esta organización todavía no completó sus datos.</span>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($isOwner): ?>
            <div class="org-header-card__actions">
                <a href="<?= e(actionUrl('manage_enrollments')) ?>" class="btn btn--secondary">
                    Inscripciones<?= $pendingCount > 0 ? ' (' . (int) $pendingCount . ')' : '' ?>
                </a>
                <a href="#editar-perfil" class="btn btn--secondary">Editar perfil</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section style="padding:0 0 76px">
    <div class="container">

        <?php if ($isOwner && !$profileComplete): ?>
            <!-- RN02 — publishing stays blocked until these three fields exist. -->
            <div class="notice notice--warning">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    <strong>Tu perfil está incompleto.</strong>
                    Para poder publicar oportunidades necesitás llenar la <strong>descripción</strong>,
                    la <strong>ubicación</strong> y el <strong>contacto</strong> de la organización.
                    <a href="#editar-perfil">Completar el perfil ahora</a>.
                </div>
            </div>
        <?php endif; ?>

        <div class="org-summary">
            <div>
                <h2 class="detail-section-title">Sobre la organización</h2>
                <p class="detail-section-text" style="margin-bottom:0">
                    <?= $organization['description'] !== null && trim((string) $organization['description']) !== ''
                        ? nl2br(e($organization['description']))
                        : 'Esta organización todavía no escribió su descripción.' ?>
                </p>
                <?php if (!empty($organization['contact'])): ?>
                    <p class="detail-section-text" style="margin-top:14px"><strong>Contacto:</strong> <?= e($organization['contact']) ?></p>
                <?php endif; ?>
            </div>
            <div class="org-summary__stats">
                <div class="org-stat"><div class="org-stat__number"><?= (int) $stats['published'] ?></div><div class="org-stat__label">Oportunidades publicadas</div></div>
                <div class="org-stat"><div class="org-stat__number"><?= (int) $stats['volunteers'] ?></div><div class="org-stat__label">Voluntarios apoyados</div></div>
            </div>
        </div>

        <div class="org-section-head">
            <h2 class="section-intro__title" style="margin:0;font-size:20px">Oportunidades publicadas</h2>
            <?php if ($isOwner): ?>
                <a href="<?= e(actionUrl('publish_opportunity')) ?>" class="btn btn--primary">Publicar oportunidad</a>
            <?php endif; ?>
        </div>

        <?php if ($opportunities === []): ?>
            <div class="empty-state">
                <i class="fa-regular fa-folder-open"></i>
                <p><?= $isOwner
                        ? 'Todavía no publicaste ninguna oportunidad.'
                        : 'Esta organización todavía no tiene oportunidades publicadas.' ?></p>
                <?php if ($isOwner && $profileComplete): ?>
                    <a href="<?= e(actionUrl('publish_opportunity')) ?>" class="btn btn--primary">Publicar la primera</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($opportunities as $opportunity): ?>
                    <?php $colors = $statusColors[$opportunity['status']] ?? $statusColors['closed']; ?>
                    <div class="opportunity-card opportunity-card--compact">
                        <div class="opportunity-card__photo">
                            <img src="<?= BASE_URL ?>assets/img/placeholder.jpg" alt="foto: <?= e($opportunity['title']) ?>">
                            <span class="opportunity-card__badge" style="background:<?= $colors['bg'] ?>;color:<?= $colors['text'] ?>"><?= e(statusLabel($opportunity['status'])) ?></span>
                        </div>
                        <div class="opportunity-card__body">
                            <div class="eyebrow-label"><?= e($opportunity['category_name']) ?></div>
                            <h3 class="opportunity-card__title"><?= e($opportunity['title']) ?></h3>
                            <div class="opportunity-card__stats">
                                <?= (int) $opportunity['taken_slots'] ?> inscritos · <?= (int) $opportunity['total_slots'] ?> cupos ·
                                <?= e(formatDate($opportunity['activity_date'], false)) ?>
                            </div>

                            <?php if ($isOwner): ?>
                                <div class="card-actions">
                                    <a href="<?= e(actionUrl('edit_opportunity', ['id' => (int) $opportunity['id']])) ?>" class="btn btn--secondary btn--sm">Editar</a>
                                    <a href="<?= e(actionUrl('manage_enrollments', ['opportunity_id' => (int) $opportunity['id']])) ?>" class="btn btn--secondary btn--sm">Inscripciones</a>
                                    <?php if ($opportunity['status'] !== 'draft'): ?>
                                        <form method="post" action="<?= e(actionUrl('close_opportunity')) ?>" style="display:inline">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="opportunity_id" value="<?= (int) $opportunity['id'] ?>">
                                            <?php if ($opportunity['status'] === 'active'): ?>
                                                <button type="submit" name="new_status" value="closed" class="btn btn--secondary btn--sm">Cerrar</button>
                                            <?php elseif ((int) $opportunity['available_slots'] > 0 && $opportunity['activity_date'] >= $today): ?>
                                                <button type="submit" name="new_status" value="active" class="btn btn--secondary btn--sm">Reabrir</button>
                                            <?php endif; ?>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <a href="<?= e(actionUrl('view_opportunity', ['id' => (int) $opportunity['id']])) ?>" class="btn btn--secondary btn--full-width">Ver detalle</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($isOwner): ?>
            <!-- ============ Editar perfil (RF04) ============ -->
            <h2 class="section-intro__title" id="editar-perfil" style="margin:44px 0 20px;font-size:20px">Editar perfil</h2>
            <div class="panel" style="padding:28px;max-width:640px">
                <form method="post" action="<?= e(actionUrl('save_organization_profile')) ?>">
                    <?= csrfField() ?>

                    <div class="form-group">
                        <label for="org-name">Nombre de la organización</label>
                        <input type="text" id="org-name" name="name" maxlength="150" value="<?= e($organization['name']) ?>">
                        <?php if (isset($formErrors['name'])): ?>
                            <p class="field-error"><?= e($formErrors['name']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group-row">
                        <div class="form-group">
                            <label for="org-category">Categoría principal</label>
                            <select id="org-category" name="category_id">
                                <option value="0">Sin indicar</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int) $category['id'] ?>" <?= (int) ($organization['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="org-founded">Año de fundación</label>
                            <input type="number" id="org-founded" name="founded_year" min="1800" max="<?= date('Y') ?>" value="<?= $organization['founded_year'] !== null ? (int) $organization['founded_year'] : '' ?>">
                            <?php if (isset($formErrors['founded_year'])): ?>
                                <p class="field-error"><?= e($formErrors['founded_year']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="org-description">Descripción <span class="required-note">(necesaria para publicar)</span></label>
                        <textarea id="org-description" name="description" rows="4" placeholder="Contá a qué se dedica la organización y con quiénes trabajan."><?= e($organization['description']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="org-location">Ubicación <span class="required-note">(necesaria para publicar)</span></label>
                        <input type="text" id="org-location" name="location" maxlength="150" value="<?= e($organization['location']) ?>" placeholder="Ej. San Carlos, Alajuela">
                    </div>

                    <div class="form-group">
                        <label for="org-contact">Contacto <span class="required-note">(necesario para publicar)</span></label>
                        <input type="text" id="org-contact" name="contact" maxlength="150" value="<?= e($organization['contact']) ?>" placeholder="Ej. 2460-1122 o contacto@organizacion.org">
                    </div>

                    <button type="submit" class="btn btn--primary">Guardar cambios</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</section>
</body>
</html>
