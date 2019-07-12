<?php if (isset($chat)): ?>
<div id="widget-chat-<?php echo intval($chat->id) ?>" class="widget-chat-wrapper block" data-url="<?php echo Zira\Helper::url('chat/index/index?'.FORMAT_GET_VAR.'='.FORMAT_JSON); ?>" data-chat="<?php echo intval($chat->id) ?>" data-delay="<?php echo intval($chat->refresh_delay) ?>">
<div class="page-header">
<h2 class="widget-title"><?php echo Zira\Helper::html($chat->title) ?></h2>
</div>
<?php if ($chat->info): ?>
<div class="chat-info alert alert-info"><p class="parse-content">
<?php echo Zira\Content\Parse::bbcode(Zira\Helper::nl2br(Zira\Helper::html($chat->info))); ?>
</p></div>
<?php endif; ?>
<div class="widget-chat-messages"></div>
<?php if (!empty($form)): ?>
<div class="widget-chat-form">
<?php echo $form; ?>
</div>
<?php endif; ?>
</div>
<?php endif; ?>