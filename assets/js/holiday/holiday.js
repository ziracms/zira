(function($){
    var zira_celebrate_pos = function() {
        if ($('.zira-celebration').length==0) return false;
        var ch = $('.zira-celebration').outerHeight();
        var wh = $(window).height();
        if (wh > ch) {
            var ct = Math.floor((wh - ch)/2);
        } else {
            var ct = 0;
        }
        $('.zira-celebration').css('top',ct);
        return true;
    };
    
    var zira_celebrate_pos_fix = function() {
        if (zira_celebrate_pos()) {
            window.setTimeout(zira_celebrate_pos_fix, 250);
        }
    };
        
    zira_celebrate = function() {
        if ($('.zira-celebration').length!=1) return;
        
        zira_celebrate_pos_fix();
        
        var asrc = $('.zira-celebration').data('asrc');
        $('.zira-celebration').click(function(){
            if ($('audio#celebration-audio').length==0) return;
            try {
                if (!$('audio#celebration-audio').get(0).paused) return;
                $('audio#celebration-audio').get(0).play();
            } catch (err) {}
        });
        $('.zira-celebration .celebration-close').click(function(){
            $('.zira-celebration').remove();
            $('.zira-celebration-bg').remove();
            if ($('audio#celebration-audio').length==0) return;
            try {
                if ($('audio#celebration-audio').get(0).ended) return;
                $('audio#celebration-audio').get(0).pause();
            } catch (err) {}
        });
        
        if (typeof asrc != "undefined" && asrc.length>0 && typeof Audio != "undefined") {
            $('body').append('<audio id="celebration-audio"><source src="'+asrc+'" type="audio/mp3" /></audio>');
            $('audio#celebration-audio').bind('ended', function(){
                $('.zira-celebration .celebration-close').trigger('click');
            });
            try {
                $('audio#celebration-audio').get(0).play();
            } catch (err) {}
        }
        
        $('.zira-celebration').show();
        $('.zira-celebration-bg').show();
        window.setTimeout(function(){
            $('.zira-celebration .celebration-close').show();
            $('.zira-celebration-bg').click(function(){
                $('.zira-celebration .celebration-close').trigger('click');
            });
        }, 10000);
    };
    
    var zira_new_year_theme = function(is_new_year) {
        if (typeof zira_base == "undefined") return;
        var base = zira_base;
        if (base.substr(-1) == '/') {
            base = base.substr(0, base.length - 1);
        }
        
        if (typeof(window.orientation) == "undefined" || typeof is_new_year == "undefined" || !is_new_year) {
            var img1 = new Image();
            img1.onload = function(){
                $('body').append('<img class="new-year-theme-img new-year-theme-img-1" src="'+img1.src+'" alt="" />');
                $('.new-year-theme-img-1').css('left','-'+img1.width+'px').show().animate({left:0},1000,function(){
                    $(this).animate({left:'-20px'},500,function(){
                        $(this).animate({left:'-10px'},1000);
                    });
                });
                $('.new-year-theme-img-1').click(function(){
                    $(this).animate({left:'-'+img1.width+'px'},1000);
                });
            };
            img1.src = base + '/assets/images/holiday/newyear1.png';
        }
        
        if (typeof(window.orientation) == "undefined") {
            var img2 = new Image();
            img2.onload = function(){
                $('body').append('<img class="new-year-theme-img new-year-theme-img-2" src="'+img2.src+'" alt="" />');
                $('.new-year-theme-img-2').css('top','-'+img2.height+'px').show().animate({top:0},1000,function(){
                    $(this).animate({top:'-20px'},500,function(){
                        $(this).animate({top:'-10px'},1000);
                    });
                });
                $('.new-year-theme-img-2').click(function(){
                    $(this).animate({top:'-'+img2.height+'px'},1000);
                });
            };
            img2.src = base + '/assets/images/holiday/newyear2.png';
        }
        
        if (typeof is_new_year != "undefined" && is_new_year) {
            var img3 = new Image();
            img3.onload = function(){
                $('body').append('<img class="new-year-theme-img new-year-theme-img-3" src="'+img3.src+'" alt="" />');
                $('.new-year-theme-img-3').css('bottom','-'+img3.height+'px').show().animate({bottom:0},1000,function(){
                    $(this).animate({bottom:'-20px'},500,function(){
                        $(this).animate({bottom:'-10px'},1000);
                    });
                });
                $('.new-year-theme-img-3').click(function(){
                    $(this).animate({bottom:'-'+img3.height+'px'},1000);
                });
            };
            img3.src = base + '/assets/images/holiday/newyear3.png';
        }
        
        var img4 = new Image();
        img4.onload = function(){
            $('header').append('<img class="new-year-theme-img new-year-theme-img-4" src="'+img4.src+'" alt="" />');
            $('.new-year-theme-img-4').css('top','-'+img4.height+'px').show().animate({top:0},1000,function(){
                $(this).animate({top:'-5px'},500,function(){
                    $(this).animate({top:'0px'},500);
                });
            });
            $('.new-year-theme-img-4').click(function(){
                $(this).animate({top:'-'+img4.height+'px'},1000);
            });
        };
        img4.src = base + '/assets/images/holiday/newyear4.png';
        
        var img5 = new Image();
        img5.onload = function(){
            $('body').append('<div class="new-year-theme-img new-year-theme-img-5" style="background-image:url('+img5.src+');" />');
            $('.new-year-theme-img-5').css('height',img5.height).css('bottom','-'+img5.height+'px').show().animate({bottom:'-10px'},1000,function(){
                $(this).animate({bottom:'-15px'},500,function(){
                    $(this).animate({bottom:'-10px'},500);
                });
            });
            $('.new-year-theme-img-5').click(function(){
                $(this).animate({bottom:'-'+img5.height+'px'},1000);
            });
        };
        img5.src = base + '/assets/images/holiday/newyear5.png';
        
        var img6 = new Image();
        img6.onload = function(){
            $('body').append('<img class="new-year-theme-img new-year-theme-img-6" src="'+img6.src+'" alt="" />');
            $('.new-year-theme-img-6').css('top','-'+img6.height+'px').show().animate({top:0},1000,function(){
                $(this).animate({top:'-5px'},500,function(){
                    $(this).animate({top:'0px'},500);
                });
            });
            $('.new-year-theme-img-6').click(function(){
                $(this).animate({top:'-'+img6.height+'px'},1000);
            });
        };
        img6.src = base + '/assets/images/holiday/newyear6.png';

        $(window).scroll(function(){
            var top = $(window).scrollTop();
            if (top>100 && !$('.new-year-theme-img-2').hasClass('faded')) {
                $('.new-year-theme-img-2').addClass('faded');
                $('.new-year-theme-img-2').animate({right:'-150px'},1000);
            } else if (top<=100 && $('.new-year-theme-img-2').hasClass('faded')) {
                $('.new-year-theme-img-2').removeClass('faded');
                $('.new-year-theme-img-2').animate({right:'0px'},1000);
            }
            if ($(window).width()<992) {
                if (top>100 && !$('.new-year-theme-img-1').hasClass('faded')) {
                    $('.new-year-theme-img-1').addClass('faded');
                    $('.new-year-theme-img-1').animate({left:'-150px'},1000);
                } else if (top<=100 && $('.new-year-theme-img-1').hasClass('faded')) {
                    $('.new-year-theme-img-1').removeClass('faded');
                    $('.new-year-theme-img-1').animate({left:'0px'},1000);
                }
                if (top>100 && !$('.new-year-theme-img-3').hasClass('faded')) {
                    $('.new-year-theme-img-3').addClass('faded');
                    $('.new-year-theme-img-3').animate({right:'-213px'},1000);
                } else if (top<=100 && $('.new-year-theme-img-3').hasClass('faded')) {
                    $('.new-year-theme-img-3').removeClass('faded');
                    $('.new-year-theme-img-3').animate({right:'0px'},1000);
                }
            }
        });
    };
    
    var zira_snow = function() {
        if (typeof zira_base == "undefined") return;
        var base = zira_base;
        if (base.substr(-1) == '/') {
            base = base.substr(0, base.length - 1);
        }
        
        ZiraSpreadInit = function(){
            $('body').ziraSnowStorm({
                count: 50,
                interval: 50,
                execTime: 90000,
                lifetime: 15000,
                createInterval: 100
            });
        };
        $('body').append('<script src="'+base+'/assets/plugins/spread.js'+'" async="true"></script>');
    };
    
    $(document).ready(function(){
        if ($('.zira-celebration').length>0 && 
            typeof(designer_style_theme)=="undefined"
        ) {
            zira_celebrate();
        }
        
        /**
         * new year theme
         */
        if (typeof(designer_style_theme)=="undefined" &&
            typeof(zira_holiday_ny_mode)!= "undefined" && 
            zira_holiday_ny_mode
        ) {
            var is_ny = typeof(zira_holiday_is_ny)!= "undefined" && zira_holiday_is_ny ? true : false;
            zira_new_year_theme(is_ny);
        }
        /**
         * snow
         */
        if (typeof(designer_style_theme)=="undefined" &&
            typeof(zira_holiday_snow)!= "undefined" && 
            zira_holiday_snow
        ) {
            zira_snow();
        }
    });
})(jQuery);

        