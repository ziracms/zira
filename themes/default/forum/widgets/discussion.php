<div class="forum-discussion-widget-wrapper">
<div class="page-header">
<h2 class="widget-category-title"><a href="<?php echo Zira\Helper::html(Zira\Helper::url('forum')) ?>" title="<?php echo tm('Discussed on forum', 'forum') ?>"><?php echo tm('Forum', 'forum') ?></a></h2>
</div>
<?php if (!empty($items)): ?>
<ul class="widget-list list forum-widget-list">
<?php $co = 0; ?>
<?php foreach($items as $item): ?>
<li class="list-item no-thumb <?php echo ($co%2==0 ? 'odd' : 'even') ?>">
<h3 class="list-title-wrapper">
<a class="list-title" href="<?php echo Zira\Helper::url(Forum\Models\Topic::generateUrl($item->topic_id)) ?>" title="<?php echo Zira\Helper::html($item->topic_title) ?>"><?php echo Zira\Helper::html($item->topic_title) ?></a>
</h3>
<div class="list-content-wrapper forum-widget-content-wrapper">
<p class="parse-content"><?php echo Zira\Content\Parse::bbcode(Zira\Helper::nl2br(Zira\Helper::html($item->content))) ?></p>
</div>
<div class="list-info-wrapper">
<span class="list-info author"><span class="glyphicon glyphicon-user"></span>
<?php if ($item->user_username): ?>
<?php echo Zira\User::generateUserProfileLink($item->creator_id, $item->user_firstname, $item->user_secondname, $item->user_username, 'author') ?>
<?php else: ?>
<?php echo tm('User deleted', 'forum'); ?>
<?php endif; ?>
</span>
<span class="list-info date"><span class="glyphicon glyphicon-time"></span> <?php echo date(Zira\Config::get('date_format'), strtotime($item->date_modified)) ?></span>
</div>
</li>
<?php $co++; ?>
<?php endforeach; ?>
</ul>
<?php endif; ?>
</div>
