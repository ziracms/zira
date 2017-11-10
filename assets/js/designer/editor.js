(function($){
    var mediaType = {
        media768: '@media (min-width:768px)',
        media1200: '@media (min-width:1200px)'
    };

    var mediaStyle = {
        container: {
            selector: '.container',
            width: '100%',
            maxWidth: '1400px'
        },
        colSm8: {
            selector: '.col-sm-8#content',
            width: '60%'
        },
        colSm4: {
            selector: '.col-sm-4.sidebar',
            width: '39.9999%'
        },
        colSm2: {
            selector: '.col-sm-2.sidebar',
            width: '19.9999%'
        }
    };
        
    $(document).ready(function(){
        $('body').append('<div class="designer_overlay"></div>');

        window.editorInit();
        
        var colorpicker_size = 22;
        var radiobtn_size = 26;
        var colorpicker_wnd_size = 245;
        var gradientpicker_wnd_size = 280;
        var container_x = $('#content').offset().left;
        
        var designer_selectors = '.designer_colorpicker, .designer_gradientpicker, .designer_imagepicker, .designer_patternpicker, .designer_fontpicker, .designer_fontpicker_sign, .designer_radio_btn';

        var designer_positions = [];
        $(window).resize(function(){
            $(designer_selectors).addClass('freeze');
            for (var name in designer_positions) {
                designer_positions[name].call();
            }
            $(designer_selectors).removeClass('freeze');
        });

        // body
        if ($('body').length>0 && $('#main-container-wrapper').length>0 && $('#main-container').length>0) {
            // body bg color
            var body_bg = $('body').css('backgroundColor');
            $('body').append('<div class="designer_colorpicker" id="body-designer-colorpicker" title="'+t('Background color')+'"></div>');
            designer_positions['body_color'] = function() {
                var body_cx = colorpicker_size/2;
                var body_cy = $('header').offset().top+$('header').outerHeight()+1.25*colorpicker_size;
                $('#body-designer-colorpicker').css({'left':body_cx,'top':body_cy});
            };
            $('#body-designer-colorpicker').tooltip();
            designer_colorpicker($('#body-designer-colorpicker'), body_bg, function(color){
                $('body').css('background', color);
                $('#main-container-wrapper').css('background', 'none');
                $('#main-container').css('background', 'none');
                if (color.indexOf('rgba')==0) {
                    setBackgroundColorStyle('body', hexColor(color));
                    setBackgroundStyle('body', color, true);
                    setFilterStyle('body', 'progid:DXImageTransform.Microsoft.gradient(startColorstr=' + rgbaToHexIE(color) + ',endColorstr=' + rgbaToHexIE(color) + ',GradientType=0)');
                } else {
                    setBackgroundStyle('body', 'none');
                    setBackgroundColorStyle('body', color, true);
                }
                setBackgroundStyle('#main-container-wrapper,#main-container', 'none');
                
                // body height btn
                if ($('#html-designer-radio-btn').length>0) {
                    $('#html-designer-radio-btn').show();
                }
            }, 'left', false);
            
            // body bg gradient
            var body_gr = extractGradient($('body'));
            $('body').append('<div class="designer_gradientpicker" id="body-designer-gradientpicker" title="'+t('Background gradient')+'"></div><div class="designer_gradientpicker_hidden" id="body-designer-gradientpicker-hidden"></div>');
            designer_positions['body_gradient'] = function() {
                var body_gx = colorpicker_size/2+1.25*colorpicker_size;
                var body_gy = $('header').offset().top+$('header').outerHeight()+.5*colorpicker_size;
                $('#body-designer-gradientpicker').css({'left':body_gx,'top':body_gy});
                $('#body-designer-gradientpicker-hidden').css({'left':body_gx+gradientpicker_wnd_size,'top':body_gy});
            };
            $('#body-designer-gradientpicker').tooltip();
            designer_gradientpicker($('#body-designer-gradientpicker'), $('#body-designer-gradientpicker-hidden'), body_gr[0], body_gr[1], function(color1, color2){
                $('body').css('background', 'linear-gradient(to bottom,' + color1 + ',' + color2 + ')');
                $('#main-container-wrapper').css('background', 'none');
                $('#main-container').css('background', 'none');
                setBackgroundColorStyle('body', hexColor(color1), true);
                setBackgroundGradientStyle('body', color1, color2);
                setBackgroundStyle('#main-container-wrapper,#main-container', 'none');
                
                // removing body height = 100%
                if ($('#html-designer-radio-btn').length>0) {
                    if ($('body').height() == $(window).height()) {
                        $('#html-designer-radio-btn').trigger('click');
                    }
                    $('#html-designer-radio-btn').hide();
                }
            }, 'left');
            
            // body bg image
            $('body').append('<div class="designer_imagepicker" id="body-designer-imagepicker" title="'+t('Background image')+'"></div>');
            designer_positions['body_image'] = function() {
                var body_ix = colorpicker_size/2+1.25*colorpicker_size;
                var body_iy = $('header').offset().top+$('header').outerHeight()+2*colorpicker_size;
                $('#body-designer-imagepicker').css({'left':body_ix,'top':body_iy});
            };
            $('#body-designer-imagepicker').tooltip();
            designer_imagepicker($('#body-designer-imagepicker'), function(url){
                var bg_color = $('body').css('backgroundColor');
                var t = (new Date()).getTime();
                var background = hexColor(bg_color) + ' url(' + url + '?t=' + t + ') no-repeat 50% 0%';
                $('body').css('background', background);
                $('#main-container-wrapper').css('background', 'none');
                $('#main-container').css('background', 'none');
                setBackgroundStyle('body', background);
                setBackgroundStyle('#main-container-wrapper,#main-container', 'none');
                
                // body height btn
                if ($('#html-designer-radio-btn').length>0) {
                    $('#html-designer-radio-btn').show();
                }
            });
            
            // body bg pattern
            $('body').append('<div class="designer_patternpicker" id="body-designer-patternpicker" title="'+t('Background pattern')+'"></div>');
            designer_positions['body_pattern'] = function() {
                var body_px = colorpicker_size/2+2.5*colorpicker_size;
                var body_py = $('header').offset().top+$('header').outerHeight()+1.25*colorpicker_size;
                $('#body-designer-patternpicker').css({'left':body_px,'top':body_py});
            };
            $('#body-designer-patternpicker').tooltip();
            designer_imagepicker($('#body-designer-patternpicker'), function(url){
                var bg_color = $('body').css('backgroundColor');
                var t = (new Date()).getTime();
                var background = hexColor(bg_color) + ' url(' + url + '?t=' + t + ') repeat 0 0';
                $('body').css('background', background);
                $('#main-container-wrapper').css('background', 'none');
                $('#main-container').css('background', 'none');
                setBackgroundStyle('body', background);
                setBackgroundStyle('#main-container-wrapper,#main-container', 'none');
                
                // body height btn
                if ($('#html-designer-radio-btn').length>0) {
                    $('#html-designer-radio-btn').show();
                }
            });
        }
        
        // header
        if ($('header').length>0) {
            // header bg color
            var header_bg = $('header').css('backgroundColor');
            $('body').append('<div class="designer_colorpicker" id="header-designer-colorpicker" title="'+t('Header color')+'"></div>');
            designer_positions['header_color'] = function() {
                var header_cx = $('header').offset().left+($('header').outerWidth()-colorpicker_size)/2-colorpicker_size;
                var header_cy = $('header').offset().top+($('header').outerHeight()-colorpicker_size)/2-.75*colorpicker_size;
                $('#header-designer-colorpicker').css({'left':header_cx,'top':header_cy});
            };
            $('#header-designer-colorpicker').tooltip();
            designer_colorpicker($('#header-designer-colorpicker'), header_bg, function(color){
                $('header').css('background', color);
                if (color.indexOf('rgba')==0) {
                    setBackgroundColorStyle('header', hexColor(color));
                    setBackgroundStyle('header', color, true);
                    setFilterStyle('header', 'progid:DXImageTransform.Microsoft.gradient(startColorstr=' + rgbaToHexIE(color) + ',endColorstr=' + rgbaToHexIE(color) + ',GradientType=0)');
                } else {
                    setBackgroundColorStyle('header', color);
                }
                setBackgroundColorStyle('header .zira-search-preview-wnd .list .list-item,header .zira-search-preview-wnd .list .list-item:hover', hexColor(color));
            }, 'right', false);
            
            // header bg gradient
            var header_gr = extractGradient($('header'));
            $('body').append('<div class="designer_gradientpicker" id="header-designer-gradientpicker" title="'+t('Header gradient')+'"></div><div class="designer_gradientpicker_hidden" id="header-designer-gradientpicker-hidden"></div>');
            designer_positions['header_gradient'] = function() {
                var header_gx = $('header').offset().left+($('header').outerWidth()-colorpicker_size)/2+.25*colorpicker_size;
                var header_gy = $('header').offset().top+($('header').outerHeight()-colorpicker_size)/2-1.5*colorpicker_size;
                $('#header-designer-gradientpicker').css({'left':header_gx,'top':header_gy});
                $('#header-designer-gradientpicker-hidden').css({'left':header_gx+gradientpicker_wnd_size,'top':header_gy});
            };
            $('#header-designer-gradientpicker').tooltip();
            designer_gradientpicker($('#header-designer-gradientpicker'), $('#header-designer-gradientpicker-hidden'), header_gr[0], header_gr[1], function(color1, color2){
                $('header').css('background', 'linear-gradient(to bottom,' + color1 + ',' + color2 + ')');
                setBackgroundColorStyle('header', hexColor(color1), true);
                setBackgroundGradientStyle('header', color1, color2);
                setBackgroundColorStyle('header .zira-search-preview-wnd .list .list-item,header .zira-search-preview-wnd .list .list-item:hover', hexColor(color1), true);
            });
            
            // header bg image
            $('body').append('<div class="designer_imagepicker" id="header-designer-imagepicker" title="'+t('Header image')+'"></div>');
            designer_positions['header_image'] = function() {
                var header_ix = $('header').offset().left+($('header').outerWidth()-colorpicker_size)/2+.25*colorpicker_size;
                var header_iy = $('header').offset().top+($('header').outerHeight()-colorpicker_size)/2;
                $('#header-designer-imagepicker').css({'left':header_ix,'top':header_iy});
            };
            $('#header-designer-imagepicker').tooltip();
            designer_imagepicker($('#header-designer-imagepicker'), function(url){
                var bg_color = $('header').css('backgroundColor');
                var t = (new Date()).getTime();
                var background = hexColor(bg_color) + ' url(' + url + '?t=' + t + ') no-repeat 50% 0%';
                $('header').css('background', background);
                setBackgroundStyle('header', background);
                if (bg_color != 'transparent') {
                    setBackgroundColorStyle('header .zira-search-preview-wnd .list .list-item,header .zira-search-preview-wnd .list .list-item:hover', bg_color, true);
                }
            });
            
            // header bg pattern
            $('body').append('<div class="designer_patternpicker" id="header-designer-patternpicker" title="'+t('Header pattern')+'"></div>');
            designer_positions['header_pattern'] = function() {
                var header_px = $('header').offset().left+($('header').outerWidth()-colorpicker_size)/2+1.5*colorpicker_size;
                var header_py = $('header').offset().top+($('header').outerHeight()-colorpicker_size)/2-.75*colorpicker_size;
                $('#header-designer-patternpicker').css({'left':header_px,'top':header_py});
            };
            $('#header-designer-patternpicker').tooltip();
            designer_imagepicker($('#header-designer-patternpicker'), function(url){
                var bg_color = $('header').css('backgroundColor');
                var t = (new Date()).getTime();
                var background = hexColor(bg_color) + ' url(' + url + '?t=' + t + ') repeat 0 0';
                $('header').css('background', background);
                setBackgroundStyle('header', background);
                if (bg_color != 'transparent') {
                    setBackgroundColorStyle('header .zira-search-preview-wnd .list .list-item,header .zira-search-preview-wnd .list .list-item:hover', bg_color, true);
                }
            });
            
            // header logo
            if ($('header #site-logo span').length>0) {
                // header logo color
                var logo_color1 = $('header #site-logo').css('color');
                $('header #site-logo').addClass('active');
                var logo_color2 = $('header #site-logo').css('color');
                $('header #site-logo').removeClass('active');
                $('body').append('<div class="designer_colorpicker" id="logo-designer-gradientpicker" title="'+t('Logo color')+'"></div><div class="designer_gradientpicker_hidden" id="logo-designer-gradientpicker-hidden"></div>');
                designer_positions['logo_color'] = function() {
                    if ($('header #site-logo').css('display')=='none' || $('header #site-logo').css('visibility')=='hidden') {
                        $('#logo-designer-gradientpicker').hide();
                        $('#logo-designer-gradientpicker-hidden').hide();
                        return;
                    }
                    var logo_cx = $('header #site-logo').offset().left+.5*colorpicker_size;
                    var logo_cy = $('header #site-logo').offset().top+($('header #site-logo').outerHeight()-colorpicker_size)/2+.75*colorpicker_size;
                    $('#logo-designer-gradientpicker').css({'left':logo_cx,'top':logo_cy});
                    $('#logo-designer-gradientpicker-hidden').css({'left':logo_cx+colorpicker_wnd_size,'top':logo_cy});
                    $('#logo-designer-gradientpicker').show();
                    $('#logo-designer-gradientpicker-hidden').show();
                };
                $('#logo-designer-gradientpicker').tooltip();
                designer_gradientpicker($('#logo-designer-gradientpicker'), $('#logo-designer-gradientpicker-hidden'), logo_color1, logo_color2, function(color1, color2){
                    $('header #site-logo').css('color', color1);
                    $('header #site-logo').stop(true, true).animate({'color':color2},1000,function(){
                        $('header #site-logo').css('color', color2);
                        $('header #site-logo').animate({'color':color1},1000,function(){
                            $('header #site-logo').css('color', color1);
                        });
                    });
                    setColorStyle('#site-logo-wrapper a#site-logo:link,#site-logo-wrapper a#site-logo:visited', color1);
                    setColorStyle('#site-logo-wrapper a#site-logo:hover,#site-logo-wrapper a#site-logo.active', color2);
                }, 'left', 'rgb');
                
                // header logo font size
                var logo_font = $('header #site-logo span').css('fontSize');
                $('body').append('<div class="designer_fontpicker" id="logo-designer-fontpicker" title="'+t('Logo font size')+'"></div>');
                designer_positions['logo_font'] = function() {
                    if ($('header #site-logo').css('display')=='none' || $('header #site-logo').css('visibility')=='hidden') {
                        $('#logo-designer-fontpicker').hide();
                        return;
                    }
                    var logo_fx = $('header #site-logo').offset().left+.5*colorpicker_size;
                    var logo_fy = $('header #site-logo').offset().top+($('header #site-logo').outerHeight()-colorpicker_size)/2-.75*colorpicker_size;
                    $('#logo-designer-fontpicker').css({'left':logo_fx,'top':logo_fy});
                    $('#logo-designer-fontpicker').show();
                };
                $('#logo-designer-fontpicker').tooltip();
                designer_fontpicker($('#logo-designer-fontpicker'), logo_font, designer_positions, function(size){
                    $('header #site-logo span').css('fontSize', size);
                    setFontSizeStyle('#site-logo-wrapper a#site-logo span', size);
                    $(window).trigger('resize');
                });
            }
            
            // header slogan
            if ($('header #site-slogan span').length>0) {
                // header slogan color
                var slogan_color = $('header #site-slogan').css('color');
                $('body').append('<div class="designer_colorpicker" id="slogan-designer-colorpicker" title="'+t('Slogan color')+'"></div>');
                designer_positions['slogan_color'] = function() {
                    if ($('header #site-slogan').css('display')=='none' || $('header #site-slogan').css('visibility')=='hidden') {
                        $('#slogan-designer-colorpicker').hide();
                        return;
                    }
                    var slogan_cx = $('header #site-slogan').offset().left+.5*colorpicker_size;
                    var slogan_cy = $('header #site-slogan').offset().top+($('header #site-slogan').outerHeight()-colorpicker_size)/2;
                    $('#slogan-designer-colorpicker').css({'left':slogan_cx,'top':slogan_cy});
                    $('#slogan-designer-colorpicker').show();
                };
                $('#slogan-designer-colorpicker').tooltip();
                designer_colorpicker($('#slogan-designer-colorpicker'), slogan_color, function(color){
                    $('header #site-slogan').css('color', color);
                    setColorStyle('#site-logo-wrapper #site-slogan', color);
                }, 'left');
                
                // header slogan font size
                var slogan_font = $('header #site-slogan').css('fontSize');
                $('body').append('<div class="designer_fontpicker" id="slogan-designer-fontpicker" title="'+t('Slogan font size')+'"></div>');
                designer_positions['slogan_font'] = function() {
                    if ($('header #site-slogan').css('display')=='none' || $('header #site-slogan').css('visibility')=='hidden') {
                        $('#slogan-designer-fontpicker').hide();
                        return;
                    }
                    var slogan_fx = $('header #site-slogan').offset().left+2*colorpicker_size;
                    var slogan_fy = $('header #site-slogan').offset().top+($('header #site-slogan').outerHeight()-colorpicker_size)/2;
                    $('#slogan-designer-fontpicker').css({'left':slogan_fx,'top':slogan_fy});
                    $('#slogan-designer-fontpicker').show();
                };
                $('#slogan-designer-fontpicker').tooltip();
                designer_fontpicker($('#slogan-designer-fontpicker'), slogan_font, designer_positions, function(size){
                    $('header #site-slogan').css('fontSize', size);
                    setFontSizeStyle('#site-logo-wrapper #site-slogan', size);
                    $(window).trigger('resize');
                });
            }
            
            // header text color
            if ($('header #header-text-example').length>0) {
                var header_text_color = $('header #header-text-example').css('color');
                $('body').append('<div class="designer_colorpicker" id="header-text-designer-colorpicker" title="'+t('Header text color')+'"></div>');
                designer_positions['header_text_color'] = function() {
                    var header_text_cx = $('header #header-text-example').offset().left+$('header #header-text-example').outerWidth()-colorpicker_size;
                    var header_text_cy = $('header #header-text-example').offset().top+$('header #header-text-example').outerHeight()+colorpicker_size/2;
                    $('#header-text-designer-colorpicker').css({'left':header_text_cx,'top':header_text_cy});
                };
                $('#header-text-designer-colorpicker').tooltip();
                designer_colorpicker($('#header-text-designer-colorpicker'), header_text_color, function(color){
                    $('header #header-text-example').css('color', color);
                    setColorStyle('header', color);
                    setColorStyle('header .zira-search-preview-wnd .list .list-item .list-content-wrapper', color);
                }, 'left');
            }
            
            // header language switcher
            if ($('header ul#language-switcher').length>0) {
                // header language switcher color
                var lang_color = $('header ul#language-switcher li a.active').css('color');
                $('body').append('<div class="designer_colorpicker" id="lang-color-designer-colorpicker" title="'+t('Language color')+'"></div>');
                designer_positions['lang_color'] = function() {
                    if ($('header ul#language-switcher').css('display')=='none' || $('header ul#language-switcher').css('visibility')=='hidden') {
                        $('#lang-color-designer-colorpicker').hide();
                        return;
                    }
                    var lang_cx = $('header ul#language-switcher').offset().left;
                    var lang_cy = $('header ul#language-switcher').offset().top+$('header ul#language-switcher').outerHeight()+.5*colorpicker_size;
                    $('#lang-color-designer-colorpicker').css({'left':lang_cx,'top':lang_cy});
                    $('#lang-color-designer-colorpicker').show();
                };
                $('#lang-color-designer-colorpicker').tooltip();
                designer_colorpicker($('#lang-color-designer-colorpicker'), lang_color, function(color){
                    $('header ul#language-switcher li a').css('color', color);
                    setColorStyle('ul#language-switcher li a:link,ul#language-switcher li a:visited,ul#language-switcher li a:hover,ul#language-switcher li a.active', color);
                });
                
                // header language switcher background
                var lang_bg = $('header ul#language-switcher li a.active').css('backgroundColor');
                $('body').append('<div class="designer_colorpicker" id="lang-bg-designer-colorpicker" title="'+t('Language background')+'"></div>');
                designer_positions['lang_bg'] = function() {
                    if ($('header ul#language-switcher').css('display')=='none' || $('header ul#language-switcher').css('visibility')=='hidden') {
                        $('#lang-bg-designer-colorpicker').hide();
                        return;
                    }
                    var lang_bx = $('header ul#language-switcher').offset().left+1.5*colorpicker_size;
                    var lang_by = $('header ul#language-switcher').offset().top+$('header ul#language-switcher').outerHeight()+.5*colorpicker_size;
                    $('#lang-bg-designer-colorpicker').css({'left':lang_bx,'top':lang_by});
                    $('#lang-bg-designer-colorpicker').show();
                };
                $('#lang-bg-designer-colorpicker').tooltip();
                designer_colorpicker($('#lang-bg-designer-colorpicker'), lang_bg, function(color){
                    $('header ul#language-switcher li a.active').css('backgroundColor', color);
                    if (color.indexOf('rgba')==0) {
                        setBackgroundColorStyle('ul#language-switcher li a:hover,ul#language-switcher li a.active', hexColor(color));
                        setBackgroundStyle('ul#language-switcher li a:hover,ul#language-switcher li a.active', color, true);
                        setFilterStyle('ul#language-switcher li a:hover,ul#language-switcher li a.active', 'progid:DXImageTransform.Microsoft.gradient(startColorstr=' + rgbaToHexIE(color) + ',endColorstr=' + rgbaToHexIE(color) + ',GradientType=0)');
                    } else {
                        setBackgroundColorStyle('ul#language-switcher li a:hover,ul#language-switcher li a.active', color);
                    }
                }, 'right', false);
            }

            // header user menu
            if ($('header ul#user-menu').length>0) {
                // header user menu color
                var usermenu_color1 = $('header ul#user-menu li a').css('color');
                $('header ul#user-menu li a').addClass('active');
                var usermenu_color2 = $('header ul#user-menu li a').css('color');
                $('header ul#user-menu li a').removeClass('active');
                $('body').append('<div class="designer_colorpicker" id="usermenu-color-designer-gradientpicker" title="'+t('User menu color')+'"></div><div class="designer_gradientpicker_hidden" id="usermenu-color-designer-gradientpicker-hidden"></div>');
                designer_positions['usermenu_color'] = function() {
                    if ($('header ul#user-menu').css('display')=='none' || $('header ul#user-menu').css('visibility')=='hidden') {
                        $('#usermenu-color-designer-gradientpicker').hide();
                        $('#usermenu-color-designer-gradientpicker-hidden').hide();
                        return;
                    }
                    var usermenu_cx = $('header ul#user-menu').offset().left;
                    var usermenu_cy = $('header ul#user-menu').offset().top+$('header ul#user-menu').outerHeight()+.5*colorpicker_size;
                    $('#usermenu-color-designer-gradientpicker').css({'left':usermenu_cx,'top':usermenu_cy});
                    $('#usermenu-color-designer-gradientpicker-hidden').css({'left':usermenu_cx-colorpicker_wnd_size,'top':usermenu_cy});
                    $('#usermenu-color-designer-gradientpicker').show();
                    $('#usermenu-color-designer-gradientpicker-hidden').show();
                };
                $('#usermenu-color-designer-gradientpicker').tooltip();
                designer_gradientpicker($('#usermenu-color-designer-gradientpicker'), $('#usermenu-color-designer-gradientpicker-hidden'), usermenu_color2, usermenu_color1, function(color1, color2){
                    $('header ul#user-menu li a').css('color', color2);
                    $('header ul#user-menu li a').stop(true, true).animate({'color':color1},1000,function(){
                        $('header ul#user-menu li a').css('color', color1);
                        $('header ul#user-menu li a').animate({'color':color2},1000,function(){
                            $('header ul#user-menu li a').css('color', color2);
                        });
                    });
                    setColorStyle('ul#user-menu li.menu-item,ul#user-menu li.menu-item a.menu-link:link,ul#user-menu li.menu-item a.menu-link:visited,ul#user-menu ul.dropdown-menu li a,ul#user-menu ul.dropdown-menu li a:hover,ul#user-menu ul.dropdown-menu li a:focus', color2);
                    setColorStyle('ul#user-menu li.menu-item > a.menu-link:hover,ul#user-menu li.menu-item > a.menu-link.active', color1);
                }, 'right', 'rgb');
                
                // header user menu background
                var usermenu_gr = extractGradient($('header ul#user-menu'));
                $('body').append('<div class="designer_gradientpicker" id="usermenu-bg-designer-gradientpicker" title="'+t('User menu background')+'"></div><div class="designer_gradientpicker_hidden" id="usermenu-bg-designer-gradientpicker-hidden"></div>');
                designer_positions['usermenu_bg'] = function() {
                    if ($('header ul#user-menu').css('display')=='none' || $('header ul#user-menu').css('visibility')=='hidden') {
                        $('#usermenu-bg-designer-gradientpicker').hide();
                        $('#usermenu-bg-designer-gradientpicker-hidden').hide();
                        return;
                    }
                    var usermenu_bx = $('header ul#user-menu').offset().left+1.5*colorpicker_size;
                    var usermenu_by = $('header ul#user-menu').offset().top+$('header ul#user-menu').outerHeight()+.5*colorpicker_size;
                    $('#usermenu-bg-designer-gradientpicker').css({'left':usermenu_bx,'top':usermenu_by});
                    $('#usermenu-bg-designer-gradientpicker-hidden').css({'left':usermenu_bx-gradientpicker_wnd_size,'top':usermenu_by});
                    $('#usermenu-bg-designer-gradientpicker').show();
                    $('#usermenu-bg-designer-gradientpicker-hidden').show();
                };
                $('#usermenu-bg-designer-gradientpicker').tooltip();
                designer_gradientpicker($('#usermenu-bg-designer-gradientpicker'), $('#usermenu-bg-designer-gradientpicker-hidden'), usermenu_gr[1], usermenu_gr[0], function(color2, color1){
                    $('header ul#user-menu').css('background', 'linear-gradient(to bottom,' + color1 + ',' + color2 + ')');
                    $('header,header ul#user-menu').css('border-color', color1);
                    setBackgroundColorStyle('header ul#user-menu', hexColor(color1), true);
                    setBackgroundGradientStyle('header ul#user-menu', color1, color2);
                    setBackgroundStyle('header ul#user-menu li.menu-item.open,header ul#user-menu ul.dropdown-menu', hexColor(color2));
                    setBackgroundStyle('ul#user-menu ul.dropdown-menu li a:hover,ul#user-menu ul.dropdown-menu li a:focus,ul#user-menu ul.dropdown-menu .divider', hexColor(color1));
                    setBorderColorStyle('header,header ul#user-menu,header ul#user-menu ul.dropdown-menu', hexColor(color1));
                });
            }

            // header top menu
            if ($('header #top-menu-wrapper nav').length>0) {
                // header top menu color
                var topmenu_color1 = $('header #top-menu-wrapper nav .active a').css('color');
                var topmenu_color2 = $('header #top-menu-wrapper nav li').not('.active').children('a').css('color');
                $('body').append('<div class="designer_colorpicker" id="topmenu-color-designer-gradientpicker" title="'+t('Top menu color')+'"></div><div class="designer_gradientpicker_hidden" id="topmenu-color-designer-gradientpicker-hidden"></div>');
                designer_positions['topmenu_color'] = function() {
                    if ($('header #top-menu-wrapper').css('display')=='none' || $('header #top-menu-wrapper').css('visibility')=='hidden') {
                        $('#topmenu-color-designer-gradientpicker').hide();
                        $('#topmenu-color-designer-gradientpicker-hidden').hide();
                        return;
                    }
                    var topmenu_cx = $('header #top-menu-wrapper nav').offset().left+($('header #top-menu-wrapper nav').outerWidth()-colorpicker_size)/2-.75*colorpicker_size;
                    var topmenu_cy = $('header #top-menu-wrapper nav').offset().top+($('header #top-menu-wrapper nav').outerHeight()-colorpicker_size)/2;
                    $('#topmenu-color-designer-gradientpicker').css({'left':topmenu_cx,'top':topmenu_cy});
                    $('#topmenu-color-designer-gradientpicker-hidden').css({'left':topmenu_cx+colorpicker_wnd_size,'top':topmenu_cy});
                    $('#toprmenu-color-designer-gradientpicker').show();
                    $('#topmenu-color-designer-gradientpicker-hidden').show();
                };
                $('#topmenu-color-designer-gradientpicker').tooltip();
                designer_gradientpicker($('#topmenu-color-designer-gradientpicker'), $('#topmenu-color-designer-gradientpicker-hidden'), topmenu_color1, topmenu_color2, function(color1, color2){
                    $('header #top-menu-wrapper nav a, header #top-menu-wrapper .form-control, header #top-menu-wrapper .form-control::placeholder, header #top-menu-wrapper .btn-default').css('color', color2);
                    $('header #top-menu-wrapper nav .active a').css('color', color1);
                    setColorStyle('header #top-menu-wrapper nav a:link,header #top-menu-wrapper nav a:visited,header #top-menu-wrapper .navbar-default .navbar-nav .active a,header #top-menu-wrapper .navbar-default .navbar-nav .open a,header #top-menu-wrapper .form-control,header #top-menu-wrapper .btn-default,header .navbar-default .navbar-toggle', color2);
                    setColorStyle('header #top-menu-wrapper .navbar-default .navbar-nav .active a,header #top-menu-wrapper .navbar-default .navbar-nav .open > a,header #top-menu-wrapper .navbar-default .navbar-nav > li > a:hover', color1);
                    setColorStyle('header #top-menu-wrapper .form-control::placeholder', color2);
                    setBackgroundColorStyle('header #top-menu-wrapper .navbar-default .navbar-toggle .icon-bar', color2);
                }, 'right', 'rgb');
                
                // header top menu background
                var topmenu_gr = extractGradient($('header #top-menu-wrapper nav'));
                $('body').append('<div class="designer_gradientpicker" id="topmenu-bg-designer-gradientpicker" title="'+t('Top menu background')+'"></div><div class="designer_gradientpicker_hidden" id="topmenu-bg-designer-gradientpicker-hidden"></div>');
                designer_positions['topmenu_bg'] = function() {
                    if ($('header #top-menu-wrapper').css('display')=='none' || $('header #top-menu-wrapper').css('visibility')=='hidden') {
                        $('#topmenu-bg-designer-gradientpicker').hide();
                        $('#topmenu-bg-designer-gradientpicker-hidden').hide();
                        return;
                    }
                    var topmenu_gx = $('header #top-menu-wrapper nav').offset().left+($('header #top-menu-wrapper nav').outerWidth()-colorpicker_size)/2+.75*colorpicker_size;
                    var topmenu_gy = $('header #top-menu-wrapper nav').offset().top+($('header #top-menu-wrapper nav').outerHeight()-colorpicker_size)/2;
                    $('#topmenu-bg-designer-gradientpicker').css({'left':topmenu_gx,'top':topmenu_gy});
                    $('#topmenu-bg-designer-gradientpicker-hidden').css({'left':topmenu_gx+gradientpicker_wnd_size,'top':topmenu_gy});
                    $('#topmenu-bg-designer-gradientpicker').show();
                    $('#topmenu-bg-designer-gradientpicker-hidden').show();
                };
                $('#topmenu-bg-designer-gradientpicker').tooltip();
                designer_gradientpicker($('#topmenu-bg-designer-gradientpicker'), $('#topmenu-bg-designer-gradientpicker-hidden'), topmenu_gr[0], topmenu_gr[1], function(color1, color2){
                    $('header #top-menu-wrapper nav').css('background', 'linear-gradient(to bottom,' + color1 + ',' + color2 + ')');
                    $('header #top-menu-wrapper .navbar-default .navbar-nav .active, header #top-menu-wrapper .navbar-default .navbar-nav .active a, header #top-menu-wrapper .form-control, header #top-menu-wrapper .btn-default').css('background', color2);
                    $('header #top-menu-wrapper .navbar-default,header #top-menu-wrapper .navbar-default .navbar-nav .active, header #top-menu-wrapper .form-control, header #top-menu-wrapper .btn-default').css('border-color', color1);
                    $('header #top-menu-wrapper nav a:link,header #top-menu-wrapper nav a:visited,header #top-menu-wrapper .btn-default').css('text-shadow', '0 1px 0 '+color2);
                    setBackgroundColorStyle('header #top-menu-wrapper nav.navbar-default', hexColor(color1), true);
                    setBackgroundGradientStyle('header #top-menu-wrapper nav.navbar-default', color1, color2);
                    setBackgroundStyle('header #top-menu-wrapper .navbar-default .navbar-nav .open,header #top-menu-wrapper .navbar-default .navbar-nav .active,header #top-menu-wrapper .navbar-default .navbar-nav .active a,header #top-menu-wrapper .navbar-default .navbar-nav .open a,header #top-menu-wrapper nav ul.dropdown-menu,header #top-menu-wrapper nav .form-control,header #top-menu-wrapper nav .btn-default,header .zira-search-preview-wnd .list .list-item:hover .list-title-wrapper', hexColor(color2));
                    setBackgroundStyle('#top-menu-wrapper ul.dropdown-menu li a:hover,#top-menu-wrapper ul.dropdown-menu li a:focus,#top-menu-wrapper .navbar-default .navbar-nav .open ul.dropdown-menu li a:hover,header .zira-search-preview-wnd .list .list-item .list-title-wrapper', hexColor(color1));
                    setBackgroundColorStyle('header #top-menu-wrapper .navbar-default .navbar-toggle:focus,header #top-menu-wrapper .navbar-default .navbar-toggle:hover', hexColor(color1), true);
                    setBorderColorStyle('header #top-menu-wrapper nav.navbar-default,header #top-menu-wrapper .navbar-default .navbar-nav .active,header #top-menu-wrapper nav ul.dropdown-menu,header #top-menu-wrapper nav .form-control,header #top-menu-wrapper nav .btn-default,header .navbar-default .navbar-toggle,header .navbar-default .navbar-collapse,.navbar-default .navbar-form,header .zira-search-preview-wnd,header .zira-search-preview-wnd .list .list-item,header .zira-search-preview-wnd .list .list-item:hover,header .zira-search-preview-wnd .list .list-item:last-child,header .zira-search-preview-wnd .list .list-item .list-title-wrapper', hexColor(color1));
                    setTextShadowStyle('header #top-menu-wrapper nav a:link,header #top-menu-wrapper nav a:visited,header #top-menu-wrapper .btn-default', '0 1px 0 '+hexColor(color2));
                    setFilterStyle('#top-menu-wrapper .navbar-default .navbar-nav .active a,#top-menu-wrapper .navbar-default .navbar-nav .open a,#top-menu-wrapper nav .btn-default', 'none');
                });
            }
        }
        
        // content
        if ($('#content main article').length>0) {
            // article
            if ($('#content main article .article').length>0) {
                // article text color
                var article_color = $('#content main article .article').css('color');
                $('body').append('<div class="designer_colorpicker" id="article-designer-colorpicker" title="'+t('Text color')+'"></div>');
                designer_positions['article_color'] = function() {
                    var article_cx = $('#content main article .article').offset().left+($('#content main article .article').outerWidth()-colorpicker_size)/2-.75*colorpicker_size;
                    var article_cy = $('#content main article .article').offset().top+($('#content main article .article').outerHeight()-colorpicker_size)/2;
                    $('#article-designer-colorpicker').css({'left':article_cx,'top':article_cy});
                };
                $('#article-designer-colorpicker').tooltip();
                designer_colorpicker($('#article-designer-colorpicker'), article_color, function(color){
                    $('body').css('color', color);
                    setColorStyle('body', color);
                });
                
                // article font size
                var article_font = $('#content main article .article').css('fontSize');
                $('body').append('<div class="designer_fontpicker" id="article-designer-fontpicker" title="'+t('Font size')+'"></div>');
                designer_positions['article_font'] = function() {
                    var article_fx = $('#content main article .article').offset().left+($('#content main article .article').outerWidth()-colorpicker_size)/2+.75*colorpicker_size;
                    var article_fy = $('#content main article .article').offset().top+($('#content main article .article').outerHeight()-colorpicker_size)/2;
                    $('#article-designer-fontpicker').css({'left':article_fx,'top':article_fy});
                };
                $('#article-designer-fontpicker').tooltip();
                designer_fontpicker($('#article-designer-fontpicker'), article_font, designer_positions, function(size){
                    $('#content main article .article,#content main article .article p').css('fontSize', size);
                    $('#content main article .article p').css('lineHeight', parseInt(size)+10+'px');
                    setFontSizeStyle('#content main article .article,#content main article .article p', size);
                    setLineHeightStyle('#content main article .article,#content main article .article p', parseInt(size)+10+'px');
                    $(window).trigger('resize');
                });
                
                // article title text color
                var article_title_color = $('h1').css('color');
                $('body').append('<div class="designer_colorpicker" id="article-title-designer-colorpicker" title="'+t('Title color')+'"></div>');
                designer_positions['article_title_color'] = function() {
                    var article_title_cx = $('h1').offset().left+$('h1').outerWidth()-4*colorpicker_size;
                    var article_title_cy = $('h1').offset().top+($('h1').outerHeight()-colorpicker_size)/2;
                    $('#article-title-designer-colorpicker').css({'left':article_title_cx,'top':article_title_cy});
                };
                $('#article-title-designer-colorpicker').tooltip();
                designer_colorpicker($('#article-title-designer-colorpicker'), article_title_color, function(color){
                    $('h1').css('color', color);
                    setColorStyle('h1', color);
                });
                
                // article title font size
                var article_title_font = $('h1').css('fontSize');
                $('body').append('<div class="designer_fontpicker" id="article-title-designer-fontpicker" title="'+t('Title font size')+'"></div>');
                designer_positions['article_title_font'] = function() {
                    var article_title_fx = $('h1').offset().left+$('h1').outerWidth()-2.5*colorpicker_size;
                    var article_title_fy = $('h1').offset().top+($('h1').outerHeight()-colorpicker_size)/2;
                    $('#article-title-designer-fontpicker').css({'left':article_title_fx,'top':article_title_fy});
                };
                $('#article-title-designer-fontpicker').tooltip();
                designer_fontpicker($('#article-title-designer-fontpicker'), article_title_font, designer_positions, function(size){
                    $('h1').css('fontSize', size);
                    setFontSizeStyle('h1', size);
                    $(window).trigger('resize');
                });
            
                // article info
                if ($('#content main article .article-info').length>0) {
                    // article info text color
                    var article_info_color = $('#content main article .article-info .datetime').css('color');
                    $('body').append('<div class="designer_colorpicker" id="article-info-designer-colorpicker" title="'+t('Date and author color')+'"></div>');
                    designer_positions['article_info_color'] = function() {
                        var article_info_cx = $('.article-info .datetime').offset().left+$('.article-info .datetime').outerWidth()+colorpicker_size;
                        var article_info_cy = $('.article-info .datetime').offset().top+($('.article-info .datetime').outerHeight()-colorpicker_size)/2;
                        $('#article-info-designer-colorpicker').css({'left':article_info_cx,'top':article_info_cy});
                    };
                    $('#article-info-designer-colorpicker').tooltip();
                    designer_colorpicker($('#article-info-designer-colorpicker'), article_info_color, function(color){
                        $('#content main article .article-info .datetime,#content main article .article-info .author').css('color', color);
                        $('.page-header').css('borderBottom', '1px solid '+color);
                        setColorStyle('#content main article .article-info .datetime,#content main article .article-info .author', color);
                        setBorderBottomStyle('.page-header', '1px solid '+color);
                        setBorderBottomStyle('.user-profile h2', '1px solid '+color);
                    });

                    // article info font size
                    var article_info_font = $('#content main article .article-info .datetime').css('fontSize');
                    $('body').append('<div class="designer_fontpicker" id="article-info-designer-fontpicker" title="'+t('Date and author font size')+'"></div>');
                    designer_positions['article_info_font'] = function() {
                        var article_info_fx = $('.article-info .datetime').offset().left+$('.article-info .datetime').outerWidth()+2.5*colorpicker_size;
                        var article_info_fy = $('.article-info .datetime').offset().top+($('.article-info .datetime').outerHeight()-colorpicker_size)/2;
                        $('#article-info-designer-fontpicker').css({'left':article_info_fx,'top':article_info_fy});
                    };
                    $('#article-info-designer-fontpicker').tooltip();
                    designer_fontpicker($('#article-info-designer-fontpicker'), article_info_font, designer_positions, function(size){
                        $('#content main article .article-info .datetime,#content main article .article-info .author').css('fontSize', size);
                        setFontSizeStyle('#content main article .article-info .datetime,#content main article .article-info .author', size);
                        $(window).trigger('resize');
                    });
                }
                
                // article links color
                var article_link_color1 = $('.article a').css('color');
                $('.article a').addClass('active');
                var article_link_color2 = $('.article a').css('color');
                $('.article a').removeClass('active');
                $('body').append('<div class="designer_colorpicker" id="article-link-designer-gradientpicker" title="'+t('Link color')+'"></div><div class="designer_gradientpicker_hidden" id="article-link-designer-gradientpicker-hidden"></div>');
                designer_positions['article_link_color'] = function() {
                    var article_link_cx = $('.article a').offset().left+$('.article a').outerWidth()+.5*colorpicker_size;
                    var article_link_cy = $('.article a').offset().top+($('.article a').outerHeight()-colorpicker_size)/2;
                    $('#article-link-designer-gradientpicker').css({'left':article_link_cx,'top':article_link_cy});
                    $('#article-link-designer-gradientpicker-hidden').css({'left':article_link_cx+colorpicker_wnd_size,'top':article_link_cy});
                };
                $('#article-link-designer-gradientpicker').tooltip();
                designer_gradientpicker($('#article-link-designer-gradientpicker'), $('#article-link-designer-gradientpicker-hidden'), article_link_color1, article_link_color2, function(color1, color2){
                    $('.article a,.article-info a,.zira-calendar-selector a,.comment-head a').css('color', color1);
                    $('.scroll-top').css('color', color1);
                    $('.article a').stop(true, true).animate({'color':color2},1000,function(){
                        $('.article a').css('color', color2);
                        $('.article a').animate({'color':color1},1000,function(){
                            $('.article a').css('color', color1);
                        });
                    });
                    setColorStyle('a:link,a:visited,a.external-url', color1);
                    setColorStyle('a:hover,a:active,a.active', color2);
                },'left', 'rgb');
            }

            // subtitle text color
            var subtitle_color = $('h2').eq(0).css('color');
            $('body').append('<div class="designer_colorpicker" id="subtitle-designer-colorpicker" title="'+t('Subtitle color')+'"></div>');
            designer_positions['subtitle_color'] = function() {
                var subtitle_cx = $('h2').eq(0).offset().left+$('h2').eq(0).outerWidth()-4*colorpicker_size;
                var subtitle_cy = $('h2').eq(0).offset().top+($('h2').eq(0).outerHeight()-colorpicker_size)/2;
                $('#subtitle-designer-colorpicker').css({'left':subtitle_cx,'top':subtitle_cy});
            };
            $('#subtitle-designer-colorpicker').tooltip();
            designer_colorpicker($('#subtitle-designer-colorpicker'), subtitle_color, function(color){
                $('h2').not('.panel-title').css('color', color);
                setColorStyle('h2', color);
                setColorStyle('.home-category-wrapper .home-category-title,.home-category-wrapper .home-category-title a:link,.home-category-wrapper .home-category-title a:visited', color);
            });

            // subtitle font size
            var subtitle_font = $('h2').eq(0).css('fontSize');
            $('body').append('<div class="designer_fontpicker" id="subtitle-designer-fontpicker" title="'+t('Subtitle font size')+'"></div>');
            designer_positions['subtitle_font'] = function() {
                var subtitle_fx = $('h2').eq(0).offset().left+$('h2').eq(0).outerWidth()-2.5*colorpicker_size;
                var subtitle_fy = $('h2').eq(0).offset().top+($('h2').eq(0).outerHeight()-colorpicker_size)/2;
                $('#subtitle-designer-fontpicker').css({'left':subtitle_fx,'top':subtitle_fy});
            };
            $('#subtitle-designer-fontpicker').tooltip();
            designer_fontpicker($('#subtitle-designer-fontpicker'), subtitle_font, designer_positions, function(size){
                $('#content h2').not('.panel-title').css('fontSize', size);
                setFontSizeStyle('h2', size);
                setFontSizeStyle('.home-category-wrapper .home-category-title,.home-category-wrapper .home-category-title a:link,.home-category-wrapper .home-category-title a:visited', size);
                $(window).trigger('resize');
            });
        }

        // footer
        if ($('footer').length>0) {
            // footer bg gradient
            var footer_gr = extractGradient($('footer'));
            $('body').append('<div class="designer_gradientpicker" id="footer-designer-gradientpicker" title="'+t('Footer background')+'"></div><div class="designer_gradientpicker_hidden" id="footer-designer-gradientpicker-hidden"></div>');
            designer_positions['footer_gradient'] = function() {
                var footer_gx = $('footer').offset().left + ($('footer').outerWidth()-colorpicker_size)/2;
                var footer_gy = $('footer').offset().top-2*colorpicker_size;
                $('#footer-designer-gradientpicker').css({'left':footer_gx,'top':footer_gy});
                $('#footer-designer-gradientpicker-hidden').css({'left':footer_gx+gradientpicker_wnd_size,'top':footer_gy});
            };
            $('#footer-designer-gradientpicker').tooltip();
            designer_gradientpicker($('#footer-designer-gradientpicker'), $('#footer-designer-gradientpicker-hidden'), footer_gr[0], footer_gr[1], function(color1, color2){
                $('footer').css('background', 'linear-gradient(to bottom,' + color1 + ',' + color2 + ')');
                $('footer').css('borderColor', color2);
                setBackgroundColorStyle('footer', hexColor(color1), true);
                setBackgroundGradientStyle('footer', color1, color2);
                setBackgroundStyle('#main-container footer ul.dropdown-menu', hexColor(color2));
                setBackgroundStyle('#main-container footer ul.dropdown-menu li:hover,#main-container footer ul.dropdown-menu li a:hover,#main-container footer ul.dropdown-menu li a:focus,#main-container footer ul.dropdown-menu .divider', hexColor(color1));
                setBorderColorStyle('#main-container footer,#main-container footer ul.dropdown-menu', hexColor(color2));
            });

            // footer text color
            if ($('footer p').length>0) {
                var footer_text_color = $('footer p').css('color');
                $('body').append('<div class="designer_colorpicker" id="footer-text-designer-colorpicker" title="'+t('Footer text color')+'"></div>');
                designer_positions['footer_text_color'] = function() {
                    var footer_text_cx = $('footer').offset().left + ($('footer').outerWidth()-colorpicker_size)/2+1.5*colorpicker_size;
                    var footer_text_cy = $('footer').offset().top-2*colorpicker_size;
                    $('#footer-text-designer-colorpicker').css({'left':footer_text_cx,'top':footer_text_cy});
                };
                $('#footer-text-designer-colorpicker').tooltip();
                designer_colorpicker($('#footer-text-designer-colorpicker'), footer_text_color, function(color){
                    $('footer p').css('color', color);
                    setColorStyle('footer p', color);
                }, 'left');
            }
            
            // footer link color
            if ($('footer a').length>0) {
                var footer_link_color1 = $('#main-container footer ul.menu li').not('.active').children('a').css('color');
                var footer_link_color2 = $('#main-container footer ul.menu li.active a').css('color');
                $('body').append('<div class="designer_colorpicker" id="footer-link-designer-gradientpicker" title="'+t('Footer link color')+'"></div><div class="designer_gradientpicker_hidden" id="footer-link-designer-gradientpicker-hidden"></div>');
                designer_positions['footer_link_color'] = function() {
                    var footer_link_cx = $('footer').offset().left + ($('footer').outerWidth()-colorpicker_size)/2-1.5*colorpicker_size;
                    var footer_link_cy = $('footer').offset().top-2*colorpicker_size;
                    $('#footer-link-designer-gradientpicker').css({'left':footer_link_cx,'top':footer_link_cy});
                    $('#footer-link-designer-gradientpicker-hidden').css({'left':footer_link_cx+colorpicker_wnd_size,'top':footer_link_cy});
                };
                $('#footer-link-designer-gradientpicker').tooltip();
                designer_gradientpicker($('#footer-link-designer-gradientpicker'), $('#footer-link-designer-gradientpicker-hidden'), footer_link_color1, footer_link_color2, function(color1, color2){
                    $('#main-container footer ul.menu li').not('.active').children('a').css('color', color1);
                    $('#main-container footer ul.menu li.active a').css('color', color2);
                    setColorStyle('footer a:link,footer a:visited,footer p a:link,footer p a:visited,#footer-menu-wrapper ul.menu li.menu-item a.menu-link:link,#footer-menu-wrapper ul.menu li.menu-item a.menu-link:visited,#main-container footer ul.dropdown-menu li a', color1);
                    setColorStyle('#footer-menu-wrapper ul.menu li.menu-item a.menu-link:hover,#footer-menu-wrapper ul.menu li.menu-item.active a.menu-link,#main-container footer ul.dropdown-menu li a:hover,#main-container footer ul.dropdown-menu li a:focus', color2);
                    setColorStyle('#footer-menu-wrapper ul.menu li.menu-item-separator::after', color1);
                    setFilterStyle('#main-container footer ul.dropdown-menu li:hover,#main-container footer ul.dropdown-menu li a:hover', 'none');
                }, 'right', 'rgb');
            }
        }
        
        // breadcrumbs
        if ($('.breadcrumb').length>0) {
            // breadcrumbs text color
            var breadcrumbs_color1 = $('.breadcrumb a').css('color');
            var breadcrumbs_color2 = $('.breadcrumb li.active').css('color');
            $('body').append('<div class="designer_colorpicker" id="breadcrumbs-designer-gradientpicker" title="'+t('Breadcrumbs color')+'"></div><div class="designer_gradientpicker_hidden" id="breadcrumbs-designer-gradientpicker-hidden"></div>');
            designer_positions['breadcrumbs_color'] = function() {
                if ($('.breadcrumb').css('display')=='none' || $('.breadcrumb').css('visibility')=='hidden') {
                    $('#breadcrumbs-designer-gradientpicker').hide();
                    $('#breadcrumbs-designer-gradientpicker-hidden').hide();
                    return;
                }
                var breadcrumbs_cx = $('.breadcrumb').offset().left+($('.breadcrumb').outerWidth()-colorpicker_size)/2-.75*colorpicker_size;
                var breadcrumbs_cy = $('.breadcrumb').offset().top+($('.breadcrumb').outerHeight()-colorpicker_size)/2;
                $('#breadcrumbs-designer-gradientpicker').css({'left':breadcrumbs_cx,'top':breadcrumbs_cy});
                $('#breadcrumbs-designer-gradientpicker-hidden').css({'left':breadcrumbs_cx+colorpicker_wnd_size,'top':breadcrumbs_cy});
                $('#breadcrumbs-designer-gradientpicker').show();
                $('#breadcrumbs-designer-gradientpicker-hidden').show();
            };
            $('#breadcrumbs-designer-gradientpicker').tooltip();
            designer_gradientpicker($('#breadcrumbs-designer-gradientpicker'), $('#breadcrumbs-designer-gradientpicker-hidden'), breadcrumbs_color1, breadcrumbs_color2, function(color1, color2){
                $('.breadcrumb a').css('color', color1);
                $('.breadcrumb li.active').css('color', color2);
                setColorStyle('.breadcrumb a:link,.breadcrumb a:visited', color1);
                setColorStyle('.breadcrumb,.breadcrumb .active,.breadcrumb li a:before', color2);
                setColorStyle('.breadcrumb li + li::before', color2);
            }, 'right', 'rgb');

            // breadcrumbs font size
            var breadcrumbs_font = $('.breadcrumb a').css('fontSize');
            $('body').append('<div class="designer_fontpicker" id="breadcrumbs-designer-fontpicker" title="'+t('Breadcrumbs font size')+'"></div>');
            designer_positions['breadcrumbs_font'] = function() {
                if ($('.breadcrumb').css('display')=='none' || $('.breadcrumb').css('visibility')=='hidden') {
                    $('#breadcrumbs-designer-colorpicker').hide();
                    return;
                }
                var breadcrumbs_fx = $('.breadcrumb').offset().left+($('.breadcrumb').outerWidth()-colorpicker_size)/2+.75*colorpicker_size;
                var breadcrumbs_fy = $('.breadcrumb').offset().top+($('.breadcrumb').outerHeight()-colorpicker_size)/2;
                $('#breadcrumbs-designer-fontpicker').css({'left':breadcrumbs_fx,'top':breadcrumbs_fy});
                $('#breadcrumbs-designer-colorpicker').show();
            };
            $('#breadcrumbs-designer-fontpicker').tooltip();
            designer_fontpicker($('#breadcrumbs-designer-fontpicker'), breadcrumbs_font, designer_positions, function(size){
                $('.breadcrumb,.breadcrumb a:link,.breadcrumb a:visited').css('fontSize', size);
                setFontSizeStyle('.breadcrumb,.breadcrumb a:link,.breadcrumb a:visited', size);
                $(window).trigger('resize');
            });
            
            // breadcrumbs background color
            var breadcrumbs_bg_color = $('.breadcrumb').css('backgroundColor');
            if (breadcrumbs_bg_color != 'transparent') {
                $('body').append('<div class="designer_colorpicker" id="breadcrumbs-bg-designer-colorpicker" title="'+t('Breadcrumbs background')+'"></div>');
                designer_positions['breadcrumbs_bg_color'] = function() {
                    if ($('.breadcrumb').css('display')=='none' || $('.breadcrumb').css('visibility')=='hidden') {
                        $('#breadcrumbs-bg-designer-colorpicker').hide();
                        return;
                    }
                    var breadcrumbs_bx = $('.breadcrumb').offset().left+($('.breadcrumb').outerWidth()-colorpicker_size)/2+3*colorpicker_size;
                    var breadcrumbs_by = $('.breadcrumb').offset().top+($('.breadcrumb').outerHeight()-colorpicker_size)/2;
                    $('#breadcrumbs-bg-designer-colorpicker').css({'left':breadcrumbs_bx,'top':breadcrumbs_by});
                    $('#breadcrumbs-bg-designer-colorpicker').show();
                };
                $('#breadcrumbs-bg-designer-colorpicker').tooltip();
                designer_colorpicker($('#breadcrumbs-bg-designer-colorpicker'), breadcrumbs_bg_color, function(color){
                    $('.breadcrumb').css('background-color', color);
                    //$('.breadcrumb').css('padding', '10px 15px');
                    if (color.indexOf('rgba')==0) {
                        setBackgroundColorStyle('.breadcrumb', hexColor(color));
                        setBackgroundStyle('.breadcrumb', color, true);
                        setFilterStyle('.breadcrumb', 'progid:DXImageTransform.Microsoft.gradient(startColorstr=' + rgbaToHexIE(color) + ',endColorstr=' + rgbaToHexIE(color) + ',GradientType=0)');
                    } else {
                        setBackgroundStyle('.breadcrumb', color);
                    }
                    //setPaddingStyle('.breadcrumb', '10px 15px');
                }, 'right', false);
            }
        }

        // gallery background color
        if ($('.gallery').length>0) {
            var gallery_color = $('.gallery').css('backgroundColor');
            $('body').append('<div class="designer_colorpicker" id="gallery-designer-colorpicker" title="'+t('Gallery background')+'"></div>');
            designer_positions['gallery_bg'] = function() {
                if ($('.gallery').css('display')=='none' || $('.gallery').css('visibility')=='hidden') {
                    $('#gallery-designer-colorpicker').hide();
                    return;
                }
                var gallery_cx = $('.gallery').offset().left+($('.gallery').outerWidth()-colorpicker_size)/2;
                var gallery_cy = $('.gallery').offset().top+($('.gallery').outerHeight()-colorpicker_size)/2;
                $('#gallery-designer-colorpicker').css({'left':gallery_cx,'top':gallery_cy});
                $('#gallery-designer-colorpicker').show();
            };
            $('#gallery-designer-colorpicker').tooltip();
            designer_colorpicker($('#gallery-designer-colorpicker'), gallery_color, function(color){
                $('.gallery').css('backgroundColor', color);
                $('.gallery, .gallery a').css('borderColor', color);
                setBackgroundStyle('.gallery,.image-wrapper,.bx-wrapper .bx-viewport', color);
                setBorderColorStyle('.gallery,.gallery li a:link,.gallery li a:visited,.image,.image-wrapper,.jplayer-video-wrapper,.jplayer-audio-wrapper,.bx-wrapper .bx-viewport', color);
                setBorderColorStyle('#yandex-map,#google-map,.contact-image', color);
            }, 'left');
        }

        // files
        if ($('.files-wrapper').length>0) {
            // files text color
            var files_color1 = $('.files-wrapper a').css('color');
            var files_color2 = $('.files-wrapper ul li').css('color');
            $('body').append('<div class="designer_colorpicker" id="files-designer-gradientpicker" title="'+t('Files color')+'"></div><div class="designer_gradientpicker_hidden" id="files-designer-gradientpicker-hidden"></div>');
            designer_positions['files_color'] = function() {
                if ($('.files-wrapper').css('display')=='none' || $('.files-wrapper').css('visibility')=='hidden') {
                    $('#files-designer-gradientpicker').hide();
                    $('#files-designer-gradientpicker-hidden').hide();
                    return;
                }
                var files_cx = $('.files-wrapper').offset().left+($('.files-wrapper').outerWidth()-colorpicker_size)/2-.75*colorpicker_size;
                var files_cy = $('.files-wrapper').offset().top+($('.files-wrapper').outerHeight()-colorpicker_size)/2;
                $('#files-designer-gradientpicker').css({'left':files_cx,'top':files_cy});
                $('#files-designer-gradientpicker-hidden').css({'left':files_cx+colorpicker_wnd_size,'top':files_cy});
                $('#files-designer-gradientpicker').show();
                $('#files-designer-gradientpicker-hidden').show();
            };
            $('#files-designer-gradientpicker').tooltip();
            designer_gradientpicker($('#files-designer-gradientpicker'), $('#files-designer-gradientpicker-hidden'), files_color1, files_color2, function(color1, color2){
                $('.files-wrapper a').css('color', color1);
                $('.files-wrapper ul li').css('color', color2);
                setColorStyle('.files-wrapper ul.files li a:link,.files-wrapper ul.files li a:visited', color1);
                setColorStyle('.files-wrapper ul.files li', color2);
                setColorStyle('.files-wrapper ul.files li a:hover', color2);
            }, 'right', 'rgb');

            // files font size
            var files_font = $('.files-wrapper a').css('fontSize');
            $('body').append('<div class="designer_fontpicker" id="files-designer-fontpicker" title="'+t('Files font size')+'"></div>');
            designer_positions['files_font'] = function() {
                if ($('.files-wrapper').css('display')=='none' || $('.files-wrapper').css('visibility')=='hidden') {
                    $('#files-designer-colorpicker').hide();
                    return;
                }
                var files_fx = $('.files-wrapper').offset().left+($('.files-wrapper').outerWidth()-colorpicker_size)/2+.75*colorpicker_size;
                var files_fy = $('.files-wrapper').offset().top+($('.files-wrapper').outerHeight()-colorpicker_size)/2;
                $('#files-designer-fontpicker').css({'left':files_fx,'top':files_fy});
                $('#files-designer-colorpicker').show();
            };
            $('#files-designer-fontpicker').tooltip();
            designer_fontpicker($('#files-designer-fontpicker'), files_font, designer_positions, function(size){
                $('.files-wrapper,.files-wrapper a').css('fontSize', size);
                setFontSizeStyle('.files-wrapper,.files-wrapper a:link,.files-wrapper a:visited', size);
                $(window).trigger('resize');
            });
            
            // files background
            var files_bg1 = $('.files-wrapper ul li').eq(0).css('backgroundColor');
            var files_bg2 = $('.files-wrapper ul li').eq(1).css('backgroundColor');
            $('body').append('<div class="designer_colorpicker" id="files-bg-designer-gradientpicker" title="'+t('Files background')+'"></div><div class="designer_gradientpicker_hidden" id="files-bg-designer-gradientpicker-hidden"></div>');
            designer_positions['files_bg_color'] = function() {
                if ($('.files-wrapper').css('display')=='none' || $('.files-wrapper').css('visibility')=='hidden') {
                    $('#files-bg-designer-gradientpicker').hide();
                    $('#files-bg-designer-gradientpicker-hidden').hide();
                    return;
                }
                var files_bg_cx = $('.files-wrapper').offset().left+($('.files-wrapper').outerWidth()-colorpicker_size)/2+3*colorpicker_size;
                var files_bg_cy = $('.files-wrapper').offset().top+($('.files-wrapper').outerHeight()-colorpicker_size)/2;
                $('#files-bg-designer-gradientpicker').css({'left':files_bg_cx,'top':files_bg_cy});
                $('#files-bg-designer-gradientpicker-hidden').css({'left':files_bg_cx+colorpicker_wnd_size,'top':files_bg_cy});
                $('#files-bg-designer-gradientpicker').show();
                $('#files-bg-designer-gradientpicker-hidden').show();
            };
            $('#files-bg-designer-gradientpicker').tooltip();
            designer_gradientpicker($('#files-bg-designer-gradientpicker'), $('#files-bg-designer-gradientpicker-hidden'), files_bg1, files_bg2, function(color1, color2){
                $('.files-wrapper ul.files').css('backgroundColor', color1);
                $('.files-wrapper ul.files').css('borderColor', color1);
                $('.files-wrapper ul.files li').css('backgroundColor', color1);
                $('.files-wrapper ul.files li:nth-child(2n)').css('backgroundColor', color2);
                setBackgroundStyle('.files-wrapper ul.files,.files-wrapper ul.files li', color1);
                setBackgroundStyle('.files-wrapper ul.files li:nth-child(2n)', color2);
                setBorderColorStyle('.files-wrapper ul.files', color1);
            }, 'right', 'rgb');
        }
        
        // information message background
        var info_msg_gr = extractGradient($('.alert-warning'));
        $('body').append('<div class="designer_gradientpicker" id="info-msg-designer-gradientpicker" title="'+t('Information message background')+'"></div><div class="designer_gradientpicker_hidden" id="info-msg-designer-gradientpicker-hidden"></div>');
        designer_positions['info_msg_bg'] = function() {
            var info_msg_bx = $('.alert-warning').offset().left+$('.alert-warning').outerWidth()-3*colorpicker_size;
            var info_msg_by = $('.alert-warning').offset().top+($('.alert-warning').outerHeight()-colorpicker_size)/2;
            $('#info-msg-designer-gradientpicker').css({'left':info_msg_bx,'top':info_msg_by});
            $('#info-msg-designer-gradientpicker-hidden').css({'left':info_msg_bx+colorpicker_wnd_size,'top':info_msg_by});
        };
        $('#info-msg-designer-gradientpicker').tooltip();
        designer_gradientpicker($('#info-msg-designer-gradientpicker'), $('#info-msg-designer-gradientpicker-hidden'), info_msg_gr[0], info_msg_gr[1], function(color1, color2){
            $('.alert-warning').css('background', 'linear-gradient(to bottom,' + color1 + ',' + color2 + ')');
            $('.alert-warning').css('border-color', color2);
            setBackgroundColorStyle('.alert-warning', hexColor(color1), true);
            setBackgroundGradientStyle('.alert-warning', color1, color2);
            setBorderColorStyle('.alert-warning', color2);
        },'right', 'rgb');
        
        // information message text
        var info_msg_color1 = $('.alert-warning a').css('color');
        var info_msg_color2 = $('.alert-warning').css('color');
        $('body').append('<div class="designer_colorpicker" id="info-msg-txt-designer-gradientpicker" title="'+t('Information message text color')+'"></div><div class="designer_gradientpicker_hidden" id="info-msg-txt-designer-gradientpicker-hidden"></div>');
        designer_positions['info_msg_txt'] = function() {
            var info_msg_tx = $('.alert-warning').offset().left+$('.alert-warning').outerWidth()-1.5*colorpicker_size;
            var info_msg_ty = $('.alert-warning').offset().top+($('.alert-warning').outerHeight()-colorpicker_size)/2;
            $('#info-msg-txt-designer-gradientpicker').css({'left':info_msg_tx,'top':info_msg_ty});
            $('#info-msg-txt-designer-gradientpicker-hidden').css({'left':info_msg_tx+colorpicker_wnd_size,'top':info_msg_ty});
        };
        $('#info-msg-txt-designer-gradientpicker').tooltip();
        designer_gradientpicker($('#info-msg-txt-designer-gradientpicker'), $('#info-msg-txt-designer-gradientpicker-hidden'), info_msg_color1, info_msg_color2, function(color1, color2){
            $('.alert-warning').css('color', color2);
            $('.alert-warning a').css('color', color1);
            setColorStyle('.alert-warning', color2);
            setColorStyle('.alert-warning a:link,.alert-warning a:visited', color1);
            setColorStyle('.alert-warning a:hover,.alert-warning a:active', color2);
        },'right', 'rgb');
        
        // error message background
        var err_msg_gr = extractGradient($('.alert-danger'));
        $('body').append('<div class="designer_gradientpicker" id="err-msg-designer-gradientpicker" title="'+t('Error message background')+'"></div><div class="designer_gradientpicker_hidden" id="err-msg-designer-gradientpicker-hidden"></div>');
        designer_positions['err_msg_bg'] = function() {
            var err_msg_bx = $('.alert-danger').offset().left+$('.alert-danger').outerWidth()-3*colorpicker_size;
            var err_msg_by = $('.alert-danger').offset().top+($('.alert-danger').outerHeight()-colorpicker_size)/2;
            $('#err-msg-designer-gradientpicker').css({'left':err_msg_bx,'top':err_msg_by});
            $('#err-msg-designer-gradientpicker-hidden').css({'left':err_msg_bx+colorpicker_wnd_size,'top':err_msg_by});
        };
        $('#err-msg-designer-gradientpicker').tooltip();
        designer_gradientpicker($('#err-msg-designer-gradientpicker'), $('#err-msg-designer-gradientpicker-hidden'), err_msg_gr[0], err_msg_gr[1], function(color1, color2){
            $('.alert-danger').css('background', 'linear-gradient(to bottom,' + color1 + ',' + color2 + ')');
            $('.alert-danger').css('border-color', color2);
            setBackgroundColorStyle('.alert-danger', hexColor(color1), true);
            setBackgroundGradientStyle('.alert-danger', color1, color2);
            setBorderColorStyle('.alert-danger', color2);
        },'right', 'rgb');
        
        // error message text
        var err_msg_color1 = $('.alert-danger a').css('color');
        var err_msg_color2 = $('.alert-danger').css('color');
        $('body').append('<div class="designer_colorpicker" id="err-msg-txt-designer-gradientpicker" title="'+t('Error message text color')+'"></div><div class="designer_gradientpicker_hidden" id="err-msg-txt-designer-gradientpicker-hidden"></div>');
        designer_positions['err_msg_txt'] = function() {
            var err_msg_tx = $('.alert-danger').offset().left+$('.alert-danger').outerWidth()-1.5*colorpicker_size;
            var err_msg_ty = $('.alert-danger').offset().top+($('.alert-danger').outerHeight()-colorpicker_size)/2;
            $('#err-msg-txt-designer-gradientpicker').css({'left':err_msg_tx,'top':err_msg_ty});
            $('#err-msg-txt-designer-gradientpicker-hidden').css({'left':err_msg_tx+colorpicker_wnd_size,'top':err_msg_ty});
        };
        $('#err-msg-txt-designer-gradientpicker').tooltip();
        designer_gradientpicker($('#err-msg-txt-designer-gradientpicker'), $('#err-msg-txt-designer-gradientpicker-hidden'), err_msg_color1, err_msg_color2, function(color1, color2){
            $('.alert-danger').css('color', color2);
            $('.alert-danger a').css('color', color1);
            setColorStyle('.alert-danger', color2);
            setColorStyle('.alert-danger a:link,.alert-danger a:visited', color1);
            setColorStyle('.alert-danger a:hover,.alert-danger a:active', color2);
        },'right', 'rgb');

        // primary button background
        var btn_pri_gr = extractGradient($('.comments-wrapper .btn-primary'));
        $('body').append('<div class="designer_gradientpicker" id="btn-pri-designer-gradientpicker" title="'+t('Primary button background')+'"></div><div class="designer_gradientpicker_hidden" id="btn-pri-designer-gradientpicker-hidden"></div>');
        designer_positions['btn_pri_bg'] = function() {
            var btn_pri_bx = $('.comments-wrapper .btn-primary').offset().left-3*colorpicker_size;
            var btn_pri_by = $('.comments-wrapper .btn-primary').offset().top+($('.comments-wrapper .btn-primary').outerHeight()-colorpicker_size)/2;
            $('#btn-pri-designer-gradientpicker').css({'left':btn_pri_bx,'top':btn_pri_by});
            $('#btn-pri-designer-gradientpicker-hidden').css({'left':btn_pri_bx+colorpicker_wnd_size,'top':btn_pri_by});
        };
        $('#btn-pri-designer-gradientpicker').tooltip();
        designer_gradientpicker($('#btn-pri-designer-gradientpicker'), $('#btn-pri-designer-gradientpicker-hidden'), btn_pri_gr[0], btn_pri_gr[1], function(color1, color2){
            $('#main-container .btn-primary').css('background', 'linear-gradient(to bottom,' + color1 + ',' + color2 + ')');
            $('#main-container .btn-primary').css('border-color', color2);
            $('#main-container .btn-primary').css('textShadow', '0 1px 0 '+color1);
            setBackgroundColorStyle('#main-container .btn-primary', hexColor(color1), true);
            setBackgroundGradientStyle('#main-container .btn-primary', color1, color2);
            setBorderColorStyle('#main-container .btn-primary', color2);
            setBackgroundGradientStyle('#main-container .btn-primary:hover,#main-container .btn-primary:focus,#main-container .btn-primary.active,#main-container .btn-primary:active,#main-container .open .dropdown-toggle.btn-primary,#main-container .btn-primary.active.focus,#main-container .btn-primary.active:focus,#main-container .btn-primary.active:hover,#main-container .btn-primary:active.focus,#main-container .btn-primary:active:focus,#main-container .btn-primary:active:hover,#main-container .open .dropdown-toggle.btn-primary.focus,#main-container .open .dropdown-toggle.btn-primary:focus,#main-container .open .dropdown-toggle.btn-primary:hover', color2, color1);
            setBorderColorStyle('#main-container .btn-primary:hover,#main-container .btn-primary:focus,#main-container .btn-primary.active,#main-container .btn-primary:active,#main-container .open .dropdown-toggle.btn-primary,#main-container .btn-primary.active.focus,#main-container .btn-primary.active:focus,#main-container .btn-primary.active:hover,#main-container .btn-primary:active.focus,#main-container .btn-primary:active:focus,#main-container .btn-primary:active:hover,#main-container .open .dropdown-toggle.btn-primary.focus,#main-container .open .dropdown-toggle.btn-primary:focus,#main-container .open .dropdown-toggle.btn-primary:hover', color1);
            setTextShadowStyle('#main-container .btn-primary', '0 1px 0 '+color1);
        },'right', 'rgb');
        
        // primary button text
        var btn_pri_color = $('.comments-wrapper .btn-primary').css('color');
        $('body').append('<div class="designer_colorpicker" id="btn-pri-txt-designer-colorpicker" title="'+t('Primary button text color')+'"></div>');
        designer_positions['btn_pri_txt'] = function() {
            var btn_pri_tx = $('.comments-wrapper .btn-primary').offset().left-1.5*colorpicker_size;
            var btn_pri_ty = $('.comments-wrapper .btn-primary').offset().top+($('.comments-wrapper .btn-primary').outerHeight()-colorpicker_size)/2;
            $('#btn-pri-txt-designer-colorpicker').css({'left':btn_pri_tx,'top':btn_pri_ty});
        };
        $('#btn-pri-txt-designer-colorpicker').tooltip();
        designer_colorpicker($('#btn-pri-txt-designer-colorpicker'), btn_pri_color, function(color){
            $('#main-container .btn-primary').css('color', color);
            setColorStyle('#main-container .btn-primary', color);
            setColorStyle('#main-container .btn-primary:hover,#main-container .btn-primary:focus,#main-container .btn-primary.active,#main-container .btn-primary:active,#main-container .open .dropdown-toggle.btn-primary,#main-container .btn-primary.active.focus,#main-container .btn-primary.active:focus,#main-container .btn-primary.active:hover,#main-container .btn-primary:active.focus,#main-container .btn-primary:active:focus,#main-container .btn-primary:active:hover,#main-container .open .dropdown-toggle.btn-primary.focus,#main-container .open .dropdown-toggle.btn-primary:focus,#main-container .open .dropdown-toggle.btn-primary:hover', color);
        });
        
        // default button background
        var btn_def_gr = extractGradient($('.comments-wrapper .btn-default'));
        $('body').append('<div class="designer_gradientpicker" id="btn-def-designer-gradientpicker" title="'+t('Default button background')+'"></div><div class="designer_gradientpicker_hidden" id="btn-def-designer-gradientpicker-hidden"></div>');
        designer_positions['btn_def_bg'] = function() {
            var btn_def_bx = $('.comments-wrapper .btn-default').offset().left+$('.comments-wrapper .btn-default').outerWidth()+.5*colorpicker_size;
            var btn_def_by = $('.comments-wrapper .btn-default').offset().top+($('.comments-wrapper .btn-default').outerHeight()-colorpicker_size)/2;
            $('#btn-def-designer-gradientpicker').css({'left':btn_def_bx,'top':btn_def_by});
            $('#btn-def-designer-gradientpicker-hidden').css({'left':btn_def_bx+colorpicker_wnd_size,'top':btn_def_by});
        };
        $('#btn-def-designer-gradientpicker').tooltip();
        designer_gradientpicker($('#btn-def-designer-gradientpicker'), $('#btn-def-designer-gradientpicker-hidden'), btn_def_gr[0], btn_def_gr[1], function(color1, color2){
            $('#main-container .btn-default').each(function(){
                if ($(this).parents('.search-simple-form').length) return true;
                $(this).css('background', 'linear-gradient(to bottom,' + color1 + ',' + color2 + ')');
                $(this).css('border-color', color2);
                $(this).css('textShadow', '0 1px 0 '+color1);
            });
            setBackgroundColorStyle('#main-container .btn-default', hexColor(color1), true);
            setBackgroundGradientStyle('#main-container .btn-default', color1, color2);
            setBorderColorStyle('#main-container .btn-default', color2);
            setBackgroundGradientStyle('#main-container .btn-default:hover,#main-container .btn-default:focus,#main-container .btn-default.active,#main-container .btn-default:active,#main-container .open .dropdown-toggle.btn-default,#main-container .btn-default.active.focus,#main-container .btn-default.active:focus,#main-container .btn-default.active:hover,#main-container .btn-default:active.focus,#main-container .btn-default:active:focus,#main-container .btn-default:active:hover,#main-container .open .dropdown-toggle.btn-default.focus,#main-container .open .dropdown-toggle.btn-default:focus,#main-container .open .dropdown-toggle.btn-default:hover', color2, color1);
            setBorderColorStyle('#main-container .btn-default:hover,#main-container .btn-default:focus,#main-container .btn-default.active,#main-container .btn-default:active,#main-container .open .dropdown-toggle.btn-default,#main-container .btn-default.active.focus,#main-container .btn-default.active:focus,#main-container .btn-default.active:hover,#main-container .btn-default:active.focus,#main-container .btn-default:active:focus,#main-container .btn-default:active:hover,#main-container .open .dropdown-toggle.btn-default.focus,#main-container .open .dropdown-toggle.btn-default:focus,#main-container .open .dropdown-toggle.btn-default:hover', color1);
            setTextShadowStyle('#main-container .btn-default', '0 1px 0 '+color1);
        },'right', 'rgb');
        
        // default button text
        var btn_def_color = $('.comments-wrapper .btn-default').css('color');
        $('body').append('<div class="designer_colorpicker" id="btn-def-txt-designer-colorpicker" title="'+t('Default button text color')+'"></div>');
        designer_positions['btn_def_txt'] = function() {
            var btn_def_tx = $('.comments-wrapper .btn-default').offset().left+$('.comments-wrapper .btn-default').outerWidth()+2*colorpicker_size;
            var btn_def_ty = $('.comments-wrapper .btn-default').offset().top+($('.comments-wrapper .btn-default').outerHeight()-colorpicker_size)/2;
            $('#btn-def-txt-designer-colorpicker').css({'left':btn_def_tx,'top':btn_def_ty});
        };
        $('#btn-def-txt-designer-colorpicker').tooltip();
        designer_colorpicker($('#btn-def-txt-designer-colorpicker'), btn_def_color, function(color){
            $('#main-container .btn-default').each(function(){
                if ($(this).parents('.search-simple-form').length) return true;
                $(this).css('color', color);
            });
            setColorStyle('#main-container .btn-default', color);
            setColorStyle('#main-container .btn-default:hover,#main-container .btn-default:focus,#main-container .btn-default.active,#main-container .btn-default:active,#main-container .open .dropdown-toggle.btn-default,#main-container .btn-default.active.focus,#main-container .btn-default.active:focus,#main-container .btn-default.active:hover,#main-container .btn-default:active.focus,#main-container .btn-default:active:focus,#main-container .btn-default:active:hover,#main-container .open .dropdown-toggle.btn-default.focus,#main-container .open .dropdown-toggle.btn-default:focus,#main-container .open .dropdown-toggle.btn-default:hover', color);
        });

        // comments
        if ($('.comments-wrapper').length>0) {
            // comments text
            var comments_color = $('.comments').css('color');
            $('body').append('<div class="designer_colorpicker" id="comments-txt-designer-colorpicker" title="'+t('Comments color')+'"></div>');
            designer_positions['comments_txt'] = function() {
                var comments_tx = $('.comments .comments-item:first-child .comment-text').offset().left+($('.comments .comments-item:first-child .comment-text').outerWidth()-colorpicker_size)/2-.75*colorpicker_size;
                var comments_ty = $('.comments .comments-item:first-child .comment-text').offset().top+($('.comments .comments-item:first-child .comment-text').outerHeight()-colorpicker_size)/2;
                $('#comments-txt-designer-colorpicker').css({'left':comments_tx,'top':comments_ty});
            };
            $('#comments-txt-designer-colorpicker').tooltip();
            designer_colorpicker($('#comments-txt-designer-colorpicker'), comments_color, function(color){
                //var txt_color = $('body').css('color');
                var txt_color = 'inherit';
                $('.comments').css('color', txt_color);
                $('.comments .comments-item .comment-text').css('color', color);
                setColorStyle('.comments', txt_color);
                setColorStyle('.comments .comments-item .comment-text', color);
            });
            
            // comments background
            var comments_bg = $('.comments .comments-item .comment-text').css('backgroundColor');
            $('body').append('<div class="designer_colorpicker" id="comments-bg-designer-colorpicker" title="'+t('Comments background')+'"></div>');
            designer_positions['comments_bg'] = function() {
                var comments_bx = $('.comments .comments-item:first-child .comment-text').offset().left+($('.comments .comments-item:first-child .comment-text').outerWidth()-colorpicker_size)/2+.75*colorpicker_size;
                var comments_by = $('.comments .comments-item:first-child .comment-text').offset().top+($('.comments .comments-item:first-child .comment-text').outerHeight()-colorpicker_size)/2;
                $('#comments-bg-designer-colorpicker').css({'left':comments_bx,'top':comments_by});
            };
            $('#comments-bg-designer-colorpicker').tooltip();
            designer_colorpicker($('#comments-bg-designer-colorpicker'), comments_bg, function(color){
                $('.comments .comments-item .comment-text').css('backgroundColor', color);
                $('.comments .comments-item .comment-text').css('borderColor', color);
                var boxShadow = 'inset 0px 0px 10px '+color;
                $('.comments .comments-item .comment-text').css('box-shadow', boxShadow);
                setBackgroundColorStyle('.comments .comments-item .comment-text', color);
                setBorderColorStyle('.comments .comments-item .comment-text', color);
                setBoxShadowStyle('.comments .comments-item .comment-text', boxShadow);
            }, 'left');
            
            // comments rating
            var comments_like_color = $('.comments .comments-item .comment-info a.comment-like').css('color');
            var comments_dislike_color = $('.comments .comments-item .comment-info a.comment-dislike').css('color');
            $('body').append('<div class="designer_colorpicker" id="comments-rating-designer-gradientpicker" title="'+t('Comments rating')+'"></div><div class="designer_gradientpicker_hidden" id="comments-rating-designer-gradientpicker-hidden"></div>');
            designer_positions['comments_rating'] = function() {
                var comments_rx = $('.comments .comments-item .comment-info a.comment-dislike').offset().left+$('.comments .comments-item .comment-info a.comment-dislike').outerWidth()+.5*colorpicker_size;
                var comments_ry = $('.comments .comments-item .comment-info').offset().top+($('.comments .comments-item .comment-info').outerHeight()-colorpicker_size)/2;
                $('#comments-rating-designer-gradientpicker').css({'left':comments_rx,'top':comments_ry});
                $('#comments-rating-designer-gradientpicker-hidden').css({'left':comments_rx+colorpicker_wnd_size,'top':comments_ry});
            };
            $('#comments-rating-designer-gradientpicker').tooltip();
            designer_gradientpicker($('#comments-rating-designer-gradientpicker'), $('#comments-rating-designer-gradientpicker-hidden'), comments_like_color, comments_dislike_color, function(color1, color2){
                $('.comments .comments-item .comment-info a.comment-like').css('color', color1);
                $('.comments .comments-item .comment-info a.comment-dislike').css('color', color2);
                setColorStyle('.comments .comments-item .comment-info a.comment-like:link,.comments .comments-item .comment-info a.comment-like:visited,.comments .comments-item .comment-info a.comment-like:hover', color1);
                setColorStyle('.comments .comments-item .comment-info a.comment-dislike:link,.comments .comments-item .comment-info a.comment-dislike:visited,.comments .comments-item .comment-info a.comment-dislike:hover', color2);
                
                setColorStyle('.forum-list.list .list-item .list-info-wrapper a.forum-like:link,.forum-list.list .list-item .list-info-wrapper a.forum-like:visited,.forum-list.list .list-item .list-info-wrapper a.forum-like:hover', color1);
                setColorStyle('.forum-list.list .list-item .list-info-wrapper a.forum-dislike:link,.forum-list.list .list-item .list-info-wrapper a.forum-dislike:visited,.forum-list.list .list-item .list-info-wrapper a.forum-dislike:hover', color2);
            }, 'right', 'rgb');
        }

        // forms
        if ($('.panel').length>0) {
            // forms text
            var forms_color = $('.panel .panel-heading').css('color');
            $('body').append('<div class="designer_colorpicker" id="forms-color-designer-colorpicker" title="'+t('Forms text color')+'"></div>');
            designer_positions['forms_txt'] = function() {
                var forms_tx = $('.panel .panel-heading').offset().left+($('.panel .panel-heading').outerWidth()-colorpicker_size)/2-.75*colorpicker_size;
                var forms_ty = $('.panel .panel-heading').offset().top+($('.panel .panel-heading').outerHeight()-colorpicker_size)/2;
                $('#forms-color-designer-colorpicker').css({'left':forms_tx,'top':forms_ty});
            };
            $('#forms-color-designer-colorpicker').tooltip();
            designer_colorpicker($('#forms-color-designer-colorpicker'), forms_color, function(color){
                $('.panel,.panel .panel-heading,.panel .panel-footer,#main-container .input-group-addon a').css('color', color);
                setColorStyle('.panel', color);
                setColorStyle('.panel .panel-heading', color);
                setColorStyle('.panel .panel-footer', color);
                setColorStyle('#main-container .help-block', color);
                setColorStyle('#main-container .form-control', color);
                setColorStyle('.emoji-editable', color);
                setColorStyle('#main-container .input-group-addon', color);
                setColorStyle('#main-container .input-group-addon a', color);
            });
            
            // forms background
            var forms_gr = extractGradient($('.panel .panel-heading'));
            $('body').append('<div class="designer_gradientpicker" id="forms-bg-designer-gradientpicker" title="'+t('Forms background')+'"></div><div class="designer_gradientpicker_hidden" id="forms-bg-designer-gradientpicker-hidden"></div>');
            designer_positions['forms_bg'] = function() {
                var forms_bx = $('.panel .panel-heading').offset().left+($('.panel .panel-heading').outerWidth()-colorpicker_size)/2+.75*colorpicker_size;
                var forms_by = $('.panel .panel-heading').offset().top+($('.panel .panel-heading').outerHeight()-colorpicker_size)/2;
                $('#forms-bg-designer-gradientpicker').css({'left':forms_bx,'top':forms_by});
                $('#forms-bg-designer-gradientpicker-hidden').css({'left':forms_bx+colorpicker_wnd_size,'top':forms_by});
            };
            $('#forms-bg-designer-gradientpicker').tooltip();
            designer_gradientpicker($('#forms-bg-designer-gradientpicker'), $('#forms-bg-designer-gradientpicker-hidden'), forms_gr[0], forms_gr[1], function(color1, color2){
                $('.panel .panel-heading').css('background', 'linear-gradient(to bottom,' + color1 + ',' + color2 + ')');
                $('.panel,.panel .panel-heading,.panel .panel-footer').css('borderColor', color2);
                $('.panel .panel-footer').css('background', color1);
                $('.panel').css('background', color2);
                $('#main-container .form-control').each(function(){
                    if ($(this).parents('.search-simple-form').length) return true;
                    $(this).css('background', color2);
                    $(this).css('borderColor', color1);
                });
                $('#main-container .input-group-addon').css('background', color2);
                $('#main-container .input-group-addon').css('borderColor', color1);
                setBackgroundColorStyle('.panel .panel-heading', hexColor(color1), true);
                setBackgroundGradientStyle('.panel .panel-heading', color1, color2);
                setBorderColorStyle('.panel .panel-heading', color2);
                setBackgroundStyle('.panel .panel-footer', color1);
                setBorderColorStyle('.panel .panel-footer', color2);
                setBorderColorStyle('.panel', color2);
                setBackgroundColorStyle('.jumbotron', hexColor(color1));
                setBackgroundColorStyle('.panel', color2);
                setBackgroundStyle('#main-container .form-control', color2);
                setBorderColorStyle('#main-container .form-control', color1);
                setBackgroundStyle('#main-container .input-group-addon', color2);
                setBorderColorStyle('#main-container .input-group-addon', color1);
                setBackgroundStyle('.emoji-editable', color2);
                setBorderColorStyle('.emoji-editable', color1);
                setBackgroundStyle('#main-container .panel .dropdown-menu', color2);
                setBackgroundStyle('#main-container .panel .bootstrap-datetimepicker-widget table td.day:hover,#main-container .panel .bootstrap-datetimepicker-widget table td.hour:hover,#main-container .panel .bootstrap-datetimepicker-widget table td.minute:hover,#main-container .panel .bootstrap-datetimepicker-widget table td.second:hover,#main-container .panel .bootstrap-datetimepicker-widget table thead tr:first-child th:hover', color1);
            }, 'right', 'rgb');
        }
        
        // lists
        if ($('.home-list').length>0) {
            // lists text
            var lists_color1 = $('.home-list .list-title-wrapper a').css('color');
            var lists_color2 = $('.home-list .list-item').css('color');
            $('body').append('<div class="designer_colorpicker" id="lists-color-designer-gradientpicker" title="'+t('Records text color')+'"></div><div class="designer_gradientpicker_hidden" id="lists-color-designer-gradientpicker-hidden"></div>');
            designer_positions['lists_txt'] = function() {
                var lists_tx = $('.home-list').offset().left+($('.home-list').outerWidth()-colorpicker_size)/2-.75*colorpicker_size;
                var lists_ty = $('.home-list').offset().top-3*colorpicker_size;
                $('#lists-color-designer-gradientpicker').css({'left':lists_tx,'top':lists_ty});
                $('#lists-color-designer-gradientpicker-hidden').css({'left':lists_tx+colorpicker_wnd_size,'top':lists_ty});
            };
            $('#lists-color-designer-gradientpicker').tooltip();
            designer_gradientpicker($('#lists-color-designer-gradientpicker'), $('#lists-color-designer-gradientpicker-hidden'), lists_color1, lists_color2, function(color1, color2){
                $('.sidebar .block').css('color', color2);
                $('.list .list-item').css('color', color2);
                $('.list .list-item .list-title-wrapper').css('color', color1);
                $('.list .list-item .list-title-wrapper a').css('color', color1);
                $('.list .list-item .list-content-wrapper').css('color', color2);
                $('.list .list-item .list-info,.list .list-item .list-info a').css('color', color1);
                $('.sidebar .widget-title').css('color', color1);
                $('.zira-calendar-wrapper .zira-calendar-days li a').css('color', color1);
                $('.zira-calendar-wrapper .zira-calendar-days li.prev-days .zira-calendar-day,.zira-calendar-wrapper .zira-calendar-days li.next-days .zira-calendar-day').css('color', color2);
                $('.pagination > li > a,.pagination > li > span').css('color', color2);
                $('.pagination > .active > a,.pagination > .active > span').css('color', color1);
                
                setColorStyle('.sidebar .block', color2);
                setColorStyle('.list .list-item', color2);
                setColorStyle('.list .list-item .list-title-wrapper', color1);
                setColorStyle('.list .list-item .list-title-wrapper a:link,.list .list-item .list-title-wrapper a:visited', color1);
                setColorStyle('.list .list-item .list-content-wrapper', color2);
                setColorStyle('.list .list-item .list-info', color1);
                setColorStyle('.list .list-item .list-info a:link,.list .list-item .list-info a:visited', color1);
                setColorStyle('.list .list-item .list-title-wrapper a:hover', color2);
                setColorStyle('.sidebar .widget-title,.sidebar .widget-title a:link,.sidebar .widget-title a:visited', color1);
                setColorStyle('.sidebar .widget-title a:hover', color2);
                setColorStyle('ul.vote-results li .vote-result', color1);
                setBackgroundStyle('.vote-results-line', color1);
                setColorStyle('.zira-calendar-wrapper', color2);
                setColorStyle('.zira-calendar-wrapper .zira-calendar-days li a', color1);
                setColorStyle('.zira-calendar-wrapper .zira-calendar-days li.prev-days .zira-calendar-day,.zira-calendar-wrapper .zira-calendar-days li.next-days .zira-calendar-day', color2);
                setColorStyle('.image-wrapper .image-description', color2);
                
                setColorStyle('.messages-panel .navbar-default .navbar-brand', color1);
                setColorStyle('.messages-panel .navbar-default .navbar-nav li a', color1);
                setColorStyle('.messages-panel .navbar-default .navbar-nav li a:hover', color2);
                setColorStyle('.messages-list li', color2);
                setColorStyle('.messages-list li a:link, .messages-list li a:visited', color1);
                setColorStyle('.messages-list li a:hover', color2);
                
                setColorStyle('.forum-list.list .list-item a.list-title:link,.forum-list.list .list-item a.list-title:visited,.forum-list.list .list-item .list-title-wrapper a:link,.forum-list.list .list-item .list-title-wrapper a:visited', color1);
                setColorStyle('.forum-list.list .list-item a.list-title:hover', color2);
                setColorStyle('.forum-list.list .list-item .list-info,.forum-list.list .list-item .list-info a:link,.forum-list.list .list-item .list-info a:visited', color1);
                
                setColorStyle('#sitemap-wrapper ul', color1);
                setColorStyle('#sitemap-wrapper ul li a:link, #sitemap-wrapper ul li a:visited', color1);
                setColorStyle('#sitemap-wrapper ul li a:hover', color2);
                
                setColorStyle('.pagination > li > a,.pagination > li > span', color2);
                setColorStyle('.pagination > li > a:focus,.pagination > li > a:hover,.pagination > li > span:focus,.pagination > li > span:hover', color2);
                setColorStyle('.pagination > .active > a,.pagination > .active > a:focus,.pagination > .active > a:hover,.pagination > .active > span,.pagination > .active > span:focus,.pagination > .active > span:hover', color1);
            }, 'right', 'rgb');
            
            // lists background
            var lists_gr = extractGradient($('.home-list .list-title-wrapper'));
            $('body').append('<div class="designer_gradientpicker" id="lists-bg-designer-gradientpicker" title="'+t('Records background')+'"></div><div class="designer_gradientpicker_hidden" id="lists-bg-designer-gradientpicker-hidden"></div>');
            designer_positions['lists_bg'] = function() {
                var lists_bx = $('.home-list').offset().left+($('.home-list').outerWidth()-colorpicker_size)/2+.75*colorpicker_size;
                var lists_by = $('.home-list').offset().top-3*colorpicker_size;
                $('#lists-bg-designer-gradientpicker').css({'left':lists_bx,'top':lists_by});
                $('#lists-bg-designer-gradientpicker-hidden').css({'left':lists_bx+colorpicker_wnd_size,'top':lists_by});
            };
            $('#lists-bg-designer-gradientpicker').tooltip();
            designer_gradientpicker($('#lists-bg-designer-gradientpicker'), $('#lists-bg-designer-gradientpicker-hidden'), lists_gr[0], lists_gr[1], function(color1, color2){
                $('#main-container .list-item').each(function(){
                    if ($(this).parents('.sidebar').length) return true;
                    $(this).children('.list-title-wrapper').css('background', 'linear-gradient(to bottom,'+color1+','+color2+')');
                    $(this).css('background', color1);
                    $(this).css('borderColor', color1);
                    $(this).children('.list-title-wrapper').css('borderColor', color1);
                });
                $('.list .list-item .list-title-wrapper a,.sidebar .widget-title').css('textShadow', '1px 1px 0px '+color2);
                if ($('.sidebar .page-header').css('backgroundColor')!='transparent') {
                    $('.sidebar .page-header').css('background', color2);
                }
                if ($('.sidebar.col-sm-4 > aside > div').not('.noframe,#secondary-menu-wrapper').css('backgroundColor')!='transparent') {
                    $('.sidebar.col-sm-4 > aside > div').not('.noframe,#secondary-menu-wrapper').css('background', color1);
                }
                $('.sidebar .page-header,.sidebar .list .list-item').css('borderColor', color2);
                $('.sidebar .list .list-item a.list-thumb:link, .sidebar .list .list-item a.list-thumb:visited').css('borderColor', color2);
                $('.sidebar .list .list-item a.list-thumb:link, .sidebar .list .list-item a.list-thumb:visited').css('background', color2);
                $('.sidebar.col-sm-4 div.calendar-widget-wrapper,.zira-calendar-wrapper .zira-calendar-selector,.zira-calendar-wrapper .zira-calendar-dows-wrapper').css('background', color1);
                $('.zira-calendar-wrapper .zira-calendar-days-wrapper').css('background', color2);
                $('.zira-calendar-wrapper .zira-calendar-days li:hover').css('background', color2);
                $('.zira-calendar-wrapper .zira-calendar-dows-wrapper').css('borderColor', color1);
                $('.zira-calendar-wrapper .zira-calendar-days-wrapper').css('borderColor', color2);
                $('.pagination > li > a,.pagination > li > span').css('background', color1);
                $('.pagination > li > a,.pagination > li > span').css('borderColor', color1);
                $('.pagination > .active > a,.pagination > .active > span').css('background', color2);
                $('.pagination > .active > a,.pagination > .active > span').css('borderColor', color1);
                
                setBackgroundColorStyle('.list .list-item .list-title-wrapper', hexColor(color1), true);
                setBackgroundGradientStyle('.list .list-item .list-title-wrapper', color1, color2);
                setBorderColorStyle('.list .list-item', color1);
                setBackgroundStyle('.list .list-item', color1);
                if ($('.sidebar .page-header').css('backgroundColor')!='transparent') {
                    setBackgroundStyle('.sidebar .page-header', color2);
                }
                setBorderColorStyle('.list .list-item .list-title-wrapper', color1);
                setTextShadowStyle('.list .list-item .list-title-wrapper a:link,.list .list-item .list-title-wrapper a:visited', '1px 1px 0px '+color2);
                setTextShadowStyle('.sidebar .widget-title', '1px 1px 0px '+color2);
                if ($('.sidebar.col-sm-4 > aside > div').not('.noframe,#secondary-menu-wrapper').css('backgroundColor')!='transparent') {
                    setBackgroundStyle('.sidebar.col-sm-4 > aside > div', color1);
                }
                setBorderColorStyle('.sidebar .page-header', color2);
                setBorderColorStyle('.sidebar .list .list-item a.list-thumb:link,.sidebar .list .list-item a.list-thumb:visited', color2);
                setBackgroundStyle('.sidebar .list .list-item a.list-thumb:link,.sidebar .list .list-item a.list-thumb:visited', color2);
                setBackgroundStyle('.sidebar .forum-discussion-widget-wrapper .forum-widget-list.list .forum-widget-content-wrapper.list-content-wrapper', color2);
                setBorderColorStyle('.sidebar .list .list-item', color2);
                setBackgroundStyle('.sidebar.col-sm-4 div.calendar-widget-wrapper,.zira-calendar-wrapper .zira-calendar-selector,.zira-calendar-wrapper .zira-calendar-dows-wrapper', color1);
                setBackgroundStyle('.zira-calendar-wrapper .zira-calendar-days-wrapper', color2);
                setBorderColorStyle('.zira-calendar-wrapper .zira-calendar-dows-wrapper', color1);
                setBorderColorStyle('.zira-calendar-wrapper .zira-calendar-days-wrapper', color2);
                setBackgroundStyle('.zira-calendar-wrapper .zira-calendar-days li:hover', color1);
                setBackgroundStyle('.image-wrapper', color1);
                setBorderColorStyle('.image-wrapper', color1);
                
                setBackgroundStyle('.messages-panel .navbar', color2);
                setFilterStyle('.messages-panel .navbar', 'none');
                setBorderColorStyle('.messages-panel .navbar', color1);
                setBackgroundStyle('.messages-list li.odd', color1);
                setBackgroundStyle('.messages-list li.even', color2);
                setBorderTopColorStyle('.messages-list li', color1);
                setBorderBottomColorStyle('.messages-list li', color2);
                setBorderColorStyle('.messages-list li.odd .message-head', color2);
                setBorderColorStyle('.messages-list li.even .message-head', color1);
                
                setBackgroundStyle('.forum-messages-panel.messages-panel nav', color2);
                setBackgroundStyle('.forum-list.list .list-item', color2);
                setBackgroundStyle('.forum-list.list .list-item.even,.forum-list.list .list-item.even-b,.forum-list.list .list-item.odd-b', color1);
                setBackgroundStyle('.forum-list.list .list-item .forum-message-attaches', color2);
                setBorderColorStyle('.forum-list.list .list-item .forum-message-attaches', color1);
                setBackgroundStyle('.forum-message-wrapper .forum-avatar-wrapper', color2);
                setBorderColorStyle('.forum-message-wrapper .forum-avatar-wrapper', color1);
                setBackgroundStyle('.forum-list.list .list-item .list-info-wrapper', color1);
                setBackgroundStyle('.forum-list.list .list-item.even .list-info-wrapper,.forum-list.list .list-item.even-b .list-info-wrapper,.forum-list.list .list-item.odd-b .list-info-wrapper', color2);
                setBackgroundStyle('.messages-panel .navbar-default .navbar-nav > .active > a,.messages-panel .navbar-default .navbar-nav > .open > a,.messages-panel .navbar-default .navbar-nav > .active > a:hover,.messages-panel .navbar-default .navbar-nav > .open > a:hover,.messages-panel .navbar-default .navbar-nav > .active > a:focus,.messages-panel .navbar-default .navbar-nav > .open > a:focus', color2);
                setFilterStyle('.messages-panel .navbar-default .navbar-nav > .active > a,.messages-panel .navbar-default .navbar-nav > .open > a,.messages-panel .navbar-default .navbar-nav > .active > a:hover,.messages-panel .navbar-default .navbar-nav > .open > a:hover,.messages-panel .navbar-default .navbar-nav > .active > a:focus,.messages-panel .navbar-default .navbar-nav > .open > a:focus', 'none');
                
                setBackgroundStyle('#sitemap-wrapper ul li.odd', color1);
                setBackgroundStyle('#sitemap-wrapper ul li.even', color2);
                setBorderTopStyle('#sitemap-wrapper ul li', '1px solid '+color1);
                setBorderBottomStyle('#sitemap-wrapper ul li', '1px solid '+color2);
                setBorderColorStyle('#sitemap-wrapper ul', color1);
                
                setBackgroundStyle('.pagination > li > a,.pagination > li > span', color1);
                setBorderColorStyle('.pagination > li > a,.pagination > li > span', color1);
                setBackgroundStyle('.pagination > li > a:focus,.pagination > li > a:hover,.pagination > li > span:focus,.pagination > li > span:hover', color2);
                setBorderColorStyle('.pagination > li > a:focus,.pagination > li > a:hover,.pagination > li > span:focus,.pagination > li > span:hover', color1);
                setBackgroundStyle('.pagination > .active > a,.pagination > .active > a:focus,.pagination > .active > a:hover,.pagination > .active > span,.pagination > .active > span:focus,.pagination > .active > span:hover', color2);
                setBorderColorStyle('.pagination > .active > a,.pagination > .active > a:focus,.pagination > .active > a:hover,.pagination > .active > span,.pagination > .active > span:focus,.pagination > .active > span:hover', color1);
                setBackgroundStyle('.pagination > .disabled > a,.pagination > .disabled > a:focus,.pagination > .disabled > a:hover,.pagination > .disabled > span,.pagination > .disabled > span:focus,.pagination > .disabled > span:hover', color1);
                setBorderColorStyle('.pagination > .disabled > a,.pagination > .disabled > a:focus,.pagination > .disabled > a:hover,.pagination > .disabled > span,.pagination > .disabled > span:focus,.pagination > .disabled > span:hover', color1);
            }, 'right', 'rgb');
        }
        
        // secondary menu
        if ($('#secondary-menu-wrapper').length>0) {
            // secondary menu text
            var sec_menu_color1 = $('#secondary-menu-wrapper ul li a').css('color');
            var sec_menu_color2 = $('#secondary-menu-wrapper ul li.active a').css('color');
            $('body').append('<div class="designer_colorpicker" id="sec-menu-color-designer-gradientpicker" title="'+t('Secondary menu text color')+'"></div><div class="designer_gradientpicker_hidden" id="sec-menu-color-designer-gradientpicker-hidden"></div>');
            designer_positions['sec_menu_txt'] = function() {
                var sec_menu_tx = $('#secondary-menu-wrapper').offset().left+($('#secondary-menu-wrapper').outerWidth()-colorpicker_size)/2-2.75*colorpicker_size;
                var sec_menu_ty = $('#secondary-menu-wrapper').offset().top+$('#secondary-menu-wrapper').outerHeight()-1.5*colorpicker_size;
                $('#sec-menu-color-designer-gradientpicker').css({'left':sec_menu_tx,'top':sec_menu_ty});
                $('#sec-menu-color-designer-gradientpicker-hidden').css({'left':sec_menu_tx+colorpicker_wnd_size,'top':sec_menu_ty});
            };
            $('#sec-menu-color-designer-gradientpicker').tooltip();
            designer_gradientpicker($('#sec-menu-color-designer-gradientpicker'), $('#sec-menu-color-designer-gradientpicker-hidden'), sec_menu_color1, sec_menu_color2, function(color1, color2){
                $('#secondary-menu-wrapper a').css('color', color1);
                $('#secondary-menu-wrapper .active a').css('color', color2);
                setColorStyle('#secondary-menu-wrapper ul li a:link,#secondary-menu-wrapper ul li a:visited', color1);
                setColorStyle('#secondary-menu-wrapper ul li.active a:link,#secondary-menu-wrapper ul li.active a:visited', color2);
                setColorStyle('#secondary-menu-wrapper ul li a:hover,#secondary-menu-wrapper ul li.active a:hover', color2);
            }, 'right', 'rgb');
            
            // secondary menu background
            var sec_menu_bg1 = $('#secondary-menu-wrapper ul li a').css('backgroundColor');
            var sec_menu_bg2 = $('#secondary-menu-wrapper ul li.active a').css('backgroundColor');
            $('body').append('<div class="designer_gradientpicker" id="sec-menu-bg-designer-gradientpicker" title="'+t('Secondary menu background')+'"></div><div class="designer_gradientpicker_hidden" id="sec-menu-bg-designer-gradientpicker-hidden"></div>');
            designer_positions['sec_menu_bg'] = function() {
                var sec_menu_bx = $('#secondary-menu-wrapper').offset().left+($('#secondary-menu-wrapper').outerWidth()-colorpicker_size)/2-1.25*colorpicker_size;
                var sec_menu_by = $('#secondary-menu-wrapper').offset().top+$('#secondary-menu-wrapper').outerHeight()-1.5*colorpicker_size;
                $('#sec-menu-bg-designer-gradientpicker').css({'left':sec_menu_bx,'top':sec_menu_by});
                $('#sec-menu-bg-designer-gradientpicker-hidden').css({'left':sec_menu_bx+colorpicker_wnd_size,'top':sec_menu_by});
            };
            $('#sec-menu-bg-designer-gradientpicker').tooltip();
            designer_gradientpicker($('#sec-menu-bg-designer-gradientpicker'), $('#sec-menu-bg-designer-gradientpicker-hidden'), sec_menu_bg1, sec_menu_bg2, function(color1, color2){
                $('#secondary-menu-wrapper ul li a').css('background', color1);
                $('#secondary-menu-wrapper ul li.active a').css('background', color2);
                setBackgroundColorStyle('#secondary-menu-wrapper ul li a:link,#secondary-menu-wrapper ul li a:visited', color1);
                setBackgroundColorStyle('#secondary-menu-wrapper ul li.active a:link,#secondary-menu-wrapper ul li.active a:visited,#secondary-menu-wrapper ul li a:hover', color2);
            }, 'right', 'rgb');
        }
        
        // container
        if ($('.container').length>0) {
            if (!isWideContainer()) {
                $('body').append('<a href="javascript:void(0)" class="designer_radio_btn designer_radio_container" id="container-designer-radio-btn" title="'+t('Set wide container')+'" data-placement="left"><span class="glyphicon glyphicon-unchecked"></span></a>');
            } else {
                $('body').append('<a href="javascript:void(0)" class="designer_radio_btn designer_radio_container" id="container-designer-radio-btn" title="'+t('Unset wide container')+'" data-placement="left"><span class="glyphicon glyphicon-check"></span></a>');
            }
            designer_positions['media_container'] = function() {
                if ($(window).width()<1200) {
                    $('#container-designer-radio-btn').hide();
                    return;
                }
                var media_container_x = $(window).width()-3*radiobtn_size;
                var media_container_y = $(window).height()-1.75*radiobtn_size;
                $('#container-designer-radio-btn').css({'left':media_container_x,'top':media_container_y});
                $('#container-designer-radio-btn').show();
            };
            $('#container-designer-radio-btn').tooltip();
            $('#container-designer-radio-btn').click(function(){
                if (!isWideContainer()) {
                    $('.container').css({width:'100%',maxWidth:'1400px'});
                    setWideContainer(true);
                    $(this).children('.glyphicon').removeClass('glyphicon-unchecked').addClass('glyphicon-check');
                    $(this).attr('title', t('Unset wide container')).tooltip('fixTitle').tooltip('hide');
                } else {
                    $('.container').css({width:'1170px',maxWidth:'inherit'});
                    setWideContainer(false);
                    $(this).children('.glyphicon').removeClass('glyphicon-check').addClass('glyphicon-unchecked');
                    $(this).attr('title', t('Set wide container')).tooltip('fixTitle').tooltip('hide');
                }
                $(window).trigger('resize');
            });
        }
        
        // cols
        if ($('.col-sm-4').length>0) {
            if (!isWideCols()) {
                $('body').append('<a href="javascript:void(0)" class="designer_radio_btn designer_radio_cols" id="cols-designer-radio-btn" title="'+t('Set wide column')+'" data-placement="left"><span class="glyphicon glyphicon-unchecked"></span></a>');
            } else {
                $('body').append('<a href="javascript:void(0)" class="designer_radio_btn designer_radio_cols" id="cols-designer-radio-btn" title="'+t('Unset wide column')+'" data-placement="left"><span class="glyphicon glyphicon-check"></span></a>');
            }
            designer_positions['media_cols'] = function() {
                if ($(window).width()<768) {
                    $('#cols-designer-radio-btn').hide();
                    return;
                }
                var media_cols_x = $(window).width()-1.75*radiobtn_size;
                var media_cols_y = $(window).height()-1.75*radiobtn_size;
                $('#cols-designer-radio-btn').css({'left':media_cols_x,'top':media_cols_y});
                $('#cols-designer-radio-btn').show();
            };
            $('#cols-designer-radio-btn').tooltip();
            $('#cols-designer-radio-btn').click(function(){
                if (!isWideCols()) {
                    $('.col-sm-4.sidebar').css({width:'39.9999%'});
                    $('.col-sm-8#content').css({width:'60%'});
                    setWideCols(true);
                    $(this).children('.glyphicon').removeClass('glyphicon-unchecked').addClass('glyphicon-check');
                    $(this).attr('title', t('Unset wide column')).tooltip('fixTitle').tooltip('hide');
                } else {
                    $('.col-sm-4.sidebar').css({width:'33.3333%'});
                    $('.col-sm-8#content').css({width:'66.6666%'});
                    setWideCols(false);
                    $(this).children('.glyphicon').removeClass('glyphicon-check').addClass('glyphicon-unchecked');
                    $(this).attr('title', t('Set wide column')).tooltip('fixTitle').tooltip('hide');
                }
                $(window).trigger('resize');
            });
        }
        
        // html
        if ($('body').height() != $(window).height()) {
            $('body').append('<a href="javascript:void(0)" class="designer_radio_btn designer_radio_html" id="html-designer-radio-btn" title="'+t('Set body height = 100%')+'" data-placement="left"><span class="glyphicon glyphicon-unchecked"></span></a>');
        } else {
            $('body').append('<a href="javascript:void(0)" class="designer_radio_btn designer_radio_html" id="html-designer-radio-btn" title="'+t('Set body height = auto')+'" data-placement="left"><span class="glyphicon glyphicon-check"></span></a>');
        }
        designer_positions['html_height'] = function() {
            var html_height_x = $(window).width()-1.75*radiobtn_size;
            var html_height_y = $(window).height()-3*radiobtn_size;
            $('#html-designer-radio-btn').css({'left':html_height_x,'top':html_height_y});
        };
        $('#html-designer-radio-btn').tooltip();
        $('#html-designer-radio-btn').click(function(){
            if ($('body').height() != $(window).height()) {
                $('html,body').css('height','100%');
                setHeightStyle('html,body','100%');
                $(this).children('.glyphicon').removeClass('glyphicon-unchecked').addClass('glyphicon-check');
                $(this).attr('title', t('Set body height = auto')).tooltip('fixTitle').tooltip('hide');
            } else {
                $('html,body').css('height','auto');
                removeHeightStyle('html,body');
                $(this).children('.glyphicon').removeClass('glyphicon-check').addClass('glyphicon-unchecked');
                $(this).attr('title', t('Set body height = 100%')).tooltip('fixTitle').tooltip('hide');
            }
        });
        
        $(designer_selectors).css('display', 'block');
        $(window).trigger('resize');
        
        if ($('body').css('backgroundImage').indexOf('gradient')>0 && $('#html-designer-radio-btn').length>0) {
            $('#html-designer-radio-btn').hide();
        }
    });
    
    var designer_colorpicker = function(element, init_color, callback, position, color_format) {
        if (typeof(position)=="undefined") position = 'right';
        if (typeof(color_format)=="undefined") color_format = 'rgb';
        $(element).colorpicker({
            customClass: 'colorpicker-2x',
            sliders: { 
                saturation: { maxLeft: 200, maxTop: 200 },
                hue: { maxTop: 200 },
                alpha: { maxTop: 200 }
            },
            color: init_color,
            align: position,
            format: color_format
        }).on('changeColor', zira_bind($(element), function(e) {
            var color = e.color.toString();
            if (typeof(callback)!="undefined") {
                callback.call(this, color);
            }
        }));
    };
    
    var designer_gradientpicker = function(element, child, init_color1, init_color2, callback, position, color_format) {
        if (typeof(position)=="undefined") position = 'right';
        if (typeof(color_format)=="undefined") color_format = false;
        $(element).colorpicker({
            customClass: 'colorpicker-2x',
            sliders: { 
                saturation: { maxLeft: 200, maxTop: 200 },
                hue: { maxTop: 200 },
                alpha: { maxTop: 200 }
            },
            color: init_color1,
            align: position,
            format: color_format
        }).on('showPicker', zira_bind($(child), function() {
            $(this).colorpicker('show');    
        })).on('changeColor', zira_bind($(child), function(e) {
            var color1 = e.color.toString();
            var color2 = $(this).data('colorpicker').color.toString();
            if (typeof(callback)!="undefined") {
                callback.call(null, color1, color2);
            }
        }));
        
        $(child).colorpicker({
            customClass: 'colorpicker-2x colorpicker-child',
            sliders: { 
                saturation: { maxLeft: 200, maxTop: 200 },
                hue: { maxTop: 200 },
                alpha: { maxTop: 200 }
            },
            color: init_color2,
            align: position,
            format: color_format
        }).on('changeColor', zira_bind($(element), function(e) {
            var color2 = e.color.toString();
            var color1 = $(this).data('colorpicker').color.toString();
            if (typeof(callback)!="undefined") {
                callback.call(null, color1, color2);
            }
        }));
    };
    
    var designer_imagepicker = function(element, callback) {
        $(element).click(zira_bind(element, function(){
            parent.jQuery('body', parent.document).trigger('designerEditorFileSelector', [zira_bind(this, function(elements){
                if (!elements || elements.length==0) return;
                var element = elements[0];
                if (element instanceof FileList) return;
                if (typeof(element)!="object" || typeof(element.type)=="undefined" || typeof(element.data)=="undefined" || typeof(element.title)=="undefined") return;
                if (typeof(element.parent)=="undefined" || element.parent!='files') return;
                if (element.type!='image') return;
                var regexp = new RegExp('\\'+this.desk_ds, 'g');
                var url = this.baseUrl(element.data.replace(regexp,'/')); 
                if (typeof(callback)!="undefined") {
                    callback.call(this, url);
                }
                parent.jQuery('body', parent.document).trigger('designerEditorFocus');
            }), this]);
        }));
    };
    
    var designer_fontpicker = function(element, size, designer_positions, callback) {
        if (typeof(size)=="undefined") size = $('body').css('fontSize');
        size = parseInt(size);
        var id = $(element).attr('id');
        $('body').append('<span id="'+id+'-plus'+'" data-sign="1" class="designer_fontpicker_sign glyphicon glyphicon-plus-sign" title="'+t('Increase')+'"></span>');
        $('body').append('<span id="'+id+'-minus'+'" data-sign="-1" class="designer_fontpicker_sign glyphicon glyphicon-minus-sign" title="'+t('Decrease')+'"></span>');
        designer_positions[id+'_fontpicker_sign'] = zira_bind(element, function() {
            var id = $(this).attr('id');
            if ($(this).css('display')=='none' || $(this).css('visibility')=='hidden') {
                $('#'+id+'-plus').hide();
                $('#'+id+'-minus').hide();
                return;
            }
            var fh = parseInt($('.designer_fontpicker_sign').css('fontSize'));
            var fx = $(this).offset().left + $(this).outerWidth() + .5*fh;
            var fy = $(this).offset().top + ($(this).outerHeight()-fh)/2;
            $('#'+id+'-plus').css({left:fx,top:fy-.5*fh-1});
            $('#'+id+'-minus').css({left:fx,top:fy+.5*fh+1});
            $('#'+id+'-plus').show();
            $('#'+id+'-minus').show();
        });
        $('#'+id+'-plus').tooltip();
        $('#'+id+'-minus').tooltip();
        $('#'+id+'-plus,#'+id+'-minus').click(function(){
            var sign = parseInt($(this).data('sign'));
            size += sign;
            if (size<1) size = 1;
            if (size>50) size = 50;
            if (typeof(callback)!="undefined") {
                callback.call(this, size+'px');
            }
        });
    };
    
    var setBackgroundColorStyle = function(element, value, addOnly) {
        if (typeof (addOnly) == "undefined") addOnly = false;
        editorMap.set(element, 'bgcolor', 'background-color:' + value + ';');
        if (!addOnly) {
            removeBackgroundGradientStyle(element);
            removeBackgroundImageStyle(element);
            removeBackgroundStyle(element);
        }
    };
    
    var getBackgroundColorStyle = function(element) {
        return editorMap.get(element, 'bgcolor');
    };
    
    var removeBackgroundColorStyle = function(element) {
        editorMap.remove(element, 'bgcolor');
    };
    
    var setBackgroundGradientStyle = function(element, value1, value2, addOnly) {
        if (typeof (addOnly) == "undefined") addOnly = false;
        editorMap.set(element, 'bggradientwebkit1', 'background:-webkit-linear-gradient(top,' + value1 + ' 0,' + value2 + ' 100%);');
        editorMap.set(element, 'bggradientwebkit2', 'background:-webkit-gradient(linear,left top,left bottom,from(' + value1 + '),to(' + value2 + '));');
        editorMap.set(element, 'bggradientopera', 'background:-o-linear-gradient(top,' + value1 + ' 0,' + value2 + ' 100%);');
        editorMap.set(element, 'bggradient', 'background:linear-gradient(to bottom,' + value1 + ',' + value2 + ');');
        editorMap.set(element, 'bggradientie', 'filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=' + rgbaToHexIE(value1) + ',endColorstr=' + rgbaToHexIE(value2) + ',GradientType=0);');
        if (!addOnly) {
            removeBackgroundImageStyle(element);
            removeBackgroundStyle(element);
        }
    };
    
    var getBackgroundGradientStyle = function(element) {
        return editorMap.get(element, 'bggradient');
    };
    
    var removeBackgroundGradientStyle = function(element) {
        editorMap.remove(element, 'bggradientwebkit1');
        editorMap.remove(element, 'bggradientwebkit2');
        editorMap.remove(element, 'bggradientopera');
        editorMap.remove(element, 'bggradient');
        editorMap.remove(element, 'bggradientie');
    };
    
    var setBackgroundImageStyle = function(element, value, addOnly) {
        if (typeof (addOnly) == "undefined") addOnly = false;
        editorMap.set(element, 'bgimage', 'background-image:url(' + value + ');');
        if (!addOnly) {
            removeBackgroundGradientStyle(element);
            removeBackgroundStyle(element);
        }
    };
    
    var getBackgroundImageStyle = function(element) {
        return editorMap.get(element, 'bgimage');
    };
    
    var removeBackgroundImageStyle = function(element) {
        editorMap.remove(element, 'bgimage');
    };
    
    var setBackgroundStyle = function(element, value, addOnly) {
        if (typeof (addOnly) == "undefined") addOnly = false;
        editorMap.set(element, 'bg', 'background:' + value + ';');
        if (!addOnly) {
            removeBackgroundColorStyle(element);
            removeBackgroundGradientStyle(element);
            removeBackgroundImageStyle(element);
        }
    };
    
    var getBackgroundStyle = function(element) {
        return editorMap.get(element, 'bg');
    };
    
    var removeBackgroundStyle = function(element) {
        editorMap.remove(element, 'bg');
    };

    var setColorStyle = function(element, value) {
        editorMap.set(element, 'color', 'color:' + value + ';');
    };
    
    var getColorStyle = function(element) {
        return editorMap.get(element, 'color');
    };
    
    var removeColorStyle = function(element) {
        editorMap.remove(element, 'color');
    };

    var setBorderColorStyle = function(element, value) {
        editorMap.set(element, 'bordercolor', 'border-color:' + value + ';');
    };
    
    var getBorderColorStyle = function(element) {
        return editorMap.get(element, 'bordercolor');
    };
    
    var removeBorderColorStyle = function(element) {
        editorMap.remove(element, 'bordercolor');
    };

    var setBorderTopColorStyle = function(element, value) {
        editorMap.set(element, 'bordertopcolor', 'border-top-color:' + value + ';');
    };
    
    var getBorderTopColorStyle = function(element) {
        return editorMap.get(element, 'bordertopcolor');
    };
    
    var removeBorderTopColorStyle = function(element) {
        editorMap.remove(element, 'bordertopcolor');
    };
    
    var setBorderBottomColorStyle = function(element, value) {
        editorMap.set(element, 'borderbottomcolor', 'border-bottom-color:' + value + ';');
    };
    
    var getBorderBottomColorStyle = function(element) {
        return editorMap.get(element, 'borderbottomcolor');
    };
    
    var removeBorderBottomColorStyle = function(element) {
        editorMap.remove(element, 'borderbottomcolor');
    };
    
    var setBorderLeftColorStyle = function(element, value) {
        editorMap.set(element, 'borderleftcolor', 'border-left-color:' + value + ';');
    };
    
    var getBorderLeftColorStyle = function(element) {
        return editorMap.get(element, 'borderleftcolor');
    };
    
    var removeBorderLeftColorStyle = function(element) {
        editorMap.remove(element, 'borderleftcolor');
    };
    
    var setBorderRightColorStyle = function(element, value) {
        editorMap.set(element, 'borderrightcolor', 'border-right-color:' + value + ';');
    };
    
    var getBorderRightColorStyle = function(element) {
        return editorMap.get(element, 'borderrightcolor');
    };
    
    var removeBorderRightColorStyle = function(element) {
        editorMap.remove(element, 'borderrightcolor');
    };

    var setBorderStyle = function(element, value) {
        editorMap.set(element, 'border', 'border:' + value + ';');
    };
    
    var getBorderStyle = function(element) {
        return editorMap.get(element, 'border');
    };
    
    var removeBorderStyle = function(element) {
        editorMap.remove(element, 'border');
    };

    var setBorderTopStyle = function(element, value) {
        editorMap.set(element, 'bordertop', 'border-top:' + value + ';');
    };
    
    var getBorderTopStyle = function(element) {
        return editorMap.get(element, 'bordertop');
    };
    
    var removeBorderTopStyle = function(element) {
        editorMap.remove(element, 'bordertop');
    };
    
    var setBorderBottomStyle = function(element, value) {
        editorMap.set(element, 'borderbottom', 'border-bottom:' + value + ';');
    };
    
    var getBorderBottomStyle = function(element) {
        return editorMap.get(element, 'borderbottom');
    };
    
    var removeBorderBottomStyle = function(element) {
        editorMap.remove(element, 'borderbottom');
    };
    
    var setBorderLeftStyle = function(element, value) {
        editorMap.set(element, 'borderleft', 'border-left:' + value + ';');
    };
    
    var getBorderLeftStyle = function(element) {
        return editorMap.get(element, 'borderleft');
    };
    
    var removeBorderLeftStyle = function(element) {
        editorMap.remove(element, 'borderleft');
    };
    
    var setBorderRightStyle = function(element, value) {
        editorMap.set(element, 'borderright', 'border-right:' + value + ';');
    };
    
    var getBorderRightStyle = function(element) {
        return editorMap.get(element, 'borderright');
    };
    
    var removeBorderRightStyle = function(element) {
        editorMap.remove(element, 'borderright');
    };
    
    var setBoxShadowStyle = function(element, value) {
        editorMap.set(element, 'boxshadow', 'box-shadow:' + value + ';');
    };
    
    var getBoxShadowStyle = function(element) {
        return editorMap.get(element, 'boxshadow');
    };
    
    var removeBoxShadowStyle = function(element) {
        editorMap.remove(element, 'boxshadow');
    };
    
    var setTextShadowStyle = function(element, value) {
        editorMap.set(element, 'textshadow', 'text-shadow:' + value + ';');
    };
    
    var getTextShadowStyle = function(element) {
        return editorMap.get(element, 'textshadow');
    };
    
    var removeTextShadowStyle = function(element) {
        editorMap.remove(element, 'textshadow');
    };

    var setFilterStyle = function(element, value) {
        editorMap.set(element, 'bggradientie', 'filter:' + value + ';');
    };
    
    var getFilterStyle = function(element) {
        return editorMap.get(element, 'bggradientie');
    };
    
    var removeFilterStyle = function(element) {
        editorMap.remove(element, 'bggradientie');
    };

    var setFontSizeStyle = function(element, value) {
        editorMap.set(element, 'fontsize', 'font-size:' + value + ';');
    };
    
    var getFontSizeStyle = function(element) {
        return editorMap.get(element, 'fontsize');
    };
    
    var removeFontSizeStyle = function(element) {
        editorMap.remove(element, 'fontsize');
    };
    
    var setLineHeightStyle = function(element, value) {
        editorMap.set(element, 'lineheight', 'line-height:' + value + ';');
    };
    
    var getLineHeightStyle = function(element) {
        return editorMap.get(element, 'lineheight');
    };
    
    var removeLineHeightStyle = function(element) {
        editorMap.remove(element, 'lineheight');
    };

    var setPaddingStyle = function(element, value) {
        editorMap.set(element, 'padding', 'padding:' + value + ';');
    };
    
    var getPaddingStyle = function(element) {
        return editorMap.get(element, 'padding');
    };
    
    var removePaddingStyle = function(element) {
        editorMap.remove(element, 'padding');
    };

    var setWidthStyle = function(element, value) {
        editorMap.set(element, 'width', 'width:' + value + ';');
    };
    
    var getWidthStyle = function(element) {
        return editorMap.get(element, 'width');
    };
    
    var removeWidthStyle = function(element) {
        editorMap.remove(element, 'width');
    };
    
    var setHeightStyle = function(element, value) {
        editorMap.set(element, 'height', 'height:' + value + ';');
    };
    
    var getHeightStyle = function(element) {
        return editorMap.get(element, 'height');
    };
    
    var removeHeightStyle = function(element) {
        editorMap.remove(element, 'height');
    };

    var setMaxWidthStyle = function(element, value) {
        editorMap.set(element, 'maxwidth', 'max-width:' + value + ';');
    };
    
    var getMaxWidthStyle = function(element) {
        return editorMap.get(element, 'maxwidth');
    };
    
    var removeMaxWidthStyle = function(element) {
        editorMap.remove(element, 'maxwidth');
    };
    
    var setMaxHeightStyle = function(element, value) {
        editorMap.set(element, 'maxheight', 'max-height:' + value + ';');
    };
    
    var getMaxHeightStyle = function(element) {
        return editorMap.get(element, 'maxheight');
    };
    
    var removeMaxHeightStyle = function(element) {
        editorMap.remove(element, 'maxheight');
    };

    var setUnknownStyle = function(element, prop, value) {
        if (typeof(setUnknownStyle.i)=="undefined") setUnknownStyle.i=0;
        setUnknownStyle.i++;
        editorMap.set(element, prop+setUnknownStyle.i, prop+':'+value+';');
    };

    var setWideContainer = function(wide) {
        editorMap.media = mediaType.media1200;
        if (wide) {
            setWidthStyle(mediaStyle.container.selector, mediaStyle.container.width);
            setMaxWidthStyle(mediaStyle.container.selector, mediaStyle.container.maxWidth);
        } else {
            removeWidthStyle(mediaStyle.container.selector);
            removeMaxWidthStyle(mediaStyle.container.selector);
        }
        editorMap.media = null;
    };
    
    var isWideContainer = function() {
        editorMap.media = mediaType.media1200;
        var val = getWidthStyle(mediaStyle.container.selector);
        editorMap.media = null;
        if (val && val=='width:'+mediaStyle.container.width+';') return true;
        else return false;
    };
    
    var setWideCols = function(wide) {
        editorMap.media = mediaType.media768;
        if (wide) {
            setWidthStyle(mediaStyle.colSm2.selector, mediaStyle.colSm2.width);
            setWidthStyle(mediaStyle.colSm4.selector, mediaStyle.colSm4.width);
            setWidthStyle(mediaStyle.colSm8.selector, mediaStyle.colSm8.width);
        } else {
            removeWidthStyle(mediaStyle.colSm2.selector);
            removeWidthStyle(mediaStyle.colSm4.selector);
            removeWidthStyle(mediaStyle.colSm8.selector);
        }
        editorMap.media = null;
    };
    
    var isWideCols = function() {
        editorMap.media = mediaType.media768;
        var val = getWidthStyle(mediaStyle.colSm4.selector);
        editorMap.media = null;
        if (val && val=='width:'+mediaStyle.colSm4.width+';') return true;
        else return false;
    };

    var prepareCode = function(code) {
        code = code.replace(/([^{};,\r\n\t][\x20\t]*[\r\n])/g,'$1;');
        code = code.replace(/^\s*(.+)\s*$/g,'$1');
        code = code.replace(/\s*([{};:,])\s*/g,'$1');
        code = code.replace(/([\(])\s*/g,'$1');
        code = code.replace(/\s*([\)])/g,'$1');
        code = code.replace(/[\x20]+/g,' ');
        code = code.replace(/[;]+/g,';');
        return code;
    };
    
    var extractMediaContent = function(code, rec) {
        if (typeof(rec)=="undefined") rec = 0;
        rec++;
        if (extractMediaContent.rec>9) return code;
        var m = null, m1 = null, m2 = null, s = null, s1 = null, s2 = null;
        m = code.indexOf('@media');
        if (m<0) return code;
        var i = 0, iter = 0;
        s = m;
        do {
            iter++;
            if (iter>99) break;
            s1 = code.indexOf('{', s);
            s2 = code.indexOf('}', s);
            if (s1<0) s1 = code.length-1;
            if (s2<0) {
                if (m1!==null) {
                    m2 = m1;
                } else {
                    m1 = m2 = s1;
                }
                break;
            }
            if (m1===null) {
                if (s1<s2) {
                    m1 = s1;
                    s = s1 + 1;
                    continue;
                } else {
                    m1 = m2 = s2;
                    break;
                }
            }
            if (s1<s2) {
                i++;
                s = s1 + 1;
                continue;
            } else {
                if (i===0) {
                    m2 = s2;
                    break;
                } else {
                    i--;
                    s = s2 + 1;
                    continue;
                }
            }
        } while (true);
        if (m1===null || m2===null) return code;
        var media = code.substr(m,m1-m);
        var content = code.substr(m1+1,m2-m1-1);
        var prop = prepareCode(content);
        parseMedia(media, content);
        code = code.substr(0, m) + code.substr(m2+1);
        return extractMediaContent(code, rec);
    };
    
    var parseMedia = function(media, content) {
        editorMap.media = media;
        parseStyles(content);
        editorMap.media = null;
    };
    
    var parseStyles = function(code) {
        var regexp = new RegExp('([^{]+)[{]([^}]*)[}]', 'gi');
        var element, prop, value, m, m2, m3, m4, regexp2, regexp3;
        while (m = regexp.exec(code)) {
            element = m[1];
            regexp2 = new RegExp('([a-z\-]+)[:]([^;]+)[;]', 'gi');
            while(m2 = regexp2.exec(m[2])) {
                prop = m2[1];
                value = m2[2];
                if (prop == 'background-image' || prop == 'background') {
                    regexp3 = new RegExp('^([a-z\-]+)[\(](.+)[\)][\x20]*$','gi');
                    m3 = regexp3.exec(value);
                    if (!m3) {
                        setBackgroundStyle(element, value, true);
                    } else if (m3[1] == 'url') {
                        setBackgroundImageStyle(element, m3[2], true);
                    } else if (m3[1] == 'linear-gradient' && (m4 = parseGradient(m3[2]))) {
                        setBackgroundGradientStyle(element, m4[1], m4[2], true);
                    } else if (m3[1] != '-webkit-linear-gradient' && 
                                m3[1] != '-webkit-gradient' && 
                                m3[1] != '-o-linear-gradient'
                        ) {
                        setBackgroundStyle(element, value, true);
                    }
                } else if (prop == 'background-color') {
                    setBackgroundColorStyle(element, value, true);
                } else if (prop == 'color') {
                    setColorStyle(element, value);
                } else if (prop == 'border-color') {
                    setBorderColorStyle(element, value);
                } else if (prop == 'border-top-color') {
                    setBorderTopColorStyle(element, value);
                } else if (prop == 'border-bottom-color') {
                    setBorderBottomColorStyle(element, value);
                } else if (prop == 'border-left-color') {
                    setBorderLeftColorStyle(element, value);
                } else if (prop == 'border-right-color') {
                    setBorderRightColorStyle(element, value);
                } else if (prop == 'border-top') {
                    setBorderTopStyle(element, value);
                } else if (prop == 'border-bottom') {
                    setBorderBottomStyle(element, value);
                } else if (prop == 'border-left') {
                    setBorderLeftStyle(element, value);
                } else if (prop == 'border-right') {
                    setBorderRightStyle(element, value);
                } else if (prop == 'border') {
                    setBorderStyle(element, value);
                } else if (prop == 'box-shadow') {
                    setBoxShadowStyle(element, value);
                } else if (prop == 'text-shadow') {
                    setTextShadowStyle(element, value);
                } else if (prop == 'filter') {
                    setFilterStyle(element, value);
                } else if (prop == 'font-size') {
                    setFontSizeStyle(element, value);
                } else if (prop == 'line-height') {
                    setLineHeightStyle(element, value);
                } else if (prop == 'padding') {
                    setPaddingStyle(element, value);
                } else if (prop == 'width') {
                    setWidthStyle(element, value);
                } else if (prop == 'height') {
                    setHeightStyle(element, value);
                } else if (prop == 'max-width') {
                    setMaxWidthStyle(element, value);
                } else if (prop == 'max-height') {
                    setMaxHeightStyle(element, value);
                } else {
                    setUnknownStyle(element, prop, value);
                }
            }
        }
    };
    
    var extractGradient = function(element) {
        var gradient = null;
        var value = $(element).css('backgroundImage');
        value = value.replace(/\s*([,])\s*/g,'$1');
        if (value != 'none') {
            regexp = new RegExp('([a-z\-]+)[\(](.+)[\)]','gi');
            if ((m = regexp.exec(value)) && m[1] == 'linear-gradient') {
                m2 = parseGradient(m[2]);
                if (m2) {
                    gradient = [m2[1], m2[2]];
                }
            }
        }
        if (!gradient) {
            var color = $(element).css('backgroundColor');
            gradient = [color, color];
        }
        return gradient;
    };
    
    var parseGradient = function(value) {
        var regexp = new RegExp('(?:[^,]+[,])?(rgb[a]?[\(][^\)]+[\)])[^,]*[,](rgb[a]?[\(][^\)]+[\)])','gi');
        var m = regexp.exec(value);
        if (!m) {
            regexp = new RegExp('(?:[^,]+[,])?(rgb[a]?[\(][^\)]+[\)])[^,]*[,]([a-z0-9#]+)','gi');
            m = regexp.exec(value);
        }
        if (!m) {
            regexp = new RegExp('(?:[^,]+[,])?([a-z0-9#]+)[^,]*[,](rgb[a]?[\(][^\)]+[\)])','gi');
            m = regexp.exec(value);
        }
        if (!m) {
            regexp = new RegExp('(?:[^,]+[,])?([a-z0-9#]+)[^,]*[,]([a-z0-9#]+)','gi');
            m = regexp.exec(value);
        }
        return m;
    };
    
    var digitToHex = function(c) {
        var hex = c.toString(16);
        return hex.length == 1 ? "0" + hex : hex;
    };

    var rgbaToHexIE = function(color) {
        if (color == 'transparent') return '#00000000';
        if (color.indexOf('#')==0) return color;
        var regexp = new RegExp('rgb(?:[a])?[\(]([^,]+)[,]([^,]+)[,]([^,\)]+)(?:[,]([^\)]+))?[\)]', 'i');
        var m = regexp.exec(color);
        if (!m) return color;
        var hex = '#';
        if (typeof(m[4])!="undefined") hex += digitToHex(Math.floor(parseFloat(m[4])*255));
        hex += digitToHex(parseInt(m[1])) + digitToHex(parseInt(m[2])) + digitToHex(parseInt(m[3]));
        return hex;
    };
    
    var hexColor = function(color) {
        if (color == 'transparent' || color.indexOf('#')==0) return color;
        var regexp = new RegExp('rgb(?:[a])?[\(]([^,]+)[,]([^,]+)[,]([^,\)]+)(?:[,]([^\)]+))?[\)]', 'i');
        var m = regexp.exec(color);
        if (!m) return color;
        var hex = '#';
        hex += digitToHex(parseInt(m[1])) + digitToHex(parseInt(m[2])) + digitToHex(parseInt(m[3]));
        return hex;
    };
    
    var editorMap = {
        map: [],
        styles: {},
        indexes: {},
        media: null,
        init: function() {
            this.map = [];
            this.styles = {};
            this.indexes = {};
            this.media = null;
        },
        set: function(element, prop, val) {
            if (this.media) {
                return mediaMap.set(this.media, element, prop, val);
            }
            if (typeof(this.styles[element])=="undefined") this.styles[element] = {};
            var i = 0;
            if (typeof(this.indexes[element])!="undefined") {
                i = this.indexes[element] + 1;
            }
            if ($.inArray(element, this.map)<0) this.map.push(element);
            this.styles[element][prop] = { index: i, value: val };
            this.indexes[element] = i;
        },
        get: function(element, prop) {
            if (this.media) {
                return mediaMap.get(this.media, element, prop);
            }
            if (typeof(this.styles[element])=="undefined") return null;
            if (typeof(this.styles[element][prop])=="undefined" || this.styles[element][prop]===null) return null;
            return this.styles[element][prop].value;
        },
        remove: function(element, prop) {
            if (this.media) {
                return mediaMap.remove(this.media, element, prop);
            }
            if (typeof(this.styles[element])=="undefined") return;
            if (typeof(this.styles[element][prop])=="undefined") return;
            this.styles[element][prop] = null;
        },
        getContent: function(pretty) {
            if (typeof(pretty)=="undefined") pretty = false;
            var content = '';
            for (var i=0; i<this.map.length; i++) {
                var element = this.map[i];
                var props = [];
                for (var prop in this.styles[element]) {
                    if (this.styles[element][prop]===null) continue;
                    props.push(this.styles[element][prop]);
                }
                if (props.length==0) continue;
                props.sort(function(a, b){
                    return a.index - b.index;
                });
                if (!pretty) {
                    content += element + '{' + $.map(props, function(value, index) { return value.value; }).join('') + '}';
                } else {
                    content += element.split(',').join(','+"\r\n") + ' {' + "\r\n\t" + $.map(props, function(value, index) { return value.value; }).join("\r\n\t") + "\r\n" + '}' + "\r\n";
                }
            }
            return content;
        }
    };
    
    var mediaMap = {
        map: [],
        eMap: {},
        styles: {},
        indexes: {},
        init: function() {
            this.map = [];
            this.eMap = {};
            this.styles = {};
            this.indexes = {};
        },
        set: function(media, element, prop, val) {
            if (typeof(this.styles[media])=="undefined") this.styles[media] = {};
            if (typeof(this.styles[media][element])=="undefined") this.styles[media][element] = {};
            if (typeof(this.indexes[media])=="undefined") this.indexes[media] = {};
            if (typeof(this.eMap[media])=="undefined") this.eMap[media] = [];
            var i = 0;
            if (typeof(this.indexes[media][element])!="undefined") {
                i = this.indexes[media][element] + 1;
            }
            if ($.inArray(media, this.map)<0) this.map.push(media);
            if ($.inArray(element, this.eMap[media])<0) this.eMap[media].push(element);
            this.styles[media][element][prop] = { index: i, value: val };
            this.indexes[media][element] = i;
        },
        get: function(media, element, prop) {
            if (typeof(this.styles[media])=="undefined") return null;
            if (typeof(this.styles[media][element])=="undefined") return null;
            if (typeof(this.styles[media][element][prop])=="undefined" || this.styles[media][element][prop]===null) return null;
            return this.styles[media][element][prop].value;
        },
        remove: function(media, element, prop) {
            if (typeof(this.styles[media])=="undefined") return;
            if (typeof(this.styles[media][element])=="undefined") return;
            if (typeof(this.styles[media][element][prop])=="undefined") return;
            this.styles[media][element][prop] = null;
        },
        getContent: function(pretty) {
            if (typeof(pretty)=="undefined") pretty = false;
            var content = '';
            for (var i=0; i<this.map.length; i++) {
                var m_content = [];
                var media = this.map[i];
                for (var y=0; y<this.eMap[media].length; y++) {
                    var element = this.eMap[media][y];
                    var props = [];
                    for (var prop in this.styles[media][element]) {
                        if (this.styles[media][element][prop]===null) continue;
                        props.push(this.styles[media][element][prop]);
                    }
                    if (props.length==0) continue;
                    props.sort(function(a, b){
                        return a.index - b.index;
                    });
                    if (!pretty) {
                        m_content.push(element + '{' + $.map(props, function(value, index) { return value.value; }).join('') + '}');
                    } else {
                        m_content.push(element.split(',').join(','+"\r\n\t") + ' {' + "\r\n\t\t" + $.map(props, function(value, index) { return value.value; }).join("\r\n\t\t") + "\r\n\t" + '}');
                    }
                }
                if (m_content.length===0) continue;
                if (!pretty) {
                    content += media + '{' + m_content.join('') + '}';
                } else {
                    content += media + ' {' + "\r\n\t" + m_content.join("\r\n\t") + "\r\n" + '}' + "\r\n";
                }
            }
            return content;
        }
    };

    $(window).keydown(function(e){
        if (e.keyCode == 83 && e.ctrlKey) {
            e.preventDefault();
            e.stopPropagation();
            parent.jQuery('body', parent.document).trigger('designerEditorSave');
        }
    });
    
    window.editorInit = function(content) {
        editorMap.init();
        mediaMap.init();
        if (typeof(content)=="undefined" && $('head style').length>0) {
            content = $('head style').text();
        }
        if (typeof(content)=="undefined") return;
        content = prepareCode(content);
        content = extractMediaContent(content);
        parseStyles(content);
    };
    
    window.editorStyle = function() {
        var content = editorMap.getContent();
        content += mediaMap.getContent();
        return content;
    };
    window.editorContent = function() {
        var content = editorMap.getContent(true);
        content += mediaMap.getContent(true);
        return content;
    };
    parent.designerEditorWindow = window;
})(jQuery);