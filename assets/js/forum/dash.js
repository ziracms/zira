var dash_forum_forums_select = function() {
    var selected = this.getSelectedContentItems();
    this.disableItemsByProperty('typo', 'page');
    if (selected && selected.length == 1 && typeof(selected[0].inactive) != "undefined" && !selected[0].inactive) {
        this.enableItemsByProperty('typo', 'page');
    }
};

var dash_forum_forums_load = function() {
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].inactive)!="undefined" && this.options.bodyItems[i].inactive) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
};

var dash_forum_category_filter = function(cat_id) {
    if (this.options.data.category_id == cat_id) return;
    var item = this.findMenuItemByProperty('category_id',cat_id);
    if (item) {
        $(item.element).parents('ul').find('.glyphicon-ok').removeClass('glyphicon-ok').addClass('glyphicon-filter');
        $(item.element).find('.glyphicon').removeClass('glyphicon-filter').addClass('glyphicon-ok');
    }
    this.options.data.category_id = cat_id;
    desk_window_reload(this);
};

var dash_forum_categories = function() {
    var data = {
        'data': {
            'items': []
        },
        'reload': this.className,
        'onClose':function(){
            desk_window_reload_all(this.options.reload);
        }
    };
    desk_call(dash_forum_categories_wnd, null, data);
};

var dash_forum_forum_create = function() {
    var data = {
        'data': {
            'items': [],
            'category_id': this.options.data.category_id
        },
        'reload': this.className,
        'onClose':function(){
            desk_window_reload_all(this.options.reload);
        }
    };
    desk_call(dash_forum_forum_wnd, null, data);
};

var dash_forum_forum_edit = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        var data = {
            'data':{
                'items': [selected[0].data],
                'category_id': this.options.data.category_id
            },
            'reload': this.className,
            'onClose':function(){
                desk_window_reload_all(this.options.reload);
            }
        };
        desk_call(dash_forum_forum_wnd, null, data);
    }
};

var dash_forum_categories_drag = function() {
    this.isContentDragging = false;
    this.dragStartY = null;
    this.dragStartItem = null;
    this.dragOverItem = null;
    this.dragReplaced = false;
    this.dragImage = new Image();
    this.dragImage.src=dash_forum_blank_src;
    $(this.content).bind('dragstart',this.bind(this,function(e){
        if (this.isDisabled()) return;
        if (typeof(e.originalEvent.target)=="undefined") return;
        if ($(e.originalEvent.target).parents('li').children('a').hasClass('inactive')) return;
        this.isContentDragging = true;
        this.dragStartY = e.originalEvent.pageY;
        this.dragStartItem = $(e.originalEvent.target).parents('li').children('a').attr('id');
        e.originalEvent.dataTransfer.setDragImage(this.dragImage,-10,0);
        $(this.content).find('#'+this.dragStartItem).parent('li').css('opacity',.5);
        for (var i=0; i<this.options.bodyItems.length; i++) {
            this.options.bodyItems[i].is_dragged = false;
        }
    }));
    $(this.content).bind('dragover',this.bind(this,function(e){
        if (this.isDisabled()) return;
        if (typeof(e.originalEvent.target)=="undefined" || !this.isContentDragging) return;
        var item = $(e.originalEvent.target).parents('li').children('a');
        if ($(item).length==0 || $(item).parents('#'+this.getId()).length==0) return;
        if ($(item).hasClass('inactive') || $(item).parent('li').hasClass('tmp-drag-category-item')) return;
        if (this.dragReplaced && $(item).attr('id') == this.dragStartItem) {
            var startItem = this.findBodyItemByProperty('id',this.dragStartItem);
            var endItem = this.findBodyItemByProperty('id',this.dragOverItem);
            if (startItem && endItem && typeof(startItem.sort_order)!="undefined" && typeof(endItem.sort_order)!="undefined") {
                var start_order = startItem.sort_order;
                var end_order = endItem.sort_order;
                startItem.sort_order = end_order;
                endItem.sort_order = start_order;
                startItem.is_dragged = true;
                endItem.is_dragged = true;
            }
            this.dragOverItem = null;
            this.dragStartY = e.originalEvent.pageY;
            this.dragReplaced = false;
        }
        if (this.dragStartItem!=$(item).attr('id') && this.dragOverItem!=$(item).attr('id')) {
            this.dragOverItem=$(item).attr('id');
            var tmp = '<li class="tmp-drag-category-item"></li>';
            if (e.originalEvent.pageY > this.dragStartY) {
                $(this.content).find('#'+this.dragOverItem).parent('li').after(tmp);
            } else {
                $(this.content).find('#'+this.dragOverItem).parent('li').before(tmp);
            }
            $(this.content).find('li.tmp-drag-category-item').replaceWith($(this.content).find('#'+this.dragStartItem).parent('li'));
            this.dragReplaced = true;
        }
    }));
    $(this.element).bind('drop',this.bind(this,function(e){
        if (this.isDisabled()) return;
        var dragged = [];
        var orders = [];
        for (var i=0; i<this.options.bodyItems.length; i++) {
            if (typeof(this.options.bodyItems[i].sort_order)!="undefined" && typeof(this.options.bodyItems[i].is_dragged)!="undefined" && this.options.bodyItems[i].is_dragged) {
                dragged.push(this.options.bodyItems[i].data);
                orders.push(this.options.bodyItems[i].sort_order);
            }
        }
        if (dragged.length>1 && orders.length>1) {
            desk_window_request(this, url('forum/dash/dragcategory'),{'categories':dragged,'orders':orders});
        }
    }));
    $(this.content).bind('dragend',this.bind(this,function(e){
        $(this.content).find('#'+this.dragStartItem).parent('li').css('opacity',1);
        this.isContentDragging = false;
        this.dragStartY = null;
        this.dragStartItem = null;
        this.dragOverItem = null;
        this.dragReplaced = false;
        $(this.content).find('li.tmp-drag-category-item').remove();
    }));
};

var dash_forum_forums_drag = function() {
    this.isContentDragging = false;
    this.dragStartY = null;
    this.dragStartItem = null;
    this.dragOverItem = null;
    this.dragReplaced = false;
    this.dragImage = new Image();
    this.dragImage.src=dash_forum_blank_src;
    $(this.content).bind('dragstart',this.bind(this,function(e){
        if (this.isDisabled()) return;
        if (typeof(e.originalEvent.target)=="undefined") return;
        if ($(e.originalEvent.target).parents('li').children('a').hasClass('inactive')) return;
        this.isContentDragging = true;
        this.dragStartY = e.originalEvent.pageY;
        this.dragStartItem = $(e.originalEvent.target).parents('li').children('a').attr('id');
        e.originalEvent.dataTransfer.setDragImage(this.dragImage,-10,0);
        $(this.content).find('#'+this.dragStartItem).parent('li').css('opacity',.5);
        for (var i=0; i<this.options.bodyItems.length; i++) {
            this.options.bodyItems[i].is_dragged = false;
        }
    }));
    $(this.content).bind('dragover',this.bind(this,function(e){
        if (this.isDisabled()) return;
        if (typeof(e.originalEvent.target)=="undefined" || !this.isContentDragging) return;
        var item = $(e.originalEvent.target).parents('li').children('a');
        if ($(item).length==0 || $(item).parents('#'+this.getId()).length==0) return;
        if ($(item).hasClass('inactive') || $(item).parent('li').hasClass('tmp-drag-forum-item')) return;
        if (this.dragReplaced && $(item).attr('id') == this.dragStartItem) {
            var startItem = this.findBodyItemByProperty('id',this.dragStartItem);
            var endItem = this.findBodyItemByProperty('id',this.dragOverItem);
            if (startItem && endItem && typeof(startItem.sort_order)!="undefined" && typeof(endItem.sort_order)!="undefined") {
                var start_order = startItem.sort_order;
                var end_order = endItem.sort_order;
                startItem.sort_order = end_order;
                endItem.sort_order = start_order;
                startItem.is_dragged = true;
                endItem.is_dragged = true;
            }
            this.dragOverItem = null;
            this.dragStartY = e.originalEvent.pageY;
            this.dragReplaced = false;
        }
        if (this.dragStartItem!=$(item).attr('id') && this.dragOverItem!=$(item).attr('id')) {
            this.dragOverItem=$(item).attr('id');
            var tmp = '<li class="tmp-drag-forum-item"></li>';
            if (e.originalEvent.pageY > this.dragStartY) {
                $(this.content).find('#'+this.dragOverItem).parent('li').after(tmp);
            } else {
                $(this.content).find('#'+this.dragOverItem).parent('li').before(tmp);
            }
            $(this.content).find('li.tmp-drag-forum-item').replaceWith($(this.content).find('#'+this.dragStartItem).parent('li'));
            this.dragReplaced = true;
        }
    }));
    $(this.element).bind('drop',this.bind(this,function(e){
        if (this.isDisabled()) return;
        var dragged = [];
        var orders = [];
        for (var i=0; i<this.options.bodyItems.length; i++) {
            if (typeof(this.options.bodyItems[i].sort_order)!="undefined" && typeof(this.options.bodyItems[i].is_dragged)!="undefined" && this.options.bodyItems[i].is_dragged) {
                dragged.push(this.options.bodyItems[i].data);
                orders.push(this.options.bodyItems[i].sort_order);
            }
        }
        if (dragged.length>1 && orders.length>1) {
            desk_window_request(this, url('forum/dash/dragforum'),{'forums':dragged,'orders':orders});
        }
    }));
    $(this.content).bind('dragend',this.bind(this,function(e){
        $(this.content).find('#'+this.dragStartItem).parent('li').css('opacity',1);
        this.isContentDragging = false;
        this.dragStartY = null;
        this.dragStartItem = null;
        this.dragOverItem = null;
        this.dragReplaced = false;
        $(this.content).find('li.tmp-drag-forum-item').remove();
    }));
};

var dash_forum_page = function() {
    var selected = this.getSelectedContentItems();
    if (selected.length==1 && typeof(selected[0].page)!="undefined") {
        window.location.href=url(selected[0].page);
    }
};

var dash_forum_settings = function() {
    desk_call(dash_forum_settings_wnd);
};

var dash_forum_threads = function() {
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
        desk_call(dash_forum_threads_wnd, null, data);
    }
};

var dash_forum_thread_create = function() {
    var data = {
        'data':{
            'forum_id': this.options.data.items[0],
            'category_id': this.options.data.category_id
        },
        'reload': this.className,
        'onClose':function(){
            desk_window_reload_all(this.options.reload);
        }
    };
    desk_call(dash_forum_thread_wnd, null, data);
};

var dash_forum_thread_edit = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        var data = {
            'data':{
                'forum_id': this.options.data.items[0],
                'category_id': this.options.data.category_id,
                'items': [selected[0].data]
            },
            'reload': this.className,
            'onClose':function(){
                desk_window_reload_all(this.options.reload);
            }
        };
        desk_call(dash_forum_thread_wnd, null, data);
    }
};

var dash_forum_messages = function() {
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
        desk_call(dash_forum_messages_wnd, null, data);
    }
};

var dash_forum_message_create = function() {
    var data = {
        'data':{
            'topic_id': this.options.data.items[0]
        },
        'reload': this.className,
        'onClose':function(){
            desk_window_reload_all(this.options.reload);
        }
    };
    desk_call(dash_forum_message_wnd, null, data);
};

var dash_forum_message_edit = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        var data = {
            'data':{
                'topic_id': this.options.data.items[0],
                'items': [selected[0].data]
            },
            'reload': this.className,
            'onClose':function(){
                desk_window_reload_all(this.options.reload);
            }
        };
        desk_call(dash_forum_message_wnd, null, data);
    }
};

var dash_forum_files = function() {
    desk_call(dash_forum_files_wnd);
};

var dash_forum_file_show = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_message(selected[0].path);
    }
};

var dash_forum_topic_activate = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_window_request(this, url('forum/dash/activatethread'), {'item': selected[0].data});
    }
};

var dash_forum_message_activate = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_window_request(this, url('forum/dash/activatemessage'), {'item': selected[0].data});
    }
};

var dash_forum_topic_close = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_window_request(this, url('forum/dash/closethread'), {'item': selected[0].data});
    }
};

var dash_forum_topic_stick = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_window_request(this, url('forum/dash/stickthread'), {'item': selected[0].data});
    }
};

var dash_forum_topics_select = function() {
    var selected = this.getSelectedContentItems();
    this.disableItemsByProperty('typo', 'close');
    this.disableItemsByProperty('typo', 'stick');
    this.disableItemsByProperty('typo', 'activate');
    this.disableItemsByProperty('typo', 'page');
    if (selected && selected.length == 1 && typeof(selected[0].inactive) != "undefined" && !selected[0].inactive && typeof(selected[0].published) != "undefined" && selected[0].published) {
        this.enableItemsByProperty('typo', 'close');
    }
    if (selected && selected.length == 1 && typeof(selected[0].sticky) != "undefined" && !selected[0].sticky && typeof(selected[0].published) != "undefined" && selected[0].published) {
        this.enableItemsByProperty('typo', 'stick');
    }
    if (selected && selected.length == 1 && typeof(selected[0].published) != "undefined" && !selected[0].published) {
        this.enableItemsByProperty('typo', 'activate');
    }
    if (selected && selected.length == 1 && typeof(selected[0].published) != "undefined" && selected[0].published) {
        this.enableItemsByProperty('typo', 'page');
    }

    if (selected && selected.length && selected.length==1 && (typeof(this.info_last_item)=="undefined" || this.info_last_item!=selected[0].data || $(this.element).find('.topics-infobar').html().length==0)) {
        this.info_last_item = selected[0].data;
        $(this.element).find('.topics-infobar').html('');
        try { window.clearTimeout(this.timer); } catch(err) {};
        this.timer = window.setTimeout(this.bind(this,function(){
            $(this.element).find('.topics-infobar').html('');
            var selected = this.getSelectedContentItems();
            if (!selected || !selected.length || selected.length!=1) return;
            desk_post(url('forum/dash/topicinfo'),{'topic_id':selected[0].data, 'token':token()}, this.bind(this, function(response){
                if (response && response.length>0) {
                    $(this.element).find('.topics-infobar').append('<div style="cursor:default;padding:0px;margin:10px 0px 0px"><span class="glyphicon glyphicon-info-sign"></span> '+t('Information')+':</div>');
                    $(this.element).find('.topics-infobar').append('<div style="border-top:1px solid #B3B6D1;border-bottom:1px solid #EDEDF6;height:1px;padding:0px;margin:10px 0px"></div>');
                    for (var i=0; i<response.length; i++) {
                        var title = response[i].replace(/^.+title="([^"]+?)".+$/, '$1');
                        $(this.element).find('.topics-infobar').append('<div style="font-weight:normal;padding:2px 0px;cursor:default;text-overflow:ellipsis;overflow:hidden" title="'+title+': '+response[i].split('>').slice(-1)[0]+'">'+response[i]+'</div>');
                    }
                }
            }));
        }),1000);
    }
};

var dash_forum_topics_load = function() {
    $(this.element).find('.topics-infobar').html('');
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].published)!="undefined" && !this.options.bodyItems[i].published) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
};

var dash_forum_messages_select = function() {
    var selected = this.getSelectedContentItems();
    this.disableItemsByProperty('typo', 'activate');
    if (selected && selected.length == 1 && typeof(selected[0].published) != "undefined" && !selected[0].published) {
        this.enableItemsByProperty('typo', 'activate');
    }
};

var dash_forum_messages_load = function() {
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].published)!="undefined" && !this.options.bodyItems[i].published) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
};

var dash_forum_message_preview = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_window_request(this, url('forum/dash/preview'),{'item':selected[0].data}, this.bind(this, function(response){
            if (!response || typeof(response.topic)=="undefined" || typeof(response.user)=="undefined" || typeof(response.content)=="undefined") return;
            var attaches = '';
            if (typeof(response.attaches)!="undefined" && response.attaches.length>0) {
                attaches = '<div style="color:black;margin: 16px 0px;padding:16px 16px;background:#fff;box-shadow:inset 0px 0px 6px #ddd">'+response.attaches+'</div>';
            }
            desk_message(response.topic+'<div style="color:black;margin: 16px 0px;padding:16px 16px;background:#fff;box-shadow:inset 0px 0px 6px #ddd">'+response.content+'</div>'+attaches+t('Author')+': '+response.user);
            try {
                zira_parse_content();
            } catch(e) {}
        }));
    }
};

var dash_forum_categories_language = function(element) {
    var language = this.options.data.language;
    var id = $(element).attr('id');
    var item = this.findMenuItemByProperty('id',id);
    if (item && typeof(item.language)!="undefined") {
        if (item.language!=language) {
            this.options.data.language=item.language;
            desk_window_reload(this);
            $(element).parents('ul').find('.glyphicon-ok').removeClass('glyphicon-ok').addClass('glyphicon-filter');
            $(element).find('.glyphicon').removeClass('glyphicon-filter').addClass('glyphicon-ok');
        } else {
            this.options.data.language='';
            desk_window_reload(this);
            $(element).parents('ul').find('.glyphicon-ok').removeClass('glyphicon-ok').addClass('glyphicon-filter');
        }
    }
};

var dash_forum_forums_language = function(element) {
    return dash_forum_categories_language.call(this, element);
};

var dash_forum_topics_language = function(element) {
    return dash_forum_categories_language.call(this, element);
};