<?php if (!empty($info)): ?>
<div class="alert alert-info forum-info" role="alert"><span class="glyphicon glyphicon-info-sign"></span> <?php echo Zira\Helper::nl2br(Zira\Helper::html($info)) ?></div>
<?php endif; ?>
<div class="messages-panel forum-messages-panel">
<nav class="navbar navbar-default">
<div class="container-fluid">
<div class="navbar-header">
<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#user-messages-panel" aria-expanded="false">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="<?php echo Zira\Helper::html(Zira\Helper::url($category_url)) ?>" title="<?php echo Zira\Helper::html(t($category_title)) ?>"><span class="glyphicon glyphicon-link"></span></a>
</div>
<div class="collapse navbar-collapse" id="user-messages-panel">
<?php if (isset($searchForm)) echo $searchForm; ?>
<a href="<?php echo Zira\Helper::html(Zira\Helper::url($compose_url)) ?>" class="btn btn-default navbar-btn navbar-right forum-btn"><span class="glyphicon glyphicon-plus-sign"></span> <?php echo tm('New thread','forum') ?></a>
</div>
</div>
</nav>
</div>

<?php if (!empty($top_items)): ?>
<ul class="forum-list forum-top-list list">
<?php $co = 0; ?>
<?php foreach($top_items as $item): ?>
<li class="list-item no-thumb <?php echo ($co%2==0 ? 'odd-b' : 'even-b') ?>">
<?php $ticon = $item->active && strtotime($item->date_modified) > time()-43200 ? '<span class="glyphicon glyphicon-flag"></span> ' : ''; ?>
<h3 class="list-title-wrapper">
<span class="glyphicon glyphicon-pushpin forum-right-item"></span>
<?php $title_ico = $item->active ? 'thread-open' : 'glyphicon glyphicon-lock'; ?>
<?php $status = $item->status ? '['.Forum\Models\Topic::getStatus($item->status).'] ' : '' ?>
<a class="list-title" href="<?php echo Zira\Helper::html(Zira\Helper::url(Forum\Models\Topic::generateUrl($item))) ?>" title="<?php echo Zira\Helper::html(t($item->title)) ?>"><?php echo $ticon ?><span class="<?php echo $title_ico ?>"></span> <?php echo Zira\Helper::html($status.t($item->title)) ?></a>
</h3>
<div class="list-info-wrapper">
<span class="list-info date"><span class="glyphicon glyphicon-time"></span> <?php echo date(Zira\Config::get('date_format'), strtotime($item->date_modified)) ?></span>
<?php if ($item->last_user_id && $item->user_username): ?>
<span class="list-info author"><span class="glyphicon glyphicon-user"></span> <?php echo Zira\User::generateUserProfileLink($item->last_user_id, $item->user_firstname, $item->user_secondname, $item->user_username) ?></span>
<?php endif; ?>
<span class="list-info counter"><span class="glyphicon glyphicon-comment"></span> <?php echo Zira\Helper::html(tm('Messages: %s', 'forum', $item->messages)) ?></span>
</div>
</li>
<?php $co++; ?>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if (!empty($items)): ?>
<ul class="forum-list list">
<?php $co = 0; ?>
<?php foreach($items as $item): ?>
<li class="list-item no-thumb <?php echo ($co%2==0 ? 'odd-b' : 'even-b') ?>">
<?php $ticon = $item->active && strtotime($item->date_modified) > time()-43200 ? '<span class="glyphicon glyphicon-flag"></span> ' : ''; ?>
<h3 class="list-title-wrapper">
<?php $title_ico = $item->active ? 'thread-open' : 'glyphicon glyphicon-lock'; ?>
<?php $status = $item->status ? '['.Forum\Models\Topic::getStatus($item->status).'] ' : '' ?>
<a class="list-title" href="<?php echo Zira\Helper::html(Zira\Helper::url(Forum\Models\Topic::generateUrl($item))) ?>" title="<?php echo Zira\Helper::html(t($item->title)) ?>"><?php echo $ticon ?><span class="<?php echo $title_ico ?>"></span> <?php echo Zira\Helper::html($status.t($item->title)) ?></a>
</h3>
<div class="list-info-wrapper">
<span class="list-info date"><span class="glyphicon glyphicon-time"></span> <?php echo date(Zira\Config::get('date_format'), strtotime($item->date_modified)) ?></span>
<?php if ($item->last_user_id && $item->user_username): ?>
<span class="list-info author"><span class="glyphicon glyphicon-user"></span> <?php echo Zira\User::generateUserProfileLink($item->last_user_id, $item->user_firstname, $item->user_secondname, $item->user_username) ?></span>
<?php endif; ?>
<span class="list-info counter"><span class="glyphicon glyphicon-comment"></span> <?php echo Zira\Helper::html(tm('Messages: %s', 'forum', $item->messages)) ?></span>
</div>
</li>
<?php $co++; ?>
<?php endforeach; ?>
</ul>
<?php if (isset($pagination)) echo $pagination ?>
<?php endif; ?>

<?php if (empty($top_items) && empty($item)): ?>
<p class="forum-empty-message"><?php echo tm('No one has posted anything yet.', 'forum') ?></p>
<?php endif; ?>
