<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo Zira\Helper::baseUrl('favicon.ico') ?>" type="image/x-icon"/>
<?php layout_head() ?>
<style>
body {
background-color: #edfff4;
}
header {
min-height: 160px;
background-color: #2582af;
border-color: #30b1ce;
}
ul#language-switcher li a.active {
background-color: #30b1ce;
}
#content .page-header {
border-bottom: 1px solid #bffbbb;
}
#content .page-header h1 {
font-size: 28px;
margin-bottom: 20px;
color: #334c6c;
text-transform: uppercase;
}
#content h2 {
font-size: 22px;
color: #6f4960;
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