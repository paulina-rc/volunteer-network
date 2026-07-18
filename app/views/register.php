<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión o crear cuenta · Enlaza</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/general-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="logo-enlaza" style="text-align:center;margin-bottom:6px">Enlaza</div>
        <p style="text-align:center;color:#8a8a85;font-size:13px;margin-bottom:28px">Conecta. Participa. Transforma.</p>

        <div class="auth-card">
            <div class="tabs" role="tablist">
                <button
                    type="button"
                    class="tabs__button <?= $activeTab === 'login' ? 'tabs__button--active' : '' ?>"
                    data-tab="login"
                    role="tab"
                >Iniciar sesión</button>
                <button
                    type="button"
                    class="tabs__button <?= $activeTab === 'register' ? 'tabs__button--active' : '' ?>"
                    data-tab="register"
                    role="tab"
                >Crear cuenta</button>
            </div>

            <div class="tabs__panel <?= $activeTab === 'login' ? 'tabs__panel--active' : '' ?>" data-panel="login">
                <?php if ($successMessage !== ''): ?>
                    <p class="success-message"><?= htmlspecialchars($successMessage) ?></p>
                <?php endif; ?>

                <?php if (!empty($loginErrors)): ?>
                    <ul class="error-list">
                        <?php foreach ($loginErrors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <form method="post" action="<?= BASE_URL ?>?action=login" novalidate>
                    <div class="form-group">
                        <label for="login-email">Correo electrónico</label>
                        <input type="email" id="login-email" name="email" value="<?= htmlspecialchars($loginEmail) ?>" required maxlength="150">
                    </div>
                    <div class="form-group">
                        <label for="login-password">Contraseña</label>
                        <input type="password" id="login-password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn--primary btn--full-width">Iniciar sesión</button>
                </form>
            </div>

            <div class="tabs__panel <?= $activeTab === 'register' ? 'tabs__panel--active' : '' ?>" data-panel="register">
                <?php if (!empty($registerErrors)): ?>
                    <ul class="error-list">
                        <?php foreach ($registerErrors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <form method="post" action="<?= BASE_URL ?>?action=register_user" novalidate id="form-register">
                    <div class="role-selector">
                        <label class="role-selector__option">
                            <input type="radio" name="role" value="volunteer" <?= ($registerData['role'] ?? 'volunteer') === 'volunteer' ? 'checked' : '' ?>>
                            <span><i class="fa-solid fa-hand-holding-heart"></i> Voluntario</span>
                        </label>
                        <label class="role-selector__option">
                            <input type="radio" name="role" value="organization" <?= ($registerData['role'] ?? '') === 'organization' ? 'checked' : '' ?>>
                            <span><i class="fa-solid fa-building"></i> Organización</span>
                        </label>
                    </div>

                    <div class="form-group" data-role-field="volunteer">
                        <label for="register-full-name">Nombre completo</label>
                        <input type="text" id="register-full-name" name="full_name" value="<?= htmlspecialchars($registerData['full_name'] ?? '') ?>" maxlength="150">
                    </div>
                    <div class="form-group" data-role-field="organization">
                        <label for="register-organization-name">Nombre de la organización</label>
                        <input type="text" id="register-organization-name" name="organization_name" value="<?= htmlspecialchars($registerData['organization_name'] ?? '') ?>" maxlength="150">
                    </div>

                    <div class="form-group">
                        <label for="register-email">Correo electrónico</label>
                        <input type="email" id="register-email" name="email" value="<?= htmlspecialchars($registerData['email'] ?? '') ?>" required maxlength="150">
                    </div>
                    <div class="form-group">
                        <label for="register-password">Contraseña</label>
                        <input type="password" id="register-password" name="password" required minlength="8">
                    </div>
                    <div class="form-group">
                        <label for="register-confirm-password">Confirmar contraseña</label>
                        <input type="password" id="register-confirm-password" name="confirm_password" required minlength="8">
                    </div>

                    <button type="submit" class="btn btn--primary btn--full-width">Crear cuenta</button>
                </form>
            </div>
        </div>
    </div>

    <script src="<?= BASE_URL ?>assets/js/register.js"></script>
</body>
</html>
