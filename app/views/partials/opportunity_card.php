<?php
/**
 * Opportunity card, shared by the home page and the search results.
 *
 * Expected variables (set by the including view before requiring this file):
 * - $cardOpportunity: array — one row as returned by OpportunityModel
 * - $cardCompact: bool — adds the opportunity-card--compact modifier (search grid)
 *
 * The markup is the same one both screens already used; only the data source
 * changed. Whether the card offers enrollment comes from acceptsEnrollments(),
 * the single definition of RN04.
 */

$cardCompact = $cardCompact ?? false;
$cardIsOpen = acceptsEnrollments($cardOpportunity);
$cardSlots = (int) ($cardOpportunity['available_slots'] ?? 0);
?>
<div class="opportunity-card<?= $cardCompact ? ' opportunity-card--compact' : '' ?>"
     data-opportunity-card data-category="<?= e($cardOpportunity['category_name']) ?>">
    <div class="opportunity-card__photo">
        <img src="<?= BASE_URL ?>assets/img/placeholder.jpg"
             alt="foto: <?= e($cardOpportunity['title']) ?>">
        <?php if ($cardIsOpen): ?>
            <span class="opportunity-card__badge" style="background:#E9A227;color:#4a3106"><?= $cardSlots ?> cupos</span>
        <?php else: ?>
            <span class="opportunity-card__badge" style="background:#F1EEE6;color:#8a8a85"><?= e(statusLabel($cardOpportunity['status'])) ?></span>
        <?php endif; ?>
        <span class="opportunity-card__tag"><?= e($cardOpportunity['category_name']) ?></span>
    </div>
    <div class="opportunity-card__body">
        <h3 class="opportunity-card__title"><?= e($cardOpportunity['title']) ?></h3>
        <div class="opportunity-card__org"><?= e($cardOpportunity['organization_name']) ?></div>
        <div class="opportunity-card__meta">
            <span class="opportunity-card__meta-item"><i class="fa-regular fa-calendar"></i> <?= e(formatDate($cardOpportunity['activity_date'], false)) ?></span>
            <span class="opportunity-card__meta-item"><i class="fa-solid fa-location-dot"></i> <?= e($cardOpportunity['location']) ?></span>
        </div>
        <a href="<?= e(actionUrl('view_opportunity', ['id' => (int) $cardOpportunity['id']])) ?>" class="btn btn--secondary btn--full-width">Ver detalle</a>
    </div>
</div>
