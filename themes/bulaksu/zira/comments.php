<?php if (empty($ajax)): ?>
<div class="comments-wrapper">
<div class="btn-group comment-btn-group" role="group">
<?php if (isset($form) && !empty($comments)): ?>
<button class="btn btn-primary comment-btn scroll-down" data-target="#comments-form"><?php echo t('Leave a comment') ?></button>
<?php endif; ?>
<?php if (!empty($comments)): ?>
<button class="btn btn-default comments-reload" type="button" title="<?php echo t('Reload') ?>" data-url="<?php echo Zira\Helper::url('comments') ?>" data-record="<?php echo intval($record_id) ?>" data-page="0"><span class="glyphicon glyphicon-refresh"></span></button>
<?php endif; ?>
</div>
<?php if (isset($total)): ?>
<h2 id="comments"><?php echo t('Comments') ?>(<?php echo $total ?>)</h2>
<?php endif; ?>
<?php endif; ?>
<?php if (!empty($comments)): ?>
<ul class="comments<?php if (!empty($ajax)) echo ' xhr-list'; ?>">
<?php foreach($comments as $comment): ?>
<?php $comment_offset_class = ''; ?>
<?php $comment_offset = count(explode(Zira\Models\Comment::PATH_DELIMITER, $comment->sort_path)) - 1; ?>
<?php if ($comment_offset>0) $comment_offset_class = ' comments-item-nested-'.($comment_offset < Zira\Config::get('comments_max_nesting', 5) ? $comment_offset : Zira\Config::get('comments_max_nesting', 5)); ?>
<li class="comments-item<?php echo $comment_offset_class ?><?php if (!$comment->published) echo ' disabled'; ?>">
<?php if ($comment->author_id > 0 && $comment->author_username !== null && $comment->author_firstname !== null && $comment->author_secondname !== null): ?>
<?php echo Zira\User::generateUserProfileThumbLink($comment->author_id, $comment->author_firstname, $comment->author_secondname, $comment->author_username, null, $comment->author_image, null, array('class'=>'comment-avatar')) ?>
<?php else: ?>
<?php echo Zira\User::generateUserProfileThumb($comment->author_image, null, array('class'=>'comment-avatar')) ?>
<?php endif; ?>
<span class="comment-head">
<?php if ($comment->author_id > 0 && $comment->author_username !== null && $comment->author_firstname !== null && $comment->author_secondname !== null): ?>
<?php echo ($comment->sender_name ? Zira\User::generateUserProfileLink($comment->author_id, null, null, $comment->sender_name) : Zira\User::generateUserProfileLink($comment->author_id, $comment->author_firstname, $comment->author_secondname, $comment->author_username)); ?>
<?php else : ?>
<?php echo ($comment->sender_name ? Zira\Helper::html($comment->sender_name) : t('Guest')); ?>
<?php endif; ?>
<?php if ($comment->recipient_name): ?>
&nbsp; <span class="glyphicon glyphicon-share-alt"></span> &nbsp;<?php echo Zira\Helper::html($comment->recipient_name) ?>
<?php endif; ?>
</span>
<p class="comment-text parse-content"><?php echo Zira\Content\Parse::bbcode(Zira\Helper::nl2br(Zira\Helper::html($comment->content))) ?></p>
<span class="comment-info">
<span class="glyphicon glyphicon-time"></span> <?php echo date(Zira\Config::get('date_format'), strtotime($comment->creation_date)) ?> &nbsp;
<a href="javascript:void(0)" class="comment-rating comment-like" data-value="1" data-type="comment" data-id="<?php echo intval($comment->id) ?>" data-token="<?php echo Zira\User::getToken() ?>" data-url="<?php echo Zira\Helper::url('poll') ?>">
<span class="glyphicon glyphicon-thumbs-up"></span>
<span class="rating-value"><?php echo intval($comment->likes); ?></span>
</a> &nbsp;
<a href="javascript:void(0)" class="comment-rating comment-dislike" data-value="-1" data-type="comment" data-id="<?php echo intval($comment->id) ?>" data-token="<?php echo Zira\User::getToken() ?>" data-url="<?php echo Zira\Helper::url('poll') ?>">
<span class="glyphicon glyphicon-thumbs-down"></span>
<span class="rating-value"><?php echo intval($comment->dislikes); ?></span>
</a> &nbsp;
<?php if (!empty($commenting_allowed) && (!Zira\User::isAuthorized() || Zira\User::getCurrent()->id != $comment->author_id) && $comment_offset+1<50): ?>
<a href="javascript:void(0)" data-parent="<?php echo ($comment_offset < Zira\Config::get('comments_max_nesting', 5) ? intval($comment->id) : intval($comment->parent_id)) ?>" data-reply="<?php echo intval($comment->id) ?>" class="comment-reply-link"><span class="glyphicon glyphicon-comment"></span> <?php echo t('Reply') ?></a>
<?php endif; ?>
</span>
</li>
<?php endforeach; ?>
</ul>
<?php if (isset($limit) && isset($page) && isset($total) && isset($record_id) && $total>$limit*($page+1)): ?>
<div class="comments-view-more-wrapper">
<button class="btn btn-primary comments-view-more" type="button" data-url="<?php echo Zira\Helper::url('comments') ?>" data-record="<?php echo intval($record_id) ?>" data-page="<?php echo intval($page)+1 ?>"><?php echo t('View more') ?>&nbsp;&rsaquo;&rsaquo;</button>
</div>
<?php endif; ?>
<?php endif; ?>
<?php if (isset($form)): ?>
<div id="comments-form" class="comment-form-wrapper">
<?php echo $form; ?>
</div>
<?php endif; ?>
<?php if (!isset($form) && Zira\Config::get('comments_allowed',true) && !Zira\Config::get('comment_anonymous',true) && !Zira\User::isAuthorized() && Zira\Page::getRecordUrl()!==null): ?>
<?php echo t('%s to leave a comment', '<a href="'.Zira\Helper::url('user/login?redirect='.Zira\Page::getRecordUrl()).'">'.t('Login').'</a>') ?>
<?php endif; ?>
<?php if (empty($ajax)): ?>
</div>
<?php endif; ?>