<?php
/**
 * Shared 404 screen.
 *
 * Expected variables:
 * - $errorTitle: string
 * - $errorMessage: string
 */
$errorTitle = $errorTitle ?? 'Página no encontrada';
$errorMessage = $errorMessage ?? 'La página que buscás no existe.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($errorTitle) ?> · Enlaza</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/general-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>

<section class="narrow-page">
    <div class="empty-state">
        <i class="fa-solid fa-compass"></i>
        <h1 class="page-heading"><?= e($errorTitle) ?></h1>
        <p class="page-subtext"><?= e($errorMessage) ?></p>
        <div class="confirmation-panel__actions">
            <a href="<?= e(actionUrl('search_opportunities')) ?>" class="btn btn--primary">Ver todas las oportunidades</a>
            <a href="<?= BASE_URL ?>" class="btn btn--secondary">Volver al inicio</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
