<div <?php echo empty($custom_id) ? 'id="secondary-menu-wrapper"' : 'class="secondary-custom-menu-wrapper"'; ?>>
<nav>
<ul class="nav nav-pills nav-stacked">
<?php foreach($items as $item): ?>
<?php $class = array('menu-item'); $class_a = 'menu-link'; ?>
<?php if ($item->active && count($item->dropdown)==0) $class []= 'active'; ?>
<?php if (count($item->dropdown)>0) $class []= 'parent'; ?>
<?php if ($item->class) $class_a .= ' '.Zira\Helper::html($item->class); ?>
<?php if (!$item->dropdown): ?>
<li<?php if (!empty($class)) echo ' class="'.implode(' ',$class).'"'; ?>><a href="<?php echo Zira\Helper::html(Zira\Menu::parseURL($item->url)) ?>"<?php if ($item->external) echo ' target="_blank"'; ?> title="<?php echo Zira\Helper::html(t($item->title)) ?>" class="<?php echo $class_a ?>"><?php echo Zira\Helper::html(t($item->title)) ?></a></li>
<?php else: ?>
<?php $class []= 'secondary-child'; ?>
<li<?php echo ' class="'.implode(' ',$class).'"'; ?>>
<a href="<?php echo Zira\Helper::html(Zira\Menu::parseURL($item->url)) ?>"<?php if ($item->external) echo ' target="_blank"'; ?> title="<?php echo Zira\Helper::html(t($item->title)) ?>" class="<?php echo $class_a ?>"><?php echo Zira\Helper::html(t($item->title)) ?> <span class="caret"></span></a>
<ul class="nav nav-pills nav-stacked">
<?php foreach($item->dropdown as $_item): ?>
<?php $_class_li = ''; $_class_a = ''; ?>
<?php if ($_item->active) $_class_li = 'active'; ?>
<?php if ($_item->class) $_class_a = Zira\Helper::html($_item->class); ?>
<li<?php if (!empty($_class_li)) echo ' class="'.$_class_li.'"'; ?>><a href="<?php echo Zira\Helper::html(Zira\Menu::parseURL($_item->url)) ?>"<?php if ($_item->external) echo ' target="_blank"'; ?> title="<?php echo Zira\Helper::html(t($_item->title)) ?>"<?php if (!empty($_class_a)) echo ' class="'.$_class_a.'"'; ?>><span class="menu-link-ico"></span> <?php echo Zira\Helper::html(t($_item->title)) ?></a></li>
<?php endforeach; ?>
</ul>
</li>
<?php endif; ?>
<?php endforeach; ?>
</ul>
</nav>
</div>