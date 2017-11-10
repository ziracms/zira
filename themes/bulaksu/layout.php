<!DOCTYPE html>
<html itemscope itemtype="http://schema.org/WebPage" lang="<?php echo Zira\Locale::getLanguage() ?>">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo Zira\Helper::baseUrl('favicon.ico') ?>" type="image/x-icon"/>
<link href="<?php echo Zira\Helper::url('rss', true, true) ?>" title="RSS" type="application/rss+xml" rel="alternate" />
<?php layout_head() ?>
</head>
<body class="<?php echo Zira\Helper::html(Zira\View::$body_class); ?>">
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
<div class="row">
<footer>
<?php layout_footer() ?>
<a href="<?php echo Zira\Helper::url('rss'); ?>" target="_blank" class="rss-link" title="<?php echo t('RSS') ?>"></a>
</footer>
</div>
</div>
</div></div><!--/main-container-wrapper-->
<a href="javascript:void(0)" class="scroll-top" title="<?php echo t('Up') ?>"><span class="glyphicon glyphicon-circle-arrow-up"></span></a>
<?php layout_body_bottom() ?>
</body>
</html>