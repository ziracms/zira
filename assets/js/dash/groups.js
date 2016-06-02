var dash_groups_load = function() {
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].activated)!="undefined" && this.options.bodyItems[i].activated==dash_groups_status_not_active) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
};

var dash_groups_delete = function() {
    var data = desk_window_selected(this);
    desk_window_request(this, url('dash/groups/delete'), data);
};

var dash_groups_close = function() {
    desk_window_reload_all(dash_groups_users_wnd);
};

var dash_groups_rename = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_prompt(t('Enter name'), this.bind(this, function(name){
            desk_window_request(this, url('dash/groups/rename'),{'name':name, 'group_id':selected[0].data});
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(selected[0].title);
    }
};

var dash_groups_deactivate = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length>0) {
        desk_window_request(this, url('dash/groups/deactivate'),{'groups':selected.items});
    }
};

var dash_groups_activate = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length>0) {
        desk_window_request(this, url('dash/groups/activate'),{'groups':selected.items});
    }
};

var dash_groups_select = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length>0) {
        this.disableItemsByProperty('typo','deactivate');
        for (var i=0; i<selected.length; i++) {
            if (typeof(selected[i].activated)!="undefined" && selected[i].activated!=dash_groups_status_not_active) {
                this.enableItemsByProperty('typo','deactivate');
                break;
            }
        }
        this.disableItemsByProperty('typo','activate');
        for (var i=0; i<selected.length; i++) {
            if (typeof(selected[i].activated)!="undefined" && selected[i].activated!=dash_groups_status_active) {
                this.enableItemsByProperty('typo','activate');
                break;
            }
        }
        this.disableItemsByProperty('typo','delete');
        for (var i=0; i<selected.length; i++) {
            if (typeof(selected[i].data)!="undefined" && selected[i].data!=dash_groups_superadmin_id && selected[i].data!=dash_groups_admin_id && selected[i].data!=dash_groups_user_id) {
                this.enableItemsByProperty('typo','delete');
                break;
            }
        }
    }
    if (selected && selected.length==1 && typeof(selected[0].data)!="undefined" && selected[0].data==dash_groups_superadmin_id) {
        this.disableItemsByProperty('typo','permissions');
    }
};

var dash_groups_create = function() {
    desk_prompt(t('Enter name'), this.bind(this, function(name){
        desk_window_request(this, url('dash/groups/create'),{'name':name});
    }));
};

var dash_groups_permissions = function() {
    var data = {'data':desk_window_selected(this,1)};
    if (typeof(data.data.items)!="undefined" && data.data.items[0]==dash_groups_superadmin_id) {
        desk_error(t('Permission denied'));
        return;
    }
    desk_call(dash_groups_permission_wnd, null, data);
};