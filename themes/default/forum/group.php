<?php if (!empty($items)): ?>
<ul class="forum-list list">
<?php $co = 0; ?>
<?php foreach($items as $item): ?>
<li class="list-item no-thumb <?php echo ($co%2==0 ? 'odd-b' : 'even-b') ?>">
<?php $ticon = date('Y-m-d', strtotime($item->date_modified)) == date('Y-m-d') ? '<span class="glyphicon glyphicon-flag"></span> ' : ''; ?>
<h3 class="list-title-wrapper">
<a class="list-title" href="<?php echo Zira\Helper::html(Zira\Helper::url(Forum\Models\Forum::generateUrl($item))) ?>" title="<?php echo Zira\Helper::html($item->title) ?>"><?php echo $ticon ?><?php echo Zira\Helper::html($item->title) ?></a>
</h3>
<div class="list-content-wrapper">
<p><?php echo Zira\Helper::nl2br(Zira\Helper::html($item->description)) ?></p>
</div>
<div class="list-info-wrapper">
<span class="list-info date"><span class="glyphicon glyphicon-time"></span> <?php echo date(Zira\Config::get('date_format'), strtotime($item->date_modified)) ?></span>
<?php if ($item->last_user_id && $item->user_username): ?>
<span class="list-info author"><span class="glyphicon glyphicon-user"></span> <?php echo Zira\User::generateUserProfileLink($item->last_user_id, $item->user_firstname, $item->user_secondname, $item->user_username) ?></span>
<?php endif; ?>
<span class="list-info counter"><span class="glyphicon glyphicon-comment"></span> <?php echo Zira\Helper::html(tm('Threads: %s', 'forum', $item->topics)) ?></span>
</div>
</li>
<?php $co++; ?>
<?php endforeach; ?>
</ul>
<?php endif; ?>
