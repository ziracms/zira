(function($){
    $(document).ready(function(){
        $('body').append('<div style="position:fixed;width:100%;height:100%;top:0;left:0;z-index:2499"></div>');
        
        var container_left = $('#content').offset().left;
        var colorpicker_size = 42;
        
        // header bg color
        if ($('header').length>0 && $('header').css('backgroundColor')!='transparent') {
            var header_bg = $('header').css('backgroundColor');
            var header_cx = $('header').offset().left+($('header').outerWidth()-colorpicker_size)/2;
            var header_cy = $('header').offset().top+($('header').outerHeight()-colorpicker_size)/2;
            $('body').append('<div class="designer_colorpicker" id="header-designer-colorpicker" title="'+t('Header background color')+'"></div>');
            $('#header-designer-colorpicker').css({'left':header_cx,'top':header_cy});
            $('#header-designer-colorpicker').tooltip();
            designer_colorpicker($('#header-designer-colorpicker'), header_bg, function(color){
                $('header').css('backgroundColor', color);
                setBackgroundColorStyle('header', color);
            });
        }
    });
    
    var designer_colorpicker = function(element, init_color, callback) {
        $(element).colorpicker({
            customClass: 'colorpicker-2x',
            sliders: { 
                saturation: { maxLeft: 200, maxTop: 200 },
                hue: { maxTop: 200 },
                alpha: { maxTop: 200 }
            },
            color: init_color
        }).on('changeColor', zira_bind($(element), function(e) {
            var color = e.color.toString('rgba');
            if (typeof(callback)!="undefined") {
                callback.call(this, color);
            }
        }));
    };
    
    var setBackgroundColorStyle = function(element, value) {
        window.editorStyles[element] = 'background-color:' + value + ';';
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
            content += prop + '{' + window.editorStyles[prop] + '}';
        }
        return content;
    };
    parent.designerEditorWindow = window;
})(jQuery);