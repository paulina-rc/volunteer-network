<?php
$activeNav = 'opportunities';

// $opportunities, $categories, $filters, $selectedCategories and
// $hasActiveFilters all come from OpportunityController::search().
// With no category checked the search is not filtered by category, so the box
// list shows every category checked — form and results always agree.
$checkedCategories = $selectedCategories === []
    ? array_map('intval', array_column($categories, 'id'))
    : $selectedCategories;

$resultCount = count($opportunities);

$dateOptions = [
    ''        => 'Cualquier fecha',
    'week'    => 'Esta semana',
    'month'   => 'Este mes',
    'quarter' => 'Próximos 3 meses',
];
$sortOptions = [
    'recent'  => 'Ordenar: más recientes',
    'soonest' => 'Ordenar: fecha más próxima',
    'slots'   => 'Ordenar: más cupos',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Oportunidades de voluntariado · Enlaza</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/general-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>

<section class="page-banner">
    <div class="container">
        <h1 class="page-banner__title">Oportunidades de voluntariado</h1>
        <p class="page-banner__text" data-results-text>
            <?php if ($resultCount === 1): ?>
                1 oportunidad encontrada
            <?php elseif ($hasActiveFilters): ?>
                <?= $resultCount ?> oportunidades encontradas
            <?php else: ?>
                <?= $resultCount ?> oportunidades activas en este momento
            <?php endif; ?>
        </p>
    </div>
</section>

<section class="container search-layout">
    <form class="panel search-sidebar" method="get" action="<?= BASE_URL ?>index.php" id="search-form" data-search-form>
        <input type="hidden" name="action" value="search_opportunities">

        <div class="search-sidebar__section">
            <label class="search-card__label" for="filter-search">Buscar</label>
            <div class="search-card__input-wrap" style="margin-top:6px">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="filter-search" name="text" value="<?= e($filters['text']) ?>" placeholder="Ej. reforestación...">
            </div>
        </div>

        <div class="search-sidebar__section">
            <div class="search-card__label" style="margin-bottom:10px">Categoría</div>
            <div class="search-sidebar__checkbox-list">
                <?php foreach ($categories as $category): ?>
                    <label class="search-sidebar__checkbox">
                        <input type="checkbox" name="categories[]" value="<?= (int) $category['id'] ?>"
                               data-category-filter="<?= e($category['name']) ?>"
                               <?= in_array((int) $category['id'], $checkedCategories, true) ? 'checked' : '' ?>>
                        <?= e($category['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="search-sidebar__section">
            <label class="search-card__label" for="filter-location">Ubicación</label>
            <div style="margin-top:6px">
                <input type="text" id="filter-location" name="location" value="<?= e($filters['location']) ?>" placeholder="San Carlos, Alajuela..." style="width:100%;border:1.5px solid var(--color-border-strong);border-radius:10px;padding:10px 12px;font-family:'Poppins',sans-serif;font-size:13.5px;color:var(--color-text)">
            </div>
        </div>

        <div class="search-sidebar__section">
            <label class="search-card__label" for="filter-date">Fecha</label>
            <select id="filter-date" name="date" style="width:100%;margin-top:6px;border:1.5px solid var(--color-border-strong);border-radius:10px;padding:10px 12px;font-family:'Poppins',sans-serif;font-size:13.5px;color:var(--color-text)">
                <?php foreach ($dateOptions as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $filters['date'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn--primary btn--full-width" style="margin-bottom:10px">Aplicar filtros</button>
        <a href="<?= e(actionUrl('search_opportunities')) ?>" class="btn btn--secondary btn--full-width" data-clear-filters>Limpiar filtros</a>
    </form>

    <div>
        <div class="search-results-bar">
            <!-- Outside the sidebar visually, but part of the same form, so ordering
                 works even without JavaScript. -->
            <select name="sort" form="search-form" data-sort-select>
                <?php foreach ($sortOptions as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $filters['sort'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ($opportunities === []): ?>
            <div class="empty-state" data-empty-state>
                <i class="fa-solid fa-magnifying-glass"></i>
                <?php if ($hasActiveFilters): ?>
                    <p>No encontramos oportunidades con esos filtros. Probá quitando alguno o ampliando la fecha.</p>
                    <a href="<?= e(actionUrl('search_opportunities')) ?>" class="btn btn--primary">Ver todas las oportunidades</a>
                <?php else: ?>
                    <p>Todavía no hay oportunidades abiertas. Volvé pronto: las organizaciones publican nuevas actividades cada semana.</p>
                    <a href="<?= BASE_URL ?>" class="btn btn--secondary">Volver al inicio</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="grid-3" data-results-grid>
                <?php foreach ($opportunities as $cardOpportunity): ?>
                    <?php $cardCompact = true; require __DIR__ . '/partials/opportunity_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script src="<?= BASE_URL ?>assets/js/search-filters.js"></script>
</body>
</html>
