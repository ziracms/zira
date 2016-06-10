<main>
<article class="user-profile">
<div class="user-head">
<div class="user-profile-photo">
<?php if (!empty($thumb) && !empty($photo)): ?>
<a href="<?php echo Zira\Helper::html($photo) ?>" data-lightbox="user-photo" data-title="<?php echo (isset($title) ? Zira\Helper::html($title) : '') ?>">
<img src="<?php echo Zira\Helper::html($thumb) ?>" width="<?php echo Zira\Config::get('user_thumb_width') ?>" height="<?php echo Zira\Config::get('user_thumb_height') ?>" alt="<?php echo (isset($title) ? Zira\Helper::html($title) : '') ?>" />
</a>
<?php else: ?>
<img src="<?php echo Zira\User::getProfileNoPhotoUrl() ?>" width="<?php echo Zira\Config::get('user_thumb_width') ?>" height="<?php echo Zira\Config::get('user_thumb_height') ?>" />
<?php endif; ?>
</div><!--/user-profile-photo-->
<?php if (isset($title)): ?>
<h1><?php echo Zira\Helper::html($title) ?></h1>
<?php endif; ?>
<?php if (!empty($email)): ?>
<div><span class="glyphicon glyphicon-envelope"></span>&nbsp;<?php echo Zira\Helper::html($email); ?><?php if (isset($verified) && !$verified) echo '&nbsp;(<a href="'.Zira\Helper::url('user/confirm').'">'.t('Verify').'</a>)' ?></div>
<?php endif; ?>
<?php if (!empty($verified)): ?>
<div><span class="glyphicon glyphicon-ok-circle"></span>&nbsp;<?php echo t('Verified'); ?></div>
<?php endif; ?>
<?php if (!empty($phone)): ?>
<div><span class="glyphicon glyphicon-earphone"></span>&nbsp;<?php echo Zira\Helper::html($phone); ?></div>
<?php endif; ?>
<?php if (isset($is_owner) && $is_owner): ?>
<div><a href="<?php echo Zira\Helper::url('user/logout') ?>"><span class="glyphicon glyphicon-log-out"></span>&nbsp;<?php echo t('Logout'); ?></a></div>
<?php endif; ?>
<?php if (empty($is_owner) && Zira\User::isAuthorized()): ?>
<div class="user-black-list-links">
<a href="javascript:void(0)" data-action="black-list" data-user="<?php echo Zira\Helper::html($id) ?>" data-url="<?php echo Zira\Helper::url('user/ajax'); ?>" data-token="<?php echo Zira\User::getToken() ?>" class="user-black-list-link<?php if (Zira\User::isUserBlocked($id)) echo ' blocked' ?>"><span class="if-not-blocked"><span class="glyphicon glyphicon-ban-circle"></span> <?php echo t('Add to black list') ?></span> <span class="if-blocked"><span class="glyphicon glyphicon-minus-sign"></span> <?php echo t('Remove from black list') ?></span></a>
</div>
<?php endif; ?>
</div><!--/user-head-->
<div class="clear"></div>
<div class="user-button">
<?php if (isset($is_owner) && $is_owner): ?>
<div class="dropdown user-profile-links">
<button class="btn btn-primary dropdown-toggle" type="button" id="user-edit-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
<?php echo t('My account'); ?>&nbsp;<span class="caret"></span>
</button>
<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="user-edit-dropdown">
<?php foreach (Zira\User::getProfileEditLinks() as $link): ?>
<?php if (array_key_exists('type', $link) && $link['type']=='separator'): ?>
<li role="separator" class="divider"></li>
<?php else: ?>
<?php $icon = !empty($link['icon']) ? '<span class="'.Zira\Helper::html($link['icon']).'"></span>' : ''; ?>
<li><a href="<?php echo Zira\Helper::html(Zira\Helper::url($link['url'])) ?>"><?php echo $icon; ?> <?php echo Zira\Helper::html($link['title']) ?></a></li>
<?php endif; ?>
<?php endforeach; ?>
</ul>
</div><!--/user-profile-links-->
<?php endif; ?>
<?php if (empty($is_owner) && Zira\User::isAuthorized()): ?>
<div class="user-profile-links">
<a class="btn btn-primary" href="<?php echo Zira\Helper::url('user/message/'.Zira\Helper::html($id)) ?>"><span class="glyphicon glyphicon-envelope"></span> <?php echo t('Send message') ?></a>
</div>
<?php endif; ?>
</div><!--/user-button-->
<h2><span class="glyphicon glyphicon-list-alt"></span>&nbsp;<?php echo t('User information') ?></h2>
<dl class="user-info">
<?php if (isset($group)) echo '<dt><span class="glyphicon glyphicon-user"></span>&nbsp;'.t('Group').': </dt><dd>'.Zira\Helper::html($group).'</dd>'; ?>
<?php if (isset($location)) echo '<dt><span class="glyphicon glyphicon-map-marker"></span>&nbsp;'.t('Location').': </dt><dd>'.Zira\Helper::html($location).'</dd>'; ?>
<?php if (isset($dob)) echo '<dt><span class="glyphicon glyphicon-gift"></span>&nbsp;'.t('Date of birth').': </dt><dd>'.Zira\Helper::html($dob).'</dd>'; ?>
<?php if (isset($date_created)) echo '<dt><span class="glyphicon glyphicon-calendar"></span>&nbsp;'.t('Sign-up date').': </dt><dd>'.Zira\Helper::html($date_created).'</dd>'; ?>
<?php if (isset($date_logged)) echo '<dt><span class="glyphicon glyphicon-log-in"></span>&nbsp;'.t('Last login date').': </dt><dd>'.Zira\Helper::html($date_logged).'</dd>'; ?>
<?php if (isset($comments)) echo '<dt><span class="glyphicon glyphicon-comment"></span>&nbsp;'.t('Comments posted').': </dt><dd>'.Zira\Helper::html($comments).'</dd>'; ?>
<?php foreach(Zira\User::getProfileExtraInfo() as $extra_info): ?>
<?php $icon = !empty($extra_info['icon']) ? '<span class="'.Zira\Helper::html($extra_info['icon']).'"></span>' : ''; ?>
<dt><?php echo $icon ?> <?php echo Zira\Helper::html($extra_info['title']) ?>: </dt><dd><?php echo Zira\Helper::html($extra_info['description']) ?></dd>
<?php endforeach; ?>
</dl>
</article>
</main>