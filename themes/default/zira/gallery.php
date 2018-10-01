<?php if (!empty($images)): ?>
<div class="gallery-wrapper">
<!--<h2><?php echo t('Gallery') ?></h2>-->
<ul class="gallery">
<?php foreach($images as $_image): ?>
<li><a data-lightbox="record-<?php echo Zira\Helper::html($_image->record_id) ?>" href="<?php echo Zira\Helper::urlencode(Zira\Helper::baseUrl($_image->image)) ?>" title="<?php echo Zira\Helper::html($_image->description) ?>"><img src="<?php echo Zira\Helper::html(Zira\Helper::baseUrl($_image->thumb)) ?>" alt="<?php echo Zira\Helper::html($_image->description) ?>" width="<?php echo Zira\Config::get('gallery_thumbs_width', Zira\Config::get('thumbs_width')) ?>" height="<?php echo Zira\Config::get('gallery_thumbs_height', Zira\Config::get('thumbs_height')) ?>" /></a></li>
<?php endforeach; ?>
</ul>
</div>
<?php if (!empty($limit) && !empty($count) && !empty($record_id) && $count>$limit): ?>
<div class="gallery-view-more-wrapper">
<button class="btn btn-primary gallery-view-more" type="button" data-url="<?php echo Zira\Helper::url('zira/records/images') ?>" data-record="<?php echo $record_id ?>" data-pages="<?php echo $pages = ceil($count / $limit); ?>"><?php echo t('View more') ?>&nbsp;&rsaquo;&rsaquo;</button>
</div>
<?php endif; ?>
<?php endif; ?>

<?php if (empty($images) && empty($access_allowed)): ?>
<div class="alert alert-warning" role="alert">
    <?php if (!Zira\User::isAuthorized()): ?>
    <?php echo t('%s to view gallery', '<a class="inline-login-link" href="'.Zira\Helper::url('user/login?redirect='.Zira\Page::getRedirectUrl()).'">'.t('Login').'</a>') ?>
    <?php else: ?>
    <?php echo t('You do not have permission to view gallery'); ?>
    <?php endif; ?>
</div>
<?php endif; ?>
