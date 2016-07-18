<div class="block">
<?php if (isset($title)): ?>
<h3 class="block-title-wrapper"><?php echo Zira\Helper::html($title) ?></h3>
<?php endif; ?>
<?php if (isset($content)) echo $content ?>
</div>