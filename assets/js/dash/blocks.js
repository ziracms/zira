var dash_blocks_install = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length>0) {
        desk_window_request(this, url('dash/system/blocks'),{'blocks':selected.items});
    }
};

var dash_blocks_select = function() {
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

var dash_blocks_text = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length==1) {
        var data = {'items':selected.items};
        desk_call(dash_blocks_blocktext, null, {'data':data});
    }
};

var dash_blocks_html = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length==1) {
        var data = {'items':selected.items};
        desk_call(dash_blocks_blockhtml, null, {'data':data});
    }
};

var dash_blocks_load = function() {
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].inactive)!="undefined" && this.options.bodyItems[i].inactive) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
};