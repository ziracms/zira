var dash_chat_widget_install = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length>0) {
        desk_window_request(this, url('chat/dash/install'),{'chats':selected.items});
    }
};

var dash_chat_chat_select = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length>0) {
        this.disableItemsByProperty('typo','install');
        for (var i=0; i<selected.length; i++) {
            if (typeof(selected[i].inactive)!="undefined" && selected[i].inactive) {
                this.enableItemsByProperty('typo','install');
                break;
            }
        }
    }
};

var dash_chat_chat_load = function() {
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].inactive)!="undefined" && this.options.bodyItems[i].inactive) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
};

var dash_chat_chat_create = function() {
    var data = {
        'data': {
            'items': []
        },
        'reload': this.className,
        'onClose':function(){
            desk_window_reload_all(this.options.reload);
        }
    };
    desk_call(dash_chat_chat_wnd, null, data);
};

var dash_chat_chat_edit = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        var data = {
            'data':{
                'items': [selected[0].data]
            },
            'reload': this.className,
            'onClose':function(){
                desk_window_reload_all(this.options.reload);
            }
        };
        desk_call(dash_chat_chat_wnd, null, data);
    }
};

var dash_chat_messages = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        var limit = 10;
        if (typeof dash_chat_messages_limit != "undefined") limit = dash_chat_messages_limit;
        var data = {
            'data':{
                'items': [selected[0].data],
                'page': 1,
                'limit': limit
            },
            'reload': this.className,
            'onClose':function(){
                desk_window_reload_all(this.options.reload);
            }
        };
        desk_call(dash_chat_messages_wnd, null, data);
    }
};

var dash_chat_message_create = function() {
    var data = {
        'data':{
            'chat_id': this.options.data.items[0]
        },
        'reload': this.className,
        'onClose':function(){
            desk_window_reload_all(this.options.reload);
        }
    };
    desk_call(dash_chat_message_wnd, null, data);
};

var dash_chat_message_edit = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        var data = {
            'data':{
                'chat_id': this.options.data.items[0],
                'items': [selected[0].data]
            },
            'reload': this.className,
            'onClose':function(){
                desk_window_reload_all(this.options.reload);
            }
        };
        desk_call(dash_chat_message_wnd, null, data);
    }
};

var dash_chat_message_preview = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_window_request(this, url('chat/dash/preview'),{'item':selected[0].data}, this.bind(this, function(response){
            if (!response || typeof(response.chat)=="undefined" || typeof(response.user)=="undefined" || typeof(response.content)=="undefined") return;
            desk_message(response.chat+'<div style="color:black;margin: 16px 0px;padding:16px 16px;background:#fff;box-shadow:inset 0px 0px 6px #ddd">'+response.content+'</div>'+t('Author')+': '+response.user);
            try {
                zira_parse_content();
            } catch(e) {}
        }));
    }
};