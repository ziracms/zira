<?php if (!empty($images)): ?>
<div class="slider-wrapper">
<ul id="slider" class="slider">
<?php foreach($images as $_image): ?>
<li><img class="image zira-lightbox" src="<?php echo Zira\Helper::urlencode(Zira\Helper::baseUrl($_image->image)) ?>" title="<?php echo Zira\Helper::html($_image->description) ?>" alt="<?php echo Zira\Helper::html($_image->description) ?>" /></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>
