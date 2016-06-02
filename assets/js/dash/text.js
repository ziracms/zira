var dash_text_load = function() {
    this.updateTitle(this.options.data.file);
};

var desk_text_editor = function(file) {
    try {
        var data = {'file':file};
        desk_call(dash_text_wnd, null, {'data':data});
    } catch(e) {
        desk_error(t('An error occurred'));
    }
};