<div id="footer-menu-wrapper">
<nav>
<ul class="menu">
<?php foreach($items as $index=>$item): ?>
<?php if ($index>0): ?>
<li class="menu-item-separator"></li>
<?php endif; ?>
<?php $class = array('menu-item'); $class_a = 'menu-link'; ?>
<?php if ($item->active) $class []= 'active'; ?>
<?php if ($item->class) $class_a .= ' '.Zira\Helper::html($item->class); ?>
<?php if (!$item->dropdown): ?>
<li<?php if (!empty($class)) echo ' class="'.implode(' ',$class).'"'; ?>><a href="<?php echo Zira\Helper::html(Zira\Menu::parseURL($item->url)) ?>"<?php if ($item->external) echo ' target="_blank"'; ?> title="<?php echo Zira\Helper::html(t($item->title)) ?>" class="<?php echo $class_a ?>"><?php echo Zira\Helper::html(t($item->title)) ?></a></li>
<?php else: ?>
<?php $class []= 'dropup'; ?>
<li<?php echo ' class="'.implode(' ',$class).'"'; ?>>
<a href="<?php echo Zira\Helper::html(Zira\Menu::parseURL($item->url)) ?>"<?php if ($item->external) echo ' target="_blank"'; ?> title="<?php echo Zira\Helper::html(t($item->title)) ?>" class="<?php echo $class_a ?> dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo Zira\Helper::html(t($item->title)) ?> <span class="caret"></span></a>
<ul class="dropdown-menu">
<?php foreach($item->dropdown as $_item): ?>
<?php $_class = array(); ?>
<?php if ($_item->class) $_class []= Zira\Helper::html($_item->class); ?>
<li<?php if (!empty($_class)) echo ' class="'.implode(' ',$_class).'"'; ?>><a href="<?php echo Zira\Helper::html(Zira\Menu::parseURL($_item->url)) ?>"<?php if ($_item->external) echo ' target="_blank"'; ?> title="<?php echo Zira\Helper::html(t($_item->title)) ?>"><?php echo Zira\Helper::html(t($_item->title)) ?></a></li>
<?php endforeach; ?>
</ul>
</li>
<?php endif; ?>
<?php endforeach; ?>
</ul>
</nav>
</div>