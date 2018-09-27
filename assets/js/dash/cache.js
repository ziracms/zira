var dash_cache_load = function() {
    $(this.element).find('.cache-infobar').html('');
};

var dash_cache_select = function() {
   var selected = this.getSelectedContentItems();
   if (selected && selected.length && selected.length==1 && (typeof(this.info_last_item)=="undefined" || this.info_last_item!=selected[0].data || $(this.element).find('.cache-infobar').html().length==0)) {
       this.info_last_item = selected[0].data;
       $(this.element).find('.cache-infobar').html('');
       try { window.clearTimeout(this.timer); } catch(err) {}
       this.timer = window.setTimeout(this.bind(this,function(){
           $(this.element).find('.cache-infobar').html('');
           var selected = this.getSelectedContentItems();
           if (!selected || !selected.length || selected.length!=1) return;
           desk_post(url('dash/system/info'),{'type':'cache','file':selected[0].data, 'token':token()}, this.bind(this, function(response){
               if (response && response.length>0) {
                   $(this.element).find('.cache-infobar').append('<div style="cursor:default;padding:0px;margin:5px 0px 0px"><span class="glyphicon glyphicon-info-sign"></span> '+t('Information')+':</div>');
                   $(this.element).find('.cache-infobar').append('<div class="devider" style="height:1px;padding:0px;margin:5px 0px"></div>');
                   for (var i=0; i<response.length; i++) {
                       $(this.element).find('.cache-infobar').append('<div style="font-weight:normal;padding:2px 0px;cursor:default;text-overflow:ellipsis;overflow:hidden" title="'+response[i].split('>').slice(-1)[0]+'">'+response[i]+'</div>');
                   }
               }
           }));
       }),1000);
   }
};

var dash_cache_clear = function() {
    desk_window_request(this, url('dash/system/cache'), {});
};