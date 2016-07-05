<?php if (empty($found)): ?>
<div class="page-header">
<h1><?php echo tm('Search forum', 'forum') ?></h1>
</div>
<?php endif; ?>
<div class="search-form-wrapper<?php if (empty($found)) echo ' no-results'; ?>">
<?php if (isset($form)) echo $form; ?>
</div>
<?php if (empty($found)): ?>
<p class="search-form-description"><?php echo tm('Enter search text','forum') ?></p>
<?php endif; ?>