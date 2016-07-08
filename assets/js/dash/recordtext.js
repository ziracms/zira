var dash_recordtext_close = function() {
    try {
        window.clearInterval(this.timer);
        this.saveDraft();
    } catch(err) {}
    desk_window_reload_all(dash_recordtext_records_wnd);
};

var dash_recordtext_save = function() {
    var data = desk_window_content(this);
    desk_window_request(this, url('dash/index/save'), data);
    this.draftContent = $(this.content).find('textarea[name=content]').val();
};

var dash_recordtext_load = function() {
    this.draftContent = $(this.content).find('textarea[name=content]').val();
    this.saveDraft = function(){
        if (typeof(this.contentModified)!="undefined" && !this.contentModified) return;
        var content = $(this.content).find('textarea[name=content]').val();
        if (content.length==0) return;
        if (content == this.draftContent) return;
        this.draftContent = content;
        this.setTitle('glyphicon glyphicon-floppy-open');
        desk_post(url('dash/records/savedraft'),{'item':this.options.data.items[0],'content':content, 'token':token()}, this.bind(this, function(response){
            if (response && typeof(response.success)!="undefined" && response.success) {
                if ($(this.content).length==0) return;
                this.setTitle();
                var content = $(this.content).find('textarea').eq(0).val();
                this.resetFooterContent();
                this.appendFooterContent(content.length+'<span style="position: absolute;right:20px">'+t('saved to drafts')+'</span>');
                $('#dashpanel-container nav').removeClass('disabled');
            }
        }), function(){
            $('#dashpanel-container nav').addClass('disabled');
        });
    };
    this.timer = window.setInterval(this.bind(this, this.saveDraft),dash_record_draft_interval);
    if (this.options.data.draft) {
        desk_confirm(t('Load saved draft ?'), this.bind(this,function(){
            desk_window_request(this,url('dash/records/draft'),{'item':this.options.data.draft}, this.bind(this, function(response){
                if (response && typeof(response.draft)!="undefined") {
                    $(this.content).find('textarea[name=content]').val(response.draft);
                    this.draftContent = response.draft;
                }
            }));
        }));
    }
    if (typeof(this.options.data.published)!="undefined" && this.options.data.published=='1') {
        this.enableItemsByProperty('typo','page');
    }
};

var dash_recordtext_preview = function() {
    if (typeof(this.options.data.page)!="undefined") {
        var language = '';
        if (typeof(this.options.data.language)!="undefined" && this.options.data.language!==null) language = this.options.data.language + '/';
        var data = {'url':[language+this.options.data.page]};
        desk_call(dash_records_web_wnd, null, {'data':data});
    }
};

var dash_recordtext_page = function() {
    if (typeof(this.options.data.page)!="undefined") {
        var language = '';
        if (typeof(this.options.data.language)!="undefined" && this.options.data.language!==null) language = this.options.data.language + '/';
        window.location.href=url(language+this.options.data.page);
    }
};