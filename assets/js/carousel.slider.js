/**
 * Fullscreen Carousel Slider
 * Zira Project
 * (c)2018 https://github.com/ziracms/zira
 */
(function($){
    function ziraCarouselSlider(elem, opts) {
        this.elem = $(elem);
        this.container = $(this.elem).parent();
        $(this.container).addClass('carousel-slider-wrapper');
        this.options = {
            speed: 800,                         // animation speed
            interval: 50,                       // timer interval (for old devices)
            timeout: 5000,                      // animation timeout
            beforeTransitionDelay: 1000,        // transition duration before animation start
            loaderDelay: 500,                   // loader hiding delay
            linkText: '--->>>',                 // "Read more" link text
            frontWidthPercent: 95,              // center image width in percents during animation
            backWidthPercent: 80,               // left & right images width in percents during animation
            frontZ: 9,                          // front image z-index
            middleZ: 8,                         // middle image z-index
            backZ: 7,                           // back image z-index
            hiddenZ: 6,                         // hidden image z-index
            leftOffsetXPercent: 50,             // x-axis offset of left image in percents
            rightOffsetXPercent: 50,            // x-axis offset of right image in percents
            animationEasing: true,              // enable animation easing
            ignoreDragLength: 20                // minimal drag by x-axis in pixels
        };
        if (typeof opts != "undefined") {
            $.extend(this.options, opts);
        }
        $(window).resize(this.bind(this, this.resize));
        this.build();
    }
    
    ziraCarouselSlider.prototype.resize = function() {
        this.setLoaderPos();
        this.calcWindowSize();
        this.setBtnPos();
        this.setDescLinkPos();
    };
    
    ziraCarouselSlider.prototype.build = function() {
        this.links = [];
        this.images = [];
        this.descriptions = [];
        this.imageObjects = [];
        this.imagesLoaded = 0;
        this.initialized = false;
        this.active = 0;
        this.animating = false;
        this.inProgress = false;
        this.animStartTime = 0;
        this.dir = 1;
        this.supportAnimation = typeof window.requestAnimationFrame != "undefined";
        this.touchX = null;
        this.dragging = false;
        this.drag_progress = 0;
        this.progress_offset = 0;
        this.calcWindowSize();
        $(this.elem).children('li').each(this.bind(this, function(index, item){
            var img = $(item).find('img').eq(0);
            if ($(img).length>0) {
                this.images.push($(img).attr('src'));
                this.descriptions.push($(img).attr('title'));
            } else {
                return true;
            }
            var link = $(item).children('a');
            if ($(link).length>0) {
                this.links.push($(link).attr('href'));
            } else {
                this.links.push('');
            }
        }));
        if (this.images.length == 0) return;
        $(this.elem).hide();
        this.loadSlider();
    };
    
    ziraCarouselSlider.prototype.calcWindowSize = function() {
        this.width = $(this.container).width();
        this.height = $(this.container).height();
        this.k = this.width / this.height;
        this.backWidth = this.width * this.options.backWidthPercent / 100;
        this.backHeight = this.backWidth / this.k;
        this.frontWidth = this.width * this.options.frontWidthPercent / 100;
        this.frontHeight = this.frontWidth / this.k;
        this.leftOffsetX = this.backWidth * this.options.leftOffsetXPercent / 100;
        this.rightOffsetX = this.backWidth * this.options.rightOffsetXPercent / 100;
        this.leftItemRect = {
            width: this.backWidth,
            height: this.backHeight,
            left: -1 * this.leftOffsetX,
            top: (this.height - this.backHeight) / 2,
            zIndex: this.options.middleZ
        };
        this.centerItemRect = {
            width: this.frontWidth,
            height: this.frontHeight,
            left: (this.width - this.frontWidth) / 2,
            top: (this.height - this.frontHeight) / 2,
            zIndex: this.options.frontZ
        };
        this.rightItemRect = {
            width: this.backWidth,
            height: this.backHeight,
            left: this.width - this.rightOffsetX,
            top: (this.height - this.backHeight) / 2,
            zIndex: this.options.middleZ
        };
        this.animateXL = Math.abs(this.centerItemRect.left - this.leftItemRect.left);
        this.animateXR = Math.abs(this.rightItemRect.left - this.centerItemRect.left);
        this.animateW = this.frontWidth - this.backWidth;
        this.animateH = this.frontHeight - this.backHeight;
        this.backLeftItemRect = {
            width: this.positive(this.backWidth-this.animateW),
            height: this.positive(this.backHeight-this.animateH),
            left: -1 * this.leftOffsetX + this.animateXL,
            top: (this.height - this.positive(this.backHeight-this.animateH)) / 2,
            zIndex: this.options.hiddenZ
        };
        this.backRightItemRect = {
            width: this.positive(this.backWidth-this.animateW),
            height: this.positive(this.backHeight-this.animateH),
            left: this.width - this.rightOffsetX - this.animateXR,
            top: (this.height - this.positive(this.backHeight-this.animateH)) / 2,
            zIndex: this.options.hiddenZ
        };
    };
    
    ziraCarouselSlider.prototype.loadSlider = function() {
        this.showLoader();
        setTimeout(this.bind(this, function() {
            $(window).load(this.bind(this, this.createSlider));
            for (var i=0; i<this.images.length; i++) {
                var img = new Image();
                this.imageObjects.push(img);
                img.onload = this.bind(this, this.onImageLoad);
                img.src = this.images[i];
            }
        }), this.options.loaderDelay);
    };
    
    ziraCarouselSlider.prototype.onImageLoad = function() {
        this.imagesLoaded++;
        if (this.imagesLoaded >= this.images.length) {
            this.createSlider();
        }
    };
    
    ziraCarouselSlider.prototype.showLoader = function() {
        $(this.container).append('<div class="carousel-slider-loader-outer"></div><div class="carousel-slider-loader"><div class="carousel-slider-loader-inner"><div class="carousel-slider-loader-point"></div></div></div>');
        this.setLoaderPos();
        var loader = $(this.container).children('.carousel-slider-loader');
        var oloader = $(this.container).children('.carousel-slider-loader-outer');
        $(loader).show();
        $(oloader).show();
    };
    
    ziraCarouselSlider.prototype.setLoaderPos = function() {
        var loader = $(this.container).children('.carousel-slider-loader');
        var oloader = $(this.container).children('.carousel-slider-loader-outer');
        if ($(loader).length==0 || $(oloader).length==0) return;
        $(loader).css({
            left: ($(this.container).width() - $(loader).outerWidth())/2,
            top: ($(this.container).height() - $(loader).outerHeight())/2,
        });
        $(oloader).css({
            left: ($(this.container).width() - $(oloader).outerWidth())/2,
            top: ($(this.container).height() - $(oloader).outerHeight())/2,
        });
    };
    
    ziraCarouselSlider.prototype.hideLoader = function() {
        $(this.container).children('.carousel-slider-loader').remove();
        $(this.container).children('.carousel-slider-loader-outer').remove();
    };
    
    ziraCarouselSlider.prototype.createSlider = function() {
        if (this.initialized) return;
        this.initialized = true;
        $(this.container).append('<div class="carousel-slider-box" />');
        this.box = $(this.container).children('.carousel-slider-box');
        for (var i=0; i<this.imageObjects.length; i++) {
            var link = this.links[i];
            var description = this.descriptions[i];
            var description_html = '';
            if (typeof description != "undefined" && description.length>0) {
                description_html = '<p><span>'+description+'</span></p>';
            }
            if (typeof link != "undefined" && link.length>0) {
                description_html += '<a href="'+link+'">'+this.options.linkText+'</a>';
            }
            $(this.box).append('<div class="carousel-slider-box-item" style="background-image:url('+this.imageObjects[i].src+')">'+description_html+'</div>');
        }
        this.setDescLinkPos();
        this.hideLoader();
        $(this.box).show();
        $(this.box).children('.carousel-slider-box-item').eq(this.active).addClass('active');
        if (this.imageObjects.length > 1) {
            $(this.container).append('<a class="carousel-slider-btn carousel-slider-prev-btn" href="javascript:void(0)"></a>');
            this.prevBtn = $(this.container).children('.carousel-slider-prev-btn');
            $(this.container).append('<a class="carousel-slider-btn carousel-slider-next-btn" href="javascript:void(0)"></a>');
            this.nextBtn = $(this.container).children('.carousel-slider-next-btn');
            this.setBtnPos();
            $(this.prevBtn).click(this.bind(this, this.prev));
            $(this.nextBtn).click(this.bind(this, this.next));
            if (this.active>0) this.enablePrevBtn();
            if (this.active<this.imageObjects.length-1) this.enableNextBtn();
            this.bindItemsTouchEvents();
            this.startTimer();
            if (!this.supportAnimation) {
                this.loopTimer = window.setInterval(this.bind(this, this.animate), this.options.interval);
            }
        }
        $(this.container).append('<a class="carousel-slider-close-btn" href="javascript:void(0)"></a>');
        $(this.container).children('.carousel-slider-close-btn').click(this.bind(this, this.destroy));
    };
    
    ziraCarouselSlider.prototype.bindItemsTouchEvents = function() {
        $(this.box).children('.carousel-slider-box-item').bind('dragstart', function(e){
            e.stopPropagation();
            e.preventDefault();
        });
        if (typeof window.ontouchstart == "undefined") {
            $(this.box).children('.carousel-slider-box-item').mousedown(this.bind(this, function(e){
                if (typeof e.pageX == "undefined") return;
                this.onDragStart(e.pageX);
            }));
            $(this.box).children('.carousel-slider-box-item').mousemove(this.bind(this, function(e){
                if (typeof e.pageX == "undefined") return;
                this.onDragMove(e.pageX);
            }));
            $(this.box).children('.carousel-slider-box-item').mouseup(this.bind(this, function(e){
                this.onDragEnd();
            }));
            $(this.box).children('.carousel-slider-box-item').mouseout(this.bind(this, function(e){
                this.onDragEnd();
            }));
        } else {
            $(this.box).children('.carousel-slider-box-item').bind('touchstart', this.bind(this, function(e){
                if (typeof(e.originalEvent)=="undefined" || typeof(e.originalEvent.touches)=="undefined" || typeof(e.originalEvent.touches[0].pageX)=="undefined") return;
                this.onDragStart(e.originalEvent.touches[0].pageX);
            }));
            $(this.box).children('.carousel-slider-box-item').bind('touchmove', this.bind(this, function(e){
                if (typeof(e.originalEvent)=="undefined" || typeof(e.originalEvent.touches)=="undefined" || typeof(e.originalEvent.touches[0].pageX)=="undefined") return;
                this.onDragMove(e.originalEvent.touches[0].pageX);
            }));
            $(this.box).children('.carousel-slider-box-item').bind('touchend', this.bind(this, function(e){
                this.onDragEnd();
            }));
        }
    };
    
    ziraCarouselSlider.prototype.onDragStart = function(pageX) {
        if (!this.initialized || this.inProgress) return;
        this.touchX = pageX;
    };
    
    ziraCarouselSlider.prototype.onDragMove = function(pageX) {
        if (!this.initialized) return;
        if (this.touchX === null) return;
        var dx = this.touchX - pageX;
        var blocked = false;
        if (dx < 0 && this.active == 0) blocked = true;
        if (dx > 0 && this.active == this.imageObjects.length-1) blocked = true;
        if (!this.dragging && Math.abs(dx) >= this.options.ignoreDragLength && !blocked) {
            this.disablePrevBtn();
            this.disableNextBtn();
            this.inProgress = true;
            this.dragging = true;
            this.stopTimer();
            this.setItemsPosForAnimate(this.animationStart);
        }
        if (this.dragging) {
            if (dx < 0) this.dir = -1;
            else this.dir = 1;
            if (this.active==0) this.dir = 1;
            if (this.active==this.imageObjects.length-1) this.dir=-1;
            this.drag_progress = Math.abs(dx) / this.width;
        }
    };
    
    ziraCarouselSlider.prototype.onDragEnd = function() {
        if (this.dragging) {
            this.animStartTime = (new Date()).getTime();
            this.progress_offset = this.drag_progress;
        }
        this.touchX = null;
        this.dragging = false;
        this.drag_progress = 0;
    };
    
    ziraCarouselSlider.prototype.setDescLinkPos = function() {
        if ($(this.box).length==0) return;
        $(this.box).children('.carousel-slider-box-item').children('a').each(this.bind(this, function(index, item){
            $(item).css({
                left: ($(this.box).width() - $(item).outerWidth())/2
            });
        }));
    };
    
    ziraCarouselSlider.prototype.setBtnPos = function() {
        if ($(this.prevBtn).length>0) {
            $(this.prevBtn).css('top', (this.height-$(this.prevBtn).height())/2);
        }
        if ($(this.nextBtn).length>0) {
            $(this.nextBtn).css('top', (this.height-$(this.nextBtn).height())/2);
        }
    };
    
    ziraCarouselSlider.prototype.disablePrevBtn = function() {
        if ($(this.prevBtn).length>0) {
            $(this.prevBtn).removeClass('enabled');
        }
    };
    
    ziraCarouselSlider.prototype.disableNextBtn = function() {
        if ($(this.nextBtn).length>0) {
            $(this.nextBtn).removeClass('enabled');
        }
    };
    
    ziraCarouselSlider.prototype.enablePrevBtn = function() {
        if ($(this.prevBtn).length>0) {
            $(this.prevBtn).addClass('enabled');
        }
    };
    
    ziraCarouselSlider.prototype.enableNextBtn = function() {
        if ($(this.nextBtn).length>0) {
            $(this.nextBtn).addClass('enabled');
        }
    };
    
    ziraCarouselSlider.prototype.startTimer = function() {
        this.timer = window.setTimeout(this.bind(this, this.change), this.options.timeout);
    };
    
    ziraCarouselSlider.prototype.stopTimer = function() {
        try {
            window.clearTimeout(this.timer);
        } catch(err) {}
    };
    
    ziraCarouselSlider.prototype.change = function() {
        if (!this.initialized || this.inProgress || this.dragging) return;
        this.disablePrevBtn();
        this.disableNextBtn();
        this.inProgress = true;
        this.setItemsPosForAnimate(this.animationStart);
    };
    
    ziraCarouselSlider.prototype.next = function() {
        if (!this.initialized || this.inProgress || this.dragging) return;
        if (this.active >= this.imageObjects.length-1) return;
        this.disablePrevBtn();
        this.disableNextBtn();
        this.stopTimer();
        this.inProgress = true;
        this.dir = 1;
        this.setItemsPosForAnimate(this.animationStart);
    };
    
    ziraCarouselSlider.prototype.prev = function() {
        if (!this.initialized || this.inProgress || this.dragging) return;
        if (this.active <= 0) return;
        this.disablePrevBtn();
        this.disableNextBtn();
        this.stopTimer();
        this.inProgress = true;
        this.dir = -1;
        this.setItemsPosForAnimate(this.animationStart);
    };
    
    ziraCarouselSlider.prototype.setItemsPosForAnimate = function(callback) {
        var left = null;
        var right = null;
        var backLeft = null;
        var backRight = null;
        if (this.active > 0) left = this.active - 1;
        if (this.active < this.imageObjects.length - 1) right = this.active + 1;
        if (this.dir > 0 && this.active < this.imageObjects.length - 2) backRight = this.active + 2;
        if (this.dir < 0 && this.active > 1) backLeft = this.active - 2;
        if (left !== null) {
            var leftCoords = this.getItemCoordStyle(left);
            $(this.box).children('.carousel-slider-box-item').eq(left).css(leftCoords).addClass('animating').addClass('carousel-left-item');
        }
        if (right !== null) {
            var rightCoords = this.getItemCoordStyle(right);
            $(this.box).children('.carousel-slider-box-item').eq(right).css(rightCoords).addClass('animating').addClass('carousel-right-item');
        }
        if (backLeft !== null) {
            var backLeftCoords = this.getItemCoordStyle(backLeft);
            $(this.box).children('.carousel-slider-box-item').eq(backLeft).css(backLeftCoords).addClass('animating').addClass('carousel-back-left-item');
        }
        if (backRight !== null) {
            var backRightCoords = this.getItemCoordStyle(backRight);
            $(this.box).children('.carousel-slider-box-item').eq(backRight).css(backRightCoords).addClass('animating').addClass('carousel-back-right-item');
        }
        var centerCoordsTo = this.getItemCoordStyle(this.active);
        var centerCoordsFrom = {
            left: 0,
            top: 0,
            width: this.width,
            height: this.height,
            zIndex: this.options.frontZ
        };
        $(this.box).children('.carousel-slider-box-item').eq(this.active).css(centerCoordsFrom).addClass('animating').addClass('carousel-center-item');
        var transitionDelay = this.options.beforeTransitionDelay;
        if (this.dragging) transitionDelay = 0;
        $(this.box).children('.carousel-slider-box-item').eq(this.active).animate(centerCoordsTo, transitionDelay, this.bind(this, function(){
            $(this.box).children('.carousel-slider-box-item').removeClass('active');
            callback.call(this);
        }));
    };
    
    ziraCarouselSlider.prototype.getItemCoordStyle = function(i) {
        if (i==this.active-2) {
            return this.backLeftItemRect;
        } else if (i==this.active-1) {
            return this.leftItemRect;
        } else if (i==this.active) {
            return this.centerItemRect;
        } else if (i==this.active+1) {
            return this.rightItemRect;
        } else if (i==this.active+2) {
            return this.backRightItemRect;
        } else {
            return {};
        }
    };
    
    ziraCarouselSlider.prototype.animationStart = function() {
        if (this.animating) return;
        this.animStartTime = (new Date()).getTime();
        this.animating = true;
        if (this.supportAnimation) {
            requestAnimationFrame(this.bind(this, this.animate));
        }
    };
    
    ziraCarouselSlider.prototype.animate = function() {
        if (!this.initialized || !this.animating) return;
        var final = false;
        if (!this.drag_progress) {
            var currentTime = (new Date()).getTime();
            var progress = (currentTime - this.animStartTime) / this.options.speed;
            if (currentTime - this.animStartTime >= this.options.speed) {
                final = true;
                progress = 1;
            }
            progress += this.progress_offset;
            if (progress > 1) progress = 1;
        } else {
            var progress = this.drag_progress;
        }
        if (this.options.animationEasing) {
            if (typeof Math.cbrt != "undefined") {
                var progressOut = Math.cbrt(progress);
            } else {
                var progressOut = Math.sqrt(progress);
            }
            var progressIn = Math.pow(progress, 3);
        } else {
            var progressOut = progress;
            var progressIn = progress;
        }
        var leftItem = $(this.box).children('.carousel-slider-box-item.animating.carousel-left-item');
        var centerItem = $(this.box).children('.carousel-slider-box-item.animating.carousel-center-item');
        var rightItem = $(this.box).children('.carousel-slider-box-item.animating.carousel-right-item');
        var backLeftItem = $(this.box).children('.carousel-slider-box-item.animating.carousel-back-left-item');
        var backRightItem = $(this.box).children('.carousel-slider-box-item.animating.carousel-back-right-item');
        if ($(leftItem).length>0) {
            var lleft = this.leftItemRect.left + progressIn * this.animateXL;
            var lwidth = this.positive(this.backWidth - progressOut * this.animateW * this.dir);
            var lheight = this.positive(this.backHeight - progressOut * this.animateH * this.dir);
            var ltop = (this.height - lheight) / 2;
            var lz = this.options.middleZ;
            if (this.dir < 0 && progress > .5) {
                lz = this.options.frontZ;
            } else if (this.dir > 0 && progress > .5) {
                lz = this.options.backZ;
            }
            $(leftItem).css({
                left: lleft,
                top: ltop,
                width: lwidth,
                height: lheight,
                zIndex: lz
            });
        }
        if ($(backLeftItem).length>0) {
            var blleft = this.backLeftItemRect.left - progressOut * this.animateXL;
            var blwidth = this.backLeftItemRect.width + progressIn * this.animateW;
            var blheight = this.backLeftItemRect.height + progressIn * this.animateH;
            var bltop = (this.height - blheight) / 2;
            var blz = this.options.hiddenZ;
            if (progress > .5) {
                blz = this.options.backZ;
            }
            $(backLeftItem).css({
                left: blleft,
                top: bltop,
                width: blwidth,
                height: blheight,
                zIndex: blz
            });
        }
        if ($(centerItem).length>0) {
            if (this.dir>0) {
                var cleft = this.centerItemRect.left - progressOut * this.animateXL;
            } else {
                var cleft = this.centerItemRect.left + progressOut * this.animateXR;
            }
            var cwidth = this.positive(this.frontWidth - progressIn * this.animateW);
            var cheight = this.positive(this.frontHeight - progressIn * this.animateH);
            var ctop = (this.height - cheight) / 2;
            var cz = this.options.frontZ;
            if (progress > .5) {
                cz = this.options.middleZ;
            }
            $(centerItem).css({
                left: cleft,
                top: ctop,
                width: cwidth,
                height: cheight,
                zIndex: cz
            });
        }
        if ($(rightItem).length>0) {
            var rleft = this.rightItemRect.left - progressIn * this.animateXR;
            var rwidth = this.positive(this.backWidth + progressOut * this.animateW * this.dir);
            var rheight = this.positive(this.backHeight + progressOut * this.animateH * this.dir);
            var rtop = (this.height - rheight) / 2;
            var rz = this.options.middleZ;
            if (this.dir > 0 && progress > .5) {
                rz = this.options.frontZ;
            } else if (this.dir < 0 && progress > .5) {
                rz = this.options.backZ;
            }
            $(rightItem).css({
                left: rleft,
                top: rtop,
                width: rwidth,
                height: rheight,
                zIndex: rz
            });
        }
        if ($(backRightItem).length>0) {
            var brleft = this.backRightItemRect.left + progressOut * this.animateXR;
            var brwidth = this.backRightItemRect.width + progressIn * this.animateW;
            var brheight = this.backRightItemRect.height + progressIn * this.animateH;
            var brtop = (this.height - brheight) / 2;
            var brz = this.options.hiddenZ;
            if (progress > .5) {
                brz = this.options.backZ;
            }
            $(backRightItem).css({
                left: brleft,
                top: brtop,
                width: brwidth,
                height: brheight,
                zIndex: brz
            });
        }
        if (final) {
            this.finishAnimation();
            return;
        }
        if (this.supportAnimation) {
            requestAnimationFrame(this.bind(this, this.animate));
        }
    };
    
    ziraCarouselSlider.prototype.finishAnimation = function() {
        this.animating = false;
        var centerCoordsTo = {
            left: 0,
            top: 0,
            width: this.width,
            height: this.height,
            zIndex: this.options.frontZ
        };
        if (this.dir > 0) {
            this.active++;
            if (this.active>=this.imageObjects.length) {
                this.active = 0;
                return;
            }
            $(this.box).children('.carousel-slider-box-item.animating.carousel-right-item').animate(centerCoordsTo, this.options.beforeTransitionDelay, this.bind(this, function(){
                this.onAnimationComplete();
            }));
        } else {
            this.active--;
            if (this.active<0) {
                this.active = this.imageObjects.length-1;
                return;
            }
            $(this.box).children('.carousel-slider-box-item.animating.carousel-left-item').animate(centerCoordsTo, this.options.beforeTransitionDelay, this.bind(this, function(){
                this.onAnimationComplete();
            }));
        }
    };
    
    ziraCarouselSlider.prototype.onAnimationComplete = function() {
        $(this.box).children('.carousel-slider-box-item').removeClass('animating').removeClass('carousel-left-item').removeClass('carousel-right-item').removeClass('carousel-center-item').removeClass('carousel-back-left-item').removeClass('carousel-back-right-item');
        $(this.box).children('.carousel-slider-box-item').eq(this.active).addClass('active');
        $(this.box).children('.carousel-slider-box-item').css({
            width: '100%',
            height: '100%'
        });
        this.progress_offset = 0;
        this.inProgress = false;
        if (this.active <=0 || this.active >= this.imageObjects.length-1) {
            this.dir *= -1;
        }
        this.startTimer();
        if (this.active>0) this.enablePrevBtn();
        if (this.active<this.imageObjects.length-1) this.enableNextBtn();
    };
    
    ziraCarouselSlider.prototype.positive = function(value) {
        if (value < 0) return 0;
        return value;
    };
    
    ziraCarouselSlider.prototype.bind = function(object, method) {
        return function(arg1, arg2) {
            method.call(object, arg1, arg2);
        };
    };
    
    ziraCarouselSlider.prototype.destroy = function() {
        this.initialized = false;
        this.stopTimer();
        try {
            window.clearInterval(this.loopTimer);
        } catch (err) {};
        $(this.container).trigger('carousel.slider.destroy');
        $(this.container).animate({height: 0}, 500, this.bind(this, function() {
            $(this.container).remove();
        }));
    };
    
    $.fn.ziraCarouselSlider = function(opts) {
        return new ziraCarouselSlider($(this), opts);
    };
})(jQuery);