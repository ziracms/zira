<?php if (!empty($images)): ?>
<div class="block carousel-wrapper">
<?php if (!empty($title)): ?>
<h2><?php echo Zira\Helper::html($title) ?></h2>
<?php endif; ?>
<?php $id = uniqid('carousel_'); ?>
<ul class="carousel<?php if (!empty($sidebar)) echo ' carousel-small'; ?>">
<?php foreach($images as $index=>$image): ?>
<li>
<a href="<?php echo !empty($links[$index]) ? Zira\Helper::html(Zira\Menu::parseURL($links[$index])) : Zira\Helper::baseUrl(zira\Helper::urlencode($image)); ?>" title="<?php echo !empty($descriptions[$index]) ? Zira\Helper::html($descriptions[$index]) : '' ?>"<?php if (empty($links[$index])) echo ' data-lightbox="'.Zira\Helper::html($id).'"'; ?>>
<img class="image" src="<?php echo Zira\Helper::baseUrl(Zira\Image::getCustomThumbUrl($image, Zira\Config::get('carousel_thumbs_width', Zira\Config::get('thumbs_width')), Zira\Config::get('carousel_thumbs_height', Zira\Config::get('thumbs_height')))) ?>" title="<?php echo !empty($descriptions[$index]) ? Zira\Helper::html($descriptions[$index]) : '' ?>" alt="<?php echo !empty($descriptions[$index]) ? Zira\Helper::html($descriptions[$index]) : '' ?>" />
</a>
</li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>
