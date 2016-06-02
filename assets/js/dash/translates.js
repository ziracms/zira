var dash_translates_create = function() {
    desk_multi_prompt(t('Enter string to translate'),this.bind(this, function(str) {
        if (str.length>0) {
            desk_window_request(this, url('dash/languages/addstring'), {'string': str,'language': this.options.data.items[0]});
        }
    }));
};

var dash_translates_edit = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_multi_prompt(t('Enter translate'),this.bind(this, function(str) {
            if (str.length>0) {
                desk_window_request(this, url('dash/languages/translate'), {'strid': selected[0].data,'translate': str,'language': this.options.data.items[0]});
            }
        }));
        $('#zira-multi-prompt-dialog textarea[name=modal-input]').val(selected[0].tooltip);
    }
};