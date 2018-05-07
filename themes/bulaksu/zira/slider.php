<?php if (!empty($images)): ?>
<div class="slider-wrapper">
<ul id="slider" class="slider">
<?php foreach($images as $_image): ?>
<li>
<?php if ($_image->link): ?>
<a href="<?php echo Zira\Helper::html(Zira\Menu::parseURL($_image->link)); ?>" title="<?php echo Zira\Helper::html($_image->description) ?>">
<?php endif; ?>
<img class="image<?php if (!$_image->link) echo ' zira-lightbox'; ?>" src="<?php echo Zira\Helper::urlencode(Zira\Helper::baseUrl($_image->image)) ?>" title="<?php echo Zira\Helper::html($_image->description) ?>" alt="<?php echo Zira\Helper::html($_image->description) ?>" />
<?php if ($_image->link): ?>
</a>
<?php endif; ?>
</li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>
