<?php if (!empty($tags)): ?>
<div class="tags-wrapper"><ul class="tags-list">
<li><span class="glyphicon glyphicon-tags"></span> <?php echo t('Tags').': '; ?></li>
<?php foreach($tags as $tag): ?>
<li><a href="<?php echo Zira\Helper::url('tags?text='.Zira\Helper::urlencode($tag)); ?>" class="tag-link"><?php echo Zira\Helper::html($tag); ?></a></li>
<?php endforeach; ?>
</ul></div>
<?php endif; ?>