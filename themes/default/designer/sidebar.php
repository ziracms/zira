<!--child menu-->
<div id="secondary-menu-wrapper">
<nav>
<ul class="nav nav-pills nav-stacked">
<li class="menu-item"><a href="<?php echo Zira\Helper::url('') ?>" class="menu-link">Lorem ipsum</a></li>
<li class="menu-item active"><a href="<?php echo Zira\Helper::url('') ?>" class="menu-link">Lorem ipsum</a></li>
<li class="menu-item"><a href="<?php echo Zira\Helper::url('') ?>" class="menu-link">Lorem ipsum</a></li>
</ul>
</nav>
</div>
<!--category widget-->
<div class="block">
<div class="page-header">
<h2 class="widget-title">Lorem ipsum</h2>
</div>
<ul class="widget-list list">
<li class="list-item with-thumb">
<h3 class="list-title-wrapper">
<a class="list-title" href="<?php echo Zira\Helper::url('') ?>">Lorem ipsum</a>
</h3>
<div class="list-content-wrapper">
<a class="list-thumb" href="<?php echo Zira\Helper::url('') ?>">
<img src="<?php echo Zira\Helper::baseUrl('assets/images/designer/thumb.jpg') ?>" width="<?php echo Zira\Config::get('thumbs_width') ?>" height="<?php echo Zira\Config::get('thumbs_height') ?>">
</a>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam tempus nec eros in auctor.</p>
</div>
<div class="list-info-wrapper">
<span class="list-info comments-count"><span class="glyphicon glyphicon-comment"></span> 5</span>
</div>
</li>
<li class="list-item with-thumb">
<h3 class="list-title-wrapper">
<a class="list-title" href="<?php echo Zira\Helper::url('') ?>">Lorem ipsum</a>
</h3>
<div class="list-content-wrapper">
<a class="list-thumb" href="<?php echo Zira\Helper::url('') ?>">
<img src="<?php echo Zira\Helper::baseUrl('assets/images/designer/thumb.jpg') ?>" width="<?php echo Zira\Config::get('thumbs_width') ?>" height="<?php echo Zira\Config::get('thumbs_height') ?>">
</a>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam tempus nec eros in auctor.</p>
</div>
<div class="list-info-wrapper">
<span class="list-info comments-count"><span class="glyphicon glyphicon-comment"></span> 5</span>
</div>
</li>
</ul>
</div>
<!--calendar widget-->
<?php echo (new Zira\Widgets\Calendar())->render(); ?>