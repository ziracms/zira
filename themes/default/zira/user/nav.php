<div id="secondary-menu-wrapper">
<ul class="nav nav-pills nav-stacked">
<?php foreach (Zira\User::getProfileEditLinks() as $link): ?>
<?php if (array_key_exists('type', $link) && $link['type']=='separator'): ?>
<li role="separator" class="divider"></li>
<?php else: ?>
<?php $icon = !empty($link['icon']) ? '<span class="'.Zira\Helper::html($link['icon']).'"></span>' : ''; ?>
<?php if (Zira\Router::getRequest() == $link['url']) $class = ' class="active"'; else $class = ''; ?>
<li<?php echo $class ?>><a href="<?php echo Zira\Helper::html(Zira\Helper::url($link['url'])) ?>"><?php echo $icon; ?> <?php echo Zira\Helper::html($link['title']) ?></a></li>
<?php endif; ?>
<?php endforeach; ?>
</ul>
</div>