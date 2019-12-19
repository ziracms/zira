var dash_push_settings_load = function() {
    desk_window_form_init(this);
    var privInput = $(this.element).find('.priv-key-input');
    var pubInput = $(this.element).find('.pub-key-input');
    var genBtn = this.findToolbarItemByProperty('action', 'generate');
    var sendBtn = this.findToolbarItemByProperty('action', 'send');
    if ($(privInput).length == 0 || $(pubInput).length == 0 || !genBtn || !sendBtn) return;
    if ($(privInput).val().length == 0 && $(pubInput).val().length == 0) {
        $(genBtn.element).removeClass('disabled');
    } else if ($(privInput).val().length > 0 && $(pubInput).val().length > 0) {
        $(sendBtn.element).removeClass('disabled');
    }
    if (window.location.protocol != 'https:') {
        desk_error(t('Push notifications require HTTPS'));
    } else if (typeof dash_push_php_version_support == "undefined" || dash_push_php_version_support == 0) {
        desk_error(t('Push notifications require the latest PHP version'));
    }
};

var dash_push_generate_keys = function() {
    desk_post(url('push/dash/generate'),{'token':token()}, this.bind(this, function(response){
        if (response && typeof response.privateKey != "undefined" && typeof response.publicKey != "undefined") {
            var privInput = $(this.element).find('.priv-key-input');
            var pubInput = $(this.element).find('.pub-key-input');
            var genBtn = this.findToolbarItemByProperty('action', 'generate');
            if ($(privInput).length == 0 || $(pubInput).length == 0 || !genBtn) return;
            $(privInput).val(response.privateKey);
            $(pubInput).val(response.publicKey);
            $(genBtn.element).addClass('disabled');
        } else {
            desk_error(t('An error occurred'));
        }
    }));
};

var dash_push_push_open = function(id) {
    var data = {};
    if (typeof id != "undefined") data.item_id = id;
    desk_call(dash_push_push_wnd, null, {'data':data});
};

var dash_push_push_load = function() {
    desk_window_form_init(this);

    $(this.content).find('input.image_input').parent().append('<span class="glyphicon glyphicon-folder-open" style="position:absolute;right:30px;top:10px;cursor:pointer"></span>');
    $(this.content).find('input.image_input').parent().children('.glyphicon').click(this.bind(this, function(){
        desk_file_selector(function(selected){
            if (selected && selected.length>0 && (typeof(selected[0].type)!="undefined" && selected[0].type=='image')) {
                var src = selected[0].data;
                var regexp = new RegExp('\\'+desk_ds, 'g');
                $(this.content).find('input.image_input').val(src.replace(regexp,'/'));
                desk_call(dash_push_push_preview, this);
            }
        }, this);
    }));

    $(this.content).find('input[type=text]').change(this.bind(this, dash_push_push_preview));
    $(this.content).find('input[type=text]').keyup(this.bind(this, dash_push_push_preview));

    var startBtn = this.findToolbarItemByProperty('action','begin');
    if (startBtn) {
        if (this.options.data.language.length > 0 && typeof(this.options.data.lang_subscribers[this.options.data.language])!="undefined") {
            $(startBtn.element).text(t('Start sending')+' ('+this.options.data.lang_subscribers[this.options.data.language]+')');
        } else {
            $(startBtn.element).text(t('Start sending')+' ('+this.options.data.subscribers+')');
        }
    }

    this.sent = 0;
    this.push = function(){
        $(this.content).find('.push-offset').eq(0).val(this.options.data.offset);
        $(this.content).find('.push-language').eq(0).val(this.options.data.language);
        var data = desk_window_content(this);
        desk_window_request(this, url('push/dash/send'), data, this.bind(this, function(response){
            if (!response || typeof(response.error)!="undefined") {
                desk_modal_progress_hide();
                return;
            }
            this.sent += response.sent;
            if (response.left>0){
                this.options.data.offset = ++response.offset;
                desk_modal_progress_update(Math.round((response.progress / response.total)*100));
                desk_window_request.finish_callback = this.bind(this, this.push);
            } else {
                this.options.data.offset = 0;
                desk_modal_progress_hide();
                desk_message(t('Successfully finished. Notifications sent:')+' '+this.sent);
            }
        }), this.bind(this, function(){
            desk_modal_progress_hide();
            desk_error(t('Load failed'));
        }));
    };

    desk_call(dash_push_push_preview, this);

    if (window.location.protocol != 'https:') {
        desk_error(t('Push notifications require HTTPS'));
    } else if (typeof dash_push_php_version_support == "undefined" || dash_push_php_version_support == 0) {
        desk_error(t('Push notifications require the latest PHP version'));
    }
};

var dash_push_push_preview = function(){
    var title = $(this.content).find('input.title_input').val();
    var body = $(this.content).find('input.body_input').val();
    var image = $(this.content).find('input.image_input').val();
    var preview = '';
    if (title.length > 0) preview += '<div style="font-weight:bold;margin-bottom:10px">'+title.replace(/</g, '&lt;').replace(/>/g, '&gt;')+'</div>';
    if (image.length > 0) preview += '<div style="float:left;margin-right:10px"><img src="'+baseUrl(image)+'" width="100" /></div>';
    if (body.length > 0) preview += '<div>'+body.replace(/</g, '&lt;').replace(/>/g, '&gt;')+'</div>';
    $(this.content).find('.push-preview').html(preview);

    if (title.length>0){
        this.enableItemsByProperty('action','begin');
    } else {
        this.disableItemsByProperty('action','begin');
    }
};

var dash_push_push_language = function(element) {
    var language = this.options.data.language;
    var id = $(element).attr('id');
    var item = this.findMenuItemByProperty('id',id);
    if (item && typeof(item.language)!="undefined") {
        if (item.language!=language) {
            this.options.data.language=item.language;
            $(element).parents('ul').find('.glyphicon-ok').removeClass('glyphicon-ok').addClass('glyphicon-filter');
            $(element).find('.glyphicon').removeClass('glyphicon-filter').addClass('glyphicon-ok');
            
            var startBtn = this.findToolbarItemByProperty('action','begin');
            if (startBtn && typeof(this.options.data.lang_subscribers[item.language])!="undefined") {
                $(startBtn.element).text(t('Start sending')+' ('+this.options.data.lang_subscribers[item.language]+')');
            }
        } else {
            this.options.data.language='';
            $(element).parents('ul').find('.glyphicon-ok').removeClass('glyphicon-ok').addClass('glyphicon-filter');
            
            var startBtn = this.findToolbarItemByProperty('action','begin');
            if (startBtn) {
                $(startBtn.element).text(t('Start sending')+' ('+this.options.data.subscribers+')');
            }
        }
    }
};

var dash_push_push_begin = function() {
    var title = $(this.content).find('input.title_input').val();
    var body = $(this.content).find('input.body_input').val();
    if (title.length>0) {
        desk_modal_progress();
        this.sent = 0;
        this.push();
    }
};

var dash_push_records_on_select = function() {
    var selected = this.getSelectedContentItems();
    this.disableItemsByProperty('typo','push');
    if (selected && selected.length == 1 && typeof(selected[0].typo)!="undefined" && selected[0].typo=="record" &&
        typeof(selected[0].activated)!="undefined" && selected[0].activated==record_status_published_id
    ) {
        this.enableItemsByProperty('typo','push');
    }
};

var dash_push_record_open = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_call(dash_push_push_open, null, selected[0].data);
    }
};