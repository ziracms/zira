<?php if (!empty($records)): ?>
<ul class="list<?php if (!empty($grid)) echo ' grid-category-wrapper grid-col-'.(intval($grid)+1) ?><?php if (isset($class)) echo ' '.$class ?>">
<?php $co = 0; ?>
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
<?php if ($record->category_name && $record->category_title): ?>
<span class="list-info category"><span class="glyphicon glyphicon-tag"></span> <a href="<?php echo Zira\Helper::url(Zira\Page::generateCategoryUrl($record->category_name)) ?>" title="<?php echo Zira\Helper::html(t($record->category_title)) ?>"><?php echo Zira\Helper::html(t($record->category_title)) ?></a></span>
<?php endif; ?>
</div>
</li>
<?php $co++; ?>
<?php if (isset($settings) && !empty($settings['limit']) && $co>=$settings['limit']) break; ?>
<?php endforeach; ?>
</ul>
<?php if (isset($pagination)): ?>
<div class="list-pagination-wrapper">
<?php echo $pagination; ?>
</div>
<?php endif; ?>
<?php if (isset($settings) && !empty($settings['limit']) && ((!empty($settings['pages']) && !empty($settings['page']) && $settings['page']<$settings['pages']) || count($records)>$settings['limit']) && !isset($pagination)): ?>
<div class="list-view-more-wrapper">
<button class="btn btn-primary list-view-more" type="button" data-url="<?php echo Zira\Helper::url('records') ?>" data-category="<?php echo Zira\Category::current()->id ?>" data-last="<?php echo $record->id ?>" data-page="<?php echo (!empty($settings['page']) ? intval($settings['page'])+1 : 0); ?>"><?php echo t('View more') ?>&nbsp;&rsaquo;&rsaquo;</button>
</div>
<?php endif; ?>
<?php endif; ?>
