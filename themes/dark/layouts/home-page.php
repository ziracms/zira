<!DOCTYPE html>
<html itemscope itemtype="http://schema.org/WebPage" lang="<?php echo Zira\Locale::getLanguage() ?>">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo Zira\Helper::baseUrl('favicon.ico') ?>" type="image/x-icon"/>
<link href="<?php echo Zira\Helper::url('rss', true, true) ?>" title="RSS" type="application/rss+xml" rel="alternate" />
<?php layout_head() ?>
</head>
<body class="home-layout">
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
<div id="content" class="col-sm-8">
<?php layout_content_top() ?>
<?php layout_content() ?>
<?php layout_content_bottom() ?>
</div>
<div class="col-sm-4 sidebar">
<aside>
<?php layout_sidebar_right() ?>
</aside>
</div>
</div>
</div>
<footer>
<div class="container">
<div class="row">
<?php layout_footer() ?>
<a href="<?php echo Zira\Helper::url('rss'); ?>" target="_blank" class="rss-link" title="<?php echo t('RSS') ?>"></a>
</div>
</div>
</footer>
</div></div><!--/main-container-wrapper-->
<?php layout_body_bottom() ?>
<a href="javascript:void(0)" class="scroll-top" title="<?php echo t('Up') ?>"></a>
</body>
</html>