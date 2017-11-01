(function($){
    $(document).ready(function(){
        $('body').append('<div class="designer_overlay"></div>');
        
        if ($('head style').length>0) {
            parseStyles($('head style').text());
        }
        
        var colorpicker_size = 22;
        var colorpicker_wnd_size = 280;
        var gradientpicker_wnd_size = 280;
        var container_x = $('#content').offset().left;
        
        var designer_positions = [];
        $(window).resize(function(){
            for (var name in designer_positions) {
                designer_positions[name].call();
            }
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
                setBackgroundColorStyle('body', color);
                setBackgroundStyle('#main-container-wrapper', 'none');
                setBackgroundStyle('#main-container', 'none');
            }, 'left');
            
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
                $('body').css('backgroundImage', 'linear-gradient(to bottom,' + color1 + ',' + color2 + ')');
                $('#main-container-wrapper').css('background', 'none');
                $('#main-container').css('background', 'none');
                setBackgroundGradientStyle('body', color1, color2);
                setBackgroundStyle('#main-container-wrapper', 'none');
                setBackgroundStyle('#main-container', 'none');
                setBackgroundColorStyle('body', color1, true);
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
                var background = bg_color + ' url(' + url + ') no-repeat 50% 0%';
                $('body').css('background', background);
                $('#main-container-wrapper').css('background', 'none');
                $('#main-container').css('background', 'none');
                setBackgroundStyle('body', background);
                setBackgroundStyle('#main-container-wrapper', 'none');
                setBackgroundStyle('#main-container', 'none');
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
                setBackgroundColorStyle('header', color);
            });
            
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
                $('header').css('backgroundImage', 'linear-gradient(to bottom,' + color1 + ',' + color2 + ')');
                setBackgroundGradientStyle('header', color1, color2);
                setBackgroundColorStyle('header', color1, true);
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
                var background = bg_color + ' url(' + url + ') no-repeat 50% 0%';
                $('header').css('background', background);
                setBackgroundStyle('header', background);
            });
            
            // header logo color
            if ($('header #site-logo').length>0) {
                var logo_color = $('header #site-logo').css('color');
                $('body').append('<div class="designer_colorpicker" id="logo-designer-colorpicker" title="'+t('Logo color')+'"></div>');
                designer_positions['logo_color'] = function() {
                    if ($('header #site-logo').css('display')=='none' || $('header #site-logo').css('visibility')=='hidden') {
                        $('#logo-designer-colorpicker').hide();
                        return;
                    }
                    var logo_cx = $('header #site-logo').offset().left+.5*colorpicker_size;
                    var logo_cy = $('header #site-logo').offset().top+($('header #site-logo').outerHeight()-colorpicker_size)/2;
                    $('#logo-designer-colorpicker').css({'left':logo_cx,'top':logo_cy});
                    $('#logo-designer-colorpicker').show();
                };
                $('#logo-designer-colorpicker').tooltip();
                designer_colorpicker($('#logo-designer-colorpicker'), logo_color, function(color){
                    $('header #site-logo').css('color', color);
                    setColorStyle('#site-logo-wrapper a#site-logo:link,#site-logo-wrapper a#site-logo:visited', color);
                }, 'left');
            }
            
            // header slogan color
            if ($('header #site-slogan').length>0) {
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
                    setBackgroundColorStyle('ul#language-switcher li a:hover,ul#language-switcher li a.active', color);
                });
            }

            // header user menu
            if ($('header ul#user-menu').length>0) {
                // header user menu color
                var usermenu_color = $('header ul#user-menu li a').css('color');
                $('body').append('<div class="designer_colorpicker" id="usermenu-color-designer-colorpicker" title="'+t('User menu color')+'"></div>');
                designer_positions['usermenu_color'] = function() {
                    if ($('header ul#user-menu').css('display')=='none' || $('header ul#user-menu').css('visibility')=='hidden') {
                        $('#usermenu-color-designer-colorpicker').hide();
                        return;
                    }
                    var usermenu_cx = $('header ul#user-menu').offset().left;
                    var usermenu_cy = $('header ul#user-menu').offset().top+$('header ul#user-menu').outerHeight()+.5*colorpicker_size;
                    $('#usermenu-color-designer-colorpicker').css({'left':usermenu_cx,'top':usermenu_cy});
                    $('#usermenu-color-designer-colorpicker').show();
                };
                $('#usermenu-color-designer-colorpicker').tooltip();
                designer_colorpicker($('#usermenu-color-designer-colorpicker'), usermenu_color, function(color){
                    $('header ul#user-menu li a').css('color', color);
                    setColorStyle('ul#user-menu li.menu-item,ul#user-menu li.menu-item a.menu-link:link,ul#user-menu li.menu-item a.menu-link:visited,ul#user-menu li.menu-item a.menu-link:hover,ul#user-menu li.menu-item a.menu-link.active,ul#user-menu ul.dropdown-menu li a,ul#user-menu ul.dropdown-menu li a:hover,ul#user-menu ul.dropdown-menu li a:focus', color);
                });
                
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
                    $('header ul#user-menu').css('backgroundImage', 'linear-gradient(to bottom,' + color1 + ',' + color2 + ')');
                    $('header ul#user-menu').css('border-color', color1);
                    setBackgroundGradientStyle('header ul#user-menu', color1, color2);
                    setBackgroundStyle('header ul#user-menu li.menu-item.open,header ul#user-menu ul.dropdown-menu', color2);
                    setBackgroundStyle('ul#user-menu ul.dropdown-menu li a:hover,ul#user-menu ul.dropdown-menu li a:focus,ul#user-menu ul.dropdown-menu .divider', color1);
                    setBackgroundColorStyle('header ul#user-menu', color1, true);
                    setBorderColorStyle('header ul#user-menu', color1);
                });
            }

            // header top menu
            if ($('header #top-menu-wrapper nav').length>0) {
                // header top menu color
                var topmenu_color = $('header #top-menu-wrapper nav a').css('color');
                $('body').append('<div class="designer_colorpicker" id="topmenu-color-designer-colorpicker" title="'+t('Top menu color')+'"></div>');
                designer_positions['topmenu_color'] = function() {
                    if ($('header #top-menu-wrapper').css('display')=='none' || $('header #top-menu-wrapper').css('visibility')=='hidden') {
                        $('#topmenu-color-designer-colorpicker').hide();
                        return;
                    }
                    var topmenu_cx = $('header #top-menu-wrapper nav').offset().left+($('header #top-menu-wrapper nav').outerWidth()-colorpicker_size)/2-.75*colorpicker_size;
                    var topmenu_cy = $('header #top-menu-wrapper nav').offset().top+($('header #top-menu-wrapper nav').outerHeight()-colorpicker_size)/2;
                    $('#topmenu-color-designer-colorpicker').css({'left':topmenu_cx,'top':topmenu_cy});
                    $('#toprmenu-color-designer-colorpicker').show();
                };
                $('#topmenu-color-designer-colorpicker').tooltip();
                designer_colorpicker($('#topmenu-color-designer-colorpicker'), topmenu_color, function(color){
                    $('header #top-menu-wrapper nav a, header #top-menu-wrapper .form-control, header #top-menu-wrapper .form-control::placeholder, header #top-menu-wrapper .btn-default').css('color', color);
                    setColorStyle('header #top-menu-wrapper nav a:link,header #top-menu-wrapper nav a:visited,header #top-menu-wrapper .navbar-default .navbar-nav .active a,header #top-menu-wrapper .navbar-default .navbar-nav .open a,header #top-menu-wrapper .form-control,header #top-menu-wrapper .btn-default,header .navbar-default .navbar-toggle', color);
                    setColorStyle('header #top-menu-wrapper .form-control::placeholder', color);
                    setBackgroundColorStyle('header #top-menu-wrapper .navbar-default .navbar-toggle .icon-bar', color);
                });
                
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
                    $('header #top-menu-wrapper nav').css('backgroundImage', 'linear-gradient(to bottom,' + color1 + ',' + color2 + ')');
                    $('header #top-menu-wrapper .navbar-default .navbar-nav .active, header #top-menu-wrapper .navbar-default .navbar-nav .active a, header #top-menu-wrapper .form-control, header #top-menu-wrapper .btn-default').css('background', color2);
                    $('header #top-menu-wrapper .navbar-default, header #top-menu-wrapper .form-control, header #top-menu-wrapper .btn-default').css('border-color', color1);
                    $('header #top-menu-wrapper .btn-default').css('text-shadow', '0 1px 0 '+color2);
                    setBackgroundGradientStyle('header #top-menu-wrapper nav.navbar-default', color1, color2);
                    setBackgroundStyle('header #top-menu-wrapper .navbar-default .navbar-nav .open,header #top-menu-wrapper .navbar-default .navbar-nav .active,header #top-menu-wrapper .navbar-default .navbar-nav .active a,header #top-menu-wrapper .navbar-default .navbar-nav .open a,header #top-menu-wrapper nav ul.dropdown-menu,header #top-menu-wrapper nav .form-control,header #top-menu-wrapper nav .btn-default', color2);
                    setBackgroundStyle('#top-menu-wrapper ul.dropdown-menu li a:hover,#top-menu-wrapper ul.dropdown-menu li a:focus,#top-menu-wrapper .navbar-default .navbar-nav .open ul.dropdown-menu li a:hover', color1);
                    setBackgroundColorStyle('header #top-menu-wrapper nav.navbar-default,header #top-menu-wrapper .navbar-default .navbar-toggle:focus,header #top-menu-wrapper .navbar-default .navbar-toggle:hover', color1, true);
                    setBorderColorStyle('header #top-menu-wrapper nav.navbar-default,header #top-menu-wrapper nav .form-control,header #top-menu-wrapper nav .btn-default,header .navbar-default .navbar-toggle,header .navbar-default .navbar-collapse,.navbar-default .navbar-form', color1);
                    setTextShadowStyle('header #top-menu-wrapper .btn-default', '0 1px 0 '+color2);
                    setFilterStyle('#top-menu-wrapper .navbar-default .navbar-nav .active a,#top-menu-wrapper .navbar-default .navbar-nav .open a,#top-menu-wrapper nav .btn-default', 'none');
                });
            }
        }
        
        $(window).trigger('resize');
    });
    
    var designer_colorpicker = function(element, init_color, callback, position) {
        if (typeof(position)=="undefined") position = 'right';
        $(element).colorpicker({
            customClass: 'colorpicker-2x',
            sliders: { 
                saturation: { maxLeft: 200, maxTop: 200 },
                hue: { maxTop: 200 },
                alpha: { maxTop: 200 }
            },
            color: init_color,
            align: position
        }).on('changeColor', zira_bind($(element), function(e) {
            var color = e.color.toString('rgba');
            if (typeof(callback)!="undefined") {
                callback.call(this, color);
            }
        }));
    };
    
    var designer_gradientpicker = function(element, child, init_color1, init_color2, callback, position) {
        if (typeof(position)=="undefined") position = 'right';
        $(element).colorpicker({
            customClass: 'colorpicker-2x',
            sliders: { 
                saturation: { maxLeft: 200, maxTop: 200 },
                hue: { maxTop: 200 },
                alpha: { maxTop: 200 }
            },
            color: init_color1,
            align: position
        }).on('showPicker', zira_bind($(child), function() {
            $(this).colorpicker('show');    
        })).on('changeColor', zira_bind($(child), function(e) {
            var color1 = e.color.toString('rgba');
            var color2 = $(this).data('colorpicker').color.toString('rgba');
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
            align: position
        }).on('changeColor', zira_bind($(element), function(e) {
            var color2 = e.color.toString('rgba');
            var color1 = $(this).data('colorpicker').color.toString('rgba');
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
    
    var setBackgroundColorStyle = function(element, value, addOnly) {
        if (typeof (addOnly) == "undefined") addOnly = false;
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bgcolor']='background-color:' + value + ';';
        if (!addOnly) {
            removeBackgroundGradientStyle(element);
            removeBackgroundImageStyle(element);
            removeBackgroundStyle(element, value);
        }
    };
    
    var getBackgroundColorStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        if (typeof(window.editorStyles[element]['bgcolor'])=="undefined") return null;
        return window.editorStyles[element]['bgcolor'];
    };
    
    var removeBackgroundColorStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bgcolor']=null;
    };
    
    var setBackgroundGradientStyle = function(element, value1, value2, addOnly) {
        if (typeof (addOnly) == "undefined") addOnly = false;
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bggradientwebkit1']='-webkit-linear-gradient(top,' + value1 + ' 0,' + value2 + ' 100%);';
        window.editorStyles[element]['bggradientwebkit2']='-webkit-gradient(linear,left top,left bottom,from(' + value1 + '),to(' + value2 + '));';
        window.editorStyles[element]['bggradientopera']='-o-linear-gradient(top,' + value1 + ' 0,' + value2 + ' 100%);';
        window.editorStyles[element]['bggradient']='background-image:linear-gradient(to bottom,' + value1 + ',' + value2 + ');';
        window.editorStyles[element]['bggradientie']='filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=' + rgbaToHex(value1) + ',endColorstr=' + rgbaToHex(value2) + ',GradientType=0);';
        if (!addOnly) {
            removeBackgroundColorStyle(element);
            removeBackgroundImageStyle(element);
            removeBackgroundStyle(element);
        }
    };
    
    var getBackgroundGradientStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        if (typeof(window.editorStyles[element]['bggradient'])=="undefined") return null;
        return window.editorStyles[element]['bggradient'];
    };
    
    var removeBackgroundGradientStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bggradientwebkit1']=null;
        window.editorStyles[element]['bggradientwebkit2']=null;
        window.editorStyles[element]['bggradientopera']=null;
        window.editorStyles[element]['bggradient']=null;
        window.editorStyles[element]['bggradientie']=null;
    };
    
    var setBackgroundImageStyle = function(element, value, addOnly) {
        if (typeof (addOnly) == "undefined") addOnly = false;
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bgimage']='background-image:url(' + value + ');';
        if (!addOnly) {
            removeBackgroundGradientStyle(element);
            removeBackgroundStyle(element);
        }
    };
    
    var getBackgroundImageStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        if (typeof(window.editorStyles[element]['bgimage'])=="undefined") return null;
        return window.editorStyles[element]['bgimage'];
    };
    
    var removeBackgroundImageStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bgimage']=null;
    };
    
    var setBackgroundStyle = function(element, value, addOnly) {
        if (typeof (addOnly) == "undefined") addOnly = false;
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bg']='background:' + value + ';';
        if (!addOnly) {
            removeBackgroundColorStyle(element);
            removeBackgroundGradientStyle(element);
            removeBackgroundImageStyle(element);
        }
    };
    
    var getBackgroundStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        if (typeof(window.editorStyles[element]['bg'])=="undefined") return null;
        return window.editorStyles[element]['bg'];
    };
    
    var removeBackgroundStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bg']=null;
    };

    var setColorStyle = function(element, value) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['color']='color:' + value + ';';
    };
    
    var getColorStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        if (typeof(window.editorStyles[element]['color'])=="undefined") return null;
        return window.editorStyles[element]['color'];
    };
    
    var removeColorStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['color']=null;
    };

    var setBorderColorStyle = function(element, value) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bordercolor']='border-color:' + value + ';';
    };
    
    var getBorderColorStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        if (typeof(window.editorStyles[element]['bordercolor'])=="undefined") return null;
        return window.editorStyles[element]['bordercolor'];
    };
    
    var removeBorderColorStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bordercolor']=null;
    };

    var setBorderStyle = function(element, value) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['border']='border:' + value + ';';
    };
    
    var getBorderStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        if (typeof(window.editorStyles[element]['border'])=="undefined") return null;
        return window.editorStyles[element]['border'];
    };
    
    var removeBorderStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['border']=null;
    };
    
    var setBoxShadowStyle = function(element, value) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['boxshadow']='box-shadow:' + value + ';';
    };
    
    var getBoxShadowStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        if (typeof(window.editorStyles[element]['boxshadow'])=="undefined") return null;
        return window.editorStyles[element]['boxshadow'];
    };
    
    var removeBoxShadowStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['boxshadow']=null;
    };
    
    var setTextShadowStyle = function(element, value) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['textshadow']='text-shadow:' + value + ';';
    };
    
    var getTextShadowStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        if (typeof(window.editorStyles[element]['textshadow'])=="undefined") return null;
        return window.editorStyles[element]['textshadow'];
    };
    
    var removeTextShadowStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['textshadow']=null;
    };

    var setFilterStyle = function(element, value) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bggradientie']='filter:' + value + ';';
    };
    
    var getFilterStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        if (typeof(window.editorStyles[element]['bggradientie'])=="undefined") return null;
        return window.editorStyles[element]['bggradientie'];
    };
    
    var removeFilterStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bggradientie']=null;
    };
    
    var parseStyles = function(code) {
        code = code.replace(/\s*([{};:,])\s*/g,'$1');
        var regexp = new RegExp('([^{]+)[{]([^}]+)[}]', 'gi');
        var m, m2, m3, m4, element, regexp2, prop, value, regexp3, regexp4;
        while (m = regexp.exec(code)) {
            element = m[1];
            regexp2 = new RegExp('([a-z\-]+)[:]([^;]+)[;]', 'gi');
            while(m2 = regexp2.exec(m[2])) {
                prop = m2[1];
                value = m2[2];
                if (prop == 'background-color') {
                    setBackgroundColorStyle(element, value, true);
                } else if (prop == 'background-image') {
                    regexp3 = new RegExp('([a-z\-]+)[\(](.+)[\)]','gi');
                    if (m3 = regexp3.exec(value)) {
                        if (m3[1] == 'url') {
                            setBackgroundImageStyle(element, m3[2], true);
                        } else if (m3[1] == 'linear-gradient') {
                            regexp4 = new RegExp('[^,]+[,](rgb[a]?[\(][^\)]+[\)])[^,]*[,](rgb[a]?[\(][^\)]+[\)])','gi');
                            m4 = regexp4.exec(m3[2]);
                            if (!m4) {
                                regexp4 = new RegExp('[^,]+[,](rgb[a]?[\(][^\)]+[\)])[^,]*[,]([a-z0-9#]+)','gi');
                                m4 = regexp4.exec(m3[2]);
                            }
                            if (!m4) {
                                regexp4 = new RegExp('[^,]+[,]([a-z0-9#]+)[^,]*[,](rgb[a]?[\(][^\)]+[\)])','gi');
                                m4 = regexp4.exec(m3[2]);
                            }
                            if (!m4) {
                                regexp4 = new RegExp('[^,]+[,]([a-z0-9#]+)[^,]*[,]([a-z0-9#]+)','gi');
                                m4 = regexp4.exec(m3[2]);
                            }
                            if (m4) {
                                setBackgroundGradientStyle(element, m4[1], m4[2], true);
                            }
                        }
                    }
                } else if (prop == 'background') {
                    setBackgroundStyle(element, value, true);
                } else if (prop == 'color') {
                    setColorStyle(element, value);
                } else if (prop == 'border-color') {
                    setBorderColorStyle(element, value);
                } else if (prop == 'border') {
                    setBorderStyle(element, value);
                } else if (prop == 'box-shadow') {
                    setBoxShadowStyle(element, value);
                } else if (prop == 'text-shadow') {
                    setTextShadowStyle(element, value);
                } else if (prop == 'filter') {
                    setFilterStyle(element, value);
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
            if (m = regexp.exec(value)) {
                if (m[1] == 'linear-gradient') {
                    var regexp2 = new RegExp('(?:[^,]+[,])?(rgb[a]?[\(][^\)]+[\)])[^,]*[,](rgb[a]?[\(][^\)]+[\)])','gi');
                    var m2 = regexp2.exec(m[2]);
                    if (!m2) {
                        regexp2 = new RegExp('(?:[^,]+[,])?(rgb[a]?[\(][^\)]+[\)])[^,]*[,]([a-z0-9#]+)','gi');
                        m2 = regexp2.exec(m[2]);
                    }
                    if (!m2) {
                        regexp2 = new RegExp('(?:[^,]+[,])?([a-z0-9#]+)[^,]*[,](rgb[a]?[\(][^\)]+[\)])','gi');
                        m2 = regexp2.exec(m[2]);
                    }
                    if (!m2) {
                        regexp2 = new RegExp('(?:[^,]+[,])?([a-z0-9#]+)[^,]*[,]([a-z0-9#]+)','gi');
                        m2 = regexp2.exec(m[2]);
                    }
                    if (m2) {
                        gradient = [m2[1], m2[2]];
                    }
                }
            }
        }
        if (!gradient) {
            var color = $(element).css('backgroundColor');
            gradient = [color, color];
        }
        return gradient;
    };
    
    var digitToHex = function(c) {
        var hex = c.toString(16);
        return hex.length == 1 ? "0" + hex : hex;
    };

    var rgbaToHex = function(color) {
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
    
    $(window).keydown(function(e){
        if (e.keyCode == 83 && e.ctrlKey) {
            e.preventDefault();
            e.stopPropagation();
            parent.jQuery('body', parent.document).trigger('designerEditorSave');
        }
    });
    
    window.editorStyles = {};
    window.editorStyle = function() {
        var content = '';
        for (var prop in window.editorStyles) {
            content += prop + '{' + $.map(window.editorStyles[prop], function(value, index) { return value; }).join('') + '}';
        }
        return content;
    };
    window.editorContent = function() {
        var content = '';
        for (var prop in window.editorStyles) {
            content += prop + ' {' + "\r\n\t" + $.map(window.editorStyles[prop], function(value, index) { return value; }).join("\r\n\t") + "\r\n" + '}' + "\r\n";
        }
        return content;
    };
    parent.designerEditorWindow = window;
})(jQuery);