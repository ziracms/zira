<?php if (empty($found)): ?>
<div class="page-header">
<h1><?php echo t('Search this site') ?></h1>
</div>
<?php endif; ?>
<div class="search-form-wrapper<?php if (empty($found)) echo ' no-results'; ?>">
<?php if (isset($form)) echo $form; ?>
</div>
<?php if (empty($found)): ?>
<p class="search-form-description"><?php echo t('Enter search text') ?></p>
<?php endif; ?>