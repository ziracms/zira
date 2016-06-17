<main>
<article>
<?php if (!empty($title)): ?>
<div class="page-header">
<h1><?php echo Zira\Helper::html($title) ?></h1>
</div>
<?php endif; ?>
<?php if (!empty($description)): ?>
<p class="description">
<?php echo nl2br(Zira\Helper::html($description)); ?>
</p>
<?php endif; ?>
<?php if (isset($content)): ?>
<div class="article<?php if (!empty($class)) echo ' '.$class ?>">
<?php echo $content; ?>
</div>
<?php endif; ?>
</article>
</main>