var dash_widgets_load = function() {
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].activated)!="undefined" && this.options.bodyItems[i].activated==dash_widget_status_not_active_id) {
            $(this.options.bodyItems[i].element).addClass('inactive');
            $(this.options.bodyItems[i].element).children('img').css('opacity',.5);
        }
    }
};

var dash_widgets_delete = function() {
    var data = desk_window_selected(this);
    desk_window_request(this, url('dash/index/delete'), data);
};

var dash_widgets_deactivate = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length>0) {
        desk_window_request(this, url('dash/widgets/deactivate'),{'widgets':selected.items});
    }
};

var dash_widgets_activate = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length>0) {
        desk_window_request(this, url('dash/widgets/activate'),{'widgets':selected.items});
    }
};

var dash_widgets_select = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length>0) {
        this.disableItemsByProperty('typo','copy');
        this.disableItemsByProperty('action','delete');
        for (var i=0; i<selected.length; i++) {
            if (typeof(selected[i].installed)!="undefined" && selected[i].installed) {
                this.enableItemsByProperty('action','delete');
                if (selected && selected.length==1) {
                    this.enableItemsByProperty('typo','copy');
                }
                break;
            }
        }
        this.disableItemsByProperty('typo','deactivate');
        this.disableItemsByProperty('typo','up');
        this.disableItemsByProperty('typo','down');
        for (var i=0; i<selected.length; i++) {
            if (typeof(selected[i].activated)!="undefined" && selected[i].activated!=dash_widget_status_not_active_id) {
                this.enableItemsByProperty('typo','deactivate');
                this.enableItemsByProperty('action','delete');
                if (selected && selected.length==1) {
                    this.enableItemsByProperty('typo','up');
                    this.enableItemsByProperty('typo','down');
                    this.enableItemsByProperty('typo','copy');
                }
                break;
            }
        }
        this.disableItemsByProperty('typo','activate');
        for (var i=0; i<selected.length; i++) {
            if (typeof(selected[i].activated)!="undefined" && selected[i].activated!=dash_widget_status_active_id) {
                this.enableItemsByProperty('typo','activate');
                break;
            }
        }
    }
};

var dash_widgets_up = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        var selected_item = selected[0];
        var changed = false;
        for (var i=0; i<this.options.bodyItems.length; i++) {
            if (this.options.bodyItems[i]==selected_item && i>0) {
                changed = this.options.bodyItems[i-1];
                break;
            }
        }
        if (!changed) return;
        var sorted_widgets = [selected_item.data, changed.data];
        desk_window_request(this, url('dash/widgets/sort'),{'widgets':sorted_widgets});
    }
};

var dash_widgets_down = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        var selected_item = selected[0];
        var changed = false;
        for (var i=0; i<this.options.bodyItems.length; i++) {
            if (this.options.bodyItems[i]==selected_item && i<this.options.bodyItems.length-1 && this.options.bodyItems[i+1].activated==dash_widget_status_active_id) {
                changed = this.options.bodyItems[i+1];
                break;
            }
        }
        if (!changed) return;
        var sorted_widgets = [changed.data, selected_item.data];
        desk_window_request(this, url('dash/widgets/sort'),{'widgets':sorted_widgets});
    }
};

var dash_widgets_placeholders_filter = function(element) {
    var placeholder = this.options.data.placeholder;
    var id = $(element).attr('id');
    var item = this.findMenuItemByProperty('id',id);
    if (item && typeof(item.placeholder)!="undefined") {
        if (item.placeholder!=placeholder) {
            this.options.data.placeholder=item.placeholder;
            desk_window_reload(this);
            $(element).parents('ul').find('.glyphicon-ok').removeClass('glyphicon-ok').addClass('glyphicon-filter');
            $(element).find('.glyphicon').removeClass('glyphicon-filter').addClass('glyphicon-ok');
        } else {
            this.options.data.placeholder=null;
            desk_window_reload(this);
            $(element).parents('ul').find('.glyphicon-ok').removeClass('glyphicon-ok').addClass('glyphicon-filter');
        }
    }
};

var dash_widgets_copy = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_window_request(this, url('dash/widgets/copy'),{'widget':selected[0].data});
    }
};

var dash_widgets_edit = function() {
    var data = {
        'data':desk_window_selected(this,1),
        'reload': this.className,
        'onClose':function(){
            desk_window_reload_all(this.options.reload);
        }
    };
    desk_call(dash_widgets_widget_wnd, null, data);
};

var dash_widgets_drag = function() {
    this.isContentDragging = false;
    this.dragStartY = null;
    this.dragStartItem = null;
    this.dragOverItem = null;
    this.dragReplaced = false;
    this.dragImage = new Image(); this.dragImage.src=dash_widgets_blank_src;
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
        if ($(item).hasClass('inactive') || $(item).parent('li').hasClass('tmp-drag-widget-item')) return;
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
            var tmp = '<li class="tmp-drag-widget-item"></li>';
            if (e.originalEvent.pageY > this.dragStartY) {
                $(this.content).find('#'+this.dragOverItem).parent('li').after(tmp);
            } else {
                $(this.content).find('#'+this.dragOverItem).parent('li').before(tmp);
            }
            $(this.content).find('li.tmp-drag-widget-item').replaceWith($(this.content).find('#'+this.dragStartItem).parent('li'));
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
            desk_window_request(this, url('dash/widgets/drag'),{'widgets':dragged,'orders':orders});
        }
    }));
    $(this.content).bind('dragend',this.bind(this,function(e){
        $(this.content).find('#'+this.dragStartItem).parent('li').css('opacity',1);
        this.isContentDragging = false;
        this.dragStartY = null;
        this.dragStartItem = null;
        this.dragOverItem = null;
        this.dragReplaced = false;
        $(this.content).find('li.tmp-drag-widget-item').remove();
    }));
};

var dash_widgets_drop = function(element) {
    if (!(element instanceof FileList) && typeof(element.parent)!="undefined" && element.parent=='files' && typeof(element.type)!="undefined" && (element.type=='txt' || element.type=='html' || element.type=='image')) {
        var path = element.data;
        desk_window_request(this, url('dash/widgets/block'),{'path':path});
    }
};