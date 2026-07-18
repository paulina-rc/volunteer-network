<?php
/**
 * Site header partial.
 *
 * Expected variables (set by the including view before requiring this file):
 * - $headerVariant: 'public' (default, full nav) or 'minimal' (logo + back link)
 * - $activeNav: 'home' | 'opportunities' | 'organizations' | null — highlights the current nav item ('public' variant)
 * - $showSearchIcon: bool — shows the search icon button ('public' variant, defaults to false)
 * - $backLinkHref / $backLinkLabel: used by the 'minimal' variant
 *
 * Logged-in state (volunteer/organization chip, logout link, "Publicar oportunidad" CTA)
 * is derived directly from $_SESSION, since login/logout (Step 2) are already implemented.
 */

$headerVariant = $headerVariant ?? 'public';
$activeNav = $activeNav ?? null;
$showSearchIcon = $showSearchIcon ?? false;

$loggedInRole = $_SESSION['role'] ?? null;
$userChipLabel = '';
$userChipInitials = '';
$userProfileHref = '';

if ($loggedInRole === ROLE_VOLUNTEER) {
    $volunteer = (new VolunteerModel())->findByUserId((int) $_SESSION['user_id']);
    $userChipLabel = $volunteer['full_name'] ?? 'Voluntario';
    $userProfileHref = BASE_URL . '?action=view_volunteer_profile';
} elseif ($loggedInRole === ROLE_ORGANIZATION) {
    $organization = (new OrganizationModel())->findByUserId((int) $_SESSION['user_id']);
    $userChipLabel = $organization['name'] ?? 'Organización';
    $userProfileHref = BASE_URL . '?action=view_organization_profile';
}

if ($userChipLabel !== '') {
    $nameParts = preg_split('/\s+/', trim($userChipLabel));
    $userChipInitials = strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[count($nameParts) > 1 ? 1 : 0], 0, 1));
}
?>
<header class="site-header">
  <div class="site-header__bar">
    <a href="<?= BASE_URL ?>" class="logo-enlaza">Enlaza</a>

    <?php if ($headerVariant === 'minimal'): ?>
      <a href="<?= htmlspecialchars($backLinkHref ?? BASE_URL) ?>" class="site-header__back-link">
        <i class="fa-solid fa-arrow-left"></i> <?= htmlspecialchars($backLinkLabel ?? 'Volver al inicio') ?>
      </a>
    <?php else: ?>
      <nav class="site-header__nav">
        <a href="<?= BASE_URL ?>" class="site-header__nav-link<?= $activeNav === 'home' ? ' site-header__nav-link--active' : '' ?>">Inicio</a>
        <a href="<?= BASE_URL ?>?action=search_opportunities" class="site-header__nav-link<?= $activeNav === 'opportunities' ? ' site-header__nav-link--active' : '' ?>">Oportunidades</a>
        <a href="<?= BASE_URL ?>?action=view_organization_profile" class="site-header__nav-link<?= $activeNav === 'organizations' ? ' site-header__nav-link--active' : '' ?>">Organizaciones</a>
      </nav>

      <div class="site-header__actions">
        <?php if ($showSearchIcon): ?>
          <a href="<?= BASE_URL ?>?action=search_opportunities" title="Buscar" class="site-header__icon-btn">
            <i class="fa-solid fa-magnifying-glass"></i>
          </a>
        <?php endif; ?>

        <?php if ($loggedInRole === ROLE_ORGANIZATION): ?>
          <a href="<?= BASE_URL ?>?action=publish_opportunity" class="btn btn--primary">Publicar oportunidad</a>
        <?php endif; ?>

        <?php if ($loggedInRole !== null): ?>
          <a href="<?= htmlspecialchars($userProfileHref) ?>" class="site-header__user">
            <span class="site-header__user-avatar"><?= htmlspecialchars($userChipInitials) ?></span>
            <span class="site-header__user-name"><?= htmlspecialchars($userChipLabel) ?></span>
          </a>
          <a href="<?= BASE_URL ?>?action=logout" title="Cerrar sesión" class="site-header__icon-btn">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
          </a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>?action=register&tab=login" class="btn btn--secondary">Iniciar sesión</a>
          <a href="<?= BASE_URL ?>?action=register&tab=register" class="btn btn--primary">Registrarme</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</header>
