var dash_modules_select = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        this.disableItemsByProperty('typo','deactivate');
        if (typeof(selected[0].activated)!="undefined" && selected[0].activated) {
            this.enableItemsByProperty('typo','deactivate');
        }
        this.disableItemsByProperty('typo','activate');
        this.disableItemsByProperty('typo','install');
        this.disableItemsByProperty('typo','uninstall');
            if (typeof(selected[0].activated)!="undefined" && !selected[0].activated) {
            if (typeof(selected[0].installable)=="undefined" || !selected[0].installable || typeof(selected[0].installed)=="undefined" || selected[0].installed) this.enableItemsByProperty('typo','activate');
            if (typeof(selected[0].installable)!="undefined" && selected[0].installable) {
                if (typeof(selected[0].installed)!="undefined" && !selected[0].installed) this.enableItemsByProperty('typo','install');
                if (typeof(selected[0].installed)!="undefined" && selected[0].installed) this.enableItemsByProperty('typo','uninstall');
            }
        }
    }
};

var dash_modules_load = function() {
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].activated)!="undefined" && !this.options.bodyItems[i].activated) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
};

var dash_modules_close = function() {
    desk_window_reload_all(dash_modules_widgets_wnd);
};

var dash_modules_activate = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length==1) {
        desk_window_request(this, url('dash/system/module'),{'module':selected.items[0], 'active': 1}, function(){
            desk_confirm(t('Page reload required. Reload now ?'), function(){
                window.location.reload();
            });
        });
    }
};

var dash_modules_deactivate = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length==1) {
        desk_window_request(this, url('dash/system/module'),{'module':selected.items[0], 'active': 0}, function(){
            desk_confirm(t('Page reload required. Reload now ?'), function(){
                window.location.reload();
            });
        });
    }
};

var dash_modules_install = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length==1) {
        desk_window_request(this, url('dash/system/module'),{'module':selected.items[0], 'install': 1});
    }
};

var dash_modules_uninstall = function() {
    desk_confirm(t('Remove module from database ?'), this.bind(this, function() {
        var selected = desk_window_selected(this);
        if (selected && typeof(selected.items)!="undefined" && selected.items.length==1) {
            desk_window_request(this, url('dash/system/module'),{'module':selected.items[0], 'install': 0});
        }
    }));
};