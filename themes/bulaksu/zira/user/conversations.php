<div class="page-header">
<a class="btn btn-primary compose-message-btn" href="<?php echo Zira\Helper::url('user/compose') ?>"><?php echo t('Compose message') ?></a>
<h1><?php echo t('Messages') ?></h1>
</div>
<?php if (!empty($items)): ?>
<div class="messages-panel">
<nav class="navbar navbar-default">
<div class="container-fluid">
<div class="navbar-header">
<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#user-messages-panel" aria-expanded="false">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
</div>
<div class="collapse navbar-collapse" id="user-messages-panel">
<ul class="nav navbar-nav">
<li class="disabled"><a href="javascript:void(0)" class="conversation-mark-btn"><span class="glyphicon glyphicon-ok-circle"></span> <?php echo t('Mark as read'); ?></a></li>
<li><a href="javascript:void(0)" class="conversation-mark-all-btn"><span class="glyphicon glyphicon-ok-sign"></span> <?php echo t('Mark all as read'); ?></a></li>
</ul>
<button type="button" class="btn btn-default navbar-btn navbar-right conversation-delete-btn" disabled="disabled"><span class="glyphicon glyphicon-trash"></span> <?php echo t('Delete') ?></button>
</div>
</div>
</nav>
</div><!--/messages-panel-->
<?php endif; ?>
<?php if (!empty($items)): ?>
<ul class="list messages-list">
<?php foreach($items as $index=>$item): ?>
<li rel="conversation-<?php echo $item->id ?>" class="<?php echo $index%2==0 ? 'odd' : 'even' ?><?php if ($item->highlight) echo ' highlight'; ?>">
<input type="checkbox" class="conversation-checkbox" name="conversation[]" value="<?php echo $item->id ?>" onchange="zira_convesation_on_select()" />
&nbsp; &nbsp;
<span class="glyphicon glyphicon-time"></span> <span><?php echo Zira\Helper::html(date(Zira\Config::get('date_format'), strtotime($item->modified_date))) ?></span>
&nbsp; &nbsp;
<span class="glyphicon glyphicon-envelope"></span> <a href="<?php echo Zira\Helper::url('user/messages/'.$item->conversation_id) ?>"><?php echo Zira\Helper::html($item->subject) ?></a>
</li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<p class="no-messages-message"><?php echo t('You have no messages'); ?></p>
<?php endif; ?>
<?php if (isset($pagination)) echo $pagination; ?>
<script type="text/javascript">
(function($) {
    zira_convesation_on_select = function () {
        var conversations = $('input.conversation-checkbox:checked');
        if ($(conversations).length == 0) {
            $('.conversation-delete-btn').attr('disabled', 'disabled');
            $('.conversation-mark-btn').parent('li').addClass('disabled');
        } else {
            $('.conversation-delete-btn').removeAttr('disabled');
            $('.conversation-mark-btn').parent('li').removeClass('disabled');
        }
    };
    zira_conversation_get_selected = function() {
        var conversations = $('input.conversation-checkbox:checked');
        var ids = [];
        $(conversations).each(function () {
            ids.push($(this).val());
        });
        return ids;
    };
    $(document).ready(function(){
        $('.conversation-mark-btn').click(function(e){
            var ids = zira_conversation_get_selected();
            if (ids.length==0) return;
            $.post('<?php echo Zira\Helper::url('user/ajax') ?>', {
                'action': 'conversation-mark-read',
                'items': ids,
                'token': '<?php echo Zira\User::getToken() ?>'
            }, function(response){
                if (!response) return;
                if (typeof(response.error)!="undefined") {
                    zira_error(response.error);
                } else if (typeof(response.message)!="undefined") {
                    zira_message(response.message);
                }
                if (typeof(response.items)!="undefined") {
                    for (var i=0; i<response.items.length; i++) {
                        $('ul.messages-list li[rel=conversation-'+response.items[i]).removeClass('highlight');
                    }
                }
            }, 'json');
        });
        $('.conversation-mark-all-btn').click(function(e){
            $.post('<?php echo Zira\Helper::url('user/ajax') ?>', {
                'action': 'conversation-mark-all-read',
                'token': '<?php echo Zira\User::getToken() ?>'
            }, function(response){
                if (!response) return;
                if (typeof(response.error)!="undefined") {
                    zira_error(response.error);
                } else if (typeof(response.message)!="undefined") {
                    zira_message(response.message);
                }
                $('ul.messages-list li').removeClass('highlight');
            }, 'json');
        });
        $('.conversation-delete-btn').click(function(e){
            var ids = zira_conversation_get_selected();
            if (ids.length==0) return;
            $.post('<?php echo Zira\Helper::url('user/ajax') ?>', {
                'action': 'conversation-delete',
                'items': ids,
                'token': '<?php echo Zira\User::getToken() ?>'
            }, function(response){
                if (!response) return;
                if (typeof(response.error)!="undefined") {
                    zira_error(response.error);
                } else if (typeof(response.message)!="undefined") {
                    zira_message(response.message);
                }
                if (typeof(response.items)!="undefined") {
                    for (var i=0; i<response.items.length; i++) {
                        $('ul.messages-list li[rel=conversation-'+response.items[i]).remove();
                    }
                }
            }, 'json');
        });
    });
})(jQuery);
</script>
