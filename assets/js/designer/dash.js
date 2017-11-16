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
    $(this.element).find('.dash_form_designer_main_style_checkbox').change(designer_style_page_input_select);
    
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
    var main = $(this).parents('form').find('.dash_form_designer_main_style_checkbox').is(':checked');
    
    if (main) {
        $(this).parents('form').find('.dash_form_designer_record_input').val('');
        $(this).parents('form').find('.dash_form_designer_record_hidden').val('');
        $(this).parents('form').find('.dash_form_designer_url_input').val('');
        
        $(this).parents('form').find('.language-select').attr('disabled','disabled'); 
        $(this).parents('form').find('.filter-select').attr('disabled','disabled'); 
        $(this).parents('form').find('.dash_form_designer_record_container').css('display', 'none');
        $(this).parents('form').find('.dash_form_designer_url_container').css('display', 'none');
        
        $(this).parents('form').find('.dash_form_designer_record_select').attr('disabled','disabled');
        
        $(this).parents('form').find('.dash_form_designer_record_select').val(-1);
        $(this).parents('form').find('.language-select').val('');
        $(this).parents('form').find('.filter-select').val('');
        
        return;
    } else {
        $(this).parents('form').find('.dash_form_designer_record_select').removeAttr('disabled','disabled'); 
    }
    
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
    designerEditorCallbacks[this.getId()]['designerEditorSave'] = (function(object, method) {
        return function(arg1, arg2) {
            method.call(object, arg1, arg2);
        };
    })(this, function(e, wndId){
        if (this.getId() != wndId) return;
        designer_designer_onsave.call(this);
        this.saveBody();
    });
    designerEditorCallbacks[this.getId()]['designerEditorFileSelector'] = (function(object, method) {
        return function(arg1, arg2, arg3, arg4) {
            method.call(object, arg1, arg2, arg3, arg4);
        };
    })(this, function(e, wndId, callback, context){
        if (this.getId() != wndId) return;
        context.desk_ds = desk_ds;
        context.baseUrl = baseUrl;
        desk_file_selector.call(this, callback);
    });
    designerEditorCallbacks[this.getId()]['designerEditorFocus'] = (function(object, method) {
        return function(arg1, arg2) {
            method.call(object, arg1, arg2);
        };
    })(this, function(e, wndId){
        if (this.getId() != wndId) return;
        this.focus();
    });
    designerEditorCallbacks[this.getId()]['designerEditorMessage'] = (function(object, method) {
        return function(arg1, arg2, arg3) {
            method.call(object, arg1, arg2, arg3);
        };
    })(this, function(e, wndId, text){
        if (this.getId() != wndId) return;
        desk_message(text);
    });
    designerEditorCallbacks[this.getId()]['designerEditorError'] = (function(object, method) {
        return function(arg1, arg2, arg3) {
            method.call(object, arg1, arg2, arg3);
        };
    })(this, function(e, wndId, text){
        if (this.getId() != wndId) return;
        desk_error(text);
    });
    designerEditorCallbacks[this.getId()]['designerChangeColor'] = (function(object, method) {
        return function(arg1, arg2, arg3) {
            method.call(object, arg1, arg2, arg3);
        };
    })(this, function(e, wndId, color){
        if (this.getId() != wndId) return;
        designer_designer_color_pallete.call(this, color);
    });
    designerEditorCallbacks[this.getId()]['designerChooseColor'] = (function(object, method) {
        return function(arg1, arg2, arg3) {
            method.call(object, arg1, arg2, arg3);
        };
    })(this, function(e, wndId, color){
        if (this.getId() != wndId) return;
        $(this.toolbar).find('input[name=designer-user-color]').val(color);
        $(this.toolbar).find('input[name=designer-user-color]').parent('.input-group').find('.glyphicon').css('color', color);
    });
    designerEditorCallbacks[this.getId()]['designerColorPickerShow'] = (function(object, method) {
        return function(arg1, arg2) {
            method.call(object, arg1, arg2);
        };
    })(this, function(e, wndId){
        if (this.getId() != wndId) return;
        designer_designer_color_pallete_show.call(this);
    });
    designerEditorCallbacks[this.getId()]['designerGradientPickerShow'] = (function(object, method) {
        return function(arg1, arg2) {
            method.call(object, arg1, arg2);
        };
    })(this, function(e, wndId){
        if (this.getId() != wndId) return;
        designer_designer_gradient_pallete_show.call(this);
    });
    designerEditorCallbacks[this.getId()]['designerColorPickerHide'] = (function(object, method) {
        return function(arg1, arg2) {
            method.call(object, arg1, arg2);
        };
    })(this, function(e, wndId){
        if (this.getId() != wndId) return;
        designer_designer_color_pallete_hide.call(this);
    });
    designerEditorCallbacks[this.getId()]['designerGradientPickerHide'] = (function(object, method) {
        return function(arg1, arg2) {
            method.call(object, arg1, arg2);
        };
    })(this, function(e, wndId){
        if (this.getId() != wndId) return;
        designer_designer_gradient_pallete_hide.call(this);
    });
    designerEditorCallbacks[this.getId()]['designerReady'] = (function(object, method) {
        return function(arg1, arg2, arg3) {
            method.call(object, arg1, arg2, arg3);
        };
    })(this, function(e, wndId, bodyFont){
        if (this.getId() != wndId) return;
        designer_designer_hide_overlay.call(this);
        designer_designer_hide_loader.call(this);
        if (typeof(bodyFont)!="undefined" && bodyFont && bodyFont.length>0) {
            bodyFont = bodyFont.replace(/^.+?[:](.+)$/g,'$1').split(',')[0].replace(/['"]/g,'');
            $(this.toolbar).find('select[name=designer-user-font]').val(bodyFont);
        } else {
            $(this.toolbar).find('select[name=designer-user-font]').val('');
        }
    });
    
    for (var eventName in designerEditorCallbacks[this.getId()]) {
        $('body').on(eventName, designerEditorCallbacks[this.getId()][eventName]);
    }
    
    var wnd = this;
    
    var fonts = [
        {name: '', value: t('Theme font')},
        {name: 'Arial', value: 'Arial'},
        {name: 'Times New Roman', value: 'Times New Roman'},
        {name: 'Verdana', value: 'Verdana'},
        {name: 'Georgia', value: 'Georgia'},
        {name: 'Helvetica', value: 'Helvetica'},
        {name: 'Courier', value: 'Courier'},
        {name: 'Garamond', value: 'Garamond'},
        {name: 'Trebuchet MS', value: 'Trebuchet MS'},
        {name: 'Impact', value: 'Impact'},
        {name: 'Tahoma', value: 'Tahoma'}
    ];
    var select_options = '';
    for (var i=0; i<fonts.length; i++) {
        select_options += '<option value="'+fonts[i].name+'">'+fonts[i].value+'</option>';
    }
    var select = '<div class="navbar-form navbar-left"><div class="form-group"><div class="input-group"><select class="form-control" name="designer-user-font">'+select_options+'</select></div></div></div>';
    var input = '<div class="navbar-form navbar-left"><div class="form-group"><div class="input-group"><input type="text" class="form-control" name="designer-user-color" placeholder="'+t('Color')+'"><span class="input-group-addon" id="basic-addon3"><span class="glyphicon glyphicon-stop" style="cursor:pointer"></span></span></div></div></div>';
    var container = $(this.toolbar).find('.navbar-default .container-fluid');
    if ($(container).length) {
        $(container).append(select);
        $(container).append(input);
    }
    
    $(this.toolbar).find('select[name=designer-user-font]').change(zira_bind(this, function(){
        var font = $(this.toolbar).find('select[name=designer-user-font]').val();
        designer_designer_user_font.call(this, font);
    }));
    
    $(this.toolbar).find('input[name=designer-user-color]').keyup(zira_bind(this, function(){
        var color = $(this.toolbar).find('input[name=designer-user-color]').val();
        $(this.toolbar).find('input[name=designer-user-color]').parent('.input-group').find('.glyphicon').css('color', color);
    }));
    
    $(this.toolbar).find('input[name=designer-user-color]').parent('.input-group').find('.glyphicon').click(function(){
        designer_designer_user_color.call(wnd, $(this).css('color'));
    });
    
    $(this.toolbar).on('click', '.colorpallete', function(){
        designer_designer_color_pallete_click.call(wnd, $(this).data('color'), $(this).data('picker'));
        $(this.toolbar).find('input[name=designer-user-color]').val($(this).data('color'));
        $(this.toolbar).find('input[name=designer-user-color]').parent('.input-group').find('.glyphicon').css('color', $(this).data('color'));
    });
};

var designer_designer_load = function() {
    if (typeof(this.options.data.items)=="undefined" || this.options.data.items.length!=1) return;
    var item = this.options.data.items[0];
    this.setBodyFullContent('<iframe src="'+designer_layout_url+'&id='+item+'#'+this.getId()+'" width="100%" height="100%" style="border:none;margin:0;padding:0"></iframe><form style="display:none"><textarea name="content"></textarea><input type="hidden" name="item" /></form>');
    designer_designer_show_overlay.call(this);
    designer_designer_show_loader.call(this);
};

var designer_designer_close = function() {
    if (typeof(designerEditorCallbacks)=="undefined") return;
    if (typeof(designerEditorCallbacks[this.getId()])=="undefined") return;
    for (var eventName in designerEditorCallbacks[this.getId()]) {
        $('body').off(eventName, designerEditorCallbacks[this.getId()][eventName]);
    }
    if (typeof(designerEditorWindow) != "undefined" && typeof(designerEditorWindow[this.getId()]) != "undefined"){
        designerEditorWindow[this.getId()] = null;
    }
};

var designer_designer_onsave = function() {
    if (typeof(designerEditorWindow) == "undefined") return;
    if (typeof(designerEditorWindow[this.getId()]) == "undefined" || !designerEditorWindow[this.getId()]) return;
    $(this.content).find('form textarea').val(designerEditorWindow[this.getId()].editorStyle());
    if (typeof(this.options.data.items)!="undefined" && this.options.data.items.length==1) {
        var item = this.options.data.items[0];
        $(this.content).find('form input[type=hidden]').val(item);
    }
};

var designer_designer_focus = function() {
    designer_designer_hide_overlay.call(this);
};

var designer_designer_blur = function() {
    designer_designer_show_overlay.call(this);
};

var designer_designer_show_overlay = function() {
    $(this.content).append('<div class="designer-designer-overlay" style="position:absolute;width:100%;height:100%;left:0;top:0;background:rgba(101,36,171,0.17);"></div>');
};

var designer_designer_hide_overlay = function() {
    $(this.content).find('.designer-designer-overlay').remove();
};

var designer_designer_show_loader = function() {
    $(this.content).append('<span class="zira-loader glyphicon glyphicon-refresh" style="position:absolute;"></span>');
    $(this.content).find('.zira-loader').css({
        'left': ($(this.content).width()-$(this.content).find('.zira-loader').width())/2,
        'top': ($(this.content).height()-$(this.content).find('.zira-loader').height())/2
    });
};

var designer_designer_hide_loader = function() {
    $(this.content).find('.zira-loader').remove();
};

var designer_designer_code = function() {
    if (typeof(designerEditorWindow) == "undefined") return;
    if (typeof(designerEditorWindow[this.getId()]) == "undefined" || !designerEditorWindow[this.getId()]) return;
    var code = designerEditorWindow[this.getId()].editorContent();
    $('#zira-message-dialog').bind('shown.bs.modal', zira_bind(this, function() {
        $('#zira-message-dialog').unbind('shown.bs.modal');
        this.cm = zira_codemirror($('#zira-message-dialog').find('textarea[name=desifner-style-code-message]'), 'css');
    }));
    desk_message('<div style="width:100%;height:400px;font-size:14px;"><textarea style="width:100%;height:400px;white-space:pre" cols="20" rows="12" name="desifner-style-code-message">'+code+'</textarea></div>', zira_bind(this, function(){;
        if (typeof(designerEditorWindow) == "undefined") return;
        if (typeof(designerEditorWindow[this.getId()]) == "undefined" || !designerEditorWindow[this.getId()]) return;
        try {
            this.cm.editor.save();
        } catch(err) {}
        var content = $('textarea[name=desifner-style-code-message]').val();
        if (typeof(content)!="undefined") {
            if (designerEditorWindow[this.getId()].editorInit(content)) {
                designerEditorWindow[this.getId()].updateStyle(content);
            }
        }
    }), false);
};

var designer_designer_user_font = function(font) {
    if (typeof(font)=="undefined") return;
    if (typeof(designerEditorWindow) == "undefined") return;
    if (typeof(designerEditorWindow[this.getId()]) == "undefined" || !designerEditorWindow[this.getId()]) return;
    designerEditorWindow[this.getId()].setFontFamily(font);
};

var designer_designer_user_color = function(color) {
    if (!color || !color.length || color=='transparent') return;
    if (typeof(designerEditorWindow) == "undefined") return;
    if (typeof(designerEditorWindow[this.getId()]) == "undefined" || !designerEditorWindow[this.getId()]) return;
    designerEditorWindow[this.getId()].setColorPickerValue(color);
    designerEditorWindow[this.getId()].setLeftGradientPickerValue(color);
    designerEditorWindow[this.getId()].setRightGradientPickerValue(color);
};

var designer_designer_color_pallete = function(color) {
    if (!color || !color.length || color=='transparent') return;
    if (typeof(designer_designer_color_pallete.colors)=="undefined") designer_designer_color_pallete.colors = {};
    if (typeof(designer_designer_color_pallete.colors[this.getId()])=="undefined") designer_designer_color_pallete.colors[this.getId()] = [];
    if ($.inArray(color, designer_designer_color_pallete.colors[this.getId()])<0) {
        designer_designer_color_pallete.colors[this.getId()].push(color);
        if (designer_designer_color_pallete.colors[this.getId()].length>7) {
            designer_designer_color_pallete.colors[this.getId()] = designer_designer_color_pallete.colors[this.getId()].slice(1);
        }
    }
};

var designer_designer_color_pallete_click = function(color, picker) {
    if (typeof(designerEditorWindow) == "undefined") return;
    if (typeof(designerEditorWindow[this.getId()]) == "undefined" || !designerEditorWindow[this.getId()]) return;
    if (typeof(color)=="undefined" || typeof(picker)=="undefined") return;
    if (picker == 'color') {
        designerEditorWindow[this.getId()].setColorPickerValue(color);
    } else if (picker == 'gradient-left') {
        designerEditorWindow[this.getId()].setLeftGradientPickerValue(color);
    } else if (picker == 'gradient-right') {
        designerEditorWindow[this.getId()].setRightGradientPickerValue(color);
    }
};

var designer_designer_color_pallete_show = function() {
    designer_designer_color_pallete_hide.call(this);
    designer_designer_gradient_pallete_hide.call(this);
    if (typeof(designer_designer_color_pallete.colors)=="undefined") return;
    if (typeof(designer_designer_color_pallete.colors[this.getId()])=="undefined") return;
    var container = $(this.toolbar).find('.navbar-default .container-fluid');
    var content = '';
    for (var i=designer_designer_color_pallete.colors[this.getId()].length-1; i>=0; i--) {
        var color = designer_designer_color_pallete.colors[this.getId()][i];
        content += '<div class="navbar-form navbar-left colorpicker-wrapper"><div class="form-group"><button class="btn btn-default colorpallete" data-color="'+color+'" data-picker="color"><span class="glyphicon glyphicon-stop" style="color:'+color+';"></span></button></div></div>';
    }
    if (!$(container).length || content.length==0) return;
    $(container).append(content);
};

var designer_designer_color_pallete_hide = function() {
    var container = $(this.toolbar).find('.navbar-default .container-fluid');
    if (!$(container).length) return;
    $(container).find('.colorpicker-wrapper').remove();
};

var designer_designer_gradient_pallete_show = function() {
    designer_designer_color_pallete_hide.call(this);
    designer_designer_gradient_pallete_hide.call(this);
    if (typeof(designer_designer_color_pallete.colors)=="undefined") return;
    if (typeof(designer_designer_color_pallete.colors[this.getId()])=="undefined") return;
    var container = $(this.toolbar).find('.navbar-default .container-fluid');
    var content = '';
    for (var i=designer_designer_color_pallete.colors[this.getId()].length-1; i>=0; i--) {
        var color = designer_designer_color_pallete.colors[this.getId()][i];
        content += '<div class="navbar-form navbar-left gradientpicker-wrapper"><div class="form-group"><div class="btn-group" role="group"><button class="btn btn-default colorpallete" data-color="'+color+'" data-picker="gradient-left"><span class="glyphicon glyphicon-stop" style="color:'+color+';"></span></button><button class="btn btn-default colorpallete" data-color="'+color+'" data-picker="gradient-right"><span class="glyphicon glyphicon-stop" style="color:'+color+';"></span></button></div></div></div>';
    }
    if (!$(container).length || content.length==0) return;
    $(container).append(content);
};

var designer_designer_gradient_pallete_hide = function() {
    var container = $(this.toolbar).find('.navbar-default .container-fluid');
    if (!$(container).length) return;
    $(container).find('.gradientpicker-wrapper').remove();
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