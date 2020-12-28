(function($){
    $(document).ready(function(){
        if (!$('body').hasClass('dashboard')) {
            resize.scrollBarWidth = detectScrollBarWidth();
            resize();
            $('#top-menu-wrapper .search-icon').click(openSearch);
            $('#top-menu-wrapper .search-close-icon').click(closeSearch);
            if ($('#user-menu').length == 0 && $('#language-switcher').length == 0 && $('.header-top-item').length == 0) {
                $('header').addClass('no-user-menu');
            }
            initOverlays();
        }
        $('body').on('mousedown', '.btn, .form-control, ul.dropdown-menu li a, #secondary-menu-wrapper ul li a, .secondary-custom-menu-wrapper ul li a, .pagination li a', rippleEffect);
        $('body').on('focus', '.form-control', function(){
            $(this).parents('.form-group').children('.control-label').addClass('active');
        });
        $('body').on('blur', '.form-control', function(){
            $(this).parents('.form-group').children('.control-label').removeClass('active');
        });
        window.setTimeout(function(){
            $('.form-control:focus').trigger('focus');
        }, 500);
    });

    if (!$('body').hasClass('dashboard')) {
        $(window).load(function(){
            $('#preloader').hide();
        });
        
        $(window).resize(function() {
            try {
                window.clearTimeout(resize.timer);
            } catch(e) {}
            resize.timer = window.setTimeout(resize, 1000);
            resize();
        });

        $(window).scroll(function() {
            try {
                window.clearTimeout(initOverlays.timer);
            } catch(e) {}
            initOverlays.timer = window.setTimeout(initOverlays, 1000);
            resize();
        });
    }

    function detectScrollBarWidth() {
        var pEl = $('<div />');
        var cEl = $('<div />');
        $(pEl).css({
           visibility: 'hidden',
           width: 100,
           overflow: 'scroll' 
        });
        $(cEl).css({
            width: '100%'
        });
        $(pEl).append(cEl);
        $('body').append(pEl);
        var w = 100 - $(cEl).outerWidth();
        $(pEl).remove();
        return w;
    }
    
    function resize() {
        // top menu
        if ($(window).width() + resize.scrollBarWidth > 768 && !$('#top-menu-wrapper').hasClass('fixed') && $('#site-logo-wrapper').width() + $('#top-menu-wrapper').width() > $('header .container').width()) {
            $('#top-menu-wrapper').css({
                'marginRight': ($(window).width() - $('#top-menu-wrapper').width())/2
            }).addClass('menuCentered');
        } else {
            $('#top-menu-wrapper').css({
                'marginRight': ''
            }).removeClass('menuCentered');
        }
    }

    function initOverlays() {
        $('.list .list-item .list-thumb').each(function(){
            if ($(this).hasClass('i-overlay')) return true;
            $(this).addClass('i-overlay');
            $(this).append('<div class="list-overlay" />');
        });
        $('.gallery li a').each(function(){
            if ($(this).hasClass('i-overlay')) return true;
            $(this).addClass('i-overlay');
            $(this).append('<div class="gallery-overlay" />').append('<div class="gallery-overlay-icon"><span class="glyphicon glyphicon-zoom-in"></span></div>');
        });
    }

    function openSearch() {
        $('#top-menu-wrapper .search-icon').addClass('active');
        $('#top-menu-wrapper .search-close-icon').addClass('visible');
        window.setTimeout(function() {
            $('body').addClass('overlayed');
            $('#top-menu-wrapper .search-close-icon').addClass('active');
            $('#top-menu-wrapper .search-simple-form-container').addClass('active');
        }, 100);
    }

    function closeSearch() {
        $('#top-menu-wrapper .search-icon').removeClass('active');
        $('#top-menu-wrapper .search-close-icon').removeClass('active');
        $('#top-menu-wrapper .search-simple-form-container').removeClass('active');
        $('body').removeClass('overlayed');
        window.setTimeout(function() {
            $('#top-menu-wrapper .search-close-icon').removeClass('visible');
        }, 700);
    }

    function rippleEffect(e) {
        if ($(this).css('backgroundImage') != 'none') return;
        var size = Math.min(parseInt($(this).width()), parseInt($(this).height()));
        var x = parseInt(e.pageX - $(this).offset().left - size/2);
        var y = parseInt(e.pageY - $(this).offset().top - size/2);
        var colorR = 128;
        var colorG = 128;
        var colorB = 128;
        var colorA = .5;
        var color = 'rgba('+colorR+', '+colorG+', '+colorB+', '+colorA+')';
        $(this).data('x', x);
        $(this).data('y', y);
        $(this).data('size', size);
        $(this).data('colorR', colorR);
        $(this).data('colorG', colorG);
        $(this).data('colorB', colorB);
        $(this).data('colorA', colorA);
        $(this).css('background-image', 'radial-gradient(circle at '+x+'px '+y+'px, '+color+' '+size+'px, transparent '+(size+1)+'px)');
        startRippleAnimation($(this), 500, 5, 0.03);
    }

    function startRippleAnimation(object, duration, deltaSize, deltaColorA) {
        delete rippleAnimation.start;
        rippleAnimation.duration = duration;
        rippleAnimation.deltaSize = deltaSize;
        rippleAnimation.deltaColorA = deltaColorA;
        window.requestAnimationFrame(zira_bind(object, rippleAnimation));
    }

    function rippleAnimation(timestamp) {
        if (typeof rippleAnimation.start == "undefined") rippleAnimation.start = timestamp;
        var elapsed = timestamp - rippleAnimation.start;
        if (typeof rippleAnimation.duration == "undefined") rippleAnimation.duration = 1000;
        if (typeof rippleAnimation.deltaSize == "undefined") rippleAnimation.deltaSize = 1;
        if (typeof rippleAnimation.deltaColorA == "undefined") rippleAnimation.deltaColorA = 0.01;
        var x = $(this).data('x');
        var y = $(this).data('y');
        var size = $(this).data('size');
        var colorR = $(this).data('colorR');
        var colorG = $(this).data('colorG');
        var colorB = $(this).data('colorB');
        var colorA = $(this).data('colorA');
        size = parseInt(size)+rippleAnimation.deltaSize;
        colorA = parseFloat(colorA)-rippleAnimation.deltaColorA;
        if (colorA < 0) colorA = 0;
        var color = 'rgba('+colorR+', '+colorG+', '+colorB+', '+colorA+')';
        $(this).data('size', size);
        $(this).data('colorA', colorA);
        $(this).css('background-image', 'radial-gradient(circle at '+x+'px '+y+'px, '+color+' '+size+'px, transparent '+(size+1)+'px)');
        if (elapsed < rippleAnimation.duration) window.requestAnimationFrame(zira_bind(this, rippleAnimation));
        else $(this).css('background-image', '');
    }
})(jQuery);