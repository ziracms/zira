var dash_stat_access_row = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_window_request(this, url('stat/dash/access'),{'item':selected[0].data}, this.bind(this, function(result){
            desk_message(t('Request')+':<div style="color:black;padding:10px 16px">'+result.join('<br />')+'</div>');
        }));
    }
};

var dash_stat_requests = function() {
    if (this.options.data.access_log_enabled=='0') {
        desk_error(t('Requests logging is disabled'));
        return;
    }
    var data = {
        'data': {
            'items': []
        },
        'reload': this.className,
        'onClose':function(){
            desk_window_reload_all(this.options.reload);
        }
    };
    desk_call(dash_stat_requests_wnd, null, data);
};

var dash_stat_settings = function() {
    var data = {
        'data': {
            'items': []
        },
        'reload': this.className,
        'onClose':function(){
            desk_window_reload_all(this.options.reload);
        }
    };
    desk_call(dash_stat_settings_wnd, null, data);
};