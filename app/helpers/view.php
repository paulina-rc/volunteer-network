<?php
/**
 * Renders a simple screen indicating that this feature is implemented
 * in a later step of the development plan (section 9 of Enlaza's
 * technical documentation).
 */
function renderPending(string $title, string $planStep): void
{
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= htmlspecialchars($title) ?> · Enlaza</title>
        <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/general-styles.css">
    </head>
    <body>
        <div class="pending-container">
            <div class="logo-enlaza">Enlaza</div>
            <h1><?= htmlspecialchars($title) ?></h1>
            <p>Esta pantalla todavía no está implementada.</p>
            <p class="plan-step"><?= htmlspecialchars($planStep) ?></p>
            <a class="btn btn--primary" href="<?= BASE_URL ?>">&larr; Volver al inicio</a>
        </div>
    </body>
    </html>
    <?php
}
