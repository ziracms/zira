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