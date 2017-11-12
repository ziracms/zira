var designer_styles_load = function() {
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].inactive)!="undefined" && this.options.bodyItems[i].inactive) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
};

var designer_styles_select = function() {
    var selected = this.getSelectedContentItems();
    this.disableItemsByProperty('typo','activate');
    if (selected && selected.length==1) {
        if (typeof(selected[0].inactive)!="undefined" && selected[0].inactive) {
            this.enableItemsByProperty('typo','activate');
        }
    }
};

var designer_styles_copy = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_prompt(t('Enter title'), this.bind(this, function(name){
            if (name.length==0) return;
            desk_window_request(this, url('designer/dash/copy'),{'title':name, 'item':selected[0].data});
        }));
        $('#zira-prompt-dialog input[name=modal-input]').val(selected[0].title);
    }
};

var designer_styles_activate = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_window_request(this, url('designer/dash/activate'),{'item':selected[0].data});
    }
};

var designer_style_load = function() {
    desk_window_form_init(this);
    
    $(this.element).find('.dash_form_designer_record_select').change(designer_style_page_input_select);
    
    var input = $(this.element).find('.dash_form_designer_record_input');
    var hidden = $(this.element).find('.dash_form_designer_record_hidden');
    var input2 = $(this.element).find('.dash_form_designer_url_input');
    
    if ($(hidden).val().length==0) {
        $(this.element).find('.dash_form_designer_record_container').css('display', 'none');
    }
    if ($(input2).val().length==0) {
        $(this.element).find('.dash_form_designer_url_container').css('display', 'none');
    }
    
    $(this.element).find('.dash_form_designer_record_select').trigger('change');
    
    $(input).unbind('keyup').keyup(zira_bind(this, function(e){
        if (typeof(e.keyCode)=="undefined") return;
        try {
            window.clearTimeout(this.autocomplete_timer);
        } catch(e){}
        if (e.keyCode == 13) return;
        if (e.keyCode == 37 || e.keyCode == 38 || e.keyCode == 39 || e.keyCode == 40 || e.keyCode == 13) return;
        if (this.isDisabled()) return;
        
        $(input).data('autocomplete_id',null);
        $(input).data('autocomplete_text',null);
        $(hidden).val('');
            
        if ($(input).val().length == 0) {
            designer_style_autocomplete_close.call();
            return;
        }
        this.autocomplete_timer = window.setTimeout(zira_bind(this, function(){
            var text = $(input).val();
            if (this.isDisabled()) return;
            try {
                window.clearTimeout(this.autocomplete_timer);
            } catch(e){}
            
            if (text.length == 0) return;
            
            desk_window_request(this, designer_style_autocomplete_url, {'search': text}, zira_bind(this, function(items){
                if (!items) return;
                designer_style_autocomplete_close.call();
                $('body').append('<ul class="zira-autocomplete-wnd"></ul>');
                for (var i in items) {
                    $('.zira-autocomplete-wnd').append('<li><a href="javascript:void(0)" data-id="'+items[i].record_id+'" data-title="'+items[i].record_title+'">'+items[i].title+'</a></li>');
                }
                $('.zira-autocomplete-wnd').css({
                    'position': 'absolute',
                    'top': $(input).offset().top + $(input).outerHeight(),
                    'left': $(input).offset().left,
                    'z-index': this.getZ()+1
                });
                designer_style_autocomplete_click.input = $(input).get(0);
                designer_style_autocomplete_click.hidden = $(hidden).get(0);
                $('body').mousedown(designer_style_autocomplete_click);
                $('body').keyup(designer_style_autocomplete_press);
                $('.zira-autocomplete-wnd a').click(designer_style_autocomplete_select);
            }));
        }),500);
    }));
};

var designer_style_autocomplete_close = function() {
    $('.zira-autocomplete-wnd').remove();
    $('body').unbind('mousedown',designer_style_autocomplete_click);
    $('body').unbind('keyup',designer_style_autocomplete_press);
};

var designer_style_autocomplete_click = function(e) {
    if (typeof(e.originalEvent)=="undefined" || typeof(e.originalEvent.target)=="undefined") return;
    if ($(e.originalEvent.target).parents('.zira-autocomplete-wnd').length==0 &&
        !$(e.originalEvent.target).is(designer_style_autocomplete_click.input)
    ) {
        designer_style_autocomplete_close.call();
    } else {
        $('.zira-autocomplete-wnd').css('z-index', parseInt($('.zira-autocomplete-wnd').css('z-index'))+1);
    }
};

var designer_style_autocomplete_select = function(e) {
    var text = $(this).data('title');
    var id = $(this).data('id');
    $(designer_style_autocomplete_click.input).val(text);
    $(designer_style_autocomplete_click.input).data('autocomplete_id',id);
    $(designer_style_autocomplete_click.input).data('autocomplete_text',text);
    $(designer_style_autocomplete_click.hidden).val(id);
    designer_style_autocomplete_close.call();
};

var designer_style_autocomplete_press = function(e) {
    if (e.keyCode == 40) { // arrow down
        e.stopPropagation();
        e.preventDefault();
        var active = $('.zira-autocomplete-wnd li a.active');
        if ($(active).length==0) {
             $('.zira-autocomplete-wnd li:first-child a').addClass('active');
        } else {
            $(active).removeClass('active');
            var next = $(active).parent('li').next('li');
            if ($(next).length==0) {
                $('.zira-autocomplete-wnd li:first-child a').addClass('active');
            } else {
                $(next).children('a').addClass('active');
            }
        }
    } else if (e.keyCode == 38) { // arrow up
        e.stopPropagation();
        e.preventDefault();
        var active = $('.zira-autocomplete-wnd li a.active');
        if ($(active).length==0) {
             $('.zira-autocomplete-wnd li:last-child a').addClass('active');
        } else {
            $(active).removeClass('active');
            var prev = $(active).parent('li').prev('li');
            if ($(prev).length==0) {
                $('.zira-autocomplete-wnd li:last-child a').addClass('active');
            } else {
                $(prev).children('a').addClass('active');
            }
        }
    } else if (e.keyCode == 13 || e.keyCode == 39) { // enter or arrow right
        var active = $('.zira-autocomplete-wnd li a.active');
        if ($(active).length!=0) {
            e.stopPropagation();
            e.preventDefault();
            designer_style_autocomplete_select.call($(active));
        }
    }
};

var designer_style_page_input_select = function() {
    if ($(this).val()!='-2') {
        $(this).parents('form').find('.dash_form_designer_record_input').val('');
        $(this).parents('form').find('.dash_form_designer_record_hidden').val('');
    }
    if ($(this).val()!='-3') {
        $(this).parents('form').find('.dash_form_designer_url_input').val('');
    }
    
    if ($(this).val()=='0') {
        $(this).parents('form').find('.language-select').removeAttr('disabled');
        $(this).parents('form').find('.filter-select').attr('disabled','disabled'); 
        $(this).parents('form').find('.dash_form_designer_record_container').css('display', 'none');
        $(this).parents('form').find('.dash_form_designer_url_container').css('display', 'none');
    } else if ($(this).val()=='-2') {
        $(this).parents('form').find('.language-select').attr('disabled','disabled'); 
        $(this).parents('form').find('.filter-select').attr('disabled','disabled'); 
        $(this).parents('form').find('.dash_form_designer_record_container').css('display', 'block');
        $(this).parents('form').find('.dash_form_designer_url_container').css('display', 'none');
    } else if ($(this).val()=='-3') {
        $(this).parents('form').find('.language-select').removeAttr('disabled'); 
        $(this).parents('form').find('.filter-select').removeAttr('disabled'); 
        $(this).parents('form').find('.dash_form_designer_record_container').css('display', 'none');
        $(this).parents('form').find('.dash_form_designer_url_container').css('display', 'block');
    } else {
        $(this).parents('form').find('.language-select').removeAttr('disabled');
        $(this).parents('form').find('.filter-select').removeAttr('disabled');
        $(this).parents('form').find('.dash_form_designer_record_container').css('display', 'none');
        $(this).parents('form').find('.dash_form_designer_url_container').css('display', 'none');
    }
};

var designer_designer_open = function() {
    if (typeof(designerEditorCallbacks)=="undefined") designerEditorCallbacks = {};
    designerEditorCallbacks[this.getId()] = {};
    designerEditorCallbacks[this.getId()]['designerEditorSave'] = zira_bind(this, function(){
        designer_designer_onsave.call(this);
        this.saveBody();
    });
    designerEditorCallbacks[this.getId()]['designerEditorFileSelector'] = (function(object, method) {
        return function(arg1, arg2, arg3) {
            method.call(object, arg1, arg2, arg3);
        };
    })(this, function(e, callback, context){
        context.desk_ds = desk_ds;
        context.baseUrl = baseUrl;
        desk_file_selector.call(this, callback);
    });
    designerEditorCallbacks[this.getId()]['designerEditorFocus'] = zira_bind(this, function(){
        this.focus();
    });
    for (var eventName in designerEditorCallbacks[this.getId()]) {
        $('body').on(eventName, designerEditorCallbacks[this.getId()][eventName]);
    }
};

var designer_designer_load = function() {
    if (typeof(this.options.data.items)=="undefined" || this.options.data.items.length!=1) return;
    var item = this.options.data.items[0];
    this.setBodyFullContent('<iframe src="'+designer_layout_url+'&id='+item+'" width="100%" height="100%" style="border:none;margin:0;padding:0"></iframe><form style="display:none"><textarea name="content"></textarea><input type="hidden" name="item" /></form>');
};

var designer_designer_close = function() {
    if (typeof(designerEditorCallbacks)=="undefined") return;
    if (typeof(designerEditorCallbacks[this.getId()])=="undefined") return;
    for (var eventName in designerEditorCallbacks[this.getId()]) {
        $('body').off(eventName, designerEditorCallbacks[this.getId()][eventName]);
    }
};

var designer_designer_onsave = function() {
    if (typeof(designerEditorWindow) == "undefined") return;
    $(this.content).find('form textarea').val(designerEditorWindow.editorStyle());
    if (typeof(this.options.data.items)!="undefined" && this.options.data.items.length==1) {
        var item = this.options.data.items[0];
        $(this.content).find('form input[type=hidden]').val(item);
    }
};

var designer_designer_code = function() {
    if (typeof(designerEditorWindow) == "undefined") return;
    var code = designerEditorWindow.editorContent();
    $('#zira-message-dialog').bind('shown.bs.modal', zira_bind(this, function() {
        $('#zira-message-dialog').unbind('shown.bs.modal');
        this.cm = zira_codemirror($('#zira-message-dialog').find('textarea[name=desifner-style-code-message]'), 'css');
    }));
    desk_message('<div style="width:100%;height:400px;font-size:14px;"><textarea style="width:100%;height:400px;white-space:pre" cols="20" rows="12" name="desifner-style-code-message">'+code+'</textarea></div>', zira_bind(this, function(){;
        if (typeof(designerEditorWindow) == "undefined") return;
        try {
            this.cm.editor.save();
        } catch(err) {}
        var content = $('textarea[name=desifner-style-code-message]').val();
        if (typeof(content)!="undefined") designerEditorWindow.editorInit(content);
        designerEditorWindow.updateStyle(content);
    }), false);
};

var designer_designer_wnd = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        var data = {'items':[selected[0].data]};
        desk_call(designer_editor_wnd, null, {
            'data':data
        });
    }
};

var designer_css_wnd = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        var data = {'items':[selected[0].data]};
        desk_call(designer_css_editor_wnd, null, {
            'data':data
        });
    }
};