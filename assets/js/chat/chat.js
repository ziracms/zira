(function($){
    $(document).ready(function(){
        $('.widget-chat-wrapper').css('visibility', 'visible');
        $('.widget-chat-wrapper').each(function(){
            $(this).find('form').submit(function(e){
                e.stopPropagation();
                e.preventDefault();
                
                $(this).trigger('xhr-submit-start');
                
                var data = $(this).serializeArray();
                
                var values = {};
                for (var i=0; i<data.length; i++) {
                    values[data[i].name] = data[i].value;
                }
                
                $(this).find('.form-control').attr('disabled', 'disabled');

                $.post($(this).attr('action'), values, zira_bind(this, function(response){
                    $(this).find('.form-control').removeAttr('disabled');
                    if (!response) {
                        $(this).trigger('xhr-submit-error');
                        return;
                    }
                    if (!response.status && response.error) {
                        $(this).trigger('xhr-submit-error');
                        zira_error(response.error);
                    }
                    if (response.status) {
                        $(this).find('.user-rich-input').val('');
                        $(this).trigger('xhr-submit-success');
                        //$(this).parents('.widget-chat-wrapper').zira_chat();
                    }
                }), 'json');
            });
            
            $(this).on('keyup','.user-rich-input, .emoji-editable',zira_bind(this, function(e){
                if (e.which == 13 && !e.shiftKey) {
                    $(this).find('form').submit();
                }
            }));
            
            $(this).zira_chat(true);
        });
    });
    
    $.fn.zira_chat = function(scrollDown) {
        var id = $(this).attr('id');
        var url = $(this).data('url');
        var chat = $(this).data('chat');
        var delay = $(this).data('delay');
        
        if (typeof(id)=="undefined" || typeof(url)=="undefined" || typeof(chat)=="undefined" || typeof(delay)=="undefined") return;
        
        chat_update.call(this, id, url, chat, delay, scrollDown);
    };
    
    var chat_update = function(id, url, chat, delay, scrollDown) {
        if (typeof(chat_update.timers)=="undefined") chat_update.timers = {};
        if (typeof(chat_update.last_ids)=="undefined") chat_update.last_ids = {};
        if (typeof(scrollDown)=="undefined") scrollDown = false;
        if (delay<1) delay = 1;
        try {
            window.clearTimeout(chat_update.timers[id]);
        } catch(e){}
        var data = {'chat_id':chat};
        if (typeof(chat_update.last_ids[id])!="undefined") {
            data['last_id'] = chat_update.last_ids[id];
        }
        $.post(url, data, zira_bind(this, function(response){
            if (!response) return;
            if (response.last_id) {
                if (typeof(chat_update.last_ids[id])!="undefined" && chat_update.last_ids[id]>=response.last_id) response.messages = null; 
                chat_update.last_ids[id] = response.last_id;
            }
            if (response.messages && response.messages.length) {
                try {
                    if ($(this).find('.widget-chat-messages').get(0).scrollTop + $(this).find('.widget-chat-messages').height() >= $(this).find('.widget-chat-messages').get(0).scrollHeight-30) {
                        scrollDown = true;
                    }
                } catch(e) {}
                for(var i=0; i<response.messages.length; i++) {
                    $(this).find('.widget-chat-messages').append(response.messages[i]);
                }
                var limit = 100;
                var total = $(this).find('.widget-chat-messages .chat-message-wrapper').length;
                if (total>limit) {
                    $(this).find('.widget-chat-messages .chat-message-wrapper:lt('+(total-limit)+')').remove();
                }
                zira_parse('.widget-chat-wrapper .parse-content');
                try {
                    emoji_parse('.widget-chat-wrapper .parse-content');
                    if (scrollDown) {
                        $(this).find('.widget-chat-messages').get(0).scrollTop = $(this).find('.widget-chat-messages').get(0).scrollHeight;
                    }
                } catch(e) {}
            }
            chat_update.timers[id] = window.setTimeout(zira_bind(this, function(){
                $(this).zira_chat();
            }), delay*1000);
        }), 'json');
    };
})(jQuery);
