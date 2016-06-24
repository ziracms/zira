<?php if (!empty($categories)): ?>
<?php foreach($categories as $category): ?>
<?php if (!$category->forums || count($category->forums)==0) continue; ?>
<div class="page-header forum-page-header">
<h2 class="forum-category-title"><a href="<?php echo Zira\Helper::html(Zira\Helper::url(Forum\Models\Category::generateUrl($category))) ?>" title="<?php echo Zira\Helper::html($category->title) ?>"><span class="glyphicon glyphicon-link"></span> <?php echo Zira\Helper::html($category->title) ?></a></h2>
</div>
<?php Zira\View::renderView(array('items'=>$category->forums), 'forum/group'); ?>
<?php endforeach; ?>
<?php endif; ?>
