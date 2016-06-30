var dash_votes_install = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length>0) {
        desk_window_request(this, url('vote/dash/install'),{'votes':selected.items});
    }
};

var dash_votes_select = function() {
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
    if (selected && selected.length && selected.length==1 && (typeof(this.info_last_item)=="undefined" || this.info_last_item!=selected[0].data || $(this.element).find('.vote-infobar').html().length==0)) {
        this.info_last_item = selected[0].data;
        $(this.element).find('.vote-infobar').html('');
        try { window.clearTimeout(this.timer); } catch(err) {};
        this.timer = window.setTimeout(this.bind(this,function(){
            $(this.element).find('.vote-infobar').html('');
            var selected = this.getSelectedContentItems();
            if (!selected || !selected.length || selected.length!=1) return;
            desk_post(url('vote/dash/info'),{'item':selected[0].data, 'token':token()}, this.bind(this, function(response){
                if (response && response.length>0) {
                    $(this.element).find('.vote-infobar').append('<div style="cursor:default;padding:0px;margin:10px 0px 0px"><span class="glyphicon glyphicon-info-sign"></span> '+t('Information')+':</div>');
                    $(this.element).find('.vote-infobar').append('<div style="border-top:1px solid #B3B6D1;border-bottom:1px solid #EDEDF6;height:1px;padding:0px;margin:10px 0px"></div>');
                    for (var i=0; i<response.length; i++) {
                        $(this.element).find('.vote-infobar').append('<div style="font-weight:normal;padding:2px 0px;cursor:default;text-overflow:ellipsis;overflow:hidden" title="'+response[i].split('>').slice(-1)[0]+'">'+response[i]+'</div>');
                    }
                }
            }));
        }),1000);
    }
};

var dash_votes_load = function() {
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].inactive)!="undefined" && this.options.bodyItems[i].inactive) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
    $(this.element).find('.vote-infobar').html('');
};

var dash_votes_options = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        var data = {
            'data':desk_window_selected(this,1),
            'reload': this.className,
            'onClose':function(){
                desk_window_reload_all(this.options.reload);
            }
        };
        desk_call(dash_votes_options_wnd, null, data);
    }
};

var dash_votes_add_option = function() {
    desk_prompt(t('Enter text'), this.bind(this, function(txt){
        desk_window_request(this, url('vote/dash/option'),{'content':txt, 'vote': this.options.data.items[0]});
    }));
};

var dash_votes_edit_option = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_prompt(t('Enter text'), this.bind(this, function(txt){
            desk_window_request(this, url('vote/dash/option'),{'content':txt, 'vote': this.options.data.items[0], 'option':selected[0].data});
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(selected[0].tooltip);
    }
};

var dash_votes_delete_options = function() {
    var selected = desk_window_selected(this);
    if (selected && selected.items.length>0) {
        desk_window_request(this, url('vote/dash/deloptions'), {'options':selected.items, 'vote': this.options.data.items[0]});
    }
};

var dash_vote_options_drag = function() {
    this.isContentDragging = false;
    this.dragStartY = null;
    this.dragStartItem = null;
    this.dragOverItem = null;
    this.dragReplaced = false;
    this.dragImage = new Image();
    this.dragImage.src=dash_votes_blank_src;
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
        if ($(item).hasClass('inactive') || $(item).parent('li').hasClass('tmp-drag-options-item')) return;
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
            var tmp = '<li class="tmp-drag-options-item"></li>';
            if (e.originalEvent.pageY > this.dragStartY) {
                $(this.content).find('#'+this.dragOverItem).parent('li').after(tmp);
            } else {
                $(this.content).find('#'+this.dragOverItem).parent('li').before(tmp);
            }
            $(this.content).find('li.tmp-drag-options-item').replaceWith($(this.content).find('#'+this.dragStartItem).parent('li'));
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
            desk_window_request(this, url('vote/dash/drag'),{'item':this.options.data.items[0], 'options':dragged,'orders':orders});
        }
    }));
    $(this.content).bind('dragend',this.bind(this,function(e){
        $(this.content).find('#'+this.dragStartItem).parent('li').css('opacity',1);
        this.isContentDragging = false;
        this.dragStartY = null;
        this.dragStartItem = null;
        this.dragOverItem = null;
        this.dragReplaced = false;
        $(this.content).find('li.tmp-drag-options-item').remove();
    }));
};

var dash_votes_results = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_window_request(this, url('vote/dash/results'),{'item':selected[0].data}, this.bind(this, function(result){
            desk_message(t('Vote results')+':<div style="color:black;padding:10px 16px">'+result.join('<br />')+'</div>');
        }));
    }
};