<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Publicar oportunidad · Enlaza</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/general-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php $headerVariant = 'minimal'; $backLinkHref = BASE_URL . '?action=view_organization_profile'; $backLinkLabel = 'Volver a mi perfil'; require __DIR__ . '/partials/header.php'; ?>

<section class="narrow-page">
    <div class="confirmation-panel" data-publish-success style="display:none">
        <i class="fa-solid fa-circle-check"></i>
        <h1>Oportunidad publicada</h1>
        <p>Ya está visible para las personas voluntarias en el buscador de Enlaza.</p>
        <div class="confirmation-panel__actions">
            <a href="<?= BASE_URL ?>?action=manage_enrollments" class="btn btn--primary">Gestionar inscripciones</a>
            <a href="<?= BASE_URL ?>?action=view_organization_profile" class="btn btn--secondary">Ver mi perfil</a>
        </div>
    </div>

    <div data-publish-form>
        <h1 class="page-heading">Publicar oportunidad</h1>
        <p class="page-subtext">Completá los datos para que las personas voluntarias puedan encontrar tu actividad.</p>

        <!-- TODO (Paso 4 del plan de desarrollo, RF05, RN02, RN03): conectar este
             formulario a ?action=publish_opportunity con OpportunityModel::create(). -->
        <form class="form-card" data-publish-opportunity-form>
            <div class="form-group">
                <label for="publish-title">Título de la oportunidad</label>
                <input type="text" id="publish-title" placeholder="Ej. Jornada de reforestación río San Carlos">
            </div>

            <div class="form-group-row">
                <div class="form-group">
                    <label for="publish-category">Categoría</label>
                    <select id="publish-category">
                        <?php foreach ($categories as $category): ?>
                            <option><?= htmlspecialchars($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="publish-slots">Cupos disponibles</label>
                    <input type="number" id="publish-slots" placeholder="Ej. 20">
                </div>
            </div>

            <div class="form-group">
                <label for="publish-description">Descripción</label>
                <textarea id="publish-description" rows="4" placeholder="Contá de qué trata la actividad, qué se va a hacer y por qué importa."></textarea>
            </div>

            <div class="form-group">
                <label for="publish-skills">Habilidades requeridas</label>
                <input type="text" id="publish-skills" placeholder="Ej. ninguna, o trabajo con niños, primeros auxilios...">
            </div>

            <div class="form-group-row">
                <div class="form-group">
                    <label for="publish-date">Fecha</label>
                    <input type="date" id="publish-date">
                </div>
                <div class="form-group">
                    <label for="publish-time">Horario</label>
                    <input type="text" id="publish-time" placeholder="Ej. 7:00 a. m. – 12:00 m.">
                </div>
            </div>

            <div class="form-group">
                <label for="publish-location">Ubicación</label>
                <input type="text" id="publish-location" placeholder="Ej. San Carlos, Alajuela">
            </div>

            <div class="form-group">
                <label>Foto de la actividad</label>
                <div class="form-card__photo-slot">
                    <img src="<?= BASE_URL ?>assets/img/placeholder.jpg" alt="arrastrá una foto real de una actividad similar">
                </div>
            </div>

            <div class="form-card__actions">
                <button type="submit" class="btn btn--primary" style="flex:1">Publicar oportunidad</button>
                <button type="button" class="btn btn--secondary">Guardar borrador</button>
            </div>
        </form>
    </div>
</section>

<script src="<?= BASE_URL ?>assets/js/publish-opportunity.js"></script>
</body>
</html>
