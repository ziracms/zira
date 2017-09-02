<?php if (!empty($files)): ?>
<?php if (!empty($poster)) $poster = ' poster="'.Zira\Helper::baseUrl(Zira\Helper::html($poster)).'"'; else $poster = ''; ?>
<div class="video-wrapper">
<?php foreach($files as $file): ?>
    <?php 
    if (!empty($file->description)) {
        $description = Zira\Helper::html($file->description);
    } else {
        $description = '';
    }
    if (!empty($file->path)) {
        $url = Zira\Helper::baseUrl($file->path);
    } else if (!empty($file->url)) {
        $url = $file->url;
    } else if (!empty($file->embed)) {
        $url = '';
    }
    ?>
    <div class="video-wrapper-item">
    <?php if (!empty($description)): ?>
    <p><?php echo Zira\Helper::html($description) ?></p>
    <?php endif; ?>
    <?php if (!empty($file->embed)) echo $file->embed; ?>
    <?php if (!empty($url)): ?>
    <video class="mediaelement" width="700" height="400" style="max-width:100%" controls="controls"<?php echo $poster ?>><source src="<?php echo Zira\Helper::html($url) ?>"></video> 
    <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (empty($files) && empty($access_allowed)): ?>
<div class="alert alert-warning" role="alert">
    <?php if (!Zira\User::isAuthorized()): ?>
    <?php echo t('%s to view video', '<a href="'.Zira\Helper::url('user/login?redirect='.Zira\Page::getRedirectUrl()).'">'.t('Login').'</a>') ?>
    <?php else: ?>
    <?php echo t('You do not have permission to view video'); ?>
    <?php endif; ?>
</div>
<?php endif; ?>
