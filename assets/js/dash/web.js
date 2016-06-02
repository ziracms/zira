var dash_web_home_page = function() {
    var url = dash_web_home_page_url;
    this.updateTitle(url);
    $(this.findToolbarItemByProperty('action', 'url').element).val(url);
    var referer = 'referer=dash';
    if (url.indexOf('?')<0) url+='?'+referer;
    else url+='&'+referer;
    $(this.content).find('#dashboard-browser-iframe').attr('src', url);
};

var dash_web_current_page = function() {
    var url = window.location.href;
    this.updateTitle(url);
    $(this.findToolbarItemByProperty('action', 'url').element).val(url);
    var referer = 'referer=dash';
    if (url.indexOf('?')<0) url+='?'+referer;
    else url+='&'+referer;
    $(this.content).find('#dashboard-browser-iframe').attr('src', url);
};

var dash_web_input = function(element) {
    var url = $(element).val();
    if (url.length==0) return;
    if (url.indexOf('http')!==0) { url = 'http://'+url; $(element).val(url); }
    if (url == dash_web_admin_url) return;
    this.updateTitle(url);
    var referer = 'referer=dash';
    if (url.indexOf('?')<0) url+='?'+referer;
    else url+='&'+referer;
    $(this.content).find('#dashboard-browser-iframe').attr('src', url);
};

var dash_web_reload = function() {
    var url = $(this.content).find('#dashboard-browser-iframe').attr('src');
    $(this.content).find('#dashboard-browser-iframe').attr('src', url);
};

var dash_web_focus = function() {
    $(this.content).find('.dashboard-browser-overlay').hide();
};

var dash_web_blur = function() {
    $(this.content).find('.dashboard-browser-overlay').show()
};

var dash_web_open = function() {
    if (window.location.href.indexOf(dash_web_admin_url)==0) {
        this.disableItemsByProperty('action','current');
    }
    if (typeof(this.options.data.url)!="undefined") {
        var url;
        if (this.options.data.url.indexOf('http')!==0) {
            url = dash_web_home_page_url + '/' + this.options.data.url;
        } else {
            url = this.options.data.url;
        }
        this.updateTitle(url);
        $(this.findToolbarItemByProperty('action', 'url').element).val(url);
        var referer = 'referer=dash';
        if (url.indexOf('?')<0) url+='?'+referer;
        else url+='&'+referer;
        $(this.content).find('#dashboard-browser-iframe').attr('src', url);
    }
};

var desk_web_browser = function(url) {
    try {
        var data = {
            'data': {
                'url': url
            }
        };
        desk_call(dash_web_wnd, null, data);
    } catch(e) {
        desk_error(t('An error occurred'));
    }
};

var desk_web_zira = function() {
    desk_web_browser('h'+'t' +'t'+'p'+ ':'+'/' +'/' +'d'+'r' +'o' +'1'+'d' +'.'+'r' +'u'+'/' +'z'+'i' +'r'+'a');
};