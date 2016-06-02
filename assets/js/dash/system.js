var dash_system_dump = function() {
    var a=document.createElement('a');
    a.href=url('dash/system/dump');
    document.body.appendChild(a);
    HTMLElementClick.call(a);
    document.body.removeChild(a);
};

var dash_system_cache = function() {
    desk_window_request(this, url('dash/system/cache'), {});
};

var dash_system_files = function() {
    desk_window_request(this, url('dash/system/tree'),{},this.bind(this, function(response){
        if (response && response.length>0) {
            var content = '<ul class="system-options-list" style="margin:20px;white-space:nowrap;">';
            for (var i=0; i<response.length; i++) {
                var color = '#e9dff5';
                if (i%2!=0) color='#f8eef5';
                content += '<li style="background-color:'+color+';padding:5px 10px">'+response[i]+'</li>';
            }
            content += '</ul>';
            if ($(this.content).find('.system-options-list').length==0) {
                this.clearBodyContent();
                this.appendBodyContent(content);
            } else {
                $(this.content).find('.system-options-list').replaceWith(content);
            }
        }
    }));
};

var dash_system_load = function() {
    $.get(baseUrl('dash/index/ping'),{}, this.bind(this, function(){
        $(this.content).find('#sys-info-clean-url-option').parent().children('.glyphicon').removeClass('glyphicon-question-sign').addClass('glyphicon-ok-sign system-ok');
        $(this.content).find('#sys-info-clean-url-option').text(t('supported'));
    }),'json').fail(this.bind(this, function(){
        $(this.content).find('#sys-info-clean-url-option').parent().children('.glyphicon').removeClass('glyphicon-question-sign').addClass('glyphicon-warning-sign system-warning');
        $(this.content).find('#sys-info-clean-url-option').text(t('not supported'));
    }));
};