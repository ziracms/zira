var dash_records_open = function() {
    this.onSpecialKey = dash_records_special_key;
};

var dash_records_load = function() {
    $(this.element).find('.record-infobar').html('');
    $(this.element).find('.sidebar-badge').remove();
    var item = this.findToolbarItemByProperty('action','level');
    var root = this.options.data.root.split('/').slice(0,-1);
    if (root.length>0) {
        this.enableToolbarItem(item);
    } else {
        this.disableToolbarItem(item);
    }
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].activated)!="undefined" && this.options.bodyItems[i].activated==record_status_not_published_id) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
    if (typeof(this.options.data.search)!="undefined") {
        var search = this.findToolbarItemByProperty('action','search');
        if (search) {
            $(search.element).val(this.options.data.search);
        }
    }
    
    if (typeof this.parent_pages == "undefined") this.parent_pages = {};
    var pi = this.options.data.root.replace(/[\/]/g,'_');
    if(pi.length == 0) pi = '_';
    this.parent_pages[pi] = this.options.data.page;
};

var dash_records_select = function() {
    var selected = this.getSelectedContentItems();
    this.disableItemsByProperty('typo','editor');
    this.disableItemsByProperty('typo','record');
    this.disableItemsByProperty('typo','publish');
    this.disableItemsByProperty('typo','front_page');
    this.disableItemsByProperty('typo','settings');
    this.disableItemsByProperty('typo','preview');
    this.disableItemsByProperty('typo','widget');
    this.disableItemsByProperty('typo','slider');
    this.disableItemsByProperty('typo','gallery');
    this.disableItemsByProperty('typo','files');
    this.disableItemsByProperty('typo','audio');
    this.disableItemsByProperty('typo','video');
    this.disableItemsByProperty('typo','rethumb');
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo == 'record') {
        this.enableItemsByProperty('typo','editor');
        this.enableItemsByProperty('typo','preview');
        if (typeof(selected[0].activated)!="undefined" && selected[0].activated==record_status_published_id) {
            this.enableItemsByProperty('typo','record');
        } else if (typeof(selected[0].activated)!="undefined" && selected[0].activated==record_status_not_published_id) {
            this.enableItemsByProperty('typo','publish');
        }
        if (typeof(selected[0].front_page)!="undefined" && selected[0].front_page==record_status_not_front_page_id) {
            this.enableItemsByProperty('typo','front_page');
        }
        if (this.options.data.slider_enabled != 0) {
            this.enableItemsByProperty('typo','slider');
        }
        if (this.options.data.gallery_enabled != 0) {
            this.enableItemsByProperty('typo','gallery');
        }
        if (this.options.data.files_enabled != 0) {
            this.enableItemsByProperty('typo','files');
        }
        if (this.options.data.audio_enabled != 0) {
            this.enableItemsByProperty('typo','audio');
        }
        if (this.options.data.video_enabled != 0) {
            this.enableItemsByProperty('typo','video');
        }
    } else if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo == 'category') {
        this.enableItemsByProperty('typo','settings');
        this.enableItemsByProperty('typo','widget');
    }
    if (selected && selected.length) {
        for (var i=0; i<selected.length; i++) {
            if (selected[i].typo == 'record') {
                this.enableItemsByProperty('typo','rethumb');
                break;
            }
        }
    }
    if (selected && selected.length && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo!='record') {
        $(this.element).find('.record-infobar').html('');
        $(this.element).find('.sidebar-badge').remove();
    }       
    if (selected && selected.length && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record' && (typeof(this.info_last_item)=="undefined" || this.info_last_item!=selected[0].data || $(this.element).find('.record-infobar').html().length==0)) {
        this.info_last_item = selected[0].data;
        $(this.element).find('.record-infobar').html('');
        $(this.element).find('.sidebar-badge').remove();
        try { window.clearTimeout(this.timer); } catch(err) {};
        this.timer = window.setTimeout(this.bind(this,function(){
            $(this.element).find('.record-infobar').html('');
            var selected = this.getSelectedContentItems();
            if (!selected || !selected.length || selected.length!=1) return;
            desk_post(url('dash/records/info'),{'item':selected[0].data, 'token':token()}, this.bind(this, function(response){
                if (response && typeof(response.info)!="undefined" && response.info.length>0) {
                    $(this.element).find('.record-infobar').append('<div style="cursor:default;padding:0px;margin:5px 0px 0px"><span class="glyphicon glyphicon-info-sign"></span> '+t('Information')+':</div>');
                    $(this.element).find('.record-infobar').append('<div style="border-top:1px solid #B3B6D1;border-bottom:1px solid #EDEDF6;height:1px;padding:0px;margin:5px 0px"></div>');
                    for (var i=0; i<response.info.length; i++) {
                        $(this.element).find('.record-infobar').append('<div style="font-weight:normal;padding:2px 0px;cursor:default;text-overflow:ellipsis;overflow:hidden" title="'+response.info[i].split('>').slice(-1)[0]+'">'+response.info[i]+'</div>');
                    }
                }
                if (response && typeof(response.slides_count)!="undefined" && typeof(response.images_count)!="undefined" && typeof(response.audio_count)!="undefined" && typeof(response.video_count)!="undefined" && typeof(response.files_count)!="undefined") {
                    dash_records_display_sidebar_counters.call(this, response.slides_count, response.images_count, response.audio_count, response.video_count, response.files_count);
                }
            }));
        }),1000);
    }
};

var dash_records_display_sidebar_counters = function(slides_count, images_count, audio_count, video_count, files_count) {
    if (typeof(slides_count) == "undefined" ||
        typeof(images_count) == "undefined" ||
        typeof(audio_count) == "undefined" ||
        typeof(video_count) == "undefined" ||
        typeof(files_count) == "undefined"
    ) return;
    
    var slider_item = this.findSidebarItemByProperty('typo', 'slider');
    var gallery_item = this.findSidebarItemByProperty('typo', 'gallery');
    var audio_item = this.findSidebarItemByProperty('typo', 'audio');
    var video_item = this.findSidebarItemByProperty('typo', 'video');
    var files_item = this.findSidebarItemByProperty('typo', 'files');
    
    if (slides_count>0) $(slider_item.element).append('<span class="badge sidebar-badge">'+slides_count+'</span>');
    if (images_count>0) $(gallery_item.element).append('<span class="badge sidebar-badge">'+images_count+'</span>');
    if (audio_count>0) $(audio_item.element).append('<span class="badge sidebar-badge">'+audio_count+'</span>');
    if (video_count>0) $(video_item.element).append('<span class="badge sidebar-badge">'+video_count+'</span>');
    if (files_count>0) $(files_item.element).append('<span class="badge sidebar-badge">'+files_count+'</span>');
};

var dash_records_drop = function(element) {
    if (element instanceof FileList) return;
    if (typeof(element.parent)!="undefined" && element.parent=='record') {
        desk_window_request(this, url('dash/records/copy'),{'root':this.options.data.root, 'item':element.data});
    }
};

var dash_records_up = function() {
    var root = this.options.data.root.split('/').slice(0,-1);
    if (root.length>0) {
        this.options.data.root=root.join('/');
        this.options.data.search='';
        var pi = this.options.data.root.replace(/[\/]/g,'_');
        if(pi.length == 0) pi = '_';
        if (typeof this.parent_pages[pi] != "undefined") {
            this.options.data.page=this.parent_pages[pi];
        } else {
            this.options.data.page=1;
        }
        desk_window_reload(this);
    }
};

var dash_records_edit = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined") {
        if (selected[0].typo == 'category') {
            var data = {
                'data':desk_window_selected(this,1),
                'reload': this.className,
                'onClose':function(){
                    desk_window_reload_all(this.options.reload);
                }
            };
            data.data.root = this.options.data.root;
            desk_call(dash_records_category_wnd, null, data);
        } else if (selected[0].typo == 'record') {
            desk_call(dash_records_open_record, this);
        }
    }
};

var dash_records_delete = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length>0) {
        var data = {'items':[],'types':[]};
        for (var i=0; i<selected.length; i++) {
            if (typeof(selected[i].data)=="undefined" || typeof(selected[i].typo)=="undefined") continue;
            data.items.push(selected[i].data);
            data.types.push(selected[i].typo);
        }
        desk_window_request(this, url('dash/index/delete'), data);
    }
};

var dash_records_copy = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        desk_prompt(t('Enter category'), this.bind(this, function(name){
            desk_window_request(this, url('dash/records/copy'),{'root':name, 'item':selected[0].data});
        }));
        var root = this.options.data.root;
        if (root.substr(0,1)=='/') root = root.substr(1);
        $('#zira-prompt-dialog input[name=modal-input]').val(root);
    }
};

var dash_records_move = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        desk_prompt(t('Enter category'), this.bind(this, function(name){
            if (name.length>0 && name.substr(0,1)!='/') name = '/'+name;
            if (name == this.options.data.root) return;
            desk_window_request(this, url('dash/records/move'),{'root':name, 'item':selected[0].data});
        }));
        var root = this.options.data.root;
        if (root.substr(0,1)=='/') root = root.substr(1);
        $('#zira-prompt-dialog input[name=modal-input]').val(root);
    }
};

var dash_records_create_record = function() {
    var data = {
        data:{},
        'reload': this.className,
        'onClose':function(){
            desk_window_reload_all(this.options.reload);
        }
    };
    data.data.root = this.options.data.root;
    data.data.language = this.options.data.language;
    desk_call(dash_records_record_wnd, null, data);
};

var dash_records_create_category = function() {
    var data = {
        data:{},
        'reload': this.className,
        'onClose':function(){
            desk_window_reload_all(this.options.reload);
        }
    };
    data.data.root = this.options.data.root;
    desk_call(dash_records_category_wnd, null, data);
};

var dash_records_open_category = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].category)!="undefined") {
        this.options.data.root = this.options.data.root+'/'+selected[0].category;
        this.options.data.search='';
        this.options.data.page=1;
        this.loadBody();
    }
};

var dash_records_category_settings = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo == 'category') {
        var data = {'data':desk_window_selected(this,1)};
        desk_call(dash_records_category_settings_wnd, null, data);
    }
};

var dash_records_category_widget = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo == 'category') {
        desk_window_request(this, url('dash/records/widget'),{'item':selected[0].data});
    }
};

var dash_records_open_record = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        var data = {
            data:{},
            'reload': this.className,
            'onClose':function(){
                desk_window_reload_all(this.options.reload);
            }
        };
        data.data.root = this.options.data.root;
        data.data.language = this.options.data.language;
        data.data.items = desk_window_selected(this,1);
        desk_call(dash_records_record_wnd, null, data);
    }
};

var dash_records_record_text = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        var data = {'items':[selected[0].data]};
        desk_call(dash_records_record_text_wnd, null, {'data':data});
    }
};

var dash_records_record_html = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        var data = {'items':[selected[0].data]};
        desk_call(dash_records_record_html_wnd, null, {'data':data});
    }
};

var dash_records_desc = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].description)!="undefined" && typeof(selected[0].typo)!="undefined") {
        if (selected[0].typo == 'category') {
            desk_prompt(t('Enter description'), this.bind(this, function(desc){
                desk_window_request(this, url('dash/system/description'),{'type': 'category', 'description':desc, 'item':selected[0].data});
            }));
            $('#zira-prompt-dialog input[name=modal-input]').val(selected[0].description);
        } else if (selected[0].typo == 'record') {
            desk_multi_prompt(t('Enter description'), this.bind(this, function(desc){
                desk_window_request(this, url('dash/records/description'),{'description':desc, 'item':selected[0].data});
            }));
            $('#zira-multi-prompt-dialog textarea[name=modal-input]').val(selected[0].description);
        }
    }
};

var dash_records_record_image = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        desk_file_selector(this.bind(this,function(elements){
            if (!elements || elements.length==0) return;
            var element = elements[0];
            if (element instanceof FileList) return;
            if (typeof(element)!="object" || typeof(element.type)=="undefined" || element.type!='image' || typeof(element.data)=="undefined") return;
            if (typeof(element.parent)=="undefined" || element.parent!='files') return;
            desk_window_request(this, url('dash/records/image'),{'image':element.data, 'item':selected[0].data});
        }));
    }
};

var dash_records_record_image_update = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length>0) {
        var data = {'items':[]};
        for (var i=0; i<selected.length; i++) {
            if (typeof(selected[i].data)=="undefined" || typeof(selected[i].typo)=="undefined" || selected[i].typo!='record') continue;
            data.items.push(selected[i].data);
        }
        if (data.items.length>0) {
            desk_window_request(this, url('dash/records/rethumb'), data);
        }
    }
};

var dash_records_seo = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined") {
        var data = {'items':[selected[0].data]};
        if (selected[0].typo == 'category') {
            desk_call(dash_records_category_meta_wnd, null, {
                'data':data,
                'reload': this.className,
                'onClose':function(){
                    desk_window_reload_all(this.options.reload);
                }
            });
        } else if (selected[0].typo == 'record') {
            desk_call(dash_records_record_meta_wnd, null, {
                'data':data,
                'reload': this.className,
                'onClose':function(){
                    desk_window_reload_all(this.options.reload);
                }
            });
        }
    }
};

var dash_records_record_page = function() {
    var selected = this.getSelectedContentItems();
    if (selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record' && typeof(selected[0].page)!="undefined") {
        var language = '';
        if (typeof(selected[0].language)!="undefined" && selected[0].language!==null) language = selected[0].language + '/';
        window.location.href=url(language+selected[0].page);
    }
};

var dash_records_record_view = function() {
    var selected = this.getSelectedContentItems();
    if (selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record' && typeof(selected[0].page)!="undefined") {
        var language = '';
        if (typeof(selected[0].language)!="undefined" && selected[0].language!==null) language = selected[0].language + '/';
        var data = {'url':[language+selected[0].page]};
        desk_call(dash_records_web_wnd, null, {'data':data});
    }
};

var dash_records_record_publish = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        var data = {'item':selected[0].data};
        desk_window_request(this, url('dash/records/publish'), data);
    }
};

var dash_records_record_front = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        var data = {'item':selected[0].data};
        desk_window_request(this, url('dash/records/frontpage'), data);
    }
};

var dash_records_record_gallery = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        var data = {'items':[selected[0].data]};
        desk_call(dash_records_record_images_wnd, null, {
            'data':data, 
            'parentWnd': this,
            'onClose': function(){
                $(this.options.parentWnd.element).find('.record-infobar').html('');
                $(this.options.parentWnd.element).find('.sidebar-badge').remove();
                desk_call(dash_records_select, this.options.parentWnd);
            }
        });
    }
};

var dash_records_record_files = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        var data = {'items':[selected[0].data]};
        desk_call(dash_records_record_files_wnd, null, {
            'data':data,
            'parentWnd': this,
            'onClose': function(){
                $(this.options.parentWnd.element).find('.record-infobar').html('');
                $(this.options.parentWnd.element).find('.sidebar-badge').remove();
                desk_call(dash_records_select, this.options.parentWnd);
            }
        });
    }
};

var dash_records_record_audio = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        var data = {'items':[selected[0].data]};
        desk_call(dash_records_record_audio_wnd, null, {
            'data':data,
            'parentWnd': this,
            'onClose': function(){
                $(this.options.parentWnd.element).find('.record-infobar').html('');
                $(this.options.parentWnd.element).find('.sidebar-badge').remove();
                desk_call(dash_records_select, this.options.parentWnd);
            }
        });
    }
};

var dash_records_record_video = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        var data = {'items':[selected[0].data]};
        desk_call(dash_records_record_video_wnd, null, {
            'data':data,
            'parentWnd': this,
            'onClose': function(){
                $(this.options.parentWnd.element).find('.record-infobar').html('');
                $(this.options.parentWnd.element).find('.sidebar-badge').remove();
                desk_call(dash_records_select, this.options.parentWnd);
            }
        });
    }
};

var dash_records_record_slider = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=='record') {
        var data = {'items':[selected[0].data]};
        desk_call(dash_records_record_slides_wnd, null, {
            'data':data,
            'parentWnd': this,
            'onClose': function(){
                $(this.options.parentWnd.element).find('.record-infobar').html('');
                $(this.options.parentWnd.element).find('.sidebar-badge').remove();
                desk_call(dash_records_select, this.options.parentWnd);
            }
        });
    }
};

var dash_records_language = function(element) {
    var language = this.options.data.language;
    var id = $(element).attr('id');
    var item = this.findMenuItemByProperty('id',id);
    if (item && typeof(item.language)!="undefined") {
        if (item.language!=language) {
            this.options.data.language=item.language;
            desk_window_reload(this);
            $(element).parents('ul').find('.glyphicon-ok').removeClass('glyphicon-ok').addClass('glyphicon-filter');
            $(element).find('.glyphicon').removeClass('glyphicon-filter').addClass('glyphicon-ok');
        } else {
            this.options.data.language='';
            desk_window_reload(this);
            $(element).parents('ul').find('.glyphicon-ok').removeClass('glyphicon-ok').addClass('glyphicon-filter');
        }
    }
};

var desk_record_category = function(item) {
    var data = {'root':item};
    desk_call(dash_records_wnd, null, {'data':data});
};

var desk_record_editor = function(item) {
    var data = {'items':[item]};
    desk_call(dash_records_record_html_wnd, null, {'data':data});
};

var dash_records_special_key = function(item, operation) {
    if (!item || !operation) return false;
    if (typeof item.data == "undefined" || typeof item.page == "undefined" || typeof item.parent == "undefined" || item.parent != 'record') return false;
    var origin = item.page.split('/').slice(0, -1).join('/');
    var root = this.options.data.root;
    if (root.substr(0,1)=='/') root = root.substr(1);
    if (operation == 'copy') {
        desk_window_request(this, url('dash/records/copy'),{'root':root, 'item':item.data});
        return true;
    } else if (operation == 'move') {
        if (origin == root) return false;
        desk_window_request(this, url('dash/records/move'),{'root':root, 'item':item.data});
        return true;
    } else if (operation == 'copypress' || operation == 'movepress') {
        return true;
    }
    return false;
};