<!DOCTYPE html>
<html class="dashboard">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="<?php echo Zira\Helper::baseUrl('favicon.ico') ?>" type="image/x-icon"/>
<?php layout_head() ?>
</head>
<body class="dashboard">
<?php Dash\Dash::getInstance()->renderPanel(); ?>
<div id="main-wrapper">
<?php layout_content() ?>
</div><!--/main-wrapper-->
<?php echo Zira\View::getBodyBottomScripts(); ?>
</body>
</html>