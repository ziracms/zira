var dash_recordfiles_load = function() {
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].inactive)!="undefined" && this.options.bodyItems[i].inactive) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
};

var dash_recordfiles_desc = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].description)!="undefined") {
        desk_prompt(t('Enter description'), this.bind(this, function(desc){
            desk_window_request(this,url('dash/records/filedesc'),{'description':desc, 'item':selected[0].data});
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(selected[0].description);
    }
};

var dash_recordfiles_drop = function(element) {
    if (element instanceof FileList) return;
    if (typeof(element.parent)=="undefined" || element.parent!='files') return;
    if (typeof(element)!="object" || typeof(element.type)=="undefined" || typeof(element.data)=="undefined") return;
    if (element.type!='folder') {
        desk_window_request(this, url('dash/records/addfile'),{'files':[element.data], 'item':this.options.data.items[0]});
    } else {
        desk_window_request(this, url('dash/records/addfiles'),{'folder':element.data, 'item':this.options.data.items[0]});
    }
};

var dash_recordfiles_add = function() {
    desk_file_selector(this.bind(this,function(elements){
        if (!elements || elements.length==0) return;
        var files = [];
        for (var i=0; i<elements.length; i++) {
            var element = elements[i];
            if (element instanceof FileList) continue;
            if (typeof(element)!="object" || typeof(element.type)=="undefined" || element.type=='folder' || typeof(element.data)=="undefined") continue;
            if (typeof(element.parent)=="undefined" || element.parent!='files') continue;
            files.push(element.data);
        }
        if (files.length==0) return;
        desk_window_request(this, url('dash/records/addfile'),{'files':files, 'item':this.options.data.items[0]});
    }));
};

var dash_recordfiles_addurl = function() {
    desk_prompt(t('Enter URL'), this.bind(this, function(address){
        if (address.length==0) return;
        desk_window_request(this, url('dash/records/addfile'),{'url':address, 'item':this.options.data.items[0]});
    }));
};