<?php if (!empty($images)): ?>
<div class="block carousel-wrapper">
<?php if (!empty($title)): ?>
<h2><?php echo Zira\Helper::html($title) ?></h2>
<?php endif; ?>
<ul class="carousel<?php if (!empty($sidebar)) echo ' carousel-small'; ?>">
<?php foreach($images as $index=>$image): ?>
<li>
<?php if (!empty($links[$index])): ?>
<a href="<?php echo Zira\Helper::html(Zira\Menu::parseURL($links[$index])); ?>" title="<?php echo !empty($descriptions[$index]) ? Zira\Helper::html($descriptions[$index]) : '' ?>">
<?php endif; ?>
<img class="image<?php if (empty($links[$index])) echo ' zira-lightbox'; ?>" src="<?php echo Zira\Helper::baseUrl(Zira\Image::getCustomThumbUrl($image, Zira\Config::get('carousel_thumbs_width', Zira\Config::get('thumbs_width')), Zira\Config::get('carousel_thumbs_height', Zira\Config::get('thumbs_height')))) ?>" title="<?php echo !empty($descriptions[$index]) ? Zira\Helper::html($descriptions[$index]) : '' ?>" alt="<?php echo !empty($descriptions[$index]) ? Zira\Helper::html($descriptions[$index]) : '' ?>" />
<?php if (!empty($links[$index])): ?>
</a>
<?php endif; ?>
</li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>
