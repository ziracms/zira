<div class="block widget-online-wrapper">
<?php if (!empty($title)): ?>
<div class="page-header">
<h2 class="widget-title"><?php echo Zira\Helper::html($title) ?></h2>
</div>
<?php endif; ?>
<div class="block-content">
<?php if (!empty($users) && !empty($count)): ?>
<ul class="widget-online-list">
<?php foreach($users as $user): ?>
<li>
    <?php echo Zira\User::generateUserProfileThumbLink($user->id, $user->firstname, $user->secondname, $user->username, null, $user->image, null, array('class'=>'user-online')) ?>
    <?php echo Zira\User::generateUserProfileLink($user->id, $user->firstname, $user->secondname, $user->username); ?>
</li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<i><?php echo t('Nobody') ?></i>
<?php endif; ?>
</div>
</div>
