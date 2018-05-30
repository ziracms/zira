(function($){
    $(document).ready(function(){
        /**
         * modal dialogs
         */
        zira_modal_init();
        /**
         * forms
         */
        $('.container #content').on('click','.form-dropdown .dropdown-menu a', zira_init_form_dropdown);
        $('.container #content').on('change', '.form-file-button :file', zira_init_form_file_button);
        if ($('.form-alert').length>0) {
            var t = $('.form-alert').eq(0).offset().top;
            if (t>100) {
                t = t - 50;
                $('html, body').animate({'scrollTop': t}, 500);
            }
        }
        /**
         * grid
         */
        if ($('.container #content .grid-category-wrapper').length>0) {
            zira_init_grid.timer = window.setInterval(zira_init_grid, 500);
            $(window).load(function(){
                try {
                    window.clearInterval(zira_init_grid.timer);
                } catch(err) {}
                zira_init_grid();
            });
            $(window).resize(zira_init_grid);
        }
        /**
         * gallery
         */
        if ($('.container #content ul.gallery li').length>2) {
            zira_init_gallery();
            $(window).resize(zira_init_gallery);
        }
        /**
         * record rating
         */
        if ($('.container #content #rating').length>0) {
            zira_init_record_rating();
        }
        /**
         * more records
         */
        if ($('.container #content .list-view-more-wrapper').length>0) {
            zira_init_records_more();
        }
        /**
         * share buttons
         */
        if ($('.share-btn').length>0) {
            zira_init_share_buttons();
        }
        /**
         * comments
         */
        if ($('.container #content .comments').length>0) {
            zira_init_comments();
            zira_init_comments_reload();
        }
        /**
         * more comments
         */
        if ($('.container #content .comments-wrapper').length>0) {
            zira_init_comments_more();
        }
        /**
         * more search results
         */
        if ($('.container #content .search-results-view-more-wrapper').length>0) {
            zira_init_search_more();
        }
        /**
         * Black list user
         */
        if ($('.user-profile .user-black-list-link').length>0) {
            zira_init_user_black_list_btn();
        }
        /**
         * Scroll to top icon
         */
        if ($('a.scroll-top').length>0) {
            zira_init_scroll_to_top();
        }
        /**
         * Scroll to element
         */
        if ($('.scroll-down').length>0) {
            zira_init_scroll_down();
        }
        /**
         * Dropdowns
         */
        $('body').on('mouseover', 'nav ul li a, .dropdown ul li a', function(){
            if ($(this).parent('li').parent('ul').hasClass('dropdown-menu')) return;
            if ($(this).parent('li').hasClass('open')) return;
            var open = $(this).parent('li').parent('ul').children('li.open');
            if ($(open).length>0) {
                $(this).trigger('click');
            }
        });
        /**
         * jPlayer
         */
        if ($('.jplayer-video-wrapper .jp-jplayer').length) {
            $('.jplayer-video-wrapper .jp-jplayer').each(function(){
                $(window).resize(zira_bind(this, zira_resize_jplayer));
                $(this).bind(jQuery.jPlayer.event.ready, zira_bind(this, zira_resize_jplayer));
                $(this).bind(jQuery.jPlayer.event.play, zira_bind(this, zira_resize_jplayer));
            });
        }
        /**
         * Fixed menu
         */
        if (typeof(zira_scroll_effects_enabled)!="undefined" && 
            zira_scroll_effects_enabled && 
            $('#top-menu-wrapper').length>0 && 
            $('#dashpanel-container').length==0 && 
            $('#dashpanel-fixed-button').length==0 && 
            typeof(designer_style_theme)=="undefined"
        ) {
            var topMenuTop = $('#top-menu-wrapper').offset().top;
            $(window).scroll(function(){
                var top = $(window).scrollTop();
                if (top>topMenuTop) {
                    $('#top-menu-wrapper').addClass('fixed');
                } else {
                    $('#top-menu-wrapper').removeClass('fixed');
                }
            });
            $(window).trigger('scroll');
            $(document).ready(function(){
                if (window.location.hash.length==0) return;
                setTimeout(function(){
                    var scrollTop = $(document).scrollTop();
                    if (scrollTop>0 && $('#top-menu-wrapper').hasClass('fixed')) {
                        $('html, body').animate({'scrollTop':scrollTop-$('#top-menu-wrapper').height()},200);
                    }
                },100);
            });
        }
        /**
         * Animation loop
         */
        if (typeof(zira_scroll_effects_enabled)!="undefined" && 
            zira_scroll_effects_enabled && 
            typeof(requestAnimationFrame)!="undefined"
        ) {
            var zira_render_callbacks = [];
            var zira_render_started = false;
            var zira_request_render = function(callback) {
                zira_render_callbacks.push(callback);
                if (!zira_render_started) {
                    zira_render();
                    zira_render_started = true;
                }
            };
            var zira_render = function() {
                for (var i=0; i<zira_render_callbacks.length; i++) {
                    zira_render_callbacks[i].call();
                }
                requestAnimationFrame(zira_render);
            };
        }
        /**
         * Header parallax
         */
        if (typeof(zira_scroll_effects_enabled)!="undefined" && 
            zira_scroll_effects_enabled && 
            $('header').length>0 && 
            $('header').css('backgroundImage').indexOf('url(')==0 && 
            $('header').css('backgroundPosition')=='50% 0%' && 
            navigator.userAgent.toLowerCase().indexOf('msie')<0 && 
            typeof(window.orientation) == "undefined" && 
            typeof(designer_style_theme)=="undefined"
        ) {
            var zira_header_parallax = function(){
                var top = $(window).scrollTop();
                var height = headerImage.height;
                if (top>height) top = height;
                var percent = -.5*(top / height * 100);
                $('header').css('backgroundPosition', '50% '+percent+'%');
            };
            var headerImage = new Image();
            var headerImageSrc = $('header').css('backgroundImage').replace(/^url[\(]['"]?(.*?)['"]?[\)]/, '$1');
            headerImage.onload = function() {
                var headerHeight = $('header').height();
                if (headerImage.height<=headerHeight+100) return;
                if (typeof(requestAnimationFrame)!="undefined") {
                    zira_request_render(zira_header_parallax);
                } else {
                    $(window).scroll(zira_header_parallax);
                    $(window).trigger('scroll');
                }
            };
            headerImage.src = headerImageSrc;
        }
        /**
         * Body parallax
         */
        if (typeof(zira_scroll_effects_enabled)!="undefined" && 
            zira_scroll_effects_enabled && 
            $('body').css('backgroundImage').indexOf('url(')==0 && 
            $('body').css('backgroundPosition')=='50% 0%' && 
            navigator.userAgent.toLowerCase().indexOf('msie')<0 && 
            typeof(window.orientation) == "undefined" && 
            typeof(designer_style_theme)=="undefined"
        ) {
            var zira_body_parallax = function(){
                var bodyHeight = $('body').height();
                if (bodyImage.height>=bodyHeight-300) return;
                var top = $(window).scrollTop();
                var height = $(document).height();
                if (top>height) top = height;
                var percent = .5*(top / height * 100);
                $('body').css('backgroundPosition', '50% '+percent+'%');
            };
            var bodyImage = new Image();
            var bodyImageSrc = $('body').css('backgroundImage').replace(/^url[\(]['"]?(.*?)['"]?[\)]/, '$1');
            bodyImage.onload = function() {
                if (typeof(requestAnimationFrame)!="undefined") {
                    zira_request_render(zira_body_parallax);
                } else {
                    $(window).scroll(zira_body_parallax);
                    $(window).trigger('scroll');
                } 
            };
            bodyImage.src = bodyImageSrc;
        }
        /**
         * Sidebar parallax
         */
        if (false && // disabled
            typeof(zira_scroll_effects_enabled)!="undefined" && 
            zira_scroll_effects_enabled && 
            $('.sidebar').length==1 && 
            $('#content').length==1 && 
            $('#top-menu-wrapper').length>0 &&
            $('.sidebar').height()>$(window).height() && 
            navigator.userAgent.toLowerCase().indexOf('msie')<0 && 
            typeof(window.orientation) == "undefined" && 
            typeof(designer_style_theme)=="undefined"
        ) {
            var sidebar_h = parseInt($('.sidebar').height());
            var content_h = parseInt($('#content').height());
            var sidebar_init_margin = parseInt($('.sidebar').css('marginTop'));
            var sidebar_init_offset = $('.sidebar').offset().top;
            if (sidebar_h+sidebar_init_margin+100<content_h) {
                var zira_sidebar_parallax=function(){
                    var top = $(window).scrollTop();
                    sidebar_offset = sidebar_init_offset - $('#top-menu-wrapper').height();
                    if ($('.sidebar').css('float')!="left" || !$('#top-menu-wrapper').hasClass('fixed') || top<sidebar_offset) {
                        $('.sidebar').css('marginTop', sidebar_init_margin+'px');
                        return;
                    }
                    var offset = parseInt($('#content').height())-parseInt($('.sidebar').height())-sidebar_init_margin;
                    var height = parseInt($(document).height())-parseInt($(window).height())-sidebar_offset;
                    var margin = Math.round((top-sidebar_offset) / height * offset);
                    $('.sidebar').css('marginTop', margin+sidebar_init_margin+'px');
                };
                if (typeof(requestAnimationFrame)!="undefined") {
                    zira_request_render(zira_sidebar_parallax);
                } else {
                    $(window).scroll(zira_sidebar_parallax);
                    $(window).trigger('scroll');
                }
            }
        }
        /**
         * captcha
         */
        if ($('.captcha-refresh-btn').length>0) {
            zira_init_captcha();
        }
        /** 
         * reCaptcha 
         **/
        if ($('.g-recaptcha').length>0) {
            zira_load_recaptcha();
        }
        /**
         * User auth popup
         */
        if ($('.usermenu-popup').length>0) {
            zira_init_auth_popup();
        }
        /**
         * ajax forms
         */
        if ($('form.xhr-form').length>0) {
            zira_init_xhr_form();
        }
    });

    zira_resize_jplayer = function() {
        var jPlayer = $(this).data('jPlayer');
        if (typeof(jPlayer)=="undefined" || jPlayer.status.cssClass == 'jp-video-full') return;
        var w = $(this).width();
        var h = w * 9 / 16;
        $('#jplayer-video').css('height',h+'px');
        $('#jplayer-video').find('img,video,object').css('height',h+'px');
        $('#jplayer-container-video').find('.jp-video-play').css({'height':h+'px',marginTop:-h+'px'});
    };
    
    zira_init_form_dropdown = function(e){
        var value = $(this).attr('rel');
        var name = $(this).text();
        var id = $(this).parent('li').parent('ul').attr('aria-labelledby');
        var field = $(this).parent('li').parent('ul').attr('rel');
        $('#'+id).text(name);
        $('input[type=hidden][name='+field+']').val(value);
    };

    zira_init_form_file_button = function() {
        var input = $(this);
        var numFiles = input.get(0).files ? input.get(0).files.length : 1;
        var label = '';
        if (numFiles>1) {
            for(var i=0; i<input.get(0).files.length; i++) {
                if (label.length>0) label += ', ';
                label += input.get(0).files[i].name;
            }
        } else {
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        }
        var name = input.attr('id');
        $('#'+name+'-text').val(label);
    };

    zira_init_grid = function() {
        $('.container #content .grid-category-wrapper .list-item').removeClass('jsed').css('height','auto');

        $('.container #content .grid-category-wrapper').each(function(){
            var list = $(this);
            if ($(list).get(0).tagName.toLowerCase() != 'ul') list = $(list).find('ul.list');
            if (!$(list).length) return true;
            var items = $(list).children('.list-item');
            if ($(items).length<2) return;
            var prev = null;
            var co = 1;
            $(items).each(function(){
                if ($(this).hasClass('jsed')) return true;
                if (co%2==0) {
                    if (prev && $(prev).offset().top == $(this).offset().top) {
                        var h = Math.max($(prev).outerHeight(), $(this).outerHeight());
                        $(prev).addClass('jsed').css('height',h);
                        $(this).addClass('jsed').css('height',h);
                    }
                } else {
                    prev = $(this);
                }
                co++;
            });
        });
    };

    zira_init_gallery = function() {
        var gw = $('.container #content ul.gallery').parent().width();
        var gm = parseInt($('.container #content ul.gallery').css('marginRight'));
        var gp = parseInt($('.container #content ul.gallery').css('paddingRight'));
        var gb = parseInt($('.container #content ul.gallery').css('borderRightWidth'));
        var iw = parseInt($('.container #content ul.gallery li img').eq(0).attr('width'));
        var ih = parseInt($('.container #content ul.gallery li img').eq(0).attr('height'));
        var im = parseInt($('.container #content ul.gallery li a').eq(0).css('marginRight'));
        var ib = parseInt($('.container #content ul.gallery li a').eq(0).css('borderRightWidth'));
        var _w = gw - 2*(gm + gp + gb);
        var w = iw + 2*(im + ib);
        var co = Math.floor(_w / w);
        var rw = co*w;
        if (gw - rw < 2) return;
        var nw = (_w / (co+1)) - 2*(im + ib);
        var nh = (nw * ih / iw);
        $('.container #content ul.gallery').css('width', gw);
        $('.container #content ul.gallery li a img').css({
            'width': nw,
            'height': nh
        });
    };

    zira_init_record_rating = function() {
        $('.container #content #rating a.like').click(function(e){
            e.stopPropagation();
            e.preventDefault();
            if ($(this).hasClass('active')) return;
            zira_poll.call(this, function(response){
                if (typeof(response.rating)=="undefined") return;
                $(this).addClass('active');
                $(this).children('.rating-value').text(response.rating);
                $(this).parent().children('.share-wrapper').css('left',0);
            }, function(){
                zira_error(t('An error occurred'));
            });
        });
    };

    zira_init_share_buttons = function() {
        $('.share-btn').click(function(e){
            e.stopPropagation();
            e.preventDefault();
            var url = $(this).attr('href');
            if (url && url.length>0) {
                var w = 650;
                var h = 450;
                var l,t;
                try {
                    l = Math.floor((window.screen.width - w) / 2);
                    t = Math.floor((window.screen.height - h) / 2);
                } catch(err) {
                    l = 0;
                    t = 0;
                }
                window.open(url, 'shareWindow', 'width='+w+',height='+h+',left='+l+',top='+t+',scrollbars=yes,toolbar=no,menubar=no');
            }
        });
    };

    zira_poll = function(success_callback, error_callback) {
        var value = $(this).data('value');
        var id = $(this).data('id');
        var type = $(this).data('type');
        var token = $(this).data('token');
        var url = $(this).data('url');
        if (typeof(value)!="undefined" &&
            typeof(id)!="undefined" &&
            typeof(type)!="undefined" &&
            typeof(token)!="undefined" &&
            typeof(url)!="undefined"
        ) {
            $.post(url,{
                'value': value,
                'id': id,
                'type': type,
                'token': token
            }, zira_bind(this, function(response){
                if (!response) {
                    if (typeof(error_callback)!="undefined") {
                        error_callback.call(this);
                    }
                } else if (typeof(success_callback)!="undefined") {
                    success_callback.call(this, response);
                }
            }),'json');
        }
    };

    zira_init_records_more = function() {
        $('.container #content').on('click', '.list-view-more', function(e){
            e.stopPropagation();
            e.preventDefault();

            var url = $(this).data('url');
            var category_id = $(this).data('category');
            var last_id = $(this).data('last');
            var page = $(this).data('page');

            if (typeof(url)=="undefined" ||
                typeof(category_id)=="undefined" ||
                (typeof(last_id)=="undefined" && typeof(page)=="undefined")
            ) {
                return;
            }
            
            if (typeof page == "undefined") page = 0;

            $(this).attr('disabled','disabled');
            $(this).parent('.list-view-more-wrapper').append('<div class="zira-loader-wrapper"><span class="zira-loader glyphicon glyphicon-refresh"></span> '+t('Please wait')+'...</div>');

            $.post(url, {
                'category_id': category_id,
                'last_id': last_id,
                'page': page
            }, zira_bind(this, function(response){
                var r = new RegExp('<ul[^>]*>([\\s\\S]+)</ul>([\\s\\S]*?)$','g');
                var m = r.exec(response);
                if (m) {
                    $(this).parent('.list-view-more-wrapper').prev('ul').append(m[1]);
                    $(this).parent('.list-view-more-wrapper').replaceWith(m[2]);
                    zira_init_grid();
                    window.setTimeout(zira_init_grid, 500);
                }
            }),'html');
        });
        
        if (typeof(zira_scroll_effects_enabled)!="undefined" && zira_scroll_effects_enabled) {
            zira_init_records_scroll();
        }
    };
    
    zira_init_records_scroll = function() {
        zira_init_records_scroll.co = 0;
        if ($('.container #content .list-view-more').length==0) return;
        $(window).unbind('scroll', zira_init_records_scroll).scroll(function(e){
            if ($('.container #content .list-view-more').length==0 ||
                zira_init_records_scroll.co >= 5
            ) {
                $(window).unbind('scroll', zira_init_records_scroll);
                return;
            }
            if ($('.container #content .list-view-more').get(0).disabled) return;
            var win_t = $(window).scrollTop();
            var btn_t = $('.container #content .list-view-more').offset().top;
            var win_h = $(window).height();
            var delta = 50;
            if (win_t+win_h+delta>btn_t) {
                zira_init_records_more.lock = true;
                $('.container #content .list-view-more').trigger('click');
                zira_init_records_scroll.co++;
            }
        });
    };

    zira_init_comments = function() {
        $('.container #content').on('click', '.comment-reply-link', function(e){
            e.stopPropagation();
            e.preventDefault();

            var parent_id = $(this).data('parent');
            if (typeof(parent_id)=="undefined") {
                parent_id = $(this).data('comment');
            }
            if (typeof(parent_id)=="undefined") return;
            var reply_id = $(this).data('reply');
            if (typeof(reply_id)=="undefined") reply_id = parent_id;

            $('.container #content form#form-comment-form input#parent_id').val(parent_id);
            $('.container #content form#form-comment-form input#reply_id').val(reply_id);

            var preview = $(this).parents('.comments-item').find('.comment-text').text();
            var html = '<label class="col-sm-3 control-label">'+t('Reply to')+'</label>';
            html += '<div class="col-sm-9"><div class="form-control" style="white-space: nowrap;text-overflow: ellipsis;overflow: hidden;">';
            html += '<a href="javascript:void(0)" onclick="zira_reset_comments_form()" style="float:right;line-height:24px;"><span class="glyphicon glyphicon-remove-circle"></span></a>';
            html += '<span>'+preview+'</span>';

            html += '</div></div>';
            $('.container #content form#form-comment-form .comment-reply-preview').html(html);

            try {
                zira_scroll('#comments-form');
            } catch(err) {}
        });
        zira_init_comments_rating();
    };

    zira_init_comments_rating = function() {
        $('.container #content .comments a.comment-rating').unbind('click').click(function(e){
            e.stopPropagation();
            e.preventDefault();
            if ($(this).hasClass('active')) return;
            zira_poll.call(this, function(response){
                if (typeof(response.rating)=="undefined") return;
                $(this).addClass('active');
                $(this).children('.rating-value').text(response.rating);
            }, function(){
                zira_error(t('An error occurred'));
            });
        });
    };

    zira_reset_comments_form = function(real_reset) {
        var form = $('.container #content form#form-comment-form');
        if ($(form).length==0) return;
        if (typeof(real_reset)!="undefined" && real_reset) {
            $(form).get(0).reset();
        }
        $(form).find('input#parent_id').val('');
        $(form).find('input#reply_id').val('');
        $(form).find('.comment-reply-preview').html('');
    };

    zira_init_comments_more = function() {
        $('.container #content').on('click', '.comments-view-more', function(e){
            e.stopPropagation();
            e.preventDefault();

            var url = $(this).data('url');
            var record_id = $(this).data('record');
            var page = $(this).data('page');

            if (typeof(url)=="undefined" ||
                typeof(record_id)=="undefined" ||
                typeof(page)=="undefined"
            ) {
                return;
            }

            $(this).attr('disabled','disabled');
            $(this).parent('.comments-view-more-wrapper').append('<div class="zira-loader-wrapper"><span class="zira-loader glyphicon glyphicon-refresh"></span> '+t('Please wait')+'...</div>');

            $.post(url, {
                'record_id': record_id,
                'page': page
            }, zira_bind(this, function(response){
                zira_comments_remove_duplicates(response);
                $(this).parent('.comments-view-more-wrapper').replaceWith(response);
                zira_init_comments_rating();
                try {
                    zira_parse_content();
                } catch(err) {}
                if (navigator.userAgent.indexOf('MSIE')<0) {
                    $('.container #content .xhr-list').hide().slideDown().removeClass('xhr-list');
                } else {
                    $('.container #content .xhr-list').removeClass('xhr-list');
                }
            }),'html');
        });
    };

    zira_comments_remove_duplicates = function(response) {
        var regexp = new RegExp('data-comment_id=["]([^"]+)["]', 'g');
        var m;
        while(m = regexp.exec(response)) {
            $('ul.comments li[data-comment_id='+m[1]+']').remove();
        }
    };

    zira_init_comments_reload = function() {
        $('.container #content .comments-reload').click(function(e){
            e.stopPropagation();
            e.preventDefault();

            var url = $(this).data('url');
            var record_id = $(this).data('record');
            var page = $(this).data('page');

            if (typeof(url)=="undefined" ||
                typeof(record_id)=="undefined" ||
                typeof(page)=="undefined"
            ) {
                return;
            }

            $(this).parents('.comments-wrapper').find('.comments-view-more-wrapper').remove();

            if (navigator.userAgent.indexOf('MSIE')<0) {
                $('.container #content .comments').slideUp();
            }

            $(this).attr('disabled','disabled');

            window.setTimeout(zira_bind(this, function(){
                $.post(url, {
                    'record_id': record_id,
                    'page': page,
                    'reload': true
                }, zira_bind(this, function(response){
                    $(this).removeAttr('disabled');
                    if (!response || typeof(response.content)=="undefined") return;
                    $('.comments').eq(0).replaceWith('<div class="comments-reload-place"></div>');
                    $('.comments').remove();
                    $(this).parents('.comments-wrapper').find('.comments-reload-place').replaceWith(response.content);
                    zira_init_comments_rating();
                    try {
                        zira_parse_content();
                    } catch(err) {}
                    if (typeof(response.total)!="undefined") {
                        var h = $('h2#comments');
                        if ($(h).length>0) {
                            var h_content = $(h).html();
                            var hr = new RegExp('^([^(]+)[(]([^)]+)[)]$');
                            var hm = hr.exec(h_content);
                            if (hm) {
                                $(h).html(hm[1]+'('+response.total+')');
                            }
                        }
                    }
                    if (navigator.userAgent.indexOf('MSIE')<0) {
                        $('.container #content .xhr-list').hide().slideDown().removeClass('xhr-list');
                    } else {
                        $('.container #content .xhr-list').removeClass('xhr-list');
                    }
                    $('body, html').animate({'scrollTop': $(this).parents('.comments-wrapper').offset().top}, 500);
                }),'json');
            }), 500);
        });
    };

    zira_insert_comment_response = function(response) {
        if (typeof(response.content)=="undefined" || typeof(response.id)=="undefined" || typeof(response.parent)=="undefined") return;
        if (response.content.lenght==0) return;
        var parent = $('ul.comments li[data-comment_id='+response.parent+']');
        var last_child = $('ul.comments li[data-comment_parent='+response.parent+']').last();
        if (response.parent>0 && $(last_child).length>0) {
            var offset_left = $(last_child).offset().left;
            var target = $(last_child);
            var next, _next, __next;
            while(true) {
                next = $(target).next('li');
                if ($(next).length>0 && $(next).offset().left>=offset_left) {
                    target = next;
                } else {
                    if ($(next).length==0) {
                        _next = $(target).parent('ul').next('ul');
                        if ($(_next).length>0) {
                            __next = $(_next).children('li').first();
                            if ($(__next).length>0 && $(__next).offset().left>=offset_left) {
                                target = __next;
                                continue;
                            }
                        }
                    }
                    break;
                }
            }
            $(target).after(response.content.replace(/<ul[^>]*>([\s\S]+?)<\/ul>/g, '$1'));
        } else if (response.parent>0 && $(parent).length>0) {
            $(parent).after(response.content.replace(/<ul[^>]*>([\s\S]+?)<\/ul>/g, '$1'));
        } else if ($('ul.comments').length>0) {
            $('ul.comments').eq(0).before(response.content);
        } else {
            $('.comment-form-wrapper').eq(0).before(response.content);
        }
        zira_init_comments_rating();
        try {
            zira_parse_content();
        } catch(err) {}
        var h = $('h2#comments');
        if ($(h).length>0) {
            var h_content = $(h).html();
            var hr = new RegExp('^([^(]+)[(]([^)]+)[)]$');
            var hm = hr.exec(h_content);
            if (hm) {
                $(h).html(hm[1]+'('+(parseInt(hm[2])+1)+')');
            }
        }
        $('.container #content .xhr-list').removeClass('xhr-list');
        $('html, body').animate({
            'scrollTop': $('ul.comments li[data-comment_id='+response.id+']').offset().top-$(window).height()+$('ul.comments li[data-comment_id='+response.id+']').height()+20
        }, 200);
    };

    zira_init_search_more = function() {
        $('.container #content').on('click', '.search-results-view-more', function(e){
            e.stopPropagation();
            e.preventDefault();

            var url = $(this).data('url');
            var text = $(this).data('text');
            var offset = $(this).data('offset');

            if (typeof(url)=="undefined" ||
                typeof(text)=="undefined" ||
                typeof(offset)=="undefined"
            ) {
                return;
            }

            $(this).attr('disabled','disabled');
            $(this).parent('.search-results-view-more-wrapper').append('<div class="zira-loader-wrapper"><span class="zira-loader glyphicon glyphicon-refresh"></span> '+t('Please wait')+'...</div>');

            $.get(url, {
                'text': text,
                'offset': offset,
                'ajax': 1
            }, zira_bind(this, function(response){
                $(this).parent('.search-results-view-more-wrapper').replaceWith(response);
                if (navigator.userAgent.indexOf('MSIE')<0) {
                    $('.container #content .xhr-list').hide().slideDown().removeClass('xhr-list');
                } else {
                    $('.container #content .xhr-list').removeClass('xhr-list');
                }
            }),'html');
        });
    };

    zira_init_search = function(form) {
        if ($(form).length==0) return;
        $(form).find('input[type=text]').unbind('keyup').keyup(function(e){
            if (typeof(e.keyCode)=="undefined") return;
            try {
                window.clearTimeout(zira_init_search.timer);
            } catch(e){}
            if (e.keyCode == 13) return;

            zira_search_on_text_change.call(this);
            if ($(this).val().length==0) return;

            var delay = 250;
            if (typeof(zira_init_search.submits)!="undefined" && zira_init_search.submits>5) {
                delay = 500;
            }
            if (typeof(zira_init_search.submits)!="undefined" && zira_init_search.submits>10) {
                delay = 1000;
            }
            zira_init_search.timer = window.setTimeout(zira_bind(this, zira_search_form_sumbit),delay);
        });

        $(form).find('input[type=text]').unbind('mouseup').mouseup(function(){
            var text = $(this).val();
            if (typeof(zira_init_search.text)!="undefined" && text==zira_init_search.text && typeof(zira_init_search.response)!="undefined") {
                zira_search_form_response.call(this, zira_init_search.response);
            }
        });

        zira_search_on_text_change.call($(form).find('input[type=text]'));
    };

    zira_search_on_text_change = function() {
        if ($(this).length==0 || $(this).parents('form').length==0) return;
        var text = $(this).val();
        if (text.length>0 && (typeof(zira_init_search.notEmpty)=="undefined" || !zira_init_search.notEmpty)) {
            $(this).parents('form').append('<span class="search-text-clear glyphicon glyphicon-remove-circle"></span>')
            $(this).parents('form').find('.search-text-clear').click(zira_bind(this,function(){
                $(this).val('');
                $(this).parents('form').find('.search-text-clear').remove();
                zira_init_search.notEmpty = false;
                zira_search_close_preview_wnd();
            }));
            zira_init_search.notEmpty = true;
        } else if (text.length==0 && typeof(zira_init_search.notEmpty)!="undefined" && zira_init_search.notEmpty) {
            $(this).parents('form').find('.search-text-clear').remove();
            zira_init_search.notEmpty = false;
            zira_search_close_preview_wnd();
        }
    };

    zira_search_form_sumbit = function(){
        if ($(this).length==0 || $(this).parents('form').length==0) return;
        if ($(this).parents('form').hasClass('loading')) return;
        var text = $(this).val();
        if (text.length<3) return;
        if (typeof(zira_init_search.text)!="undefined" && zira_init_search.text==text) {
            if (typeof(zira_init_search.response)!="undefined") {
                zira_search_form_response.call(this, zira_init_search.response);
            }
            return;
        }
        zira_init_search.text = text;
        var sdata = $(this).parents('form').serializeArray();
        var data = {};
        for (var i=0; i<sdata.length; i++) {
            data[sdata[i].name] = sdata[i].value;
        }
        data['ajax'] = 1;
        data['simple'] = 1;
        $.get($(this).parents('form').attr('action'), data, zira_bind(this, zira_search_form_response));
        $(this).parents('form').addClass('loading').append('<span class="zira-loader glyphicon glyphicon-refresh"></span>');

        if (typeof(zira_init_search.submits)=="undefined") {
            zira_init_search.submits = 0;
        }
        zira_init_search.submits++;
    };

    zira_search_form_response = function(response){
        if ($(this).length==0 || $(this).parents('form').length==0) return;
        $(this).parents('form').removeClass('loading').find('.zira-loader').remove();
        zira_search_close_preview_wnd();
        if (!response || response.lenght==0 || $(this).val().length==0) return;
        zira_init_search.response = response;
        $(this).parents('form').parent().append('<div class="zira-search-preview-wnd">'+response+'</div>');
        $('body').unbind('click', zira_search_close_preview_wnd).bind('click',zira_search_close_preview_wnd);

        if (zira_init_search.text && zira_init_search.text.length>0 && $(this).val()!=zira_init_search.text) {
            zira_search_form_sumbit.call(this);
        }
    };

    zira_search_close_preview_wnd = function(e) {
        if (typeof(e)!="undefined" && typeof(e.originalEvent)!="undefined" && typeof(e.originalEvent.target)!="undefined") {
            if ($(e.originalEvent.target).parents('.zira-search-preview-wnd').length>0) {
                var item = $(e.originalEvent.target).parents('.list-item');
                if ($(item).length==0) return;
                var link = $(item).find('a.list-title');
                if ($(link).length==0) return;
                var url = $(link).attr('href');
                if (url && url.indexOf('http')!=0 && url.indexOf('#')!=0 && url!='javascript:void(0)') {
                    window.location.href = url;
                }
                return;
            } else if ($(e.originalEvent.target).parents('#top-menu-wrapper').length>0) {
                return;
            }
        }
        $('.zira-search-preview-wnd').remove();
        $('body').unbind('click', zira_search_close_preview_wnd);
    };

    zira_init_user_black_list_btn = function() {
        $('.user-profile .user-black-list-link').click(function(e){

            var action = $(this).data('action');
            var user = $(this).data('user');
            var token = $(this).data('token');
            var url = $(this).data('url');
            if (typeof(action)!="undefined" &&
                typeof(user)!="undefined" &&
                typeof(token)!="undefined" &&
                typeof(url)!="undefined"
            ) {
                if ($(this).hasClass('blocked')) {
                    zira_black_list_user.call(this, url, action, user, null, token);
                } else {
                    zira_prompt(t('Reason'), zira_bind(this, function(message){
                        if (message.length==0) {
                            zira_error(t('Please specify the reason'));
                        } else {
                            zira_black_list_user.call(this, url, action, user, message, token);
                        }
                    }));
                }
            }
        });
    };

    zira_black_list_user = function(url, action, user, message, token) {
        if (typeof(zira_black_list_user.inprogress)!="undefined" &&
            zira_black_list_user.inprogress
        ) {
            return;
        }
        $.post(url,{
            'action': action,
            'user_id': user,
            'message': message,
            'token': token
        }, zira_bind(this, function(response){
            zira_black_list_user.inprogress = false;
            if (!response || typeof(response.success)=="undefined" || !response.success) {
                zira_error(t('An error occurred'));
                return;
            }
            if ($(this).hasClass('blocked')) {
                $(this).removeClass('blocked');
            } else {
                $(this).addClass('blocked');
            }
        }),'json');
        zira_black_list_user.inprogress = true;
    };

    zira_user_message_sent_success = function(response) {
        if (typeof(response.redirect)=="undefined") return;
        if (response.redirect.indexOf('http')>=0) return;
        window.location.href = response.redirect;
    };

    zira_init_scroll_to_top = function() {
        $('a.scroll-top').click(function(e){
            e.stopPropagation();
            e.preventDefault();
            $('html, body').animate({'scrollTop':0},1000);
        });
        $(window).scroll(function(){
            var scrollTop = $(window).scrollTop();
            if (scrollTop >= $(window).height()) {
                $('a.scroll-top').addClass('visible');
            } else {
                $('a.scroll-top').removeClass('visible');
            }
        });
    };
    
    zira_init_scroll_down = function() {
        $('.scroll-down').click(function(e){
            e.stopPropagation();
            e.preventDefault();
            var selector = $(this).data('target');
            if (typeof selector == "undefined") return;
            zira_scroll(selector);
        });
    };
    
    zira_init_auth_popup = function() {
        $('.usermenu-popup a.user-login-menu, .inline-login-link').click(function(e){
            e.stopPropagation();
            e.preventDefault();
            zira_modal(t('Authorization'), '<div style="text-align:center;padding:100px"><span class="zira-loader glyphicon glyphicon-refresh"></span></div>', null, false, 'zira-auth-dialog');
            var url = $(this).attr('href');
            $.get(url, {
                format : 'json'
            }, zira_auth_popup_response, 'json');
        });
    };
    
    zira_auth_popup_response = function(response){
        if (!response || typeof response.form == "undefined") {
            zira_error(t('An error occurred'));
            return;
        }
        if (response && typeof response.redirect != "undefined" && response.redirect.length>0) {
            if (response.redirect == 'refresh') {
                window.location.reload();
            } else {
                window.location.href = response.redirect;
            }
            return;
        }
        $('#zira-auth-dialog .modal-body').html(response.form);
        $('#zira-auth-dialog .modal-body').find('input[type=text]').first().addClass('modal-focus');
        $('#zira-auth-dialog .modal-body form').submit(function(e){
            e.stopPropagation();
            e.preventDefault();
            var data = $(this).serialize();
            var url = $(this).attr('action');
            $('#zira-auth-dialog .modal-body').html('<div style="text-align:center;padding:100px"><span class="zira-loader glyphicon glyphicon-refresh"></span></div>');
            if (url.indexOf('?')<0) url += '?';
            else url += '&';
            url += 'format=json';
            $.post(url, data, zira_auth_popup_response, 'json');
        });
        if ($('#zira-auth-dialog .g-recaptcha').length>0) {
            if (typeof zira_load_recaptcha.loaded == "undefined") {
                zira_load_recaptcha();
            } else {
                try {
                    grecaptcha.render($('#zira-auth-dialog .g-recaptcha').get(0));
                } catch(e) {}
            }
        } else if ($('.captcha-refresh-btn').length>0) {
            zira_init_captcha();
        }
    };

    /**
     * modal
     */
    function zira_modal_init(){
        // default modal
        zira_modal_create('zira-modal-dialog', '', '', '', zira_modal_close_btn());
        // message dialog
        zira_modal_create('zira-message-dialog', 'zira-message-modal', t('Message'), '', zira_modal_close_btn());
        // error dialog
        zira_modal_create('zira-error-dialog', 'zira-error-modal', t('Error'), '', zira_modal_close_btn());
        // confirm dialog
        zira_modal_create('zira-confirm-dialog', 'zira-confirm-modal', t('Confirmation'), '', zira_modal_no_btn()+zira_modal_yes_btn());
        // prompt dialog
        zira_modal_create('zira-prompt-dialog', 'zira-prompt-modal', '', zira_modal_input(), zira_modal_cancel_btn()+zira_modal_ok_btn());
        // multi prompt dialog
        zira_modal_create('zira-multi-prompt-dialog', 'zira-prompt-modal', '', zira_modal_multi_input(), zira_modal_cancel_btn()+zira_modal_ok_btn());
        if ($('.usermenu-popup').length>0) {
            zira_modal_create('zira-auth-dialog', '', '', '', '');
        }
    }

    zira_modal_create = function(id, className, title, content, buttons) {
        if (className.length>0) className += ' ';
        var html = '<div class="'+className+'zira-modal modal fade" id="'+id+'" tabindex="-1" role="dialog" aria-labelledby="'+id+'-label">';
            html += '<div class="modal-dialog" role="document">';
            html += '<div class="modal-content">';
            html += '<div class="modal-header">';
            html += '<button type="button" class="close" data-dismiss="modal" aria-label="'+t('Close')+'"><span aria-hidden="true">&times;</span></button>';
            html += '<h4 class="modal-title" id="'+id+'-label">'+title+'</h4>';
            html += '</div>';
            html += '<div class="modal-body">';
            html += content;
            html += '</div>';
            html += '<div class="modal-footer">';
            html += buttons;
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';

        $('body').append(html);
    };

    zira_modal_close_btn = function() {
        return '<button type="button" class="btn btn-default modal-focus" data-dismiss="modal">'+t('Close')+'</button>';
    };

    zira_modal_no_btn = function() {
        return '<button type="button" class="btn btn-default" data-dismiss="modal">'+t('No')+'</button>';
    };

    zira_modal_yes_btn = function() {
        return '<button type="button" class="btn btn-primary modal-focus">'+t('Yes')+'</button>';
    };

    zira_modal_cancel_btn = function() {
        return '<button type="button" class="btn btn-default" data-dismiss="modal">'+t('Cancel')+'</button>'
    };

    zira_modal_ok_btn = function() {
        return '<button type="button" class="btn btn-primary">'+t('OK')+'</button>';
    };

    zira_modal_input = function() {
        return '<input type="text" name="modal-input" class="form-control modal-focus" />';
    };

    zira_modal_multi_input = function() {
        return '<textarea class="form-control modal-focus" name="modal-input"></textarea>';
    };

    zira_modal = function(title, content, callback, is_static, id) {
        if ($('.modal-backdrop').length>0) $('.modal-backdrop').remove();
        if (typeof (id) == 'undefined') id = 'zira-modal-dialog';
        var options = null;
        if (typeof (is_static) != "undefined" && is_static) options = {backdrop:'static'};
        if (typeof (title)!="undefined" && title!==null) $('#'+id+' .modal-title').text(title);
        if (typeof (content)!="undefined" && content!==null) $('#'+id+' .modal-body').html(content);
        $('#'+id).bind('shown.bs.modal', function() {
            $('#'+id).unbind('shown.bs.modal');
            $('#'+id+' .modal-focus').focus();
        });
        $('#'+id).bind('hidden.bs.modal', function() {
            $('#'+id).unbind('hidden.bs.modal');
            if (typeof(callback)!="undefined" && callback!==null) callback.call();
        });
        if (options) {
            $('#'+id).modal(options);
        } else {
            $('#'+id).modal('show');
        }
        // fixing z-index
        $('.zira-modal').css('zIndex',1050);
        $('.modal-backdrop').css('zIndex',1040);
    };

    zira_modal_disable_buttons = function() {
        $('.modal-dialog button').attr('disabled','disabled');
    };

    zira_modal_enable_buttons = function() {
        $('.modal-dialog button').removeAttr('disabled');
    };

    zira_modal_progress = function(title) {
        if (typeof(title)=="undefined") title = t('Sending');
        var progress = '<div class="progress xhr-progress-bar"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em;">0%</div></div>';
        zira_modal(title, t('Please wait')+progress, null, true, 'zira-modal-dialog');
        zira_modal_disable_buttons();
    };

    zira_modal_progress_update = function(percent) {
        $('.xhr-progress-bar .progress-bar').attr('aria-valuenow',percent).css('width',percent+'%').text(percent+'%');
    };

    zira_modal_progress_hide = function(callback) {
        var id = 'zira-modal-dialog';
        zira_modal_enable_buttons();
        if (typeof(callback)!="undefined" && callback!==null) {
            $('#'+id).bind('hidden.bs.modal', function() {
                $('#'+id).unbind('hidden.bs.modal');
                callback.call();
            });
        }
        $('#'+id).modal('hide');
    };

    zira_message = function(message, callback, icon) {
        if (typeof(icon)=="undefined") icon = true;
        if (icon) {
            message = '<div class="modal-success"><span class="glyphicon glyphicon-ok"></span> ' + message + '</div>';
        }
        zira_modal(null, message, callback, false, 'zira-message-dialog');
    };

    zira_error = function(message, callback, icon) {
        if (typeof(icon)=="undefined") icon = true;
        if (icon) {
            message = '<div class="modal-error"><span class="glyphicon glyphicon-warning-sign"></span> ' + message + '</div>';
        }
        zira_modal(null, message, callback, false, 'zira-error-dialog');
    };

    zira_confirm = function(message, yes_callback, no_callback) {
        zira_confirm.confirm = false;
        var id = 'zira-confirm-dialog';
        $('#'+id+' button.btn-primary').unbind('click').click(function(e){
            if (typeof(e.originalEvent)=="undefined") {
                e.stopPropagation();
                e.preventDefault();
                return;
            }
            zira_confirm.confirm = true;
            $('#'+id).modal('hide');
        });
        zira_modal(null, message, function(){
            $('#'+id+' button.btn-primary').unbind('click');
            if (zira_confirm.confirm && typeof(yes_callback)!="undefined") yes_callback.call();
            else if (!zira_confirm.confirm && typeof(no_callback)!="undefined") no_callback.call();
        }, false, id);
    };

    zira_prompt = function(message, ok_callback, cancel_callback) {
        zira_prompt.ok = false;
        var id = 'zira-prompt-dialog';
        $('#'+id+' button.btn-primary').unbind('click').click(function(e){
            if (typeof(e.originalEvent)=="undefined") {
                e.stopPropagation();
                e.preventDefault();
                return;
            }
            zira_prompt.ok = true;
            $('#'+id).modal('hide');
        });
        $('#'+id+' input[name=modal-input]').unbind('keypress').keypress(function(e){
            if (typeof(e.originalEvent)=="undefined") {
                e.stopPropagation();
                e.preventDefault();
                return;
            }
            if (e.which==13) {
                zira_prompt.ok = true;
                $('#'+id).modal('hide');
            }
        }).val('');
        zira_modal(message, null, function(){
            $('#'+id+' button.btn-primary').unbind('click');
            var val = $('#'+id+' input[name=modal-input]').unbind('keypress').val();
            if (zira_prompt.ok && typeof(ok_callback)!="undefined") ok_callback.call(null,val);
            else if (!zira_prompt.ok && typeof(cancel_callback)!="undefined") cancel_callback.call();
        }, false, id);
    };

    zira_multi_prompt = function(message, ok_callback, cancel_callback) {
        zira_multi_prompt.ok = false;
        var id = 'zira-multi-prompt-dialog';
        $('#'+id+' button.btn-primary').unbind('click').click(function(e){
            if (typeof(e.originalEvent)=="undefined") {
                e.stopPropagation();
                e.preventDefault();
                return;
            }
            zira_multi_prompt.ok = true;
            $('#'+id).modal('hide');
        });
        $('#'+id+' textarea[name=modal-input]').val('');
        zira_modal(message, null, function(){
            $('#'+id+' button.btn-primary').unbind('click');
            var val = $('#'+id+' textarea[name=modal-input]').val();
            if (zira_multi_prompt.ok && typeof(ok_callback)!="undefined") ok_callback.call(null,val);
            else if (!zira_multi_prompt.ok && typeof(cancel_callback)!="undefined") cancel_callback.call();
        }, false, id);
    };
    
    zira_calendar = function(selector, start_dow, callback) {
        if (typeof start_dow == "undefined") start_dow = 0;
        // month is zero based
        function create_calendar_table(month, year) {
            var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            var dowNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
            var cmonth = (new Date(year, month, 1)).getMonth();
            var cyear = (new Date(year, month, 1)).getFullYear();
            var days = (new Date(year, month+1, 0)).getDate();
            var dow = (new Date(year, month, 1)).getDay();
            var prev_days = (new Date(year, month, 0)).getDate();
            var last_dow = (new Date(year, month, days)).getDay();
            var now = new Date();
            var html = '<div class="zira-calendar-wrapper">';
            html += '<div class="zira-calendar-selector">';
            html += '<a href="javascript:void(0)" class="zira-calendar-month-switcher zira-calendar-prev-month" data-month="'+(cmonth-1)+'" data-year="'+cyear+'"><span class="glyphicon glyphicon-chevron-left"></span></a>';
            html += '<a href="javascript:void(0)" class="zira-calendar-month-switcher zira-calendar-next-month" data-month="'+(cmonth+1)+'" data-year="'+cyear+'"><span class="glyphicon glyphicon-chevron-right"></span></a>';
            html += '<span class="zira-calendar-month">'+t(monthNames[cmonth])+', '+cyear+'</span>';
            html += '</div>';
            html += '<div class="zira-calendar-dows-wrapper">';
            html += '<ul class="zira-calendar-dows">';
            for (var i=start_dow; i<7+start_dow; i++) {
                html += '<li class="zira-calendar-dow">'+t(dowNames[i%7])+'</li>';
            }
            html += '</ul>';
            html += '</div>';
            html += '<div class="zira-calendar-days-wrapper">';
            html += '<ul class="zira-calendar-days">';
            for (var i=prev_days-((dow-start_dow)+7)%7+1; i<=prev_days; i++) {
                html += '<li class="prev-days"><a href="javascript:void(0)" class="zira-calendar-day" data-day="'+i+'" data-month="'+(cmonth-1)+'" data-year="'+cyear+'">'+i+'</a></li>';
            }
            var cl;
            for (var i=1; i<=days; i++) {
                if (cyear == now.getFullYear() && cmonth == now.getMonth() && i==now.getDate()) cl = ' today';
                else cl = '';
                html += '<li class="this-days'+cl+'"><a href="javascript:void(0)" class="zira-calendar-day" data-day="'+i+'" data-month="'+(cmonth)+'" data-year="'+cyear+'">'+i+'</a></li>';
            }
            for (var i=1; i<=(6-(last_dow-start_dow))%7; i++) {
                html += '<li class="next-days"><a href="javascript:void(0)" class="zira-calendar-day" data-day="'+i+'" data-month="'+(cmonth+1)+'" data-year="'+cyear+'">'+i+'</a></li>';
            }
            html += '</ul>';
            html += '</div>';
            html += '</div>';
            $(this).html(html);
        }
        
        $(selector).each(function(index) {
            var now = new Date();
            var id = (selector+'-'+(index+1)).replace(/[.#]/g, '');
            $(this).replaceWith('<div id="'+id+'" class="'+$(this).attr('class')+'"></div>');
            create_calendar_table.call($('#'+id), now.getMonth(), now.getFullYear());
        });
        $(selector).on('click', '.zira-calendar-month-switcher', function(){
            var wrapper = $(this).parents('.zira-calendar-wrapper').parent();
            var month = $(this).data('month');
            var year = $(this).data('year');
            create_calendar_table.call(wrapper, month, year);
        });
        if (typeof callback != "undefined") {
            $(selector).on('click', '.zira-calendar-day', function(){
                var day = $(this).data('day');
                var month = $(this).data('month');
                var year = $(this).data('year');
                var date = new Date(year, month, day);
                callback.call(null, date);
            });
        }
    };
    
    zira_scroll = function(element) {
        var top = $(element).offset().top;
        jQuery('html, body').animate({'scrollTop':top},800);
    };

    zira_bind = function(object, method) {
        return function(arg) {
            method.call(object, arg);
        };
    };
    
    zira_init_xhr_form = function() {
        $('form.xhr-form').each(function() {
            if ($(this).find('.g-recaptcha').length>0 || $(this).find('.captcha-refresh-btn').length>0) return true;
            $(this).bind('xhr-submit-error', function(e, response){
                if (!response) return;
                if (typeof response.captcha_error != "undefined" && response.captcha_error) {
                    zira_confirm(t('Too many requests. Page need to be reloaded. Reload now ?'), function(){
                       window.location.reload(); 
                    });
                }
            });
        });
    };
    
    zira_init_captcha = function() {
        $('.captcha-refresh-btn').each(function(){
            if ($(this).hasClass('jsed')) return true;
            $(this).addClass('jsed');
            $(this).click(zira_bind(this, function(){
                var id = $(this).data('id');
                if (typeof id == "undefined") return;
                var img = $('#'+id);
                if ($(img).length==0) return;
                var src = $(img).attr('src');
                $(img).attr('src', src+Math.floor(Math.random()*10));
            }));
            $(this).parents('form.xhr-form').submit(zira_bind(this, function(){
                window.setTimeout(zira_bind(this, function(){
                    $(this).trigger('click');
                }), 3000);
            }));
        });
    };

    zira_load_recaptcha = function() {
        if (typeof zira_recaptcha_url == "undefined") return;
        if (typeof zira_load_recaptcha.loaded != 'undefined') return;
        zira_load_recaptcha.loaded = true;
        $('body').append('<script src="'+zira_recaptcha_url+'"></script>');

        $('.g-recaptcha').each(function(){
            $(this).parents('form.xhr-form').submit(function(){
                window.setTimeout(function(){
                    try {
                        grecaptcha.reset();
                    } catch(err) {}
                }, 3000);
            });
        });
    };

    /**
     * translate
     */
    t = function(str) {
        if (typeof (zira_strings) == 'undefined') return str;
        if (typeof (zira_strings[str]) == 'undefined') return str;
        return zira_strings[str];
    };

    /**
     * Mozilla's cookie framework
     *
     * @param sKey - The name of the cookie to create/overwrite (string)
     * @param sValue - The value of the cookie (string)
     * @param vEnd (Optional) - The max-age in seconds (e.g. 31536e3 for a year, Infinity for a never-expires cookie), or the expires date in GMTString format or as Date object; if not, the specified the cookie will expire at the end of the session (number – finite or Infinity – string, Date object or null).
     * @param sPath (Optional) - The path from where the cookie will be readable. E.g., "/", "/mydir"; if not specified, defaults to the current path of the current document location (string or null). The path must be absolute (see RFC 2965). For more information on how to use relative paths in this argument, see this paragraph.
     * @param sDomain (Optional) - The domain from where the cookie will be readable. E.g., "example.com", ".example.com" (includes all subdomains) or "subdomain.example.com"; if not specified, defaults to the host portion of the current document location (string or null).
     * @param bSecure (Optional) - The cookie will be transmitted only over secure protocol as https (boolean or null).
     * @returns {boolean}
     */
    setCookie = function (sKey, sValue, vEnd, sPath, sDomain, bSecure) {
        if (!sKey || /^(?:expires|max\-age|path|domain|secure)$/i.test(sKey)) { return false; }
        var sExpires = "";
        if (vEnd) {
            switch (vEnd.constructor) {
                case Number:
                    sExpires = vEnd === Infinity ? "; expires=Fri, 31 Dec 9999 23:59:59 GMT" : "; max-age=" + vEnd;
                break;
                case String:
                    sExpires = "; expires=" + vEnd;
                break;
                case Date:
                    sExpires = "; expires=" + vEnd.toUTCString();
                break;
            }
        }
        document.cookie = encodeURIComponent(sKey) + "=" + encodeURIComponent(sValue) + sExpires + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "") + (bSecure ? "; secure" : "");
        return true;
    };

    getCookie = function (sKey) {
        if (!sKey) { return null; }
        return decodeURIComponent(document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + encodeURIComponent(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1")) || null;
    };
})(jQuery);