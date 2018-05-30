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
<p class="comment-text parse-content"><?php echo Zira\Content\Parse::bbcode(Zira\Helper::nl2br(Zira\Helper::html($record->comment_content))) ?></p>
</div>
<div class="list-info-wrapper">
<span class="list-info author">
<span class="glyphicon glyphicon-user"></span> 
<?php if ($record->comment_author_id > 0 && $record->comment_author_username !== null && $record->comment_author_firstname !== null && $record->comment_author_secondname !== null): ?>
<?php echo ($record->comment_sender_name ? Zira\User::generateUserProfileLink($record->comment_author_id, null, null, $record->comment_sender_name) : Zira\User::generateUserProfileLink($record->comment_author_id, $record->comment_author_firstname, $record->comment_author_secondname, $record->comment_author_username)); ?>
<?php else : ?>
<?php echo ($record->comment_sender_name ? Zira\Helper::html($record->comment_sender_name) : t('Guest')); ?>
<?php endif; ?>
</span>
<?php if (empty($settings['sidebar']) && $record->category_name && $record->category_title): ?>
<?php if ($record->comment_parent_id): ?>
<span class="list-info author">
<span class="glyphicon glyphicon-share-alt"></span> &nbsp;<?php echo $record->comment_recipient_name ? Zira\Helper::html($record->comment_recipient_name) : t('Guest') ?>
</span>
<?php endif; ?>
<span class="list-info category"><span class="glyphicon glyphicon-tag"></span> <a href="<?php echo Zira\Helper::url(Zira\Page::generateCategoryUrl($record->category_name)) ?>" title="<?php echo Zira\Helper::html(t($record->category_title)) ?>"><?php echo Zira\Helper::html(t($record->category_title)) ?></a></span>
<span class="list-info date">
<span class="glyphicon glyphicon-time"></span> <?php echo date(Zira\Config::get('date_format'), strtotime($record->comment_creation_date)) ?> 
</span>
<?php endif; ?>
</div>
</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
</div>
