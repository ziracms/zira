(function($){
    $(document).ready(function(){
        $('body').append('<div style="position:fixed;width:100%;height:100%;top:0;left:0;z-index:2499"></div>');
        
        if ($('head style').length>0) {
            parseStyles($('head style').text());
        }
        
        var colorpicker_size = 42;
        var colorpicker_wnd_size = 250;
        var container_x = $('#content').offset().left;

        // body bg color
        if ($('body').length>0 && $('#main-container-wrapper').length>0 && $('#main-container').length>0) {
            var body_bg = $('body').css('backgroundColor');
            var body_cx = colorpicker_size/2;
            var body_cy = $('header').offset().top+$('header').outerHeight()+colorpicker_size;
            $('body').append('<div class="designer_colorpicker" id="body-designer-colorpicker" title="'+t('Background color')+'"></div>');
            $('#body-designer-colorpicker').css({'left':body_cx,'top':body_cy});
            $('#body-designer-colorpicker').tooltip();
            designer_colorpicker($('#body-designer-colorpicker'), body_bg, function(color){
                $('body').css('background', color);
                $('#main-container-wrapper').css('background', 'none');
                $('#main-container').css('background', 'none');
                setBackgroundColorStyle('body', color);
                setBackgroundStyle('#main-container-wrapper', 'none');
                setBackgroundStyle('#main-container', 'none');
            }, 'left');
        }
        
        // header bg color
        if ($('header').length>0) {
            var header_bg = $('header').css('backgroundColor');
            var header_cx = $('header').offset().left+($('header').outerWidth()-colorpicker_size)/2;
            var header_cy = $('header').offset().top+($('header').outerHeight()-colorpicker_size)/2;
            $('body').append('<div class="designer_colorpicker" id="header-designer-colorpicker" title="'+t('Header color')+'"></div>');
            $('#header-designer-colorpicker').css({'left':header_cx,'top':header_cy});
            $('#header-designer-colorpicker').tooltip();
            designer_colorpicker($('#header-designer-colorpicker'), header_bg, function(color){
                $('header').css('background', color);
                setBackgroundColorStyle('header', color);
            });
        }
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
    
    var setBackgroundColorStyle = function(element, value, addOnly) {
        if (typeof (addOnly) == "undefined") addOnly = false;
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bgcolor']='background-color:' + value + ';';
        if (!addOnly) {
            setBackgroundStyle(element, value);
        }
    };
    
    var removeBackgroundColorStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bgcolor']=null;
    };
    
    var setBackgroundGradientStyle = function(element, value1, value2, addOnly) {
        if (typeof (addOnly) == "undefined") addOnly = false;
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bggradient']='background-image:linear-gradient(to bottom,' + value1 + ',' + value2 + ');';
        if (!addOnly) {
            removeBackgroundImageStyle(element);
            removeBackgroundStyle(element);
        }
    };
    
    var removeBackgroundGradientStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bggradient']=null;
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
    
    var removeBackgroundImageStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bgimage']=null;
    };
    
    var setBackgroundStyle = function(element, value, addOnly) {
        if (typeof (addOnly) == "undefined") addOnly = false;
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bg']='background:' + value + ';';
        if (!addOnly) {
            removeBackgroundGradientStyle(element);
            removeBackgroundImageStyle(element);
        }
    };
    
    var removeBackgroundStyle = function(element) {
        if (typeof(window.editorStyles[element])=="undefined") window.editorStyles[element] = {};
        window.editorStyles[element]['bg']=null;
    };
    
    var parseStyles = function(code) {
        code = code.replace(/\s*([{}\(\);:,])\s*/g,'$1').toLowerCase();
        var regexp = new RegExp('([a-z0-9_#\.\-]+(?:[:][a-z]+)?)[{]([^}]+)[}]', 'g');
        var m, m2, m3, m4, element, regexp2, prop, value, regexp3, regexp4;
        while (m = regexp.exec(code)) {
            element = m[1];
            regexp2 = new RegExp('([a-z\-]+)[:]([^;]+)[;]', 'g');
            while(m2 = regexp2.exec(m[2])) {
                prop = m2[1];
                value = m2[2];
                if (prop == 'background-color') {
                    setBackgroundColorStyle(element, value, true);
                } else if (prop == 'background-image') {
                    regexp3 = new RegExp('([a-z\-]+)[\(]([^\)]+)[\)]','g');
                    if (m3 = regexp3.exec(value)) {
                        if (m3[1] == 'url') {
                            setBackgroundImageStyle(element, m3[2], true);
                        } else if (m3[1] == 'linear-gradient') {
                            regexp4 = new RegExp('[^,]+[,]([^,]+)[,]([^,]+)','g');
                            if (m4 = regexp4.exec(m3[2])) {
                                setBackgroundGradientStyle(element, m4[1], m4[2], true);
                            }
                        }
                    }
                } else if (prop == 'background') {
                    setBackgroundStyle(element, value, true);
                }
            }
        }
    };
    
    $(window).keydown(function(e){
        if (e.keyCode == 83 && e.ctrlKey) {
            e.preventDefault();
            e.stopPropagation();
            var parentDesignerWindowId = window.location.hash.replace('#','');
            if (parentDesignerWindowId.length>0 && typeof(parent.designerWindows[parentDesignerWindowId])!="undefined") {
                parent.designerWindows[parentDesignerWindowId].saveBody();
            }
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