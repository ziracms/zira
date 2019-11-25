<div class="jumbotron error-page">
<h1><?php echo $code ?></h1>
<h3><?php echo $message ?></h3>
<?php if (isset($content)): ?>
<div><?php echo $content; ?></div>
<?php endif; ?>
</div>
<a href="https://ziracms.github.io" class="label label-info">Zira</a>
