<div <?php echo empty($custom_id) ? 'id="top-menu-wrapper"' : 'class="top-custom-menu-wrapper"'; ?>>
<nav class="navbar navbar-default">
<div class="container-fluid">
<div class="navbar-header">
<?php // mobile logo
if (empty($custom_id) && isset($site_logo) && isset($site_name)) {
echo '<div class="top-menu-logo top-menu-header-logo"><a href="'.Zira\Helper::url('').'" title="'.Zira\Helper::html($site_name).'">';
if (!empty($site_logo)) echo '<img src="'.Zira\Helper::html(Zira\Helper::baseUrl($site_logo)).'" alt="'.Zira\Helper::html($site_name).'" />';
if (!empty($site_name)) echo '<span>'.Zira\Helper::html($site_name).'</span>';
echo '</a></div>';
}
?>
<?php if (empty($custom_id)): ?>
<button type="button" class="mobile-search-button navbar-toggle collapsed" data-toggle="collapse" data-target=".mobile-search-wrapper" aria-expanded="false">
<span class="glyphicon glyphicon-search"></span>
</button>
<?php endif; ?>
<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#<?php echo empty($custom_id) ? 'top-menu-container' : 'top-custom-menu-container-'.$custom_id; ?>" aria-expanded="false">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<?php if (empty($custom_id) && isset($mobileSearch)) echo '<div class="mobile-search-wrapper collapse">'.$mobileSearch.'</div>'; ?>
</div>
<div class="collapse navbar-collapse" <?php echo empty($custom_id) ? 'id="top-menu-container"' : 'id="top-custom-menu-container-'.$custom_id.'" class="top-custom-menu-container"'; ?>>
<?php if (empty($custom_id) && isset($site_logo) && isset($site_name)): ?>
<div class="top-menu-logo">
<?php // logo
echo '<a href="'.Zira\Helper::url('').'" title="'.Zira\Helper::html($site_name).'">';
if (!empty($site_logo)) echo '<img src="'.Zira\Helper::html(Zira\Helper::baseUrl($site_logo)).'" alt="'.Zira\Helper::html($site_name).'" />';
if (!empty($site_name)) echo '<span>'.Zira\Helper::html($site_name).'</span>';
echo '</a>';
?>
</div>
<?php endif; ?>
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
<a href="<?php echo Zira\Helper::html(Zira\Menu::parseURL($item->url)) ?>"<?php if ($item->external) echo ' target="_blank"'; ?> title="<?php echo Zira\Helper::html(t($item->title)) ?>" class="dropdown-toggle menu-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo Zira\Helper::html(t($item->title)) ?> <span class="glyphicon glyphicon-menu-down arrow-down"></span></a>
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

</div>
</div>
</nav>
<?php if (isset($search)): ?>
<div class="search-simple-form-container">
<?php echo $search.''; ?>
</div>
<span class="search-icon">
<i class="search-icon-overlay">&nbsp;</i>
<span class="glyphicon glyphicon-search"></span>
</span>
<span class="search-close-icon">
<span class="glyphicon glyphicon-remove-circle"></span>
</span>
<?php endif; ?>
</div>