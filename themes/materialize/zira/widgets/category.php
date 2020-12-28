<div class="block widget-category-wrapper<?php if (!empty($grid)) echo ' grid-category-wrapper grid-col-'.(intval($grid)+1) ?>">
<?php if (!empty($title)): ?>
<div class="page-header">
<?php if (!empty($url)): ?>
<h2 class="widget-title"><a href="<?php echo Zira\Helper::html(Zira\Helper::url($url)) ?>" title="<?php echo Zira\Helper::html($title) ?>"><?php echo Zira\Helper::html($title) ?></a></h2>
<?php else: ?>
<h2 class="widget-title"><?php echo Zira\Helper::html($title) ?></h2>
<?php endif; ?>
</div>
<?php endif; ?>
<?php if (!empty($records)): ?>
<ul class="widget-list list<?php if (isset($class)) echo ' '.$class ?>">
<?php foreach($records as $record): ?>
<li class="list-item top-image <?php echo $record->thumb ? 'with-thumb' : 'no-thumb' ?>">
<div class="list-content-wrapper">
<?php if ($record->thumb): ?>
<a class="list-thumb" href="<?php echo Zira\Helper::url(Zira\Page::generateRecordUrl($record->category_name, $record->name)) ?>" title="<?php echo Zira\Helper::html($record->title) ?>">
<img src="<?php echo Zira\Helper::baseUrl(Zira\Helper::html($record->thumb)) ?>"  alt="<?php echo Zira\Helper::html($record->title) ?>" width="<?php echo Zira\Config::get('thumbs_width') ?>" height="<?php echo Zira\Config::get('thumbs_height') ?>" />
</a>
<?php endif; ?>
<h3 class="list-title-wrapper">
<a class="list-title" href="<?php echo Zira\Helper::url(Zira\Page::generateRecordUrl($record->category_name, $record->name)) ?>" title="<?php echo Zira\Helper::html($record->title) ?>"><?php echo Zira\Helper::html($record->title) ?></a>
</h3>
<p><?php echo Zira\Helper::nl2br(Zira\Helper::html($record->description)) ?></p>
<?php Zira\Page::renderRecordWidgetPreview($record->id); ?>
</div>
<?php if (isset($settings) && empty($settings['sidebar'])): ?>
<div class="list-info-wrapper">
<?php if (isset($settings) && !empty($settings['display_date'])): ?>
<span class="list-info date"><span class="glyphicon glyphicon-time"></span> <?php echo date(Zira\Config::get('date_format'), strtotime($record->creation_date)) ?></span>
<?php endif; ?>
<?php if (isset($settings) && !empty($settings['display_author'])): ?>
<span class="list-info author"><span class="glyphicon glyphicon-user"></span> <?php echo Zira\User::generateUserProfileLink($record->author_id, $record->author_firstname, $record->author_secondname, $record->author_username) ?></span>
<?php endif; ?>
<?php if (isset($settings) && !empty($settings['comments_enabled'])): ?>
<span class="list-info comments-count"><span class="glyphicon glyphicon-comment"></span> <?php echo $record->comments ?></span>
<?php endif; ?>
<?php if (isset($settings) && !empty($settings['rating_enabled'])): ?>
<span class="list-info likes-count"><span class="glyphicon glyphicon-thumbs-up"></span> <?php echo $record->rating ?></span>
<?php endif; ?>
</div>
<?php endif; ?>
</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
</div>
