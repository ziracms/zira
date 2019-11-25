<?php if (!empty($tags)): ?>
<div class="block tags-cloud-wrapper">
<div class="page-header">
<h2 class="widget-title"><?php echo t('Tags cloud') ?></h2>
</div>
<ul class="tags-cloud-list">
<?php foreach($tags as $tag=>$co): ?>
<?php $fontSize = 100; ?>
<?php if (!empty($max)) $fontSize = ($co - 1) * $fontSize / $max + $fontSize; ?>
<li><a href="<?php echo Zira\Helper::url('tags?text='.Zira\Helper::urlencode($tag)) ?>" style="font-size:<?php echo intval($fontSize) ?>%"><?php echo Zira\Helper::html($tag); ?></a></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>