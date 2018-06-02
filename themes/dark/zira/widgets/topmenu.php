<div <?php echo empty($custom_id) ? 'id="top-menu-wrapper"' : 'class="top-custom-menu-wrapper"'; ?>>
<nav class="navbar navbar-default">
<div class="container-fluid">
<div class="navbar-header">
<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#<?php echo empty($custom_id) ? 'top-menu-container' : 'top-custom-menu-container-'.$custom_id; ?>" aria-expanded="false">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
</div>
<div class="collapse navbar-collapse" <?php echo empty($custom_id) ? 'id="top-menu-container"' : 'id="top-custom-menu-container-'.$custom_id.'" class="top-custom-menu-container"'; ?>>
<ul class="nav navbar-nav">
<?php foreach($items as $item): ?>
<?php $class = array('menu-item'); ?>
<?php if ($item->active) $class []= 'active'; ?>
<?php if ($item->class) $class []= Zira\Helper::html($item->class); ?>
<?php if (!$item->dropdown): ?>
<li<?php if (!empty($class)) echo ' class="'.implode(' ',$class).'"'; ?>><a href="<?php echo Zira\Helper::html(Zira\Menu::parseURL($item->url)) ?>"<?php if ($item->external) echo ' target="_blank"'; ?> title="<?php echo Zira\Helper::html(t($item->title)) ?>" class="menu-link"><?php echo Zira\Helper::html(t($item->title)) ?></a></li>
<?php else: ?>
<?php $class []= 'dropdown'; ?>
<li<?php echo ' class="'.implode(' ',$class).'"'; ?>>
<a href="<?php echo Zira\Helper::html(Zira\Menu::parseURL($item->url)) ?>"<?php if ($item->external) echo ' target="_blank"'; ?> title="<?php echo Zira\Helper::html(t($item->title)) ?>" class="dropdown-toggle menu-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo Zira\Helper::html(t($item->title)) ?> <span class="caret"></span></a>
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
<?php if (isset($search)) echo $search; ?>
</div>
</div>
</nav>
</div>