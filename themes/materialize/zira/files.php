<?php if (!empty($files)): ?>
<div class="files-wrapper">
<ul class="files">
<?php foreach($files as $file): ?>
    <?php 
    if (!empty($file->description)) {
        $description = Zira\Helper::html($file->description).'&nbsp;&nbsp;&nbsp;';
    } else {
        $description = '';
    }
    if (!empty($file->path)) {
        $url = Zira\Config::get('hide_file_path') ? $file->id : $file->path;
        $url = Zira\Helper::fileUrl($url);
        $filename = Zira\Helper::basename($file->path);
        $real_path = ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file->path);
        if (file_exists($real_path)) $size = filesize($real_path);
        else $size = '';
        if (is_numeric($size)) {
            $size_suffix = 'B';
            if ($size > 1024) {
                $size = $size / 1024;
                $size_suffix = 'kB';
            }
            if ($size > 1024) {
                $size = $size / 1024;
                $size_suffix = 'MB';
            }
            $size = '&nbsp;&nbsp;&nbsp;('.number_format($size, 2).$size_suffix.')';
        }
    } else {
        $url = $file->url;
        $filename = Zira\Helper::basename($file->url);
        $size = '';
    }
    ?>
    <li><?php echo $description ?><a href="<?php echo Zira\Helper::urlencode($url) ?>" title="<?php echo Zira\Helper::html($file->description) ?>" target="_blank" download="<?php echo Zira\Helper::html($filename) ?>"><?php echo Zira\Helper::html($filename) ?></a><?php echo $size; ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<?php if (empty($files) && empty($access_allowed)): ?>
<div class="alert alert-warning" role="alert">
    <?php if (!Zira\User::isAuthorized()): ?>
    <?php echo t('%s to download files', '<a class="inline-login-link" href="'.Zira\Helper::url('user/login?redirect='.Zira\Page::getRedirectUrl()).'">'.t('Login').'</a>') ?>
    <?php else: ?>
    <?php echo t('You do not have permission to download files'); ?>
    <?php endif; ?>
</div>
<?php endif; ?>
