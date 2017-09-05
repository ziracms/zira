<?php if (isset($chat)): ?>
<div id="widget-chat-<?php echo intval($chat->id) ?>" class="widget-chat-wrapper widget-category-wrapper" data-url="<?php echo Zira\Helper::url('chat/index/index?'.FORMAT_GET_VAR.'='.FORMAT_JSON); ?>" data-chat="<?php echo intval($chat->id) ?>" data-delay="<?php echo intval($chat->refresh_delay) ?>">
    <div class="page-header">
    <h2 class="widget-category-title"><?php echo Zira\Helper::html($chat->title) ?></h2>
    </div>
    <div class="widget-chat-messages"></div>
    <?php if (!empty($form)): ?>
    <div class="widget-chat-form">
        <?php echo $form; ?>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>