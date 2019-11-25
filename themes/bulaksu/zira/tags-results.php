<?php if (!empty($records)): ?>
<ul class="list<?php if (isset($class)) echo ' '.$class ?>">
<?php $co=0; ?>
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
</div>
<?php if (!isset($settings) || empty($settings['simple'])): ?>
<div class="list-info-wrapper">
<?php if ($record->category_name && $record->category_title): ?>
<span class="list-info category"><span class="glyphicon glyphicon-tag"></span> <a href="<?php echo Zira\Helper::url(Zira\Page::generateCategoryUrl($record->category_name)) ?>" title="<?php echo Zira\Helper::html(t($record->category_title)) ?>"><?php echo Zira\Helper::html(t($record->category_title)) ?></a></span>
<?php endif; ?>
</div>
<?php endif; ?>
</li>
<?php $co++; ?>
<?php if (isset($settings) && !empty($settings['limit']) && $co>=$settings['limit']) break; ?>
<?php endforeach; ?>
</ul>
<?php if (isset($settings) && !empty($settings['text']) && !empty($settings['limit']) && count($records)>$settings['limit'] && isset($settings['offset'])): ?>
<div class="search-results-view-more-wrapper">
<button class="btn btn-primary search-results-view-more" type="button" data-url="<?php echo Zira\Helper::url('tags') ?>" data-text="<?php echo Zira\Helper::html($settings['text']) ?>" data-offset="<?php echo Zira\Helper::html($settings['offset']+$co) ?>"><?php echo t('View more') ?>&nbsp;&rsaquo;&rsaquo;</button>
</div>
<?php endif; ?>
<?php endif; ?>