<?php
/**
 * Renderiza una pantalla simple indicando que esa funcionalidad
 * se implementa en un paso posterior del plan de desarrollo
 * (sección 9 de la documentación técnica de Enlaza).
 */
function renderPendiente(string $titulo, string $pasoPlan): void
{
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= htmlspecialchars($titulo) ?> · Enlaza</title>
        <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/estilos-generales.css">
    </head>
    <body>
        <div class="contenedor-pendiente">
            <div class="logo-enlaza">Enlaza</div>
            <h1><?= htmlspecialchars($titulo) ?></h1>
            <p>Esta pantalla todavía no está implementada.</p>
            <p class="paso-plan"><?= htmlspecialchars($pasoPlan) ?></p>
            <a class="btn btn--primario" href="<?= BASE_URL ?>">&larr; Volver al inicio</a>
        </div>
    </body>
    </html>
    <?php
}
