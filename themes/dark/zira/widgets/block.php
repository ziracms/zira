<div class="block">
<?php if (isset($title)): ?>
<div class="page-header">
<h3 class="widget-title"><?php echo Zira\Helper::html($title) ?></h3>
</div>
<?php endif; ?>
<?php if (isset($content)) echo '<div class="block-content">'.$content.'</div>' ?>
</div>