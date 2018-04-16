var dash_logs_edit = function() {
    try { window.clearTimeout(this.timer); } catch(err) {};
    var selected = this.getSelectedContentItems();
    if (!selected || selected.length!=1) return;
    if (typeof(selected[0].type)!="undefined" && selected[0].type=='folder') return;
    var data = dsah_logs_dir + desk_ds +selected[0].data;
    desk_text_editor(data);
};

var dash_logs_load = function() {
    $(this.element).find('.logs-infobar').html('');  
};

var dash_logs_select = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length && selected.length==1 && (typeof(this.info_last_item)=="undefined" || this.info_last_item!=selected[0].data || $(this.element).find('.logs-infobar').html().length==0)) {
        this.info_last_item = selected[0].data;
        $(this.element).find('.logs-infobar').html('');
        try { window.clearTimeout(this.timer); } catch(err) {};
        this.timer = window.setTimeout(this.bind(this,function(){
            $(this.element).find('.logs-infobar').html('');
            var selected = this.getSelectedContentItems();
            if (!selected || !selected.length || selected.length!=1) return;
            desk_post(url('dash/system/info'),{'type':'logs','file':selected[0].data, 'token':token()}, this.bind(this, function(response){
                if (response && response.length>0) {
                    $(this.element).find('.logs-infobar').append('<div style="cursor:default;padding:0px;margin:5px 0px 0px"><span class="glyphicon glyphicon-info-sign"></span> '+t('Information')+':</div>');
                    $(this.element).find('.logs-infobar').append('<div style="border-top:1px solid #B3B6D1;border-bottom:1px solid #EDEDF6;height:1px;padding:0px;margin:5px 0px"></div>');
                    for (var i=0; i<response.length; i++) {
                        $(this.element).find('.logs-infobar').append('<div style="font-weight:normal;padding:2px 0px;cursor:default;text-overflow:ellipsis;overflow:hidden" title="'+response[i].split('>').slice(-1)[0]+'">'+response[i]+'</div>');
                    }
                }
            }));
        }),1000);
    }
};