var dash_recordimages_desc = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].description)!="undefined") {
        desk_prompt(t('Enter description'), this.bind(this, function(desc){
            desk_window_request(this,url('dash/records/imagedesc'),{'description':desc, 'item':selected[0].data});
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(selected[0].description);
    }
};

var dash_recordimages_drop = function(element) {
    if (element instanceof FileList) return;
    if (typeof(element.parent)=="undefined" || element.parent!='files') return;
    if (typeof(element)!="object" || typeof(element.type)=="undefined" || element.type!='image' || typeof(element.data)=="undefined") return;
    desk_window_request(this, url('dash/records/addimage'),{'images':[element.data], 'item':this.options.data.items[0]});
};

var dash_recordimages_add = function() {
    desk_file_selector(this.bind(this,function(elements){
        if (!elements || elements.length==0) return;
        var images = [];
        for (var i=0; i<elements.length; i++) {
            var element = elements[i];
            if (element instanceof FileList) continue;
            if (typeof(element)!="object" || typeof(element.type)=="undefined" || element.type!='image' || typeof(element.data)=="undefined") continue;
            if (typeof(element.parent)=="undefined" || element.parent!='files') continue;
            images.push(element.data);
        }
        if (images.length==0) return;
        desk_window_request(this, url('dash/records/addimage'),{'images':images, 'item':this.options.data.items[0]});
    }));
};