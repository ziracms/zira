<?php if (!empty($images)): ?>
<div class="gallery-wrapper">
<h2><?php echo t('Gallery') ?></h2>
<ul class="gallery">
<?php foreach($images as $_image): ?>
<li><a data-lightbox="record-<?php echo Zira\Helper::html($_image->record_id) ?>" href="<?php echo Zira\Helper::html(Zira\Helper::baseUrl($_image->image)) ?>" title="<?php echo Zira\Helper::html($_image->description) ?>"><img src="<?php echo Zira\Helper::html(Zira\Helper::baseUrl($_image->thumb)) ?>" alt="<?php echo Zira\Helper::html($_image->description) ?>" width="<?php echo Zira\Config::get('thumbs_width') ?>" height="<?php echo Zira\Config::get('thumbs_height') ?>" /></a></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>