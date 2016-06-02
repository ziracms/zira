var dash_cron_open = function() {
    desk_window_request(this, url('cron'), {}, this.bind(this, function(response){
        var output = '';
        if (response && response.length>0) {
            for (var i=0; i<response.length; i++) {
                output += '<p style="padding:10px 10px 0px">'+response[i].replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;')+'</p>';
            }
        }
        this.clearBodyContent();
        this.appendBodyContent(output);
    }));
};