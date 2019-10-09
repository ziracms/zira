/**
 * Zira project
 * (c)2018 https://github.com/ziracms/zira
 */

(function($){
    $.fn.ziraSlider = function(options) {
        return new ziraSlider($(this), options);
    };
    
    function ziraSlider(element, options) {
        this.element = $(element);
        this.timer = null;
        this.interval = 5000;
        this.transition_time = 600;
        this.delay_time = 200;
        this.support3d = false;
        this.supportTransition = false;
        this.images = [];
        this.total = 0;
        this.loaded = 0;
        this.parts = 3;
        this.width = 0;
        this.height = 0;
        this.part_height = 0;
        this.direction = 1;
        this.inProgress = false;
        this.initialized = false;
        this.enableIE = true;
        this.isIE = false;
        this.touchX = null;
        this.touchMinMove = 30;
        this.blank_src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
        this.transitionEndEvents = 'webkitTransitionEnd transitionend msTransitionEnd oTransitionEnd';
        this.setOptions(options);
        this.init();
    }
    
    ziraSlider.prototype.setOptions = function(options) {
        if (typeof options == "undefined") return;
        if (typeof options.interval != "undefined") this.interval = options.interval;
        if (typeof options.transition_time != "undefined") this.transition_time = options.transition_time;
        if (typeof options.delay_time != "undefined") this.delay_time = options.delay_time;
        if (typeof options.parts != "undefined") this.parts = options.parts;
	if (typeof options.enableIE != "undefined") this.enableIE = options.enableIE;
    };
    
    ziraSlider.prototype.init = function() {
        this.total = $(this.element).find('img').length;
        if (this.total==0) return;
        var test = document.createElement('div');
        if (typeof test.style.transition == "string") {
            this.supportTransition = true;
        }
        if (this.supportTransition &&
            typeof test.style.perspective == "string" &&
            typeof test.style.transform == "string"  &&
            typeof test.style.transformOrigin == "string" && 
            typeof test.style.transformStyle == "string"
        ) {
            this.support3d = true;
        }
        var ua = navigator.userAgent;
        if (ua.indexOf('MSIE')>=0 || ua.indexOf('Trident/')>=0 || ua.indexOf('Edge/')>=0) {
                this.isIE = true;
                if (!this.enableIE) this.support3d = false;
        }
        var min_interval = this.transition_time + this.delay_time*this.parts;
        if (this.interval < min_interval) this.interval = min_interval;
        if (!$(this.element).hasClass('zira-slider')) $(this.element).addClass('zira-slider');
        $(this.element).wrap('<div class="zira-slider-wrapper"></div>');
        $(this.element).find('li:first').addClass('visible').addClass('active');
        this.createLoader();
        $(this.element).find('img').each(this.bind(this, function(index, element){
            var img = new Image();
            this.images.push(img);
            img.onload = this.bind(this, this.onImageLoad);
            img.src = $(element).attr('src');
            $(element).data('image', img);
        }));
        $(window).load(this.bind(this, this.onLoad));
    };
    
    ziraSlider.prototype.onImageLoad = function() {
        this.loaded++;
        var percent = Math.round((this.loaded / this.images.length) * 100);
        this.updateLoader(percent);
        if (this.loaded >= this.total && !this.initialized) {
            this.onLoad();
        }
    };
    
    ziraSlider.prototype.createLoader = function() {
        $(this.element).parent().append('<div class="zs-loader-wrapper"><div class="zs-loader"></div></div>');
        $(this.element).parent().find('.zs-loader').css('width', '0%');
    };
    
    ziraSlider.prototype.updateLoader = function(percent) {
        var loader = $(this.element).parent().find('.zs-loader');
        if ($(loader).length==0) return;
        $(loader).css('width', percent+'%').html('<span>'+percent+'%</span>');
    };
    
    ziraSlider.prototype.destroyLoader = function() {
        $(this.element).parent().children('.zs-loader-wrapper').remove();
    };
    
    ziraSlider.prototype.onLoad = function() {
        if (this.initialized) return;
        this.initialized = true;
        this.destroyLoader();
        $(this.element).find('li').each(this.bind(this, function(index, element){
            var img = $(element).find('img').first();
            var title = $(img).attr('title');
            if (typeof title != "undefined" && title.length>0) {
                $(element).append('<p class="zs-desc">'+title+'</p>');
            }
        }));
        if (this.images.length>1) {
            $(this.element).parent().append('<div class="zs-nav zs-nav-prev"></div>').append('<div class="zs-nav zs-nav-next"></div>');
            $(this.element).parent().children('.zs-nav-prev').click(this.bind(this, this.onPrev));
            $(this.element).parent().children('.zs-nav-next').click(this.bind(this, this.onNext));
        }
        $(window).resize(this.bind(this, function(){
            if (this.support3d) {
                this.destroy();
                this.create();
            } else {
                this.updateRect();
            }
        }));
        this.create();
    };
    
    ziraSlider.prototype.create = function() {
        $(this.element).find('li').removeClass('visible').removeClass('active');
        $(this.element).find('li:first').addClass('visible').addClass('active');
        this.updateRect();
        if (this.support3d) {
            $(this.element).after('<div class="zira-slider-box"></div>');
            this.createCube();
            $(this.element).parent().children('.zira-slider-box').css('opacity', 0);
            this.setCubeImages();
        }
        if (!this.support3d && this.supportTransition) {
            $(this.element).find('li').css('transition','opacity '+this.transition_time+'ms ease');
        }
        if (this.images.length>1) {
            $(this.element).bind('dragstart', function(e){
                e.stopPropagation();
                e.preventDefault();
            });
            if (typeof window.ontouchstart == "undefined") {
                $(this.element).unbind('mousedown').mousedown(this.bind(this, function(e){
                    if (typeof e.pageX == "undefined") return;
                    this.onDragStart(e.pageX);
                }));
                $(this.element).unbind('mousemove').mousemove(this.bind(this, function(e){
                    if (typeof e.pageX == "undefined") return;
                    this.onDragMove(e.pageX);
                }));
                $(this.element).unbind('mouseup').mouseup(this.bind(this, function(e){
                    this.onDragEnd();
                }));
                $(this.element).unbind('mouseout').mouseout(this.bind(this, function(e){
                    this.onDragEnd();
                }));
            } else {
                $(this.element).unbind('touchstart').bind('touchstart', this.bind(this, function(e){
                    if (typeof(e.originalEvent)=="undefined" || typeof(e.originalEvent.touches)=="undefined" || typeof(e.originalEvent.touches[0].pageX)=="undefined") return;
                    this.onDragStart(e.originalEvent.touches[0].pageX);
                }));
                $(this.element).unbind('touchmove').bind('touchmove', this.bind(this, function(e){
                    if (typeof(e.originalEvent)=="undefined" || typeof(e.originalEvent.touches)=="undefined" || typeof(e.originalEvent.touches[0].pageX)=="undefined") return;
                    this.onDragMove(e.originalEvent.touches[0].pageX);
                }));
                $(this.element).unbind('touchend').bind('touchend', this.bind(this, function(e){
                    this.onDragEnd();
                }));
            }
            this.startTimer();
        }
    };
    
    ziraSlider.prototype.onDragStart = function(pageX) {
        this.touchX = pageX;
    };
    
    ziraSlider.prototype.onDragMove = function(pageX) {
        if (this.touchX === null) return;
        var dx = this.touchX - pageX;
        if (Math.abs(dx) >= this.touchMinMove) {
            if (dx<0) $(this.element).parent().children('.zs-nav-prev').trigger('click');
            else $(this.element).parent().children('.zs-nav-next').trigger('click');
            this.touchX = null;
        }
    };
    
    ziraSlider.prototype.onDragEnd = function() {
        this.touchX = null;
    };
    
    ziraSlider.prototype.updateRect = function() {
        this.width = $(this.element).width();
        this.height = $(this.element).height();
        this.part_height = this.height/this.parts;
        if (this.images.length>1) {
            var navs = $(this.element).parent().children('.zs-nav');
            $(navs).css({
                'top': (this.height-$(navs).height())/2+'px',
                'display': 'block'
            });
        }
    };
    
    ziraSlider.prototype.destroy = function() {
        this.stopTimer();
        $(this.element).css('opacity', 1);
        $(this.element).parent().children('.zira-slider-box').remove();
        $(this.element).parent().removeClass('zs-progress');
        this.inProgress = false;
    };
    
    ziraSlider.prototype.createCube = function() {
        for (var y=0; y<this.parts; y++) {
            this.createCubePart(y);
        }
    };
    
    ziraSlider.prototype.createCubePart = function(offset) {
        var container = $(this.element).parent().children('.zira-slider-box');
        var item = $('<div class="zs-cube-part" />');
        $(item).css({
            'backgroundImage': 'url('+this.blank_src+')',
            'backgroundRepeat': 'no-repeat',
            'backgroundPosition': '0px '+(this.part_height*offset)*-1+'px',
            'backgroundSize': '100%',
            'width': this.width,
            'height': this.part_height,
            'top': (this.part_height*offset)+'px',
            'transformOrigin': '50% 50% -'+Math.floor(this.width/2)+'px',
            'transition': 'transform '+this.transition_time+'ms ease',
            'transitionDelay': Math.floor(this.delay_time*offset)+'ms',
            'zIndex': 2
        });
        if (this.isIE) {
            $(item).css('zIndex', this.parts-offset+1);
        }
        var item_right = $(item).clone().addClass('zs-cube-part-right');
        $(item_right).css('transform', 'rotate3d(0, 1, 0, 90deg)');
        $(container).append(item_right);
        var item_center = $(item).clone().addClass('zs-cube-part-center');
        $(container).append(item_center);
        var item_left = $(item).clone().addClass('zs-cube-part-left');
        $(item_left).css('transform', 'rotate3d(0, 1, 0, -90deg)');
        $(container).append(item_left);
        var item_top = $('<div class="zs-cube-part" />').addClass('zs-cube-part-top');
        $(item_top).css({
            'width': this.width,
            'height': this.width,
            'top': (this.part_height*offset)+'px',
            'transformOrigin': '50% 50% 0px',
            'transition': 'transform '+this.transition_time+'ms ease',
            'transitionDelay': Math.floor(this.delay_time*offset)+'ms',
            'transform': 'translate3d(0, -'+Math.floor(this.width/2)+'px, -'+Math.floor(this.width/2)+'px) rotate3d(1, 0, 0, 90deg)',
            'zIndex': 1
        });
        if (this.isIE) {
            $(item_top).css('zIndex', this.parts-offset);
        }
        $(container).append(item_top);
        var item_bottom = $(item_top).clone().removeClass('zs-cube-part-top').addClass('zs-cube-part-bottom');
        $(item_bottom).css('transform', 'translate3d(0, '+(this.part_height-this.width/2)+'px, -'+Math.floor(this.width/2)+'px) rotate3d(1, 0, 0, 90deg)');
        $(container).append(item_bottom);
    };
    
    ziraSlider.prototype.rotateCubeLeft = function() {
        var container = $(this.element).parent().children('.zira-slider-box');
        $(container).children('.zs-cube-part-center').css('transform', 'rotate3d(0, 1, 0, -90deg)');
        if (!this.isIE) {
            $(container).children('.zs-cube-part-left').css('transform', 'rotate3d(0, 1, 0, -180deg)');
        }
        $(container).children('.zs-cube-part-right').css('transform', 'rotate3d(0, 1, 0, 0deg)');
        $(container).children('.zs-cube-part-top').css('transform', 'translate3d(0, -'+Math.floor(this.width/2)+'px, -'+Math.floor(this.width/2)+'px) rotate3d(1, 0, 0, 90deg) rotate3d(0, 0, 1, 90deg)');
        $(container).children('.zs-cube-part-bottom').css('transform', 'translate3d(0, '+(this.part_height-this.width/2)+'px, -'+Math.floor(this.width/2)+'px) rotate3d(1, 0, 0, 90deg) rotate3d(0, 0, 1, 90deg)');
    };
    
    ziraSlider.prototype.rotateCubeRight = function() {
        var container = $(this.element).parent().children('.zira-slider-box');
        $(container).children('.zs-cube-part-center').css('transform', 'rotate3d(0, 1, 0, 90deg)');
        $(container).children('.zs-cube-part-left').css('transform', 'rotate3d(0, 1, 0, 0deg)');
        if (!this.isIE) {
            $(container).children('.zs-cube-part-right').css('transform', 'rotate3d(0, 1, 0, 180deg)');
        }
        $(container).children('.zs-cube-part-top').css('transform', 'translate3d(0, -'+Math.floor(this.width/2)+'px, -'+Math.floor(this.width/2)+'px) rotate3d(1, 0, 0, 90deg) rotate3d(0, 0, 1, -90deg)');
        $(container).children('.zs-cube-part-bottom').css('transform', 'translate3d(0, '+(this.part_height-this.width/2)+'px, -'+Math.floor(this.width/2)+'px) rotate3d(1, 0, 0, 90deg) rotate3d(0, 0, 1, -90deg)');
    };
    
    ziraSlider.prototype.rotateCubeCenter = function() {
        var container = $(this.element).parent().children('.zira-slider-box');
        $(container).children('.zs-cube-part-center').css('transform', 'rotate3d(0, 1, 0, 0deg)');
        $(container).children('.zs-cube-part-left').css('transform', 'rotate3d(0, 1, 0, -90deg)');
        $(container).children('.zs-cube-part-right').css('transform', 'rotate3d(0, 1, 0, 90deg)');
        $(container).children('.zs-cube-part-top').css('transform', 'translate3d(0, -'+Math.floor(this.width/2)+'px, -'+Math.floor(this.width/2)+'px) rotate3d(1, 0, 0, 90deg) rotate3d(0, 0, 1, 0deg)');
        $(container).children('.zs-cube-part-bottom').css('transform', 'translate3d(0, '+(this.part_height-this.width/2)+'px, -'+Math.floor(this.width/2)+'px) rotate3d(1, 0, 0, 90deg) rotate3d(0, 0, 1, 0deg)');
    };
    
    ziraSlider.prototype.setCubeImages = function() {
        var active = $(this.element).find('li.active');
        var next = $(active).next('li');
        if ($(next).length==0) next = $(this.element).find('li:first');
        var prev = $(active).prev('li');
        if ($(prev).length==0) prev = $(this.element).find('li:last');
        var src1 = $(active).find('img').data('image').src;
        var src2 = $(next).find('img').data('image').src;
        var src3 = $(prev).find('img').data('image').src;
        var container = $(this.element).parent().children('.zira-slider-box');
        $(container).children('.zs-cube-part-center').css('backgroundImage','url('+src1+')');
        $(container).children('.zs-cube-part-right').css('backgroundImage','url('+src2+')');
        $(container).children('.zs-cube-part-left').css('backgroundImage','url('+src3+')');
    }
    
    ziraSlider.prototype.startTimer = function() {
        this.timer = window.setTimeout(this.bind(this, this.direction>0 ? this.next : this.prev), this.interval);
    };
    
    ziraSlider.prototype.stopTimer = function() {
        try {
            window.clearTimeout(this.timer);
        } catch(e) {}
    };
    
    ziraSlider.prototype.next = function() {
        if (this.inProgress) return;
        if (this.support3d) {
            this.next3d();
        } else {
            this.nextFallback();
        }
    };
    
    ziraSlider.prototype.next3d = function() {
        this.inProgress = true;
        var active = $(this.element).find('li.active');
        var next = $(active).next('li');
        if ($(next).length==0) next = $(this.element).find('li:first');
        $(next).css('zIndex', parseInt($(active).css('zIndex'))+1);
        var last_cube_part = $(this.element).parent().children('.zira-slider-box').children('.zs-cube-part').last();
        $(last_cube_part).on(this.transitionEndEvents,this.bind(this,function(){
            $(last_cube_part).off(this.transitionEndEvents);
            $(next).addClass('visible').addClass('active');
            $(active).removeClass('visible').removeClass('active');
            $(this.element).css('opacity',1);
            $(this.element).parent().children('.zira-slider-box').css('opacity', 0);
            $(this.element).parent().children('.zira-slider-box').children('.zs-cube-part').addClass('reset');
            $(last_cube_part).on(this.transitionEndEvents,this.bind(this,function(){
                $(last_cube_part).off(this.transitionEndEvents);
                if (this.isIE) {
                    $(this.element).parent().children('.zira-slider-box').children('.zs-cube-part-left').css('opacity', 1);
                }
                $(this.element).parent().children('.zira-slider-box').children('.zs-cube-part').removeClass('reset');
                $(this.element).parent().removeClass('zs-progress');
                this.updateRect();
                this.inProgress = false;
            }));
            this.setCubeImages();
            this.rotateCubeCenter();
            this.startTimer();
        }));
        if (this.isIE) {
            $(this.element).parent().children('.zira-slider-box').children('.zs-cube-part-left').css('opacity', 0);
        }
        this.rotateCubeLeft();
        $(this.element).css('opacity',0);
        $(this.element).parent().addClass('zs-progress');
        $(this.element).parent().children('.zira-slider-box').css('opacity', 1);
    };
    
    ziraSlider.prototype.nextFallback = function() {
        this.inProgress = true;
        var active = $(this.element).find('li.active');
        var next = $(active).next('li');
        if ($(next).length==0) next = $(this.element).find('li:first');
        $(next).css('zIndex', parseInt($(active).css('zIndex'))+1);
        if (this.supportTransition) {
            $(next).on(this.transitionEndEvents,this.bind(this,function(){
                $(next).off(this.transitionEndEvents);
                $(active).removeClass('visible').removeClass('active');
                $(next).addClass('active');
                $(this.element).parent().removeClass('zs-progress');
                this.updateRect();
                this.startTimer();
                this.inProgress = false;
            }));
            $(next).addClass('visible');
            $(this.element).parent().addClass('zs-progress');
        } else {
            $(next).stop(true, true).animate({opacity:1},this.transition_time, this.bind(this,function(){
                $(active).css('opacity',0);
                $(active).removeClass('visible').removeClass('active');
                $(next).addClass('visible').addClass('active');
                $(this.element).parent().removeClass('zs-progress');
                this.updateRect();
                this.startTimer();
                this.inProgress = false;
            }));
            $(this.element).parent().addClass('zs-progress');
        }
    };
    
    ziraSlider.prototype.prev = function() {
        if (this.inProgress) return;
        if (this.support3d) {
            this.prev3d();
        } else {
            this.prevFallback();
        }
    };
    
    ziraSlider.prototype.prev3d = function() {
        this.inProgress = true;
        var active = $(this.element).find('li.active');
        var prev = $(active).prev('li');
        if ($(prev).length==0) prev = $(this.element).find('li:last');
        $(prev).css('zIndex', parseInt($(active).css('zIndex'))+1);
        var last_cube_part = $(this.element).parent().children('.zira-slider-box').children('.zs-cube-part').last();
        $(last_cube_part).on(this.transitionEndEvents,this.bind(this,function(){
            $(last_cube_part).off(this.transitionEndEvents);
            $(prev).addClass('visible').addClass('active');
            $(active).removeClass('visible').removeClass('active');
            $(this.element).css('opacity',1);
            $(this.element).parent().children('.zira-slider-box').css('opacity', 0);
            $(this.element).parent().children('.zira-slider-box').children('.zs-cube-part').addClass('reset');
            $(last_cube_part).on(this.transitionEndEvents,this.bind(this,function(){
                $(last_cube_part).off(this.transitionEndEvents);
                if (this.isIE) {
                    $(this.element).parent().children('.zira-slider-box').children('.zs-cube-part-right').css('opacity', 1);
                }
                $(this.element).parent().children('.zira-slider-box').children('.zs-cube-part').removeClass('reset');
                $(this.element).parent().removeClass('zs-progress');
                this.updateRect();
                this.inProgress = false;
            }));
            this.setCubeImages();
            this.rotateCubeCenter();
            this.startTimer();
        }));
        if (this.isIE) {
            $(this.element).parent().children('.zira-slider-box').children('.zs-cube-part-right').css('opacity', 0);
        }
        this.rotateCubeRight();
        $(this.element).css('opacity',0);
        $(this.element).parent().addClass('zs-progress');;
        $(this.element).parent().children('.zira-slider-box').css('opacity', 1);
    };
    
    ziraSlider.prototype.prevFallback = function() {
        this.inProgress = true;
        var active = $(this.element).find('li.active');
        var prev = $(active).prev('li');
        if ($(prev).length==0) prev = $(this.element).find('li:last');
        $(prev).css('zIndex', parseInt($(active).css('zIndex'))+1);
        if (this.supportTransition) {
            $(prev).on(this.transitionEndEvents,this.bind(this,function(){
                $(prev).off(this.transitionEndEvents);
                $(active).removeClass('visible').removeClass('active');
                $(prev).addClass('active');
                $(this.element).parent().removeClass('zs-progress');
                this.updateRect();
                this.startTimer();
                this.inProgress = false;
            }));
            $(prev).addClass('visible');
            $(this.element).parent().addClass('zs-progress');
        } else {
            $(prev).stop(true, true).animate({opacity:1},this.transition_time, this.bind(this,function(){
                $(active).css('opacity',0);
                $(active).removeClass('visible').removeClass('active');
                $(prev).addClass('visible').addClass('active');
                $(this.element).parent().removeClass('zs-progress');
                this.updateRect();
                this.startTimer();
                this.inProgress = false;
            }));
            $(this.element).parent().addClass('zs-progress');
        }
    };
    
    ziraSlider.prototype.onPrev = function() {
        if (this.inProgress) return;
        this.stopTimer();
        this.prev();
        this.direction = -1;
    };
    
    ziraSlider.prototype.onNext = function() {
        if (this.inProgress) return;
        this.stopTimer();
        this.next();
        this.direction = 1;
    };
    
    ziraSlider.prototype.bind = function(object, method) {
        return function(arg1, arg2) {
            method.call(object, arg1, arg2);
        }
    };
})(jQuery);