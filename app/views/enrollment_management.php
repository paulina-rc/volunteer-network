<?php
/**
 * Enrollment management (RF08, CU06).
 *
 * From EnrollmentController::manage():
 * - $enrollments            every enrollment in scope, used for the tab counts
 * - $visibleEnrollments     the ones left after the ?status= filter
 * - $counts                 countByStatus() over $enrollments
 * - $statusFilter           'all' or one of the enrollment states
 * - $selectedOpportunity    the chosen opportunity, or null for "all"
 * - $organizationOpportunities  this organization's opportunities, for the picker
 */
$statusColors = [
    'pending'   => ['bg' => '#E9A227', 'text' => '#4a3106'],
    'accepted'  => ['bg' => '#A8C9A1', 'text' => '#0B3945'],
    'rejected'  => ['bg' => '#F1EEE6', 'text' => '#8a8a85'],
    'completed' => ['bg' => '#0F4C5C', 'text' => '#fff'],
];

$tabs = [
    'all'       => 'Todas',
    'pending'   => 'Pendientes',
    'accepted'  => 'Aceptadas',
    'completed' => 'Completadas',
    'rejected'  => 'Rechazadas',
];

// Keeps the chosen opportunity while switching tabs.
$tabParams = $selectedOpportunity !== null
    ? ['opportunity_id' => (int) $selectedOpportunity['id']]
    : [];

$today = date('Y-m-d');
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
<?php $headerVariant = 'minimal'; $backLinkHref = actionUrl('view_organization_profile'); $backLinkLabel = 'Volver a mi perfil'; require __DIR__ . '/partials/header.php'; ?>

<section class="narrow-page" style="max-width:1000px">
    <h1 class="page-heading">Gestionar inscripciones</h1>
    <p class="page-subtext">
        <?php if ($selectedOpportunity !== null): ?>
            <?= e($selectedOpportunity['title']) ?> · <?= e(formatDate($selectedOpportunity['activity_date'])) ?>
            · <?= (int) $selectedOpportunity['available_slots'] ?> de <?= (int) $selectedOpportunity['total_slots'] ?> cupos libres
            · <?= e(statusLabel($selectedOpportunity['status'])) ?>
        <?php else: ?>
            Todas las inscripciones que recibieron tus oportunidades.
        <?php endif; ?>
    </p>

    <form method="get" action="<?= BASE_URL ?>index.php" style="margin-bottom:24px">
        <input type="hidden" name="action" value="manage_enrollments">
        <label class="search-card__label" for="opportunity-picker">Oportunidad</label>
        <select id="opportunity-picker" name="opportunity_id" onchange="this.form.submit()"
                style="width:100%;max-width:520px;margin-top:6px;border:1.5px solid var(--color-border-strong);border-radius:10px;padding:10px 12px;font-family:'Poppins',sans-serif;font-size:13.5px;color:var(--color-text)">
            <option value="0">Todas mis oportunidades</option>
            <?php foreach ($organizationOpportunities as $organizationOpportunity): ?>
                <option value="<?= (int) $organizationOpportunity['id'] ?>"
                    <?= $selectedOpportunity !== null && (int) $selectedOpportunity['id'] === (int) $organizationOpportunity['id'] ? 'selected' : '' ?>>
                    <?= e($organizationOpportunity['title']) ?> (<?= e(statusLabel($organizationOpportunity['status'])) ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <noscript><button type="submit" class="btn btn--secondary btn--sm" style="margin-top:8px">Ver</button></noscript>
    </form>

    <div style="display:flex;gap:10px;margin-bottom:28px;flex-wrap:wrap">
        <?php foreach ($tabs as $tabKey => $tabLabel): ?>
            <a href="<?= e(actionUrl('manage_enrollments', $tabParams + ($tabKey === 'all' ? [] : ['status' => $tabKey]))) ?>"
               class="pill-filter pill-filter--sm<?= $statusFilter === $tabKey ? ' pill-filter--active' : '' ?>"
               data-enrollment-filter="<?= e($tabKey) ?>">
                <?= e($tabLabel) ?> (<?= (int) ($tabKey === 'all' ? $counts['all'] : $counts[$tabKey]) ?>)
            </a>
        <?php endforeach; ?>
    </div>

    <?php if ($visibleEnrollments === []): ?>
        <div class="empty-state" data-enrollment-empty>
            <i class="fa-regular fa-folder-open"></i>
            <p>No hay inscripciones en esta categoría.</p>
        </div>
    <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:14px" data-enrollment-list>
            <?php foreach ($visibleEnrollments as $enrollment): ?>
                <?php
                $status = $enrollment['status'];
                $colors = $statusColors[$status] ?? $statusColors['rejected'];
                $canComplete = $status === 'accepted' && $enrollment['activity_date'] < $today;
                ?>
                <div class="enrollment-row" data-enrollment-row data-status="<?= e($status) ?>">
                    <div class="enrollment-row__avatar"><?= e(initials($enrollment['volunteer_name'])) ?></div>
                    <div class="enrollment-row__info">
                        <?php if ($selectedOpportunity === null): ?>
                            <div class="eyebrow-label" style="margin-bottom:0"><?= e($enrollment['title']) ?></div>
                        <?php endif; ?>
                        <div class="enrollment-row__name"><?= e($enrollment['volunteer_name']) ?></div>
                        <div class="enrollment-row__contact">
                            <?= e($enrollment['volunteer_email']) ?>
                            <?php if (!empty($enrollment['volunteer_location'])): ?>
                                · <?= e($enrollment['volunteer_location']) ?>
                            <?php endif; ?>
                            · Inscrita el <?= e(formatDate($enrollment['enrollment_date'], false)) ?>
                        </div>
                    </div>
                    <div class="enrollment-row__status" data-status-badge
                         style="background:<?= $colors['bg'] ?>;color:<?= $colors['text'] ?>"><?= e(statusLabel($status)) ?></div>
                    <div class="enrollment-row__actions" data-status-actions>
                        <?php if ($status === 'pending'): ?>
                            <form method="post" action="<?= e(actionUrl('accept_enrollment')) ?>" style="display:inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="enrollment_id" value="<?= (int) $enrollment['id'] ?>">
                                <button type="submit" class="btn btn--sm" style="background:var(--color-primary);color:#fff;border:none" data-accept-button>Aceptar</button>
                            </form>
                            <form method="post" action="<?= e(actionUrl('reject_enrollment')) ?>" style="display:inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="enrollment_id" value="<?= (int) $enrollment['id'] ?>">
                                <button type="submit" class="btn btn--sm" style="border-color:var(--color-border-strong);color:#6d6d68;background:transparent" data-reject-button>Rechazar</button>
                            </form>
                        <?php elseif ($canComplete): ?>
                            <form method="post" action="<?= e(actionUrl('complete_enrollment')) ?>" style="display:inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="enrollment_id" value="<?= (int) $enrollment['id'] ?>">
                                <button type="submit" class="btn btn--sm" style="background:var(--color-primary);color:#fff;border:none">Marcar como completada</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <p class="stats-banner__note" style="text-align:left;margin-top:28px">
        Cuando se acepta la inscripción que ocupa el último cupo, la oportunidad pasa
        automáticamente al estado <strong>Cerrada</strong> y deja de aparecer en el buscador (RN05).
        Si después se rechaza una inscripción aceptada y la fecha todavía no venció, el cupo se
        libera y la oportunidad vuelve a abrirse.
    </p>
</section>
</body>
</html>
