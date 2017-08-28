var dash_recordaudio_select = function() {
    var selected = this.getSelectedContentItems();
    this.disableItemsByProperty('typo','edit');
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined") {
        if (selected[0].typo == 'url') {
            this.enableItemsByProperty('typo','edit');
        }
        if (selected[0].typo == 'embed') {
            this.enableItemsByProperty('typo','edit');
        }
    }
};

var dash_recordaudio_desc = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].description)!="undefined") {
        desk_prompt(t('Enter description'), this.bind(this, function(desc){
            desk_window_request(this,url('dash/records/audiodesc'),{'description':desc, 'item':selected[0].data});
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(selected[0].description);
    }
};

var dash_recordaudio_drop = function(element) {
    if (element instanceof FileList) return;
    if (typeof(element.parent)=="undefined" || element.parent!='files') return;
    if (typeof(element)!="object" || typeof(element.type)=="undefined" || element.type!='audio' || typeof(element.data)=="undefined") return;
    desk_window_request(this, url('dash/records/addaudio'),{'files':[element.data], 'item':this.options.data.items[0]});
};

var dash_recordaudio_add = function() {
    desk_file_selector(this.bind(this,function(elements){
        if (!elements || elements.length==0) return;
        var files = [];
        for (var i=0; i<elements.length; i++) {
            var element = elements[i];
            if (element instanceof FileList) continue;
            if (typeof(element)!="object" || typeof(element.type)=="undefined" || element.type!='audio' || typeof(element.data)=="undefined") continue;
            if (typeof(element.parent)=="undefined" || element.parent!='files') continue;
            files.push(element.data);
        }
        if (files.length==0) return;
        desk_window_request(this, url('dash/records/addaudio'),{'files':files, 'item':this.options.data.items[0]});
    }));
};

var dash_recordaudio_addurl = function() {
    desk_prompt(t('Enter URL'), this.bind(this, function(address){
        if (address.length==0) return;
        desk_window_request(this, url('dash/records/addaudio'),{'url':address, 'item':this.options.data.items[0]});
    }));
};

var dash_recordaudio_embed = function() {
    desk_multi_prompt(t('Enter code'), this.bind(this, function(code){
        if (code.length==0) return;
        desk_window_request(this, url('dash/records/addaudio'),{'code':code, 'item':this.options.data.items[0]});
    }));
};

var dash_recordaudio_edit = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined") {
        if (selected[0].typo == 'url' && typeof(selected[0].editval)!="undefined") {
            desk_prompt(t('Enter URL'), this.bind(this, function(address){
                if (address.length==0) return;
                desk_window_request(this, url('dash/records/editaudio'),{'url':address, 'item':selected[0].data});
            }));
            $('#zira-prompt-dialog input[name=modal-input]').val(selected[0].editval);
        } else if (selected[0].typo == 'embed' && typeof(selected[0].editval)!="undefined") {
            desk_multi_prompt(t('Enter code'), this.bind(this, function(code){
                if (code.length==0) return;
                desk_window_request(this, url('dash/records/editaudio'),{'code':code, 'item':selected[0].data});
            }));
            $('#zira-multi-prompt-dialog textarea[name=modal-input]').val(selected[0].editval);
        }
    }
};