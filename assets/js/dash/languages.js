var dash_languages_load = function() {
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].activated)!="undefined" && !this.options.bodyItems[i].activated) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
};

var dash_languages_select = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        this.disableItemsByProperty('typo','deactivate');
        if (typeof(selected[0].activated)!="undefined" && selected[0].activated && typeof(selected[0].is_default)!="undefined" && !selected[0].is_default) {
            this.enableItemsByProperty('typo','deactivate');
        }
        this.disableItemsByProperty('typo','activate');
        if (typeof(selected[0].activated)!="undefined" && !selected[0].activated) {
            this.enableItemsByProperty('typo','activate');
        }
        this.disableItemsByProperty('typo','default');
        if (typeof(selected[0].activated)!="undefined" && selected[0].activated && typeof(selected[0].is_default)!="undefined" && !selected[0].is_default) {
            this.enableItemsByProperty('typo','default');
        }
        this.disableItemsByProperty('typo','panel_default');
        if (typeof(selected[0].is_panel_default)!="undefined" && !selected[0].is_panel_default) {
            this.enableItemsByProperty('typo','panel_default');
        }
        this.disableItemsByProperty('typo','up');
        this.disableItemsByProperty('typo','down');
        if (typeof(selected[0].activated)!="undefined" && selected[0].activated) {
            this.enableItemsByProperty('typo','up');
            this.enableItemsByProperty('typo','down');
        }
    }
};

var dash_languages_deactivate = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length==1) {
        desk_window_request(this, url('dash/languages/deactivate'),{'language':selected.items[0]});
    }
};

var dash_languages_activate = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length==1) {
        desk_window_request(this, url('dash/languages/activate'),{'language':selected.items[0]});
    }
};

var dash_languages_default = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length==1) {
        desk_window_request(this, url('dash/languages/setdefault'),{'language':selected.items[0]});
    }
};

var dash_languages_panel_default = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length==1) {
        desk_window_request(this, url('dash/languages/setpaneldefault'),{'language':selected.items[0]});
    }
};

var dash_languages_up = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length==1) {
        desk_window_request(this, url('dash/languages/up'),{'language':selected.items[0]});
    }
};

var dash_languages_down = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length==1) {
        desk_window_request(this, url('dash/languages/down'),{'language':selected.items[0]});
    }
};

var dash_languages_translates = function() {
    if (typeof(this.options.data.db_translated_enabled)!="undefined" && !this.options.data.db_translated_enabled) {
        desk_error(t('DB translates are not enabled'));
        return;
    }
    var data = {'data':desk_window_selected(this,1)};
    desk_call(dash_languages_translates_wnd, null, data);
};

var dash_languages_drag = function() {
    this.isContentDragging = false;
    this.dragStartY = null;
    this.dragStartItem = null;
    this.dragOverItem = null;
    this.dragReplaced = false;
    this.dragImage = new Image(); this.dragImage.src=dash_languages_blank_src;
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
        if ($(item).hasClass('inactive') || $(item).parent('li').hasClass('tmp-drag-language-item')) return;
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
            var tmp = '<li class="tmp-drag-language-item"></li>';
            if (e.originalEvent.pageY > this.dragStartY) {
                $(this.content).find('#'+this.dragOverItem).parent('li').after(tmp);
            } else {
                $(this.content).find('#'+this.dragOverItem).parent('li').before(tmp);
            }
            $(this.content).find('li.tmp-drag-language-item').replaceWith($(this.content).find('#'+this.dragStartItem).parent('li'));
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
            desk_window_request(this, url('dash/languages/drag'),{'languages':dragged,'orders':orders});
        }
    }));
    $(this.content).bind('dragend',this.bind(this,function(e){
        $(this.content).find('#'+this.dragStartItem).parent('li').css('opacity',1);
        this.isContentDragging = false;
        this.dragStartY = null;
        this.dragStartItem = null;
        this.dragOverItem = null;
        this.dragReplaced = false;
        $(this.content).find('li.tmp-drag-language-item').remove();
    }));
};