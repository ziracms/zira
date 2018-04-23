<div class="block<?php if (!empty($grid)) echo ' grid-category-wrapper' ?>">
<?php if (!empty($title)): ?>
<div class="page-header">
<h2 class="widget-title"><?php echo Zira\Helper::html($title) ?></h2>
</div>
<?php endif; ?>
<?php if (!empty($records)): ?>
<ul class="widget-list list<?php if (isset($class)) echo ' '.$class ?>">
<?php foreach($records as $record): ?>
<li class="list-item <?php echo $record->thumb ? 'with-thumb' : 'no-thumb' ?>">
<h3 class="list-title-wrapper">
<a class="list-title" href="<?php echo Zira\Helper::url(Zira\Page::generateRecordUrl($record->category_name, $record->name)) ?>" title="<?php echo Zira\Helper::html($record->title) ?>"><?php echo Zira\Helper::html($record->title) ?></a>
</h3>
<div class="list-content-wrapper">
<?php if ($record->thumb): ?>
<a class="list-thumb" href="<?php echo Zira\Helper::url(Zira\Page::generateRecordUrl($record->category_name, $record->name)) ?>" title="<?php echo Zira\Helper::html($record->title) ?>">
<img src="<?php echo Zira\Helper::baseUrl(Zira\Helper::html($record->thumb)) ?>"  alt="<?php echo Zira\Helper::html($record->title) ?>" width="<?php echo Zira\Config::get('thumbs_width') ?>" height="<?php echo Zira\Config::get('thumbs_height') ?>" />
</a>
<?php endif; ?>
<p><?php echo Zira\Helper::nl2br(Zira\Helper::html($record->description)) ?></p>
<?php Zira\Page::renderRecordPreview($record->id); ?>
</div>
<?php if (!empty($grid)): ?>
<div class="list-info-wrapper">
<?php if ($record->category_name && $record->category_title): ?>
<span class="list-info category"><span class="glyphicon glyphicon-tag"></span> <a href="<?php echo Zira\Helper::url(Zira\Page::generateCategoryUrl($record->category_name)) ?>" title="<?php echo Zira\Helper::html($record->category_title) ?>"><?php echo Zira\Helper::html($record->category_title) ?></a></span>
<?php endif; ?>
</div>
<?php endif; ?>
</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
</div>
