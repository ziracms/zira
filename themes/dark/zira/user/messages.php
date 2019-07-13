<?php if (!empty($conversation)): ?>
<div class="page-header">
<h1><?php echo t('Subject'); ?>: <?php echo Zira\Helper::html($conversation->subject); ?></h1>
</div>
<?php if (isset($form) && !empty($items)): ?>
<ul id="conversation-<?php echo $conversation->id ?>" class="user-messages-resipients-list">
<?php if (!empty($users) && count($users)>0): ?>
<li><span class="glyphicon glyphicon-share-alt"></span> &nbsp;</li>
<?php foreach($users as $index=>$user): ?>
<?php if ($index>0) echo '<li class="separator"></li>'; ?>
<li><?php echo Zira\User::generateUserProfileLink($user->id, $user->firstname, $user->secondname, $user->username, null); ?></li>
<?php endforeach; ?>
<?php else: ?>
<li><span class="glyphicon glyphicon-lock"></span> &nbsp;</li>
<li><?php echo t('Conversation is closed'); ?></li>
<?php endif; ?>
</ul>
<div class="messages-panel">
<nav class="navbar navbar-default">
<div class="container-fluid">
<div class="navbar-header">
<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#user-messages-panel" aria-expanded="false">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="<?php echo Zira\Helper::url('user/messages') ?>"><?php echo t('Messages') ?></a>
</div>
<div class="collapse navbar-collapse" id="user-messages-panel">
<?php if (isset($form) && !empty($users) && count($users)>0): ?>
<button class="btn btn-default navbar-btn navbar-right reply-btn scroll-down" data-target=".form-panel"><span class="glyphicon glyphicon-share-alt"></span> <?php echo t('Reply') ?></button>
<?php endif; ?>
</div>
</div>
</nav>
</div>
<?php endif; ?>
<?php if (!empty($items)): ?>
<ul class="list messages-list">
<?php foreach($items as $index=>$item): ?>
<li id="conversation-msg-<?php echo $item->id ?>" class="<?php echo $index%2==0 ? 'odd' : 'even' ?>">
<div class="message-head">
<?php if ($item->username): ?>
<?php echo Zira\User::generateUserProfileLink($item->user_id, $item->firstname, $item->secondname, $item->username); ?>
<?php else: ?>
<?php echo t('User deleted'); ?>
<?php endif; ?>
&nbsp; &nbsp;
<div class="message-date"><span class="glyphicon glyphicon-time"></span> <?php echo Zira\Helper::datetime(strtotime($item->creation_date)) ?></div>
</div>
<div class="message-avatar">
<?php if ($item->username): ?>
<?php echo Zira\User::generateUserProfileThumbLink($item->user_id, $item->firstname, $item->secondname, $item->username, null, $item->image); ?>
<?php endif; ?>
</div>
<div class="message-content">
<p class="parse-content"><?php echo Zira\Content\Parse::bbcode(Zira\Helper::nl2br(Zira\Helper::html($item->content))) ?></p>
</div>
</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<?php if (isset($pagination)) echo $pagination; ?>
<?php if (isset($form) && !empty($users) && count($users)>0) echo $form; ?>
<?php endif; ?>