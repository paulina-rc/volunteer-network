<?php
$activeNav = 'home';
$showSearchIcon = true;

$heroSlides = [
    [
        'eyebrow' => 'Conecta. Participa. Transforma.',
        'title' => 'Encontrá la oportunidad de voluntariado que va con vos',
        'text' => 'Enlaza conecta a personas con ganas de ayudar y organizaciones sin fines de lucro que necesitan apoyo. La tecnología al servicio de la comunidad.',
        'cta1' => 'Quiero ser voluntario',
        'cta1Href' => BASE_URL . '?action=register&tab=register',
        'cta2' => 'Soy una organización',
        'cta2Href' => BASE_URL . '?action=register&tab=register',
        'photo' => 'carrusel-1.jpg',
        'gradient' => 'linear-gradient(120deg, #0F4C5C 0%, #146579 55%, #A8C9A1 130%)',
    ],
    [
        'eyebrow' => $platformStats['organizations'] . ' organizaciones registradas',
        'title' => 'Organizaciones de la Zona Norte que buscan voluntarios',
        'text' => 'Publicá tus oportunidades, gestioná inscripciones y encontrá voluntarios afines a tu causa en un solo lugar.',
        'cta1' => 'Publicar oportunidad',
        'cta1Href' => BASE_URL . '?action=publish_opportunity',
        'cta2' => 'Ver organizaciones',
        'cta2Href' => BASE_URL . '?action=view_organization_profile',
        'photo' => 'carrusel-2.jpg',
        'gradient' => 'linear-gradient(120deg, #F26B4A 0%, #c9502f 60%, #E9A227 140%)',
    ],
    [
        'eyebrow' => 'Ambiente, educación, salud y más',
        'title' => 'Explorá oportunidades por categoría, ubicación y fecha',
        'text' => 'Revisá lo que las organizaciones tienen publicado, leé los detalles de cada actividad e inscribite en la que te sirva.',
        'cta1' => 'Explorar oportunidades',
        'cta1Href' => BASE_URL . '?action=search_opportunities',
        'cta2' => null,
        'cta2Href' => null,
        'photo' => 'carrusel-3.jpg',
        'gradient' => 'linear-gradient(120deg, #E9A227 0%, #c78415 55%, #0F4C5C 140%)',
    ],
];

// $featuredOpportunities, $categories (with opportunity_count) and $platformStats
// all come from OpportunityController::home(), straight out of the database.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enlaza — Conecta. Participa. Transforma.</title>
    <link rel="stylesheet" href="<?= assetUrl('css/general-styles.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>

<section class="hero">
    <?php foreach ($heroSlides as $index => $slide): ?>
        <?php
        // Each slide uses its photo when the file exists; otherwise it falls back to its brand gradient.
        $slideHasPhoto = is_file(dirname(__DIR__, 2) . '/public/assets/img/' . $slide['photo']);
        $slideBackground = $slideHasPhoto
            ? "url('" . assetUrl('img/' . $slide['photo']) . "') center / cover no-repeat"
            : $slide['gradient'];
        ?>
        <div class="hero__slide<?= $index === 0 ? ' hero__slide--active' : '' ?>" style="background:<?= $slideBackground ?>" data-slide="<?= $index ?>">
            <div class="<?= $slideHasPhoto ? 'hero__overlay' : 'hero__gradient' ?>"></div>
            <div class="hero__content">
                <div class="hero__eyebrow"><?= e($slide['eyebrow']) ?></div>
                <h1 class="hero__title"><?= e($slide['title']) ?></h1>
                <p class="hero__text"><?= e($slide['text']) ?></p>
                <div class="hero__actions">
                    <a href="<?= e($slide['cta1Href']) ?>" class="btn btn--primary"><?= e($slide['cta1']) ?> <i class="fa-solid fa-arrow-right"></i></a>
                    <?php if ($slide['cta2'] !== null): ?>
                        <a href="<?= e($slide['cta2Href']) ?>" class="btn hero__cta--light"><?= e($slide['cta2']) ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <button type="button" class="hero__nav-btn hero__nav-btn--prev" data-hero-prev aria-label="Diapositiva anterior"><i class="fa-solid fa-chevron-left"></i></button>
    <button type="button" class="hero__nav-btn hero__nav-btn--next" data-hero-next aria-label="Diapositiva siguiente"><i class="fa-solid fa-chevron-right"></i></button>
    <div class="hero__dots">
        <?php foreach ($heroSlides as $index => $slide): ?>
            <button type="button" class="hero__dot<?= $index === 0 ? ' hero__dot--active' : '' ?>" data-hero-dot="<?= $index ?>" aria-label="Ir a la diapositiva <?= $index + 1 ?>"></button>
        <?php endforeach; ?>
    </div>
</section>

<form class="search-card" method="get" action="<?= BASE_URL ?>index.php">
    <input type="hidden" name="action" value="search_opportunities">
    <div class="search-card__field">
        <label class="search-card__label" for="home-search-query">¿Qué buscás?</label>
        <div class="search-card__input-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="home-search-query" name="text" placeholder="Ej. reforestación, tutorías, feria de salud...">
        </div>
    </div>
    <div class="search-card__field">
        <label class="search-card__label" for="home-search-category">Categoría</label>
        <div class="search-card__input-wrap">
            <i class="fa-solid fa-layer-group"></i>
            <select id="home-search-category" name="categories[]">
                <option value="">Todas las categorías</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>"><?= e($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="search-card__field">
        <label class="search-card__label" for="home-search-location">Ubicación</label>
        <div class="search-card__input-wrap">
            <i class="fa-solid fa-location-dot"></i>
            <input type="text" id="home-search-location" name="location" placeholder="San Carlos, Alajuela...">
        </div>
    </div>
    <button type="submit" class="btn btn--primary search-card__submit"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
</form>

<section class="stats-banner">
    <div class="container stats-banner__grid">
        <div><div class="stats-banner__number"><?= (int) $platformStats['opportunities'] ?></div><div class="stats-banner__label">Oportunidades activas</div></div>
        <div><div class="stats-banner__number"><?= (int) $platformStats['organizations'] ?></div><div class="stats-banner__label">Organizaciones registradas</div></div>
        <div><div class="stats-banner__number"><?= (int) $platformStats['volunteers'] ?></div><div class="stats-banner__label">Voluntarios registrados</div></div>
        <div><div class="stats-banner__number"><?= (int) $platformStats['locations'] ?></div><div class="stats-banner__label">Comunidades con actividades</div></div>
    </div>
    <p class="stats-banner__note">Datos tomados de la base de datos del sistema.</p>
</section>

<section id="categorias" class="page-section">
    <div class="container">
        <div class="section-intro">
            <div class="section-intro__eyebrow">Por área</div>
            <h2 class="section-intro__title">Categorías de voluntariado</h2>
            <p class="section-intro__text">Elegí un área para ver las oportunidades abiertas en ella.</p>
        </div>
        <div class="grid-5">
            <?php foreach ($categories as $category): ?>
                <a href="<?= e(actionUrl('search_opportunities', ['categories' => [(int) $category['id']]])) ?>" class="category-card">
                    <div class="icon" style="background:<?= e($category['color_hex']) ?>;color:<?= contrastColor($category['color_hex']) ?>">
                        <i class="fa-solid <?= e($category['icon']) ?>"></i>
                    </div>
                    <h3 class="category-card__title"><?= e($category['name']) ?></h3>
                    <p class="category-card__desc">
                        <?php $count = (int) $category['opportunity_count']; ?>
                        <?= $count === 1 ? '1 oportunidad activa' : $count . ' oportunidades activas' ?>
                    </p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section id="oportunidades" class="page-section page-section--muted">
    <div class="container">
        <div class="section-intro">
            <div class="section-intro__eyebrow">Recién publicadas</div>
            <h2 class="section-intro__title">Oportunidades destacadas</h2>
            <p class="section-intro__text">Las últimas oportunidades que publicaron las organizaciones.</p>
        </div>
        <?php if ($featuredOpportunities === []): ?>
            <div class="empty-state">
                <i class="fa-regular fa-calendar"></i>
                <p>Todavía no hay oportunidades publicadas. Volvé pronto.</p>
            </div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($featuredOpportunities as $cardOpportunity): ?>
                    <?php $cardCompact = false; require __DIR__ . '/partials/opportunity_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div style="text-align:center;margin-top:36px">
            <a href="<?= BASE_URL ?>?action=search_opportunities" class="btn btn--primary">Ver todas las oportunidades <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<section id="como-funciona" class="page-section">
    <div class="container">
        <div class="section-intro" style="margin-bottom:44px">
            <div class="section-intro__eyebrow">Paso a paso</div>
            <h2 class="section-intro__title">¿Cómo funciona Enlaza?</h2>
        </div>
        <div style="display:flex;justify-content:center;gap:10px;margin-bottom:44px">
            <button type="button" class="pill-filter pill-filter--active" data-how-tab="volunteer">Soy voluntario</button>
            <button type="button" class="pill-filter" data-how-tab="organization">Soy organización</button>
        </div>

        <div class="steps-grid" data-how-panel="volunteer">
            <div class="step">
                <div class="step__number">1</div>
                <h3 class="step__title">Registrate y creá tu perfil</h3>
                <p class="step__text">Indicá tus habilidades, intereses, disponibilidad y ubicación.</p>
            </div>
            <div class="step">
                <div class="step__number">2</div>
                <h3 class="step__title">Explorá y filtrá oportunidades</h3>
                <p class="step__text">Buscá por categoría, ubicación y fecha, o recibí recomendaciones según tu perfil.</p>
            </div>
            <div class="step">
                <div class="step__number">3</div>
                <h3 class="step__title">Inscribite y participá</h3>
                <p class="step__text">Dale seguimiento al estado de tu inscripción y sumate a la actividad.</p>
            </div>
        </div>

        <div class="steps-grid" data-how-panel="organization" style="display:none">
            <div class="step">
                <div class="step__number">1</div>
                <h3 class="step__title">Completá tu perfil institucional</h3>
                <p class="step__text">Nombre, descripción y datos de contacto de tu organización.</p>
            </div>
            <div class="step">
                <div class="step__number">2</div>
                <h3 class="step__title">Publicá tu oportunidad</h3>
                <p class="step__text">Título, descripción, habilidades requeridas, ubicación, fecha y cupos.</p>
            </div>
            <div class="step">
                <div class="step__number">3</div>
                <h3 class="step__title">Gestioná tus inscripciones</h3>
                <p class="step__text">Revisá perfiles y aceptá o rechazá inscripciones de voluntarios.</p>
            </div>
        </div>
    </div>
</section>

<section class="testimonial">
    <div class="container">
        <blockquote class="testimonial__quote">"Juntos generamos cambios que importan."</blockquote>
        <div class="testimonial__author">— Equipo Enlaza</div>
    </div>
</section>

<section class="cta-band">
    <div class="container">
        <h2 class="cta-band__title">¿Querés sumarte?</h2>
        <p class="cta-band__text">Creá una cuenta como voluntario para inscribirte en actividades, o como organización para publicar las que necesitan apoyo.</p>
        <div class="cta-band__actions">
            <a href="<?= BASE_URL ?>?action=register&tab=register" class="btn btn--primary">Quiero ser voluntario</a>
            <a href="<?= BASE_URL ?>?action=publish_opportunity" class="btn btn--secondary btn--outline-light">Quiero publicar oportunidades</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>

<script src="<?= assetUrl('js/home.js') ?>"></script>
</body>
</html>
