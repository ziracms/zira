(function($) {
    $(document).ready(function () {
        $('.container #content').on('keyup', '.form-input-autocomplete', zira_autocomplete);
        $('.container #content').on('focus', '.form-input-autocomplete', zira_autocomplete_focus);
        $('.container #content .form-input-autocomplete').attr('autocomplete','off');
    });

    zira_autocomplete = function(e) {
        if (typeof(e)!="undefined") {
            if (e.keyCode == 13) {
                e.stopPropagation();
                e.preventDefault();
            }
            if (e.keyCode == 37 || e.keyCode == 38 || e.keyCode == 39 || e.keyCode == 40 || e.keyCode == 13) return;
        }

        if (typeof(zira_autocomplete.inprogress)!="undefined" && zira_autocomplete.inprogress) return;
        try {
            window.clearTimeout(zira_autocomplete.timer);
        } catch(err) {}
        if (typeof(zira_autocomplete.text)!="undefined" && zira_autocomplete.text==$(this).val() &&
            typeof(zira_autocomplete.url)!="undefined" && zira_autocomplete.url==$(this).data('url') &&
            typeof(zira_autocomplete.token)!="undefined" && zira_autocomplete.token==$(this).data('token') &&
            typeof(zira_autocomplete.items)!="undefined"
        ) {
            zira_autocomplete_open.call(this, zira_autocomplete.items);
            return;
        }
        zira_autocomplete.token = $(this).data('token');
        zira_autocomplete.url = $(this).data('url');
        zira_autocomplete.text = $(this).val();
        if (zira_autocomplete.text.length==0 ||
            typeof(zira_autocomplete.url)=="undefined" ||
            typeof(zira_autocomplete.token)=="undefined"
        ) {
            zira_autocomplete_close.call(this);
            return;
        }
        if (typeof(zira_autocomplete.iterator)=="undefined") {
            zira_autocomplete.iterator = 0;
        }
        var delay = 250;
        if (zira_autocomplete.iterator >= 5) delay = 500;
        if (zira_autocomplete.iterator >= 10) delay = 1000;
        zira_autocomplete.timer = window.setTimeout(zira_bind(this, function(){
            zira_autocomplete.inprogress = true;
            zira_autocomplete.iterator++;
            $.post(zira_autocomplete.url,{
                'text': zira_autocomplete.text,
                'token': zira_autocomplete.token
            },zira_bind(this, function(response){
                zira_autocomplete.inprogress = false;
                if (!response) return;
                if (typeof(response.error)!="undefined") {
                    zira_error(response.error);
                } else if (typeof(response.items)!="undefined") {
                    zira_autocomplete.items = response.items;
                    zira_autocomplete_open.call(this, response.items);
                }
                if ($(this).val() != zira_autocomplete.text) {
                    zira_autocomplete.call(this);
                }
            }),'json');
        }), delay);

        $(this).data('autocomplete_id',null);
        $(this).data('autocomplete_text',null);
    };

    zira_autocomplete_focus = function() {
        if (typeof(zira_autocomplete.inprogress)!="undefined" && zira_autocomplete.inprogress) return;
        if (typeof(zira_autocomplete.text)!="undefined" && zira_autocomplete.text==$(this).val() &&
            typeof(zira_autocomplete.url)!="undefined" && zira_autocomplete.url==$(this).data('url') &&
            typeof(zira_autocomplete.token)!="undefined" && zira_autocomplete.token==$(this).data('token') &&
            typeof(zira_autocomplete.items)!="undefined"
        ) {
            zira_autocomplete_open.call(this, zira_autocomplete.items);
        }
    };

    zira_autocomplete_open = function(items) {
        zira_autocomplete_close.call(this);
        $('body').append('<ul class="zira-autocomplete-wnd"></ul>');
        for (var i in items) {
            $('.zira-autocomplete-wnd').append('<li><a href="javascript:void(0)" data-id="'+i+'">'+items[i]+'</a></li>');
        }
        $('.zira-autocomplete-wnd').css({
            'position': 'absolute',
            'top': $(this).offset().top + $(this).outerHeight(),
            'left': $(this).offset().left
        });
        zira_autocomplete_click.input = this;
        $('body').mousedown(zira_autocomplete_click);
        $('body').keyup(zira_autocomplete_press);
        $('.zira-autocomplete-wnd a').click(zira_autocomplete_select);
        $(this).parents('form').submit(zira_autocomplete_prevent_submit);
    };

    zira_autocomplete_close = function() {
        $('.zira-autocomplete-wnd').remove();
        $('body').unbind('mousedown',zira_autocomplete_click);
        $('body').unbind('keyup',zira_autocomplete_press);
        $(this).parents('form').unbind('submit',zira_autocomplete_prevent_submit);
    };

    zira_autocomplete_click = function(e) {
        console.log(2)
        if (typeof(e.originalEvent)=="undefined" || typeof(e.originalEvent.target)=="undefined") return;
        if ($(e.originalEvent.target).parents('.zira-autocomplete-wnd').length==0 &&
            !$(e.originalEvent.target).is(zira_autocomplete_click.input)
        ) {
            zira_autocomplete_close.call(zira_autocomplete_click.input);
        }
    };

    zira_autocomplete_select = function(e) {
        var text = $(this).text();
        var id = $(this).data('id');
        $(zira_autocomplete_click.input).val(text);
        $(zira_autocomplete_click.input).data('autocomplete_id',id);
        $(zira_autocomplete_click.input).data('autocomplete_text',text);
        zira_autocomplete_close.call(zira_autocomplete_click.input);
    };

    zira_autocomplete_press = function(e) {
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
                zira_autocomplete_select.call($(active));
            }
        }
    };

    zira_autocomplete_prevent_submit = function(e) {
        e.stopPropagation();
        e.preventDefault();
        e.keyCode = 13;
        zira_autocomplete_press.call(this, e);
    };
})(jQuery);