var dash_files_load = function() {
    $(this.element).find('.filemanager-infobar').html('');
    var item = this.findToolbarItemByProperty('action','level');
    var root = this.options.data.root.split(desk_ds).slice(0,-1);
    if (root.length>0) {
        this.enableToolbarItem(item);
    } else {
        this.disableToolbarItem(item);
    }
    
    if (typeof this.parent_pages == "undefined") this.parent_pages = {};
    
    var r = new RegExp('[\\'+desk_ds+']', 'g');
    var pi = this.options.data.root.replace(r,'_');
    if(pi.length == 0) pi = '_';
    this.parent_pages[pi] = this.options.data.page;
    
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].is_widget)=="undefined" || !this.options.bodyItems[i].is_widget) continue;
        if (typeof(this.options.bodyItems[i].inactive)!="undefined" && this.options.bodyItems[i].inactive) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
};

var dash_files_open = function() {
    if (typeof(this.options.data)!="undefined" && typeof(this.options.data.max_upload_size)!="undefined") this.max_upload_size=this.options.data.max_upload_size; else this.max_upload_size=null;
    if (typeof(this.options.data)!="undefined" && typeof(this.options.data.max_upload_files)!="undefined") this.max_upload_files=this.options.data.max_upload_files; else this.max_upload_files=0;
    $(this.element).find('.dashwindow-upload-form input[type=file]').change(this.bind(this, function(){
        var root = this.options.data.root;
        desk_upload(token(),url('dash/files/xhrupload'), root, $(this.element).find('.dashwindow-upload-form input[type=file]').get(0).files, null, this.max_upload_size, this.max_upload_files, this.className);
    }));
    this.disableItemsByProperty('action','call');
    this.onSpecialKey = dash_files_special_key;
};

var dash_files_drop = function(element) {
    if (element instanceof FileList) {
        var root = this.options.data.root;
        desk_upload(token(),url('dash/files/xhrupload'), root, element, null, this.max_upload_size, this.max_upload_files, this.className);
    } else if (typeof(element.parent)!="undefined" && element.parent=='files') {
        desk_window_request(this, url('dash/files/copy'),{'path':this.options.data.root, 'file':element.data});
    }
};

var dash_files_select = function() {
    var selected = this.getSelectedContentItems();
    if (!selected || selected.length!=1) this.disableItemsByProperty('action','call');
    else this.enableItemsByProperty('action','call');
    if (!selected || selected.length!=1 || typeof(selected[0].type)=="undefined" || selected[0].type=='folder') {
        this.disableItemsByProperty('typo','download');
    }
    if (!selected || selected.length!=1 || typeof(selected[0].type)=="undefined" || selected[0].type!='archive') {
        this.disableItemsByProperty('typo','archive');
    }
    if (!selected || selected.length!=1 || typeof(selected[0].type)=="undefined" || (selected[0].type!='image' && selected[0].type!='txt' && selected[0].type!='html' && (typeof selected[0].is_widget == "undefined" || !selected[0].is_widget))) {
        this.disableItemsByProperty('action','edit');
    }
    if (!selected || selected.length!=1 || typeof(selected[0].type)=="undefined" || selected[0].type=='folder' || selected[0].type=='image'|| selected[0].type=='archive') {
        this.disableItemsByProperty('typo','notepad');
    }
    if (!selected || selected.length!=1 || typeof(selected[0].type)=="undefined" || selected[0].type!='image') {
        this.disableItemsByProperty('typo','show_image');
    }
    if (!selected || selected.length!=1 || typeof(selected[0].type)=="undefined" || selected[0].type!='folder') {
        this.disableItemsByProperty('typo','carousel');
    }
    if (selected && selected.length && selected.length==1 && (typeof(this.info_last_item)=="undefined" || this.info_last_item!=selected[0].data || $(this.element).find('.filemanager-infobar').html().length==0)) {
        this.info_last_item = selected[0].data;
        $(this.element).find('.filemanager-infobar').html('');
        try { window.clearTimeout(this.timer); } catch(err) {};
        this.timer = window.setTimeout(this.bind(this,function(){
            $(this.element).find('.filemanager-infobar').html('');
            var selected = this.getSelectedContentItems();
            if (!selected || !selected.length || selected.length!=1) return;
            desk_post(url('dash/files/info'),{'dirroot':this.options.data.root,'file':selected[0].data, 'token':token()}, this.bind(this, function(response){
                if (response && response.length>0) {
                    $(this.element).find('.filemanager-infobar').append('<div style="cursor:default;padding:0px;margin:5px 0px 0px"><span class="glyphicon glyphicon-info-sign"></span> '+t('Information')+':</div>');
                    $(this.element).find('.filemanager-infobar').append('<div class="devider" style="height:1px;padding:0px;margin:5px 0px"></div>');
                    for (var i=0; i<response.length; i++) {
                        $(this.element).find('.filemanager-infobar').append('<div style="font-weight:normal;padding:2px 0px;cursor:default;text-overflow:ellipsis;overflow:hidden" title="'+response[i].split('>').slice(-1)[0]+'">'+response[i]+'</div>');
                    }
                }
            }));
        }),1000);
    }
};

var dash_files_up = function() {
    var root = this.options.data.root.split(desk_ds).slice(0,-1);
    if (root.length>0) {
        this.options.data.root=root.join(desk_ds);
        var r = new RegExp('[\\'+desk_ds+']', 'g');
        var pi = this.options.data.root.replace(r,'_');
        if(pi.length == 0) pi = '_';
        if (typeof this.parent_pages[pi] != "undefined") {
            this.options.data.page=this.parent_pages[pi];
        } else {
            this.options.data.page=1;
        }
        desk_window_reload(this);
    }
};

var dash_files_mkdir = function() {
    desk_prompt(t('Enter name'), this.bind(this, function(name){
        var root = this.options.data.root;
        desk_window_request(this, url('dash/files/mkdir'),{'name':name, 'root':root});
    }));
};

var dash_files_new_text_file = function() {
    desk_prompt(t('Enter name'), this.bind(this, function(name){
        var root = this.options.data.root;
        desk_window_request(this, url('dash/files/textfile'),{'name':name, 'root':root});
    }));
};

var dash_files_new_html_file = function() {
    desk_prompt(t('Enter name'), this.bind(this, function(name){
        var root = this.options.data.root;
        desk_window_request(this, url('dash/files/htmlfile'),{'name':name, 'root':root});
    }));
};

var dash_files_rename = function() {
    var selected = desk_window_selected(this);
    if (typeof(selected.items)!="undefined" && selected.items.length==1) {
        desk_prompt(t('Enter name'), this.bind(this, function(name){
            desk_window_request(this, url('dash/files/rename'),{'name':name, 'file':selected.items[0]});
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(selected.items[0].split(desk_ds).slice(-1)[0]);
    }
};
    
var dash_files_upload_url = function() {
    desk_prompt(t('Enter URL address'),this.bind(this, function(address) {
        var root = this.options.data.root;
        if (address.length>0) {
            desk_window_request(this, url('dash/files/xhruploadsrc'), {'dirroot': root, 'url': address});
        }
    }));
};    
    
var dash_files_download = function() {
    var selected = this.getSelectedContentItems();
    if (selected.length==1 && typeof(selected[0].type)!="undefined" && selected[0].type!='folder') {
        var a=document.createElement('a');
        var regexp = new RegExp('\\'+desk_ds, 'g');
        a.href=baseUrl(selected[0].data.replace(regexp, '/'));
        a.download=selected[0].data.split(desk_ds).slice(-1)[0];
        document.body.appendChild(a);
        HTMLElementClick.call(a);
        document.body.removeChild(a);
    }
};
    
var dash_files_copy = function() {
    var selected = desk_window_selected(this);
    if (typeof(selected.items)!="undefined" && selected.items.length>0) {
        desk_prompt(t('Enter folder path'), this.bind(this, function(path){
            if (selected.items.length==1) {
                desk_window_request(this, url('dash/files/copy'),{'path':path, 'file':selected.items[0]});
            } else {
                desk_window_request(this, url('dash/files/copies'),{'path':path, 'files':selected.items});
            }
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(selected.items[0].split(desk_ds).slice(0,-1).join(desk_ds));
    }
};

var dash_files_move = function() {
    var selected = desk_window_selected(this);
    if (typeof(selected.items)!="undefined" && selected.items.length>0) {
        desk_prompt(t('Enter folder path'), this.bind(this, function(path){
            if (selected.items.length==1) {
                desk_window_request(this, url('dash/files/move'),{'path':path, 'file':selected.items[0]});
            } else {
                desk_window_request(this, url('dash/files/moves'),{'path':path, 'files':selected.items});
            }
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(selected.items[0].split(desk_ds).slice(0,-1).join(desk_ds));
    }
};
    
var dash_files_pack = function() {
    var selected = desk_window_selected(this);
    if (typeof(selected.items)!="undefined" && selected.items.length>0) {
        desk_prompt(t('Enter archive name'), this.bind(this, function(name){
            var root = this.options.data.root;
            desk_window_request(this, url('dash/files/pack'),{'name':name, 'files':selected.items, 'dirroot': root});
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(selected.items[0].split(desk_ds).slice(-1)[0].split('.')[0]);
    }
};    
    
var dash_files_unpack = function() {
    var selected = this.getSelectedContentItems();
    if (selected.length==1 && typeof(selected[0].type)!="undefined" && selected[0].type=='archive') {
        desk_prompt(t('Enter folder path'), this.bind(this, function(path){
            desk_window_request(this, url('dash/files/unpack'),{'dirroot':path, 'file':selected[0].data});
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(this.options.data.root);
    }
};    
    
var dash_files_notepad = function() {
    var selected = this.getSelectedContentItems();
    if (!selected || selected.length!=1) return;
    var data=selected[0].data;
    desk_text_editor(data);
};    
    
var dash_files_show_image = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].image_url)!="undefined" && selected[0].image_url.length>0) {
        $('body').append('<a href="'+encodeURIComponent(selected[0].image_url).replace(/%2F/gi,'/')+'" data-lightbox="filemanager_zoomer" id="dashwindow-filemanager-zoomer-lightbox"></a>');
        $('#dashwindow-filemanager-zoomer-lightbox').trigger('click');
        $('#dashwindow-filemanager-zoomer-lightbox').remove();
    }
};    

var dash_files_edit = function() {
    try { window.clearTimeout(this.timer); } catch(err) {};
    var selected = this.getSelectedContentItems();
    if (!selected || selected.length!=1) return;
    var data=selected[0].data;
    if (typeof(selected[0].type)!="undefined" && selected[0].type=='folder') {
        this.options.data.root=data;
        this.options.data.page=1;
        desk_window_reload(this);
    } else if (typeof(selected[0].type)!="undefined" && selected[0].type=='archive') {
        desk_call(dash_files_unpack, this);
    } else if (typeof(selected[0].type)!="undefined" && selected[0].type=='txt') {
        desk_text_editor(data);
    } else if (typeof(selected[0].type)!="undefined" && selected[0].type=='html') {
        desk_html_editor(data);
    } else if (typeof(selected[0].type)!="undefined" && selected[0].type=='image') {
        desk_image_editor(data);
    } else if (typeof(selected[0].is_widget)!="undefined" && selected[0].is_widget) {
        desk_call(dash_files_carousel_wnd_open, this, data);
    }
};

var dash_files_carousel = function() {
    var selected = this.getSelectedContentItems();
    if (selected.length==1 && typeof(selected[0].type)!="undefined" && selected[0].type=='folder') {
        desk_prompt(t('Enter title'), this.bind(this, function(title){
            desk_window_request(this, url('dash/files/carousel'),{'title':title, 'folder':selected[0].data}, this.bind(this, function(){
                this.options.data.root=dash_files_widgets_folder_name;
                this.options.data.page=1;
                window.setTimeout(this.bind(this, function(){
                    desk_window_reload(this);
                }), 100);
            }));
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val('');
    }
};  

var dash_files_carousel_wnd_open = function(item) {
    if (typeof(item)!="undefined") {
        var data = {
            'data':{
                'items':[item]
            },
            'reload': this.className,
            'onClose':function(){
                desk_window_reload_all(this.options.reload);
            }
        };
        desk_call(dash_files_carousel_wnd, null, data);
    }
};

var dash_files_carousel_load = function() {
    if (typeof this.options.data.widget_exists == "undefined") return;
    this.disableItemsByProperty('typo','widget');
    if (!this.options.data.widget_exists) {
        this.enableItemsByProperty('typo','widget');
    }
};

var dash_files_carousel_title = function() {
    if (typeof this.options.data.items == "undefined" || this.options.data.items.length!=1) return;
    if (typeof this.options.data.widget_title == "undefined") return;
    desk_prompt(t('Enter title'), this.bind(this, function(title){
        desk_window_request(this, url('dash/files/carouseltitle'),{'title':title, 'widget':this.options.data.items[0]});
    }));
    $('#zira-prompt-dialog input[name=modal-input]').val(this.options.data.widget_title);
};

var dash_files_carousel_desc = function() {
    if (typeof this.options.data.items == "undefined" || this.options.data.items.length!=1) return;
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].description)!="undefined") {
        desk_prompt(t('Enter description'), this.bind(this, function(desc){
            desk_window_request(this, url('dash/files/carouseldesc'),{'description':desc, 'item':selected[0].data, 'widget':this.options.data.items[0]});
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(selected[0].description);
    }
};

var dash_files_carousel_link = function() {
    if (typeof this.options.data.items == "undefined" || this.options.data.items.length!=1) return;
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].link)!="undefined") {
        desk_prompt(t('Enter URL address'), this.bind(this, function(link){
            desk_window_request(this, url('dash/files/carousellink'),{'link':link, 'item':selected[0].data, 'widget':this.options.data.items[0]});
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(selected[0].link);
    }
};

var dash_files_carousel_widget = function() {
    if (typeof this.options.data.items == "undefined" || this.options.data.items.length!=1) return;
    if (typeof this.options.data.widget_exists == "undefined" || this.options.data.widget_exists) return;
    desk_window_request(this, url('dash/files/carouselwidget'),{'widget':this.options.data.items[0]});
};

var dash_files_special_key = function(items, operation) {
    if (!items || !operation) return false;
    if (items.length==0) return false;
    var origin = null;
    var root = this.options.data.root;
    var files = [];
    for (var i=0; i<items.length; i++) {
        if (typeof items[i].data == "undefined" || typeof items[i].parent == "undefined" || items[i].parent != 'files') return false;
        var _origin = items[i].data.split(desk_ds).slice(0,-1).join(desk_ds);
        if (origin != _origin && origin!==null) return false;
        origin = _origin;
        files.push(items[i].data);
    }
    if (files.length==0) return false;
    if (operation == 'copy') {
        desk_window_request(this, url('dash/files/copies'),{'path':root, 'files':files});
        return true;
    } else if (operation == 'move') {
        if (origin == root) return false;
        desk_window_request(this, url('dash/files/moves'),{'path':root, 'files':files});
        return true;
    } else if (operation == 'copypress' || operation == 'movepress') {
        return true;
    }
    return false;
};