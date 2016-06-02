var dash_html_load = function() {
    this.updateTitle(this.options.data.file);
};

var desk_html_editor = function(file) {
    try {
        var data = {'file':file};
        desk_call(dash_html_wnd, null, {'data':data});
    } catch(e) {
        desk_error(t('An error occurred'));
    }
};