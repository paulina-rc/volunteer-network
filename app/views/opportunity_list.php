<?php
$activeNav = 'opportunities';

// TODO (Paso 5 del plan de desarrollo, RF06): reemplazar por
// OpportunityModel::searchWithFilters() con los filtros reales (categoría,
// ubicación, fecha, texto) en vez de este arreglo de ejemplo.
$exampleOpportunities = [
    ['tag' => 'Ambiental', 'title' => 'Jornada de reforestación río San Carlos', 'org' => 'Fundación Verde Norte', 'date' => '24 ago 2026', 'location' => 'San Carlos', 'slots' => '8', 'photoAlt' => 'foto: voluntarios sembrando árboles junto al río'],
    ['tag' => 'Educativo', 'title' => 'Tutorías de matemáticas para primaria', 'org' => 'Asociación Aprender Juntos', 'date' => '2 sept 2026', 'location' => 'Ciudad Quesada', 'slots' => '3', 'photoAlt' => 'foto: tutora ayudando a un estudiante en el aula'],
    ['tag' => 'Salud', 'title' => 'Feria de salud comunitaria', 'org' => 'Cruz Roja — sede local', 'date' => '14 sept 2026', 'location' => 'Florencia', 'slots' => '12', 'photoAlt' => 'foto: fila de vecinos en feria de salud'],
    ['tag' => 'Ambiental', 'title' => 'Limpieza de playa y clasificación de residuos', 'org' => 'Colectivo Costas Limpias', 'date' => '5 oct 2026', 'location' => 'Playa Grande, Guanacaste', 'slots' => '15', 'photoAlt' => 'foto: voluntarios recogiendo residuos en la playa'],
    ['tag' => 'Comunitario', 'title' => 'Acompañamiento a personas adultas mayores', 'org' => 'Red de Cuido San Carlos', 'date' => '18 sept 2026', 'location' => 'Quesada Centro', 'slots' => '6', 'photoAlt' => 'foto: voluntaria conversando con una persona adulta mayor'],
    ['tag' => 'Cultural', 'title' => 'Rescate de tradiciones orales boyeras', 'org' => 'Casa de la Cultura Zarcero', 'date' => '11 oct 2026', 'location' => 'Zarcero, Alajuela', 'slots' => '10', 'photoAlt' => 'foto: entrevista a un boyero artesano en su taller'],
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
        <p class="page-banner__text" data-results-text><?= count($exampleOpportunities) ?> oportunidades activas en este momento</p>
    </div>
</section>

<section class="container search-layout">
    <aside class="panel search-sidebar">
        <div class="search-sidebar__section">
            <label class="search-card__label" for="filter-search">Buscar</label>
            <div class="search-card__input-wrap" style="margin-top:6px">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="filter-search" placeholder="Ej. reforestación...">
            </div>
        </div>

        <div class="search-sidebar__section">
            <div class="search-card__label" style="margin-bottom:10px">Categoría</div>
            <div class="search-sidebar__checkbox-list">
                <?php foreach ($categories as $category): ?>
                    <label class="search-sidebar__checkbox">
                        <input type="checkbox" checked data-category-filter="<?= htmlspecialchars($category['name']) ?>">
                        <?= htmlspecialchars($category['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="search-sidebar__section">
            <label class="search-card__label" for="filter-location">Ubicación</label>
            <div style="margin-top:6px">
                <input type="text" id="filter-location" placeholder="San Carlos, Alajuela..." style="width:100%;border:1.5px solid var(--color-border-strong);border-radius:10px;padding:10px 12px;font-family:'Poppins',sans-serif;font-size:13.5px;color:var(--color-text)">
            </div>
        </div>

        <div class="search-sidebar__section">
            <label class="search-card__label" for="filter-date">Fecha</label>
            <select id="filter-date" style="width:100%;margin-top:6px;border:1.5px solid var(--color-border-strong);border-radius:10px;padding:10px 12px;font-family:'Poppins',sans-serif;font-size:13.5px;color:var(--color-text)">
                <option>Cualquier fecha</option>
                <option>Esta semana</option>
                <option>Este mes</option>
                <option>Próximos 3 meses</option>
            </select>
        </div>

        <button type="button" class="btn btn--secondary btn--full-width" data-clear-filters>Limpiar filtros</button>
    </aside>

    <div>
        <div class="search-results-bar">
            <select>
                <option>Ordenar: más recientes</option>
                <option>Ordenar: fecha más próxima</option>
                <option>Ordenar: menos cupos</option>
            </select>
        </div>
        <div class="grid-3" data-results-grid>
            <?php foreach ($exampleOpportunities as $opportunity): ?>
                <div class="opportunity-card opportunity-card--compact" data-opportunity-card data-category="<?= htmlspecialchars($opportunity['tag']) ?>">
                    <div class="opportunity-card__photo">
                        <img src="<?= BASE_URL ?>assets/img/placeholder.jpg" alt="<?= htmlspecialchars($opportunity['photoAlt']) ?>">
                        <span class="opportunity-card__badge" style="background:#E9A227;color:#4a3106"><?= htmlspecialchars($opportunity['slots']) ?> cupos</span>
                        <span class="opportunity-card__tag"><?= htmlspecialchars($opportunity['tag']) ?></span>
                    </div>
                    <div class="opportunity-card__body">
                        <h3 class="opportunity-card__title"><?= htmlspecialchars($opportunity['title']) ?></h3>
                        <div class="opportunity-card__org"><?= htmlspecialchars($opportunity['org']) ?></div>
                        <div class="opportunity-card__meta">
                            <span class="opportunity-card__meta-item"><i class="fa-regular fa-calendar"></i> <?= htmlspecialchars($opportunity['date']) ?></span>
                            <span class="opportunity-card__meta-item"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($opportunity['location']) ?></span>
                        </div>
                        <a href="<?= BASE_URL ?>?action=view_opportunity" class="btn btn--secondary btn--full-width">Ver detalle</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="empty-state" data-empty-state style="display:none">
            <i class="fa-solid fa-magnifying-glass"></i>
            <p>No encontramos oportunidades con esos filtros. Probá quitando alguno.</p>
        </div>
    </div>
</section>

<script src="<?= BASE_URL ?>assets/js/search-filters.js"></script>
</body>
</html>
