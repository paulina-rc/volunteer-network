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
<?php $headerVariant = 'minimal'; $backLinkHref = BASE_URL; $backLinkLabel = 'Volver al inicio'; require __DIR__ . '/partials/header.php'; ?>

<section class="auth-split">
    <div class="auth-split__photo">
        <img src="<?= BASE_URL ?>assets/img/placeholder.jpg" alt="foto: grupo de voluntarios y organizadores conversando antes de iniciar una actividad, San Carlos">
        <div class="auth-split__photo-overlay"></div>
        <div class="auth-split__photo-content">
            <div class="auth-split__quote">"Conecta. Participa. Transforma."</div>
            <div class="auth-split__quote-note">Más de 800 voluntarios ya son parte de Enlaza.</div>
        </div>
    </div>

    <div class="auth-split__form-side">
        <div class="auth-split__form-wrap">
            <div class="segmented" role="tablist">
                <button type="button" class="segmented__option<?= $activeTab === 'login' ? ' segmented__option--active' : '' ?>" data-tab="login" role="tab">Iniciar sesión</button>
                <button type="button" class="segmented__option<?= $activeTab === 'register' ? ' segmented__option--active' : '' ?>" data-tab="register" role="tab">Crear cuenta</button>
            </div>

            <div class="tabs__panel<?= $activeTab === 'login' ? ' tabs__panel--active' : '' ?>" data-panel="login">
                <h1 class="auth-heading">Bienvenido de vuelta</h1>
                <p class="auth-subtext">Ingresá con tu correo para continuar en Enlaza.</p>

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
                        <input type="email" id="login-email" name="email" value="<?= htmlspecialchars($loginEmail) ?>" placeholder="vos@correo.com" required maxlength="150">
                    </div>
                    <div class="form-group">
                        <label for="login-password">Contraseña</label>
                        <input type="password" id="login-password" name="password" placeholder="••••••••" required>
                    </div>
                    <a href="#" class="auth-forgot-link">¿Olvidaste tu contraseña?</a>
                    <button type="submit" class="btn btn--primary btn--full-width">Iniciar sesión</button>
                </form>
            </div>

            <div class="tabs__panel<?= $activeTab === 'register' ? ' tabs__panel--active' : '' ?>" data-panel="register">
                <h1 class="auth-heading">Creá tu cuenta</h1>
                <p class="auth-subtext">Elegí el tipo de cuenta que querés crear.</p>

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
                            <i class="fa-solid fa-hand-holding-heart"></i>
                            <span>Soy voluntario</span>
                        </label>
                        <label class="role-selector__option">
                            <input type="radio" name="role" value="organization" <?= ($registerData['role'] ?? '') === 'organization' ? 'checked' : '' ?>>
                            <i class="fa-solid fa-people-roof"></i>
                            <span>Soy organización</span>
                        </label>
                    </div>

                    <div class="form-group" data-role-field="volunteer">
                        <label for="register-full-name">Nombre completo</label>
                        <input type="text" id="register-full-name" name="full_name" value="<?= htmlspecialchars($registerData['full_name'] ?? '') ?>" placeholder="Ana Rodríguez" maxlength="150">
                    </div>
                    <div class="form-group" data-role-field="organization">
                        <label for="register-organization-name">Nombre de la organización</label>
                        <input type="text" id="register-organization-name" name="organization_name" value="<?= htmlspecialchars($registerData['organization_name'] ?? '') ?>" placeholder="Fundación Verde Norte" maxlength="150">
                    </div>

                    <div class="form-group">
                        <label for="register-email">Correo electrónico</label>
                        <input type="email" id="register-email" name="email" value="<?= htmlspecialchars($registerData['email'] ?? '') ?>" placeholder="vos@correo.com" required maxlength="150">
                    </div>
                    <div class="form-group">
                        <label for="register-password">Contraseña</label>
                        <input type="password" id="register-password" name="password" placeholder="Mínimo 8 caracteres" required minlength="8">
                    </div>
                    <div class="form-group">
                        <label for="register-confirm-password">Confirmar contraseña</label>
                        <input type="password" id="register-confirm-password" name="confirm_password" placeholder="Mínimo 8 caracteres" required minlength="8">
                    </div>

                    <button type="submit" class="btn btn--primary btn--full-width">Crear cuenta</button>
                </form>
            </div>

            <p class="auth-legal">Al continuar aceptás los <a href="#">Términos de uso</a> y la <a href="#">Política de privacidad</a> de Enlaza.</p>
        </div>
    </div>
</section>

<script src="<?= BASE_URL ?>assets/js/register.js"></script>
</body>
</html>
