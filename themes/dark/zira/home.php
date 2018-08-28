<?php if (!empty($categories)): ?>
<?php foreach($categories as $category): ?>
<?php if (empty($category['records'])) continue; ?>
<div class="home-category-wrapper<?php if (!empty($grid)) echo ' grid-category-wrapper grid-col-'.(intval($grid)+1) ?>">
<?php if (!empty($category['title'])): ?>
<div class="page-header">
<?php if (!empty($category['url'])): ?>
<h2 class="home-category-title"><a href="<?php echo Zira\Helper::html(Zira\Helper::url($category['url'])) ?>" title="<?php echo Zira\Helper::html($category['title']) ?>"><span class="glyphicon glyphicon-link"></span> <?php echo Zira\Helper::html($category['title']) ?></a></h2>
<?php else: ?>
<h2 class="home-category-title"><span class="glyphicon glyphicon-link"></span> <?php echo Zira\Helper::html($category['title']) ?></h2>
<?php endif; ?>
</div>
<?php endif; ?>
<ul class="list home-list">
<?php foreach($category['records'] as $record): ?>
<li class="list-item <?php echo $record->thumb ? 'with-thumb' : 'no-thumb' ?>">
<div class="list-title-wrapper">
<a class="list-title" href="<?php echo Zira\Helper::url(Zira\Page::generateRecordUrl($record->category_name, $record->name)) ?>" title="<?php echo Zira\Helper::html($record->title) ?>"><?php echo Zira\Helper::html($record->title) ?></a>
</div>
<div class="list-content-wrapper">
<?php if ($record->thumb): ?>
<a class="list-thumb" href="<?php echo Zira\Helper::url(Zira\Page::generateRecordUrl($record->category_name, $record->name)) ?>" title="<?php echo Zira\Helper::html($record->title) ?>">
<img src="<?php echo Zira\Helper::baseUrl(Zira\Helper::html($record->thumb)) ?>"  alt="<?php echo Zira\Helper::html($record->title) ?>" width="<?php echo Zira\Config::get('thumbs_width') ?>" height="<?php echo Zira\Config::get('thumbs_height') ?>" />
</a>
<?php endif; ?>
<p><?php echo Zira\Helper::nl2br(Zira\Helper::html($record->description)) ?></p>
<?php Zira\Page::renderRecordPreview($record->id); ?>
</div>
<div class="list-info-wrapper">
<?php if (!empty($category['settings']['display_date'])): ?>
<span class="list-info date"><span class="glyphicon glyphicon-time"></span> <?php echo date(Zira\Config::get('date_format'), strtotime($record->creation_date)) ?></span>
<?php endif; ?>
<?php if (!empty($category['settings']['display_author']) && empty($grid)): ?>
<span class="list-info author"><span class="glyphicon glyphicon-user"></span> <?php echo Zira\User::generateUserProfileLink($record->author_id, $record->author_firstname, $record->author_secondname, $record->author_username) ?></span>
<?php endif; ?>
<?php if (!empty($category['settings']['comments_enabled'])): ?>
<span class="list-info comments-count"><span class="glyphicon glyphicon-comment"></span> <?php echo $record->comments ?></span>
<?php endif; ?>
<?php if (!empty($category['settings']['rating_enabled'])): ?>
<span class="list-info likes-count"><span class="glyphicon glyphicon-thumbs-up"></span> <?php echo $record->rating ?></span>
<?php endif; ?>
</div>
</li>
<?php endforeach; ?>
</ul>
</div>
<?php endforeach; ?>
<?php endif; ?>
