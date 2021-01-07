var dash_editor_text_focus = function() {
    $(this.content).find('textarea').focus();
};

var dash_editor_text_load = function() {
    this.contentModified = false;
    this.lineWrapping = parseInt(getCookie('zira_cm_line_wrapping')) ? true : false;
    this.lineNumbers = parseInt(getCookie('zira_cm_line_numbers')) ? true : false;
    $(this.content).find('textarea').focus();
    $(this.content).find('textarea').eq(0).keyup(this.bind(this, function(){
        var val = $(this.content).find('textarea').eq(0).val();
        this.resetFooterContent();
        this.appendFooterContent(val.length);
        this.contentModified = true;
    }));
    var val = $(this.content).find('textarea').eq(0).val();
    this.resetFooterContent();
    this.appendFooterContent(val.length);
    if (typeof this.options.data == "undefined") this.options.data = {};
    this.cm = zira_codemirror($(this.content).find('textarea'), this.options.data.highlight_mode, this.lineWrapping, this.lineNumbers);
    this.cm.change = zira_bind(this, function(){
        this.contentModified = true;
        var val = this.cm.editor.getDoc().getValue();
        this.resetFooterContent();
        this.appendFooterContent(val.length);
    });
    var lineWrapItem = this.findMenuItemByProperty('typo', 'line_wrapping');
    if (lineWrapItem && this.lineWrapping) {
        $(lineWrapItem.element).find('.glyphicon').removeClass('glyphicon-unchecked').addClass('glyphicon-check');
    }
    var lineNumItem = this.findMenuItemByProperty('typo', 'line_numbers');
    if (lineNumItem && this.lineNumbers) {
        $(lineNumItem.element).find('.glyphicon').removeClass('glyphicon-unchecked').addClass('glyphicon-check');
    }
};

var dash_editor_text_update = function() {
    if (typeof(this.cm)=="undefined") return;
    try {
        this.cm.editor.save();
    } catch(err) {}
};

var dash_editor_text_resize = function() {
    if (typeof(this.cm)=="undefined") return;
    try {
        window.clearTimeout(this.timer);
    } catch(err) {}
    this.timer = window.setTimeout(this.bind(this, function(){
        this.cm.editor.toTextArea();
        this.cm = zira_codemirror($(this.content).find('textarea'), this.options.data.highlight_mode, this.lineWrapping, this.lineNumbers);
    }), 500);
};

var dash_editor_text_line_wrap = function(element) {
    if (typeof this.lineWrapping == "undefined") return;
    if (!this.lineWrapping) {
        this.lineWrapping = true;
        $(element).find('.glyphicon').removeClass('glyphicon-unchecked').addClass('glyphicon-check');
    } else {
        this.lineWrapping = false;
        $(element).find('.glyphicon').removeClass('glyphicon-check').addClass('glyphicon-unchecked');
    }
    this.cm.editor.toTextArea();
    this.cm = zira_codemirror($(this.content).find('textarea'), this.options.data.highlight_mode, this.lineWrapping, this.lineNumbers);
    setCookie('zira_cm_line_wrapping', (this.lineWrapping ? 1 : 0), Infinity, '/');
};

var dash_editor_text_line_numbers = function(element) {
    if (typeof this.lineNumbers == "undefined") return;
    if (!this.lineNumbers) {
        this.lineNumbers = true;
        $(element).find('.glyphicon').removeClass('glyphicon-unchecked').addClass('glyphicon-check');
    } else {
        this.lineNumbers = false;
        $(element).find('.glyphicon').removeClass('glyphicon-check').addClass('glyphicon-unchecked');
    }
    this.cm.editor.toTextArea();
    this.cm = zira_codemirror($(this.content).find('textarea'), this.options.data.highlight_mode, this.lineWrapping, this.lineNumbers);
    setCookie('zira_cm_line_numbers', (this.lineNumbers ? 1 : 0), Infinity, '/');
};

var dash_editor_html_update = function() {
    if (typeof(this.editor)=="undefined") return;
    $(this.content).find('textarea[name=content]').val(this.editor.getContent());
};

var dash_editor_html_focus = function() {
    var l='';
    if (typeof(this.editor)!="undefined") {
        this.editor.focus();
        l = this.editor.getContent().replace(/&nbsp;/g,'').replace(/<[\s\S]*?>/g,'').length;
    }
    this.resetFooterContent();
    this.appendFooterContent(l);
};

var dash_editor_html_blur = function() {
    try {
        this.editor.iframeElement.blur();
    } catch(err) {}
};

var dash_editor_html_resize = function() {
    if (typeof(this.editor)=="undefined") return;
    var content_height = $(this.content).height();
    var editor_panel_height = $(this.content).find('.mce-toolbar[role=toolbar]').eq(0).parents('.mce-panel').outerHeight();
    this.editor.theme.resizeTo('100%',content_height-editor_panel_height);
};

var dash_editor_html_update = function() {
    if (typeof(this.editor)=="undefined") return;
    $(this.content).find('textarea[name=content]').val(this.editor.getContent());
};

var dash_editor_html_drop = function(element) {
    if (typeof(this.editor)=="undefined") return;
    if (element instanceof FileList) return;
    if (typeof(element)!="object" || typeof(element.type)=="undefined" || typeof(element.data)=="undefined" || typeof(element.title)=="undefined") return;
    if (typeof(element.parent)=="undefined") return;
    var html = '';
    if (element.parent=='files' && element.type=='image') {
        var width = '';
        var height = '';
        if (typeof(element.image_width)!="undefined") width=element.image_width;
        if (typeof(element.image_height)!="undefined") height=element.image_height;
        var regexp = new RegExp('\\'+desk_ds, 'g');
        html='<img class="image" src="'+encodeURIComponent(baseUrl(element.data.replace(regexp,'/'))).replace(/%2F/gi,'/')+'" alt="" width="'+width+'" height="'+height+'" />';
    } else if (element.parent=='files' && element.type!='folder') {
        var regexp = new RegExp('\\'+desk_ds, 'g');
        html='<a href="'+encodeURIComponent(baseUrl(element.data.replace(regexp,'/'))).replace(/%2F/gi,'/')+'" title="'+element.title+'" download="'+element.title+'">'+element.title+'</a>';
    } else if (element.parent=='record' && typeof(element.typo)!="undefined" && element.typo=='record' && typeof(element.page)!="undefined") {
        var language = '';
        if (typeof(element.language)!="undefined" && element.language!==null) language = element.language + '/';
        var page = url(language+element.page);
        var title = element.tooltip && element.tooltip.length>0 ? element.tooltip : element.title;
        html='<a href="'+page+'" title="'+element.title+'">'+title+'</a>';
    }
    if (html.length>0){
        this.editor.execCommand('mceInsertContent', false, html);
    }
};

var dash_editor_html_load = function() {
    this.contentModified = false;
    tinymce.init({
        selector:'#'+$(this.content).find('.editable').attr('id') ,
        plugins: 'paste, advlist, link, image, media, table, hr, pagebreak, code, contextmenu, textcolor',
        toolbar: ['desk_save | undo redo | table | bullist numlist | desk_file_selector  image media link | outdent indent | hr pagebreak | code', 'styleselect | bold italic underline | forecolor backcolor | removeformat |  aligncenter alignleft alignright alignjustify | desk_collapse_block '],
        menubar: false,
        language: dash_editor_language,
        valid_elements : '*[*]',
        paste_word_valid_elements: 'b,strong,i,em,h1,h2,h3,h4,h5,h6,p,ul,ol,li,hr,br,table,tr,td',
        paste_filter_drop: false,
        convert_urls: false,
        statusbar: false,
        auto_focus: true,
        init_instance_callback: this.bind(this, function (editor) {
            this.editor = editor;
            this.onResize();
            window.setTimeout(this.bind(this, this.onResize),1000);
            var openDialog = editor.windowManager.open;
            editor.windowManager.open = function(args, params){
                Desk.disableEvents();
                var modal = openDialog.call(this, args, params);
                modal.on('close', function(){
                    window.setTimeout(Desk.bind(Desk,Desk.enableEvents,500));
                });
                return modal;
            };
            editor.on('focus', this.bind({'desk':Desk,'wnd':this},function(){
                this.desk.focusWindow(this.wnd);
                this.wnd.onResize();
                if (this.wnd.isMenuDropdownOpened()) this.wnd.hideMenuDropdown();
            }));
            $(editor.getBody()).keydown(this.bind(Desk, function(e){
                if (e.shiftKey && e.keyCode==9) {
                    this.shift_pressed = true; this.keys_pressed = 1; this.onKeyDown(e); this.keys_pressed = 1; this.onKeyUp(e);
                } else if (e.ctrlKey && (e.keyCode==37 || e.keyCode==38 || e.keyCode==39 || e.keyCode==40)) {
                    this.ctrl_pressed = true; this.keys_pressed = 1; this.onKeyDown(e); this.onKeyUp(e);
                } else if (e.ctrlKey && e.keyCode==83) {
                    this.ctrl_pressed = true; this.keys_pressed = 1; this.onKeyDown(e); this.onKeyUp(e);
                } else if (!e.ctrlKey && !e.shiftKe && e.keyCode==27) {
                    this.keys_pressed = 0; this.onKeyDown(e); this.onKeyUp(e);
                }
            }));
            $(editor.getBody()).keyup(this.bind(this, function(e){
                if (!this.isInitialized() || this.isDisabled() || this.isMinimized()) {
                    e.stopPropagation(); e.preventDefault();
                }
                var l='';
                if (typeof(this.editor)!="undefined") {
                    l = this.editor.getContent().replace(/&nbsp;/g,'').replace(/<[\s\S]*?>/g,'').length;
                }
                this.resetFooterContent();
                this.appendFooterContent(l);
                this.contentModified = true;
            }));
            $(this.editor.getDoc()).unbind('dragover').bind('dragover',this.bind({'desk':Desk,'wnd':this}, function(e){this.desk.focusWindow(this.wnd);}));
            $(this.editor.getDoc()).unbind('drop').bind('drop',this.bind(Desk, function(e){this.onDrop(e);}));
        }),
        content_css: dash_editor_css,
        setup: this.bind(this,function(editor){
            editor.addButton('desk_save',{ 'icon': 'save', 'title':t('Save'), 'onclick': this.bind(this,function(){
                desk_window_save(this);
            })});
            editor.addButton('desk_file_selector',{ 'icon': 'browse', 'title':t('File manager'), 'onclick': this.bind(this,function(){
                desk_file_selector(this.bind(this,function(elements){
                for (var i=0; i<elements.length; i++){
                    var element = elements[i];
                    if (typeof(this.editor)=="undefined") return;
                    if (element instanceof FileList) return;
                    if (typeof(element)!="object" || typeof(element.type)=="undefined" || typeof(element.data)=="undefined" || typeof(element.title)=="undefined") return;
                    if (typeof(element.parent)=="undefined" || element.parent!='files') return;
                    var html = '';
                    if (i>0) html+= ' ';
                    if (element.type=='image') {
                        var width = '';
                        var height = '';
                        if (typeof(element.image_width)!="undefined") width=element.image_width;
                        if (typeof(element.image_height)!="undefined") height=element.image_height;
                        var regexp = new RegExp('\\'+desk_ds, 'g');
                        html+='<img src="'+encodeURIComponent(baseUrl(element.data.replace(regexp,'/'))).replace(/%2F/gi,'/')+'" alt="" width="'+width+'" height="'+height+'" class="image" />';
                    } else if (element.type!='folder') {
                        var regexp = new RegExp('\\'+desk_ds, 'g');
                        html+='<a href="'+encodeURIComponent(baseUrl(element.data.replace(regexp,'/'))).replace(/%2F/gi,'/')+'" title="'+element.title+'" download="'+element.title+'">'+element.title+'</a>';
                    }
                    if (html.length>0){
                        this.editor.execCommand('mceInsertContent', false, html);
                    }
                }
                }));
            })});
            editor.addButton('desk_collapse_block',{ 'icon': 'template', 'title':t('Add collapsible block'), 'onclick': this.bind(this,function(){
                var text='<p>'+t('Collapsible block')+'</p>';
                var selection = this.editor.selection.getContent({format: 'html'});
                if (selection && selection.length>0) text = selection;
                var id = (new Date()).getTime();
                var html = '<div class="zira-collapse-toggle" data-toggle="collapse" data-target=".collapse-block-'+id+'">'+t('Hidden text')+'</div>';
                html += '<div class="collapse zira-collapse-block collapse-block-'+id+'">'+text+'</div>&nbsp;';
                this.editor.execCommand('mceInsertContent', false, html);
            })});
        })
    });
};