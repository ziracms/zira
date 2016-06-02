<div class="block">
<?php if (isset($title)): ?>
<h3><?php echo Zira\Helper::html($title) ?></h3>
<?php endif; ?>
<?php if (isset($content)) echo $content ?>
</div>