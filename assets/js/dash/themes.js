var dash_themes_select = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        this.disableItemsByProperty('typo','activate');
        if (typeof(selected[0].activated)!="undefined" && !selected[0].activated) {
            this.enableItemsByProperty('typo','activate');
        }
        this.disableItemsByProperty('typo','preview');
        if (typeof(selected[0].src)!="undefined" && selected[0].src.length>0 && selected[0].src.indexOf(dash_themes_blank_src)!=0) {
            this.enableItemsByProperty('typo','preview');
        }
        this.disableItemsByProperty('typo','panel');
        if (typeof(selected[0].is_panel_theme)!="undefined" && !selected[0].is_panel_theme) {
            this.enableItemsByProperty('typo','panel');
        }
    }
};

var dash_themes_activate = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length==1) {
        desk_window_request(this, url('dash/system/theme'),{'theme':selected.items[0]}, function(){
            desk_confirm(t('Page reload required. Reload now ?'), function(){
                window.location.reload();
            });
        });
    }
};

var dash_themes_panel = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length==1) {
        desk_window_request(this, url('dash/system/dashtheme'),{'theme':selected.items[0]}, function(){
            desk_confirm(t('Page reload required. Reload now ?'), function(){
                window.location.reload();
            });
        });
    }
};

var dash_themes_preview = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].src)!="undefined" && selected[0].src.length>0 && selected[0].src.indexOf(dash_themes_blank_src)!=0) {
        $('body').append('<a href="'+selected[0].src+'" data-lightbox="theme-preview" id="dashwindow-theme-preview-lightbox"></a>');
        $('#dashwindow-theme-preview-lightbox').trigger('click');
        $('#dashwindow-theme-preview-lightbox').remove();
    }
};