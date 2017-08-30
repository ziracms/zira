var dash_widget_load = function() {
    desk_window_form_init(this);
    
    $(this.element).find('.dash_form_widget_record_select').change(dash_widget_page_input_select);
    
    var input = $(this.element).find('.dash_form_widget_record_input');
    var hidden = $(this.element).find('.dash_form_widget_record_hidden');
    
    if ($(hidden).val().length==0) {
        $(this.element).find('.dash_form_widget_record_container').css('display', 'none');
    }
    
    $(this.element).find('.dash_form_widget_record_select').trigger('change');
    
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
            dash_widget_autocomplete_close.call();
            return;
        }
        this.autocomplete_timer = window.setTimeout(zira_bind(this, function(){
            var text = $(input).val();
            if (this.isDisabled()) return;
            try {
                window.clearTimeout(this.autocomplete_timer);
            } catch(e){}
            
            if (text.length == 0) return;
            
            desk_window_request(this, dash_widget_autocomplete_url, {'search': text}, zira_bind(this, function(items){
                if (!items) return;
                dash_widget_autocomplete_close.call();
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
                dash_widget_autocomplete_click.input = $(input).get(0);
                dash_widget_autocomplete_click.hidden = $(hidden).get(0);
                $('body').mousedown(dash_widget_autocomplete_click);
                $('body').keyup(dash_widget_autocomplete_press);
                $('.zira-autocomplete-wnd a').click(dash_widget_autocomplete_select);
            }));
        }),500);
    }));
};

var dash_widget_autocomplete_close = function() {
    $('.zira-autocomplete-wnd').remove();
    $('body').unbind('mousedown',dash_widget_autocomplete_click);
    $('body').unbind('keyup',dash_widget_autocomplete_press);
};

var dash_widget_autocomplete_click = function(e) {
    if (typeof(e.originalEvent)=="undefined" || typeof(e.originalEvent.target)=="undefined") return;
    if ($(e.originalEvent.target).parents('.zira-autocomplete-wnd').length==0 &&
        !$(e.originalEvent.target).is(dash_widget_autocomplete_click.input)
    ) {
        dash_widget_autocomplete_close.call();
    } else {
        $('.zira-autocomplete-wnd').css('z-index', parseInt($('.zira-autocomplete-wnd').css('z-index'))+1);
    }
};

var dash_widget_autocomplete_select = function(e) {
    var text = $(this).data('title');
    var id = $(this).data('id');
    $(dash_widget_autocomplete_click.input).val(text);
    $(dash_widget_autocomplete_click.input).data('autocomplete_id',id);
    $(dash_widget_autocomplete_click.input).data('autocomplete_text',text);
    $(dash_widget_autocomplete_click.hidden).val(id);
    dash_widget_autocomplete_close.call();
};

var dash_widget_autocomplete_press = function(e) {
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
            dash_widget_autocomplete_select.call($(active));
        }
    }
};

var dash_widget_page_input_select = function() {
    if ($(this).val()!='-2') {
        $(this).parents('form').find('.dash_form_widget_record_input').val('');
        $(this).parents('form').find('.dash_form_widget_record_hidden').val('');
    }
    
    if ($(this).val()=='0') {
        $(this).parents('form').find('.language-select').removeAttr('disabled');
        $(this).parents('form').find('.filter-select').attr('disabled','disabled'); 
        $(this).parents('form').find('.dash_form_widget_record_container').css('display', 'none');
    } else if ($(this).val()=='-2') {
        $(this).parents('form').find('.language-select').attr('disabled','disabled'); 
        $(this).parents('form').find('.filter-select').attr('disabled','disabled'); 
        $(this).parents('form').find('.dash_form_widget_record_container').css('display', 'block');
    } else {
        $(this).parents('form').find('.language-select').removeAttr('disabled');
        $(this).parents('form').find('.filter-select').removeAttr('disabled');
        $(this).parents('form').find('.dash_form_widget_record_container').css('display', 'none');
    }
};