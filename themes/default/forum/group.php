<?php if (isset($searchForm)): ?>
<div class="messages-panel forum-messages-panel">
<nav class="navbar navbar-default">
<div class="container-fluid">
<div class="navbar-header">
<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#user-messages-panel" aria-expanded="false">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="<?php echo Zira\Helper::html(Zira\Helper::url(Forum\Forum::ROUTE)) ?>" title="<?php echo tm('Forum','forum') ?>"><span class="glyphicon glyphicon-link"></span></a>
</div>
<div class="collapse navbar-collapse" id="user-messages-panel">
<?php if (Zira\User::isAuthorized()): ?>
<ul class="nav navbar-nav">
<li><a href="<?php echo Zira\Helper::html(Zira\Helper::url(Forum\Forum::ROUTE.'/user')) ?>"><span class="glyphicon glyphicon-comment"></span> <?php echo tm('My discussions','forum') ?></a></li>
</ul>
<?php endif; ?>
<?php echo $searchForm; ?>
</div>
</div>
</nav>
</div>
<?php endif; ?>

<?php if (!empty($items)): ?>
<ul class="forum-list list">
<?php $co = 0; ?>
<?php foreach($items as $item): ?>
<li class="list-item no-thumb <?php echo ($co%2==0 ? 'odd-b' : 'even-b') ?>">
<?php $ticon = strtotime($item->date_modified) > time()-43200 ? '<span class="glyphicon glyphicon-flag"></span> ' : ''; ?>
<h3 class="list-title-wrapper">
<a class="list-title" href="<?php echo Zira\Helper::html(Zira\Helper::url(Forum\Models\Forum::generateUrl($item))) ?>" title="<?php echo Zira\Helper::html(t($item->title)) ?>"><?php echo $ticon ?><?php echo Zira\Helper::html(t($item->title)) ?></a>
</h3>
<div class="list-content-wrapper">
<p><?php echo Zira\Helper::nl2br(Zira\Helper::html(t(str_replace("\r\n","\n",$item->description)))) ?></p>
</div>
<div class="list-info-wrapper">
<span class="list-info date"><span class="glyphicon glyphicon-time"></span> <?php echo date(Zira\Config::get('date_format'), strtotime($item->date_modified)) ?></span>
<?php if ($item->last_user_id && $item->user_username): ?>
<span class="list-info author"><span class="glyphicon glyphicon-user"></span> <?php echo Zira\User::generateUserProfileLink($item->last_user_id, $item->user_firstname, $item->user_secondname, $item->user_username) ?></span>
<?php endif; ?>
<span class="list-info counter"><span class="glyphicon glyphicon-comment"></span> <?php echo Zira\Helper::html(tm('Threads: %s', 'forum', $item->topics)) ?></span>
</div>
</li>
<?php $co++; ?>
<?php endforeach; ?>
</ul>
<?php endif; ?>
