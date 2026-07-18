<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enlaza — Base del proyecto</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/general-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div style="max-width:900px;margin:0 auto;padding:40px 24px 80px;text-align:center">
        <div class="logo-enlaza" style="font-size:30px;margin-bottom:6px">Enlaza</div>
        <p style="color:#8a8a85;font-size:13px;margin-bottom:32px">La tecnología al servicio de la comunidad</p>

        <div class="status-banner">
            <i class="fa-solid fa-circle-check"></i>
            Conexión a la base de datos exitosa — <?= count($categories) ?> categorías cargadas desde MySQL
        </div>

        <h1 style="color:var(--color-primary);font-size:22px;margin-bottom:6px">Base del proyecto lista</h1>
        <p style="color:#5c5c58;font-size:14.5px;max-width:560px;margin:0 auto 36px">
            Esta pantalla confirma que el router, la conexión PDO, el esquema de base de datos
            y el primer modelo (<code>CategoryModel</code>) funcionan de punta a punta.
            Las pantallas del prototipo (Home, Búsqueda, Perfiles, etc.) se implementan
            en los siguientes pasos del plan de desarrollo.
        </p>

        <div class="categories-grid">
            <?php foreach ($categories as $category): ?>
                <div class="category-card">
                    <div class="icon" style="background:<?= htmlspecialchars($category['color_hex']) ?>">
                        <i class="fa-solid <?= htmlspecialchars($category['icon']) ?>"></i>
                    </div>
                    <div style="font-size:13.5px;font-weight:600;color:var(--color-primary)">
                        <?= htmlspecialchars($category['name']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <p style="margin-top:44px;font-size:12.5px;color:#a8a8a3">
            Próximo paso del plan: registro y login (Paso 2).
        </p>
    </div>
</body>
</html>
