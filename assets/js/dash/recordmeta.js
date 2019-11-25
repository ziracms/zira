var dash_recordmeta_load = function() {
    desk_window_form_init(this);
    
    var input = $(this.element).find('.dash_form_record_tags_input');
    var hidden = $(this.element).find('.dash_form_record_tags_hidden');
    var container = $(this.element).find('.dash_form_record_tags_container');
    
    if ($(hidden).val().length>0) {
        dash_recordmeta_tags_show(container, hidden);
    }
    
    $(input).unbind('keyup').keyup(zira_bind(this, function(e){
        if (typeof(e.keyCode)=="undefined") return;
        try {
            window.clearTimeout(this.autocomplete_timer);
        } catch(e){}
        if (e.keyCode == 13 && $(input).val().length > 0 && $('.zira-autocomplete-wnd').length == 0) {
            dash_recordmeta_tags_add(input, hidden, container);
            return;
        }
        if (e.key == ',' && $(input).val().length > 0 && $('.zira-autocomplete-wnd').length == 0) {
            var text = $(input).val();
            if (text.substr(text.length-1) == ',') text = text.substr(0, text.length-1);
            $(input).val(text);
            dash_recordmeta_tags_add(input, hidden, container);
            return;
        }
        if (e.keyCode == 37 || e.keyCode == 38 || e.keyCode == 39 || e.keyCode == 40 || e.keyCode == 13) return;
        if (this.isDisabled()) return;
        
        if ($(input).val().length == 0) {
            dash_recordmeta_tags_autocomplete_close.call();
            return;
        }
        this.autocomplete_timer = window.setTimeout(zira_bind(this, function(){
            var text = $(input).val();
            if (this.isDisabled()) return;
            try {
                window.clearTimeout(this.autocomplete_timer);
            } catch(e){}
            
            if (text.length == 0) return;
            
            desk_window_request(this, dash_recordmeta_tags_autocomplete_url, {'text': text}, zira_bind(this, function(items){
                if (!items) return;
                dash_recordmeta_tags_autocomplete_close.call();
                if (items.length == 0) return;
                $('body').append('<ul class="zira-autocomplete-wnd"></ul>');
                for (var i in items) {
                    $('.zira-autocomplete-wnd').append('<li><a href="javascript:void(0)" data-text="'+items[i].text+'">'+items[i].text+'</a></li>');
                }
                $('.zira-autocomplete-wnd').css({
                    'position': 'absolute',
                    'top': $(input).offset().top + $(input).outerHeight(),
                    'left': $(input).offset().left,
                    'z-index': this.getZ()+1
                });
                dash_recordmeta_tags_autocomplete_click.input = $(input).get(0);
                dash_recordmeta_tags_autocomplete_click.hidden = $(hidden).get(0);
                dash_recordmeta_tags_autocomplete_click.container = $(container).get(0);
                $('body').mousedown(dash_recordmeta_tags_autocomplete_click);
                $('body').keyup(dash_recordmeta_tags_autocomplete_press);
                $('.zira-autocomplete-wnd a').click(dash_recordmeta_tags_autocomplete_select);
            }));
        }),500);
    }));

    $(this.element).find('.dash_form_record_tags_wrapper').children('.add').click(function(){
        var text = $(input).val();
        dash_recordmeta_tags_add(input, hidden, container);
    });
};

var dash_recordmeta_tags_autocomplete_close = function() {
    $('.zira-autocomplete-wnd').remove();
    $('body').unbind('mousedown',dash_recordmeta_tags_autocomplete_click);
    $('body').unbind('keyup',dash_recordmeta_tags_autocomplete_press);
};

var dash_recordmeta_tags_autocomplete_click = function(e) {
    if (typeof(e.originalEvent)=="undefined" || typeof(e.originalEvent.target)=="undefined") return;
    if ($(e.originalEvent.target).parents('.zira-autocomplete-wnd').length==0 &&
        !$(e.originalEvent.target).is(dash_recordmeta_tags_autocomplete_click.input)
    ) {
        dash_recordmeta_tags_autocomplete_close.call();
    } else {
        $('.zira-autocomplete-wnd').css('z-index', parseInt($('.zira-autocomplete-wnd').css('z-index'))+1);
    }
};

var dash_recordmeta_tags_autocomplete_press = function(e) {
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
            dash_recordmeta_tags_autocomplete_select.call($(active));
        }
    }
};

var dash_recordmeta_tags_add = function(input, hidden, container) {
    var text = $(input).val();
    $(input).val('');
    if (text.length == 0) return;
    var hidden_text = $(hidden).val();
    if (hidden_text.length > 0) hidden_text += ',';
    hidden_text += text;
    $(hidden).val(hidden_text);
    dash_recordmeta_tags_autocomplete_close.call();
    dash_recordmeta_tags_show(container, hidden);
};

var dash_recordmeta_tags_autocomplete_select = function(e) {
    var text = $(this).data('text');
    $(dash_recordmeta_tags_autocomplete_click.input).val('');
    if (text.length == 0) return;
    var hidden_text = $(dash_recordmeta_tags_autocomplete_click.hidden).val();
    if (hidden_text.length > 0) hidden_text += ',';
    hidden_text += text;
    $(dash_recordmeta_tags_autocomplete_click.hidden).val(hidden_text);
    dash_recordmeta_tags_autocomplete_close.call();
    dash_recordmeta_tags_show(dash_recordmeta_tags_autocomplete_click.container, dash_recordmeta_tags_autocomplete_click.hidden);
};

var dash_recordmeta_tags_show = function(container, hidden) {
    $(container).html('');
    var hidden_text = $(hidden).val();
    if (hidden_text.length == 0) return;
    var hidden_arr = hidden_text.split(',');
    var added = [];
    for (var i=0; i<hidden_arr.length; i++) {
        if ($.inArray(hidden_arr[i], added) >= 0) continue;
        added.push(hidden_arr[i]);
        $(container).append('<span class="label label-primary" style="display:inline-block;font-size:100%;font-weight:normal;margin:0px 5px 5px 0px;"><span class="text">'+hidden_arr[i].replace(/</g,'&lt;').replace(/>/g,'&gt;')+'</span> <span class="glyphicon glyphicon-remove-circle remove" style="top:2px;cursor:pointer"></span></span> ');
    }
    $(container).find('.remove').click(function(){
        var label = $(this).parent();
        var text = $(label).children('.text').text();
        var hidden_text = $(hidden).val();
        if (hidden_text.length == 0) return;
        var hidden_arr = hidden_text.split(',');
        var new_hidden_text = '';
        for (var i=0; i<hidden_arr.length; i++) {
            if (hidden_arr[i] == text) continue;
            if (new_hidden_text.length > 0) new_hidden_text += ',';
            new_hidden_text += hidden_arr[i];
        }
        $(hidden).val(new_hidden_text);
        dash_recordmeta_tags_show(container, hidden);
    });
};