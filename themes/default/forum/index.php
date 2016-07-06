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
<?php if (Zira\User::isAuthorized()): ?>
<a class="navbar-brand" href="<?php echo Zira\Helper::html(Zira\Helper::url(Forum\Forum::ROUTE.'/user')) ?>" title="<?php echo tm('My discussions','forum') ?>"><span class="glyphicon glyphicon-comment"></span></a>
<?php else: ?>
<a class="navbar-brand" href="<?php echo Zira\Helper::html(Zira\Helper::url('')) ?>" title="<?php echo t('Home') ?>"><span class="glyphicon glyphicon-home"></span></a>
<?php endif; ?>
</div>
<div class="collapse navbar-collapse" id="user-messages-panel">
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
<h2 class="forum-category-title"><a href="<?php echo Zira\Helper::html(Zira\Helper::url(Forum\Models\Category::generateUrl($category))) ?>" title="<?php echo Zira\Helper::html($category->title) ?>"><span class="glyphicon glyphicon-link"></span> <?php echo Zira\Helper::html($category->title) ?></a></h2>
</div>
<?php Zira\View::renderView(array('items'=>$category->forums), 'forum/group'); ?>
<?php endforeach; ?>
<?php endif; ?>
