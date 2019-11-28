var dash_push_settings_load = function() {
    desk_window_form_init(this);
    var privInput = $(this.element).find('.priv-key-input');
    var pubInput = $(this.element).find('.pub-key-input');
    var genBtn = this.findToolbarItemByProperty('action', 'generate');
    if ($(privInput).length == 0 || $(pubInput).length == 0 || !genBtn) return;
    if ($(privInput).val().length == 0 && $(pubInput).val().length == 0) {
        $(genBtn.element).removeClass('disabled');
    }
};

var dash_push_generate_keys = function() {
    desk_post(url('push/dash/generate'),{'token':token()}, this.bind(this, function(response){
        if (response && typeof response.privateKey != "undefined" && typeof response.publicKey != "undefined") {
            var privInput = $(this.element).find('.priv-key-input');
            var pubInput = $(this.element).find('.pub-key-input');
            var genBtn = this.findToolbarItemByProperty('action', 'generate');
            if ($(privInput).length == 0 || $(pubInput).length == 0 || !genBtn) return;
            $(privInput).val(response.privateKey);
            $(pubInput).val(response.publicKey);
            $(genBtn.element).addClass('disabled');
        } else {
            desk_error(t('An error occurred'))
        }
    }));
};