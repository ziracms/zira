<?php if (!empty($files)): ?>
<div class="audio-wrapper">
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
    <div class="audio-wrapper-item">
    <?php if (!empty($description)): ?>
    <p><?php echo Zira\Helper::html($description) ?></p>
    <?php endif; ?>
    <?php if (!empty($file->embed)) echo $file->embed; ?>
    <?php if (!empty($url)): ?>
    <audio class="mediaelement" width="100%" style="max-width:100%" controls="controls"><source src="<?php echo Zira\Helper::html($url) ?>"></audio> 
    <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (empty($files) && empty($access_allowed)): ?>
<div class="alert alert-warning alert-dark" role="alert">
    <?php if (!Zira\User::isAuthorized()): ?>
    <?php echo t('%s to listen to audio', '<a href="'.Zira\Helper::url('user/login?redirect='.Zira\Page::getRedirectUrl()).'">'.t('Login').'</a>') ?>
    <?php else: ?>
    <?php echo t('You do not have permission to listen to audio'); ?>
    <?php endif; ?>
</div>
<?php endif; ?>
