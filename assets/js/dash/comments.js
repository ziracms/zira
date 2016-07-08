var dash_comments_select = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length>0) {
    this.disableItemsByProperty('typo','activate');
        for (var i=0; i<selected.length; i++) {
            if (typeof(selected[i].inactive)!="undefined" && selected[i].inactive) {
                this.enableItemsByProperty('typo','activate');
                break;
            }
        }
    }
    if (selected && selected.length && selected.length==1 && (typeof(this.info_last_item)=="undefined" || this.info_last_item!=selected[0].data || $(this.element).find('.comment-infobar').html().length==0)) {
        this.info_last_item = selected[0].data;
        $(this.element).find('.comment-infobar').html('');
        try { window.clearTimeout(this.timer); } catch(err) {};
        this.timer = window.setTimeout(this.bind(this,function(){
            $(this.element).find('.comment-infobar').html('');
            var selected = this.getSelectedContentItems();
            if (!selected || !selected.length || selected.length!=1) return;
            desk_post(url('dash/comments/info'),{'type':'comment','item':selected[0].data, 'token':token()}, this.bind(this, function(response){
                if (response && response.length>0) {
                    $(this.element).find('.comment-infobar').append('<div style="cursor:default;padding:0px;margin:10px 0px 0px"><span class="glyphicon glyphicon-info-sign"></span> '+t('Information')+':</div>');
                    $(this.element).find('.comment-infobar').append('<div style="border-top:1px solid #B3B6D1;border-bottom:1px solid #EDEDF6;height:1px;padding:0px;margin:10px 0px"></div>');
                    for (var i=0; i<response.length; i++) {
                        $(this.element).find('.comment-infobar').append('<div style="font-weight:normal;padding:2px 0px;cursor:default;text-overflow:ellipsis;overflow:hidden" title="'+response[i].split('>').slice(-1)[0]+'">'+response[i]+'</div>');
                    }
                }
            }));
        }),1000);
    }
};

var dash_comments_preview_simple = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_message(t('Comment')+':<div style="color:black;padding:10px 16px">'+selected[0].title.replace(/\r\n/g,'<br />')+'</div>');
    }
};

var dash_comments_preview = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_window_request(this, url('dash/comments/preview'),{'item':selected[0].data}, this.bind(this, function(response){
            if (!response || typeof(response.record)=="undefined" || typeof(response.user)=="undefined" || typeof(response.content)=="undefined") return;
            desk_message(response.record+'<div style="color:black;margin: 16px 0px;padding:16px 16px;background:#fff;box-shadow:inset 0px 0px 6px #ddd">'+response.content+'</div>'+t('Author')+': '+response.user);
            try {
                zira_parse_content();
            } catch(e) {}
        }));
    }
};

var dash_comments_activate = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length>0) {
        desk_window_request(this, url('dash/comments/activate'), desk_window_selected(this));
    }
};

var dash_comments_edit = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_multi_prompt(t('Comment'), this.bind(this, function(comment){
            desk_window_request(this, url('dash/comments/edit'),{'comment':comment, 'item':selected[0].data});
        }));
        var textarea = document.createElement('textarea');
        textarea.innerHTML = selected[0].title;
        $('#zira-multi-prompt-dialog textarea[name=modal-input]').val(textarea.value);
    }
};

var dash_comments_open = function() {
    var selected = this.getSelectedContentItems();
    if (selected.length==1 && typeof(selected[0].page)!="undefined") {
        window.location.href=url(selected[0].page);
    }
};

var dash_comments_view = function() {
    var selected = this.getSelectedContentItems();
    if (selected.length==1 && typeof(selected[0].page)!="undefined") {
        var data = {'url':[selected[0].page]};
        desk_call(dash_comments_web, null, {'data':data});
    }
};

var dash_comments_load = function() {
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].inactive)!="undefined" && this.options.bodyItems[i].inactive) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
};