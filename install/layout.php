<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo Zira\Helper::baseUrl('favicon.ico') ?>" type="image/x-icon"/>
<?php layout_head() ?>
<style>
header {
min-height: 160px;
}
</style>
</head>
<body>
<?php layout_body_top() ?>
<div id="main-container-wrapper"><div id="main-container">
<header>
<div class="container">
<div class="row">
<?php layout_header() ?>
</div>
</div>
</header>
<div class="container">
<div class="row">
<div class="col-sm-2 sidebar">
<aside>
<?php layout_sidebar_left() ?>
</aside>
</div>
<div id="content" class="col-sm-8">
<?php breadcrumbs(); ?>
<?php layout_content_top() ?>
<?php layout_content() ?>
<?php layout_content_bottom() ?>
</div>
<div class="col-sm-2 sidebar">
<aside>
<?php layout_sidebar_right() ?>
</aside>
</div>
</div>
</div>
<!--
<footer>
<div class="container">
<div class="row">
<?php //layout_footer() ?>
</div>
</div>
</footer>
-->
</div></div><!--/main-container-wrapper-->
<?php layout_body_bottom() ?>
</body>
</html>