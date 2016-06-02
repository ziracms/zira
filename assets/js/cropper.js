/**
 * Zira project.
 * Image cropper
 * (c)2015 http://dro1d.ru
 *
 * Usage:
 * $(document).ready(function(){
 *     $('img').cropper([options]);
 * });
 */

(function($){
    Cropper = function(element, options) {
        this.container_class = 'image-cropper';
        this.selector_class = 'image-cropper-selector';
        this.resizer_class = 'image-cropper-resizer';
        this.previewer_class = 'image-cropper-preview';
        this.custom_previewer_class = 'image-cropper-preview-custom';
        this.body_on_resize_class = 'image-cropper-resizing';
        this.body_on_dragging_class = 'image-cropper-dragging';
        this.events = {};
        this.disabled = false;

        var defaults = {
            'src_w': null,     // source image container width
            'src_h': null,     // source image container height
            'dst_w': 100,      // preview image container width
            'dst_h': 100,      // preview image container height
            'sel_w': null,     // cropping box width on start
            'sel_h': null,     // cropping box height on start
            'sel_mw': null,    // cropping box min width
            'sel_mh': null,    // cropping box min height
            'res_s': 10,       // resizing box size
            'fixed': true,     // keep aspect ratio
            'preview': true,   // preview result image
            'preview_m': 7,    // preview box margin
            'previewer': null, // custom preview container id
            'input_x': null,   // x-offset storage input id
            'input_y': null,   // y-offset storage input id
            'input_w': null,   // width storage input id
            'input_h': null,   // height storage input id
            'block_mode': false
        };
        if(typeof(options)!="undefined") {
            this.options = $.extend(defaults, options);
        } else {
            this.options = defaults;
        }

        var src = $(element).attr('src');
        if (!this.options.src_w) this.options.src_w = $(element).width();
        if (!this.options.src_h) this.options.src_h = $(element).height();
        if (!this.options.block_mode) {
            $(element).after('<div class="'+this.container_class+'"><img /></div>');
            this.container = $(element).next('.'+this.container_class);
            $(this.container).children('img').load(this.bind(this,this.initialize));
            $(this.container).children('img').css({
                'width': this.options.src_w,
                'height': this.options.src_h
            });
            this.source = $(this.container).children('img');
            $(element).remove();
            $(this.container).children('img').attr('src',src);
        } else {
            if ($(element).parent().css('position')=='static') $(element).parent().css('position','relative');
            $(element).after('<div class="'+this.container_class+'"></div>');
            this.container = $(element).next('.'+this.container_class);
            $(this.container).css({
                'position': 'absolute',
                'left': $(element).offset().left - $(element).parent().offset().left,
                'top': $(element).offset().top - $(element).parent().offset().top
            });
            this.source = $(element);
            this.initialize();
        }
    };

    Cropper.prototype.initialize = function() {
        $(this.container).css({
            'width':this.options.src_w,
            'height':this.options.src_h
        });
        this.container_x = $(this.container).offset().left;
        this.container_y = $(this.container).offset().top;

        if (!this.options.sel_w) this.options.sel_w = this.options.dst_w;
        if (!this.options.sel_h) this.options.sel_h = this.options.dst_h;
        if (!this.options.sel_mw) this.options.sel_mw = this.options.sel_w;
        if (!this.options.sel_mh) this.options.sel_mh = this.options.sel_h;
        if (this.options.sel_w>this.options.src_w) this.options.sel_w = this.options.src_w;
        if (this.options.sel_h>this.options.src_h) this.options.sel_h = this.options.src_h;
        if (this.options.sel_mw>this.options.sel_w) this.options.sel_mw = this.options.sel_w;
        if (this.options.sel_mh>this.options.sel_h) this.options.sel_mh = this.options.sel_h;
        $(this.container).append('<div class="'+this.selector_class+'"></div>');
        this.selector = $(this.container).children('.'+this.selector_class);
        $(this.selector).css({
            'width':this.options.sel_w,
            'height':this.options.sel_h,
            'left': (this.options.src_w-this.options.sel_w)/2,
            'top': (this.options.src_h-this.options.sel_h)/2
        });
        this.selector_w = this.options.sel_w;
        this.selector_h = this.options.sel_h;

        if (this.options.input_w &&
            this.options.input_h &&
            this.options.input_x &&
            this.options.input_y
        ) {
            var input_w = $('#'+this.options.input_w).val();
            var input_h = $('#'+this.options.input_h).val();
            var input_x = $('#'+this.options.input_x).val();
            var input_y = $('#'+this.options.input_y).val();
            if (input_w.length && input_h.length && input_x.length && input_y.length) {
                input_w = parseFloat(input_w) * this.options.src_w / 100;
                input_h = parseFloat(input_h) * this.options.src_h / 100;
                input_x = parseFloat(input_x) * this.options.src_w / 100;
                input_y = parseFloat(input_y) * this.options.src_h / 100;
                $(this.selector).css({
                    'width':input_w,
                    'height':input_h,
                    'left': input_x,
                    'top': input_y
                });
                this.selector_w = input_w;
                this.selector_h = input_h;
            }
        }
        this.selector_x = $(this.selector).offset().left;
        this.selector_y = $(this.selector).offset().top;

        $(this.selector).append('<div class="'+this.resizer_class+'"></div>');
        this.resizer = $(this.selector).children('.'+this.resizer_class);
        this.resizer_w = this.options.res_s;
        this.resizer_h = this.options.res_s;
        $(this.resizer).css({
            'width': this.resizer_w,
            'height': this.resizer_h,
            'right': -(this.resizer_w/2),
            'bottom': -(this.resizer_h/2)
        });
        this.resizer_x = $(this.resizer).offset().left;
        this.resizer_y = $(this.resizer).offset().top;

        if (this.options.preview) {
            if (!this.options.previewer) {
                $(this.container).append('<div class="'+this.previewer_class+'"><img /></div>');
                this.previewer = $(this.container).children('.'+this.previewer_class);
                $(this.previewer).css({
                    'width':this.options.dst_w,
                    'height':this.options.dst_h,
                    'right': -this.options.dst_w-this.options.preview_m,
                    'top': 0
                });
            } else {
                this.previewer = $('#'+this.options.previewer);
                $(this.previewer).addClass(this.custom_previewer_class).html('<img />');
                $(this.previewer).css({
                    'width':this.options.dst_w,
                    'height':this.options.dst_h
                });
                if ($(this.previewer).css('position') == 'static') {
                    $(this.previewer).css('position','relative');
                }
            }

            $(this.previewer).children('img').load(this.bind(this,this.update));
            $(this.previewer).children('img').attr('src',$(this.source).attr('src'));
        } else {
            this.previewer = null;
        }

        this.dragging = false;
        this.resizing = false;
        this.dragX = 0;
        this.dragY = 0;

        this.touchesEnabled = false;
        this.bindEvents();
    };

    Cropper.prototype.updateOffsets = function() {
        this.container_x = $(this.container).offset().left;
        this.container_y = $(this.container).offset().top;
        this.selector_x = $(this.selector).offset().left;
        this.selector_y = $(this.selector).offset().top;
        this.resizer_x = $(this.resizer).offset().left;
        this.resizer_y = $(this.resizer).offset().top;
    };

    Cropper.prototype.onTouchStartEvent = function(e) {
        this.touchesEnabled = true;
        e.pageX = e.originalEvent.touches[0].pageX;
        e.pageY = e.originalEvent.touches[0].pageY;
        this.onMouseDown(e);
    };

    Cropper.prototype.onTouchMoveEvent = function(e) {
        e.pageX = e.originalEvent.touches[0].pageX;
        e.pageY = e.originalEvent.touches[0].pageY;
        this.onMouseMove(e);
    };

    Cropper.prototype.onTouchEndEvent = function(e) {
        this.onMouseUp(e);
    };

    Cropper.prototype.onMouseDownEvent = function(e) {
        if (this.touchesEnabled) return;
        this.onMouseDown(e);
    };

    Cropper.prototype.onMouseMoveEvent = function(e) {
        if (this.touchesEnabled) return;
        this.onMouseMove(e);
    };

    Cropper.prototype.onMouseUpEvent = function(e) {
        if (this.touchesEnabled) return;
        this.onMouseUp(e);
    };

    Cropper.prototype.onMouseLeaveEvent = function(e) {
        $('body').trigger('mouseup');
    };

    Cropper.prototype.bindEvents = function() {
        this.events['touchstart'] = this.bind(this, this.onTouchStartEvent);
        this.events['touchmove'] = this.bind(this, this.onTouchMoveEvent);
        this.events['touchend'] = this.bind(this, this.onTouchEndEvent);
        this.events['mousedown'] = this.bind(this, this.onMouseDownEvent);
        this.events['mousemove'] = this.bind(this, this.onMouseMoveEvent);
        this.events['mouseup'] = this.bind(this, this.onMouseUpEvent);
        this.events['mouseleave'] = this.bind(this, this.onMouseLeaveEvent);

        $('body').bind('touchstart', this.events['touchstart']);
        $('body').bind('touchmove', this.events['touchmove']);
        $('body').bind('touchend', this.events['touchend']);
        $('body').mousedown(this.events['mousedown']);
        $('body').mousemove(this.events['mousemove']);
        $('body').mouseup(this.events['mouseup']);
        $('body').mouseleave(this.events['mouseleave']);
    };

    Cropper.prototype.unbindEvents = function() {
        $('body').unbind('touchstart', this.events['touchstart']);
        $('body').unbind('touchmove', this.events['touchmove']);
        $('body').unbind('touchend', this.events['touchend']);
        $('body').unbind('mousedown', this.events['mousedown']);
        $('body').unbind('mousemove', this.events['mousemove']);
        $('body').unbind('mouseup', this.events['mouseup']);
        $('body').unbind('mouseleave', this.events['mouseleave']);
    };

    Cropper.prototype.bind = function(object, method) {
        return function(arg) {
            return method.call(object,arg);
        }
    };

    Cropper.prototype.onMouseDown = function(e) {
        if (this.disabled) return;
        this.updateOffsets();
        if (
            e.pageX > this.resizer_x &&
                e.pageX < this.resizer_x + this.resizer_w &&
                e.pageY > this.resizer_y &&
                e.pageY < this.resizer_y + this.resizer_h
            ) {
            this.resizing = true;
            this.dragging = false;
            this.dragX = e.pageX;
            this.dragY = e.pageY;
            e.stopPropagation();
            e.preventDefault();
            $('body').addClass(this.body_on_resize_class);
        }else if (
            e.pageX > this.selector_x &&
                e.pageX < this.selector_x + this.selector_w &&
                e.pageY > this.selector_y &&
                e.pageY < this.selector_y + this.selector_h
            ) {
            this.dragging = true;
            this.resizing = false;
            this.dragX = e.pageX;
            this.dragY = e.pageY;
            e.stopPropagation();
            e.preventDefault();
            $('body').addClass(this.body_on_dragging_class);
        } else if (
            e.pageX > this.container_x &&
                e.pageX < this.container_x + this.options.src_w &&
                e.pageY > this.container_y &&
                e.pageY < this.container_y + this.options.src_h
            ) {
            this.dragging = false;
            this.resizing = false;
            e.stopPropagation();
            e.preventDefault();
        } else {
            this.dragging = false;
        }
    };

    Cropper.prototype.onMouseMove = function(e) {
        if (this.dragging || this.resizing) {
            var dx = e.pageX - this.dragX;
            var dy = e.pageY - this.dragY;
            if (this.dragging) {
                this.moveSelector(dx, dy);
            } else if (this.resizing) {
                this.resizeSelector(dx, dy);
            }
            this.dragX += dx;
            this.dragY += dy;
        }
    };

    Cropper.prototype.onMouseUp = function(e) {
        if (this.dragging || this.resizing) {
            this.resizer_x = $(this.resizer).offset().left;
            this.resizer_y = $(this.resizer).offset().top;
            this.dragging = false;
            this.resizing = false;
            $('body').removeClass(this.body_on_resize_class);
        }
    };

    Cropper.prototype.moveSelector = function(dx, dy) {
        var x = this.selector_x - this.container_x + dx;
        var y = this.selector_y - this.container_y + dy;

        if (x<0) x=0;
        if (y<0) y=0;
        if (x>this.options.src_w-this.selector_w) x=this.options.src_w-this.selector_w;
        if (y>this.options.src_h-this.selector_h) y=this.options.src_h-this.selector_h;

        $(this.selector).css({
            'left': x,
            'top': y
        });

        this.selector_x = x + this.container_x;
        this.selector_y = y + this.container_y;

        this.update();
    };

    Cropper.prototype.resizeSelector = function(dx, dy) {
        this.selector_w += dx;
        this.selector_h += dy;

        if (this.selector_w<this.options.sel_mw) this.selector_w=this.options.sel_mw;
        if (this.selector_h<this.options.sel_mh) this.selector_h=this.options.sel_mh;
        if (this.selector_x+this.selector_w>this.container_x+this.options.src_w) {
            this.selector_w = this.container_x+this.options.src_w-this.selector_x;
        }
        if (this.selector_y+this.selector_h>this.container_y+this.options.src_h) {
            this.selector_h = this.container_y+this.options.src_h-this.selector_y;
        }

        if (this.options.fixed) {
            var selector_w = this.selector_h * this.options.sel_w / this.options.sel_h;
            var selector_h = this.selector_h;
            if (this.selector_x+selector_w>this.container_x+this.options.src_w) {
                selector_h = this.selector_w * this.options.sel_h / this.options.sel_w;
                selector_w = this.selector_w;
            }
            this.selector_w = selector_w;
            this.selector_h = selector_h;
        }

        $(this.selector).css({
            'width': this.selector_w,
            'height': this.selector_h
        });

        this.update();
    };

    Cropper.prototype.getRect = function() {
        return {
            x: ((this.selector_x - this.container_x) / this.options.src_w * 100).toFixed(2),
            y: ((this.selector_y - this.container_y) / this.options.src_h * 100).toFixed(2),
            w: (this.selector_w / this.options.src_w * 100).toFixed(2),
            h: (this.selector_h / this.options.src_h * 100).toFixed(2)
        };
    };

    Cropper.prototype.update = function() {
        var rect = this.getRect();
        if (this.options.input_w) $('#'+this.options.input_w).val(rect.w);
        if (this.options.input_h) $('#'+this.options.input_h).val(rect.h);
        if (this.options.input_x) $('#'+this.options.input_x).val(rect.x);
        if (this.options.input_y) $('#'+this.options.input_y).val(rect.y);
        if (this.options.preview) this.preview(rect);
    };

    Cropper.prototype.preview = function(rect) {
        var width = Math.round(this.options.dst_w * 100 / rect.w);
        var height = Math.round(this.options.dst_h * 100 / rect.h);
        var left = -Math.round(rect.x * width / 100);
        var top = -Math.round(rect.y * height / 100);

        $(this.previewer).children('img').css({
            'width': width,
            'height': height,
            'left': left,
            'top': top
        });
    };

    Cropper.prototype.destroy = function() {
        $(this.container).remove();
        this.unbindEvents();
    };

    $.fn.cropper = function(options) {
        if (!$(this).attr('src')) {
            throw 'Cropper should be attached to image';
        }
        return new Cropper($(this), options);
    };
})(jQuery);