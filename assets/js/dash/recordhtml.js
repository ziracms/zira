var dash_recordhtml_load = function() {
    this.draftContent = $(this.content).find('textarea[name=content]').val();
    this.saveDraft = function(){
        if (typeof(this.editor)=="undefined") return;
        if (typeof(this.contentModified)!="undefined" && !this.contentModified) return;
        var content = this.editor.getContent();
        if (content.length==0) return;
        if (content == this.draftContent) return;
        this.draftContent = content;
        this.setTitle('glyphicon glyphicon-floppy-open');
        desk_post(url('dash/records/savedraft'),{'item':this.options.data.items[0],'content':content, 'token':token()}, this.bind(this, function(response){
            if (response && typeof(response.success)!="undefined" && response.success) {
                if ($(this.content).length==0) return;
                this.setTitle();
                var l='';
                if (typeof(this.editor)!="undefined") {
                l = this.editor.getContent().replace(/&nbsp;/g,'').replace(/<[\s\S]*?>/g,'').length;
                }
                this.resetFooterContent();
                this.appendFooterContent(l+'<span style="position: absolute;right:20px">'+t('saved to drafts')+'</span>');
                $('#dashpanel-container nav').removeClass('disabled');
            }
        }), function(){
            $('#dashpanel-container nav').addClass('disabled');
        });
    };
    this.timer = window.setInterval(this.bind(this, this.saveDraft),dash_record_draft_interval);
    if (this.options.data.draft) {
        window.setTimeout(this.bind(this, function(){
            desk_confirm(t('Load saved draft ?'), this.bind(this,function(){
                desk_window_request(this,url('dash/records/draft'),{'item':this.options.data.draft}, this.bind(this, function(response){
                    if (response && typeof(response.draft)!="undefined") {
                        $(this.content).find('textarea[name=content]').val(response.draft);
                        this.draftContent = response.draft;
                        if (typeof(this.editor)=="undefined") return;
                        this.editor.setContent(response.draft);
                    }
                }));
            }));
        }), 1000);
    }
};