<?php if (!empty($items)): ?>
<ul class="forum-list list<?php if (isset($class)) echo ' '.$class ?>">
<?php $co=0; ?>
<?php foreach($items as $item): ?>
<li class="list-item no-thumb">
<h3 class="list-title-wrapper">
<?php $title_ico = $item->active ? 'thread-open' : 'glyphicon glyphicon-lock'; ?>
<?php $status = $item->status ? '['.Forum\Models\Topic::getStatus($item->status).'] ' : '' ?>
<a class="list-title" href="<?php echo Zira\Helper::html(Zira\Helper::url(Forum\Models\Topic::generateUrl($item))) ?>" title="<?php echo Zira\Helper::html($item->title) ?>"><span class="<?php echo $title_ico ?>"></span> <?php echo Zira\Helper::html($status.$item->title) ?></a>
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
<?php if (isset($settings) && !empty($settings['limit']) && $co>=$settings['limit']) break; ?>
<?php endforeach; ?>
</ul>
<?php if (isset($settings) && !empty($settings['text']) && !empty($settings['limit']) && count($items)>$settings['limit'] && isset($settings['offset']) && isset($settings['forum_id'])): ?>
<div class="forum-search-results-view-more-wrapper">
<button class="btn btn-primary forum-search-results-view-more" type="button" data-url="<?php echo Zira\Helper::url(Forum\Forum::ROUTE.'/search') ?>" data-text="<?php echo Zira\Helper::html($settings['text']) ?>" data-offset="<?php echo Zira\Helper::html($settings['offset']+$co) ?>" data-forum_id="<?php echo Zira\Helper::html($settings['forum_id']) ?>"><?php echo t('View more') ?>&nbsp;&rsaquo;&rsaquo;</button>
</div>
<?php endif; ?>
<?php endif; ?>
