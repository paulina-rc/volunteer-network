<?php
/**
 * Publish / edit an opportunity (RF05, RN02, RN03).
 *
 * From OpportunityController::renderForm():
 * $opportunity (null when publishing), $isEditing, $formData, $formErrors,
 * $categories, $allSkills, $profileComplete
 */
$formAction = $isEditing
    ? actionUrl('edit_opportunity')
    : actionUrl('publish_opportunity');

$heading = $isEditing ? 'Editar oportunidad' : 'Publicar oportunidad';
$submitLabel = $isEditing
    ? ($opportunity['status'] === 'draft' ? 'Publicar oportunidad' : 'Guardar cambios')
    : 'Publicar oportunidad';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($heading) ?> · Enlaza</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/general-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php $headerVariant = 'minimal'; $backLinkHref = actionUrl('view_organization_profile'); $backLinkLabel = 'Volver a mi perfil'; require __DIR__ . '/partials/header.php'; ?>

<section class="narrow-page">
    <h1 class="page-heading"><?= e($heading) ?></h1>
    <p class="page-subtext">Completá los datos para que las personas voluntarias puedan encontrar tu actividad.</p>

    <?php if (!$profileComplete): ?>
        <!-- RN02 — drafts are still allowed, publishing is not. -->
        <div class="notice notice--warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
                <strong>Tu perfil está incompleto.</strong>
                Para publicar necesitás la descripción, la ubicación y el contacto de tu organización.
                Mientras tanto podés guardar esta oportunidad como borrador.
                <a href="<?= e(actionUrl('view_organization_profile')) ?>#editar-perfil">Completar el perfil</a>.
            </div>
        </div>
    <?php endif; ?>

    <?php if ($formErrors !== []): ?>
        <ul class="error-list">
            <li>Revisá los campos marcados en rojo: hay <?= count($formErrors) ?> <?= count($formErrors) === 1 ? 'dato' : 'datos' ?> por corregir.</li>
        </ul>
    <?php endif; ?>

    <form class="form-card" method="post" action="<?= e($formAction) ?>">
        <?= csrfField() ?>
        <?php if ($isEditing): ?>
            <input type="hidden" name="opportunity_id" value="<?= (int) $opportunity['id'] ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="publish-title">Título de la oportunidad</label>
            <input type="text" id="publish-title" name="title" maxlength="150"
                   value="<?= e($formData['title']) ?>"
                   placeholder="Ej. Jornada de reforestación río San Carlos">
            <?php if (isset($formErrors['title'])): ?>
                <p class="field-error"><?= e($formErrors['title']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group-row">
            <div class="form-group">
                <label for="publish-category">Categoría</label>
                <select id="publish-category" name="category_id">
                    <option value="0">Elegí una categoría</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int) $category['id'] ?>" <?= (int) $formData['category_id'] === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($formErrors['category_id'])): ?>
                    <p class="field-error"><?= e($formErrors['category_id']) ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="publish-slots">Cupos disponibles</label>
                <input type="number" id="publish-slots" name="total_slots" min="1"
                       value="<?= e((string) $formData['total_slots']) ?>" placeholder="Ej. 20">
                <?php if (isset($formErrors['total_slots'])): ?>
                    <p class="field-error"><?= e($formErrors['total_slots']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="publish-description">Descripción</label>
            <textarea id="publish-description" name="description" rows="4"
                      placeholder="Contá de qué trata la actividad, qué se va a hacer y por qué importa."><?= e($formData['description']) ?></textarea>
            <?php if (isset($formErrors['description'])): ?>
                <p class="field-error"><?= e($formErrors['description']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="publish-requirements">Requisitos <span class="required-note">(uno por línea)</span></label>
            <textarea id="publish-requirements" name="requirements" rows="3"
                      placeholder="Ropa de manga larga y botas cerradas&#10;Disponibilidad de 5 horas"><?= e($formData['requirements']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Habilidades requeridas</label>
            <div class="checkbox-grid">
                <?php foreach ($allSkills as $skill): ?>
                    <label class="search-sidebar__checkbox">
                        <input type="checkbox" name="skills[]" value="<?= (int) $skill['id'] ?>"
                               <?= in_array((int) $skill['id'], $formData['skills'], true) ? 'checked' : '' ?>>
                        <?= e($skill['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php if (isset($formErrors['skills'])): ?>
                <p class="field-error"><?= e($formErrors['skills']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group-row">
            <div class="form-group">
                <label for="publish-date">Fecha</label>
                <input type="date" id="publish-date" name="activity_date" value="<?= e($formData['activity_date']) ?>">
                <?php if (isset($formErrors['activity_date'])): ?>
                    <p class="field-error"><?= e($formErrors['activity_date']) ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="publish-time">Horario</label>
                <input type="text" id="publish-time" name="time" maxlength="50"
                       value="<?= e($formData['time']) ?>" placeholder="Ej. 7:00 a. m. – 12:00 m.">
            </div>
        </div>

        <div class="form-group">
            <label for="publish-location">Ubicación</label>
            <input type="text" id="publish-location" name="location" maxlength="150"
                   value="<?= e($formData['location']) ?>" placeholder="Ej. San Carlos, Alajuela">
            <?php if (isset($formErrors['location'])): ?>
                <p class="field-error"><?= e($formErrors['location']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-card__actions">
            <button type="submit" name="submit_action" value="publish" class="btn btn--primary" style="flex:1"><?= e($submitLabel) ?></button>
            <button type="submit" name="submit_action" value="draft" class="btn btn--secondary">Guardar borrador</button>
        </div>
    </form>
</section>
</body>
</html>
