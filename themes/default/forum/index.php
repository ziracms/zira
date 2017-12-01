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
<a class="navbar-brand" href="<?php echo Zira\Helper::html(Zira\Helper::url('')) ?>" title="<?php echo t('Home') ?>"><span class="glyphicon glyphicon-home"></span></a>
</div>
<div class="collapse navbar-collapse" id="user-messages-panel">
<ul class="nav navbar-nav">
<?php if (Zira\User::isAuthorized()): ?>
<li><a href="<?php echo Zira\Helper::html(Zira\Helper::url(Forum\Forum::ROUTE.'/user')) ?>"><span class="glyphicon glyphicon-comment"></span> <?php echo tm('My discussions','forum') ?></a></li>
<?php else: ?>
<li><a href="javascript:void(0)"><span class="glyphicon glyphicon-comment"></span> <?php echo tm('Published %s messages', 'forum', Forum\Models\Message::getPublishedMessagesCount()) ?></a></li>
<?php endif; ?>
</ul>
<?php echo $searchForm; ?>
</div>
</div>
</nav>
</div>
<?php endif; ?>

<?php if (!empty($categories)): ?>
<?php foreach($categories as $category): ?>
<?php if (!$category->forums || count($category->forums)==0) continue; ?>
<div class="page-header forum-page-header">
<h2 class="forum-category-title"><a href="<?php echo Zira\Helper::html(Zira\Helper::url(Forum\Models\Category::generateUrl($category))) ?>" title="<?php echo Zira\Helper::html(t($category->title)) ?>"><span class="glyphicon glyphicon-link"></span> <?php echo Zira\Helper::html(t($category->title)) ?></a></h2>
</div>
<?php Zira\View::renderView(array('items'=>$category->forums), 'forum/group'); ?>
<?php endforeach; ?>
<?php endif; ?>
