<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión o crear cuenta · Enlaza</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/estilos-generales.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="contenedor-auth">
        <div class="logo-enlaza" style="text-align:center;margin-bottom:6px">Enlaza</div>
        <p style="text-align:center;color:#8a8a85;font-size:13px;margin-bottom:28px">Conecta. Participa. Transforma.</p>

        <div class="card-auth">
            <div class="tabs" role="tablist">
                <button
                    type="button"
                    class="tabs__boton <?= $pestanaActiva === 'iniciar-sesion' ? 'tabs__boton--activo' : '' ?>"
                    data-tab="iniciar-sesion"
                    role="tab"
                >Iniciar sesión</button>
                <button
                    type="button"
                    class="tabs__boton <?= $pestanaActiva === 'crear-cuenta' ? 'tabs__boton--activo' : '' ?>"
                    data-tab="crear-cuenta"
                    role="tab"
                >Crear cuenta</button>
            </div>

            <div class="tabs__panel <?= $pestanaActiva === 'iniciar-sesion' ? 'tabs__panel--activo' : '' ?>" data-panel="iniciar-sesion">
                <?php if ($mensajeExito !== ''): ?>
                    <p class="mensaje-exito"><?= htmlspecialchars($mensajeExito) ?></p>
                <?php endif; ?>

                <?php if (!empty($erroresLogin)): ?>
                    <ul class="lista-errores">
                        <?php foreach ($erroresLogin as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <form method="post" action="<?= BASE_URL ?>?accion=iniciar_sesion" novalidate>
                    <div class="form-grupo">
                        <label for="login-correo">Correo electrónico</label>
                        <input type="email" id="login-correo" name="correo" value="<?= htmlspecialchars($correoLogin) ?>" required maxlength="150">
                    </div>
                    <div class="form-grupo">
                        <label for="login-contrasena">Contraseña</label>
                        <input type="password" id="login-contrasena" name="contrasena" required>
                    </div>
                    <button type="submit" class="btn btn--primario btn--ancho">Iniciar sesión</button>
                </form>
            </div>

            <div class="tabs__panel <?= $pestanaActiva === 'crear-cuenta' ? 'tabs__panel--activo' : '' ?>" data-panel="crear-cuenta">
                <?php if (!empty($erroresRegistro)): ?>
                    <ul class="lista-errores">
                        <?php foreach ($erroresRegistro as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <form method="post" action="<?= BASE_URL ?>?accion=registrar_usuario" novalidate id="form-registro">
                    <div class="selector-rol">
                        <label class="selector-rol__opcion">
                            <input type="radio" name="rol" value="voluntario" <?= ($datosRegistro['rol'] ?? 'voluntario') === 'voluntario' ? 'checked' : '' ?>>
                            <span><i class="fa-solid fa-hand-holding-heart"></i> Voluntario</span>
                        </label>
                        <label class="selector-rol__opcion">
                            <input type="radio" name="rol" value="organizacion" <?= ($datosRegistro['rol'] ?? '') === 'organizacion' ? 'checked' : '' ?>>
                            <span><i class="fa-solid fa-building"></i> Organización</span>
                        </label>
                    </div>

                    <div class="form-grupo" data-campo-rol="voluntario">
                        <label for="registro-nombre-completo">Nombre completo</label>
                        <input type="text" id="registro-nombre-completo" name="nombre_completo" value="<?= htmlspecialchars($datosRegistro['nombre_completo'] ?? '') ?>" maxlength="150">
                    </div>
                    <div class="form-grupo" data-campo-rol="organizacion">
                        <label for="registro-nombre-organizacion">Nombre de la organización</label>
                        <input type="text" id="registro-nombre-organizacion" name="nombre_organizacion" value="<?= htmlspecialchars($datosRegistro['nombre_organizacion'] ?? '') ?>" maxlength="150">
                    </div>

                    <div class="form-grupo">
                        <label for="registro-correo">Correo electrónico</label>
                        <input type="email" id="registro-correo" name="correo" value="<?= htmlspecialchars($datosRegistro['correo'] ?? '') ?>" required maxlength="150">
                    </div>
                    <div class="form-grupo">
                        <label for="registro-contrasena">Contraseña</label>
                        <input type="password" id="registro-contrasena" name="contrasena" required minlength="8">
                    </div>
                    <div class="form-grupo">
                        <label for="registro-confirmar-contrasena">Confirmar contraseña</label>
                        <input type="password" id="registro-confirmar-contrasena" name="confirmar_contrasena" required minlength="8">
                    </div>

                    <button type="submit" class="btn btn--primario btn--ancho">Crear cuenta</button>
                </form>
            </div>
        </div>
    </div>

    <script src="<?= BASE_URL ?>assets/js/registro.js"></script>
</body>
</html>
