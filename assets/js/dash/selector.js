var dash_selector_body_item_callback = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].type)!="undefined" && selected[0].type=='folder') {
        desk_window_edit_item(this);
    } else {
        desk_window_close(this);
    }
};

var dash_selector_select = function() {
    var selected = this.getSelectedContentItems();
    this.disableItemsByProperty('action','select');
    if (selected && selected.length>0) {
        for (var i=0; i<selected.length; i++) {
            if (typeof(selected[i].type)!="undefined" && selected[i].type!='folder') {
                this.enableItemsByProperty('action','select');
                break;
            }
        }
    }
};

var dash_selector_choose = function() {
    var unselect = [];
    var selected = this.getSelectedContentItems();
    for (var i=0; i<selected.length; i++) {
        if (typeof(selected[i].type)!="undefined" && selected[i].type=='folder') unselect.push(selected[i]);
    }
    for (var i=0; i<unselect.length; i++) {
        this.unselectContentItem(unselect[i].element);
    }
    desk_window_close(this);
};

var desk_file_selector = function(callback, object) {
    try {
        var data = {
            'onClose': function() {
                var selected = this.getSelectedContentItems();
                if (selected && selected.length>0 && typeof(callback)!="undefined") {
                    if (typeof(object) == "undefined") object = this;
                    callback.call(object, selected);
                    if ((object instanceof DashWindow) && object != this) {
                        object.focus();
                        object.setZ(this.getZ());
                    }
                }
            }
        };
        desk_call(dash_selector_wnd, null, data);
    } catch(e) {
        desk_error(t('An error occurred'));
    }
};