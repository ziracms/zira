(function($) {
    $(document).ready(function () {
        zira_parse_content();
    });

    $(window).load(function(){
        zira_parse_bind_lightbox();
    });

    zira_parse_content = function() {
        zira_parse('.parse-content');
        if (typeof(zira_parse_content.extra)!="undefined") {
            for (var i=0; i<zira_parse_content.extra.length; i++) {
                zira_parse_content.extra[i].call();
            }
        }
    };

    zira_parse = function(selector) {
        $(selector).each(function(){
            if ($(this).hasClass('parsed-content')) return true;
            var content = $(this).html();
            var fs;
            var p1 = $(this).css('line-height');
            var p2 = $(this).css('font-size');
            if (p1) p1 = parseInt(p1);
            if (p2) p2 = parseInt(p2);
            if (p1 && p2) fs = Math.max(p1,p2);
            else fs = 20;
            if (fs<20) fs = 20;
            else if (fs>64) fs = 64;
            content = zira_parse_emoji(content, fs);
            content = zira_parse_urls(content);
            content = zira_parse_quote_tags(content);
            content = zira_parse_code_tags(content);
            content = zira_parse_pre_tags(content);
            content = zira_parse_images(content);
            $(this).html(content);
            $(this).addClass('parsed-content');
        });

        if (typeof(zira_parse.highlight)!="undefined" && zira_parse.highlight) {
            zira_parse_load_highlighter();
        }
        
        zira_parse_tables(selector);
    };

    zira_parse_emoji = function(content, size) {
        var smilies = {
            ':)': '1f642',
            ':D': '1f600',
            ':(': '1f641',
            ':\'(': '1f622',
            ':P': '1f60b',
            'O:)': '1f607',
            '3:)': '1f608',
            'o.O': '1f615',
            ';)': '1f609',
            ':O': '1f632',
            '-_-': '1f611',
            '8-)': '1f60e',
            ']:(': '1f621'
        };

        var base = zira_base;
        if (base.substr(-1) == '/') {
            base = base.substr(0, base.length - 1);
        }
        var emojiBase = base+'/assets/images/smileys';
        var p, src;
        for (var i in smilies) {
            p = new RegExp('(^|\\s|>)('+preg_quote(i)+')($|\\s|<)','g');
            if (supportsSVG()) {
                src = emojiBase + '/svg/' + smilies[i] + '.svg';
            } else {
                src = emojiBase + '/png/' + smilies[i] + '.png';
            }
            content = content.replace(p, '$1<img class="emoji emoji-' + smilies[i] + '" src="'+src+'" alt="$2" width="'+size+'" height="'+size+'" />$3');
        }

        return content;
    };

    zira_parse_urls = function(content) {
        var p = new RegExp('(^|\\s|>)(http(?:s)?[:][\/][\/][^\\s"\'<]+?)([,\\.\\!\\(\\)\\[\\]])?($|\\s|<(?!\/a))','g');
        var m;
        var i = 0;
        while(m=p.exec(content)) {
            if (i>999) break;
            if (typeof(m)=="undefined" || typeof(m[0])=="undefined" || typeof(m[1])=="undefined" || typeof(m[2])=="undefined" || typeof(m[4])=="undefined") continue;
            if (typeof(m[3])=="undefined") m[3] = '';
            content = content.replace(m[0], m[1]+'<a class="external-url" href="'+m[2]+'" rel="nofollow" target="_blank">'+m[2]+'</a>'+m[3]+m[4]);
            i++;
        }
        return content;
    };

    zira_parse_quote_tags = function(content) {
        var p = new RegExp('<q>(?:<br>)?([\\s\\S]+?)(?:<br>)?</q>','ig');
        content = content.replace(p, '<q class="parsed-quote-tag">$1</q>');
        return content;
    };

    zira_parse_code_tags = function(content) {
        var p = new RegExp('<code>(?:<br>)?([\\s\\S]+?)(?:<br>)?</code>','gi');
        var m;
        var i=0;
        while (m=p.exec(content)) {
            if (i>999) break;
            if (typeof(m[0])=="undefined" || typeof(m[1])=="undefined") continue;
            var tab = '&nbsp;&nbsp;&nbsp;&nbsp;';
            m[1] = m[1].replace(/[\x20]{4}/g, tab);
            m[1] = m[1].replace(/[\t]/g, tab);
            content = content.replace(m[0], '<code class="parsed-code-tag">'+m[1]+'</code>');
            i++;
        }
        return content;
    };

    zira_parse_pre_tags = function(content) {
        var p = new RegExp('<pre>(?:<br>)?([\\s\\S]+?)(?:<br>)?</pre>','gi');
        var m;
        var i=0;
        while (m=p.exec(content)) {
            if (i>999) break;
            if (typeof(m[0])=="undefined" || typeof(m[1])=="undefined") continue;
            content = content.replace(m[0], '<pre class="highlight">'+m[1]+'</pre>');
            zira_parse.highlight = true;
            i++;
        }
        return content;
    };

    zira_parse_images = function(content) {
        if (typeof(zira_show_images_description)=="undefined" || !zira_show_images_description) return content;
        var p = new RegExp('<img ([^>]*)alt=(?:["])?([^">]+)(?:["])?([\x20][^>]*)?>','gi');
        var m;
        var i=0;
        while (m=p.exec(content)) {
            if (i>999) break;
            if (typeof(m[0])=="undefined" ||
                typeof(m[1])=="undefined" ||
                typeof(m[2])=="undefined" ||
                typeof(m[3])=="undefined"
            ) continue;
            if ((m[0].indexOf(' class="image"')<0 && m[0].indexOf(' class=image')<0) || m[0].indexOf(' class="image parsed-image"')>0) continue;
            var s = '';
            var _p = new RegExp('style=(?:["])?([^">]+)(?:["])?', 'i');
            var _m = _p.exec(m[0]);
            if (_m && typeof(_m[0]) != "undefined" && typeof(_m[1]) != "undefined") {
                s += _m[1];
                if (s.indexOf('width')<0) {
                    m[1] = m[1].replace(_m[0], ' ');
                    m[3] = m[3].replace(_m[0], ' ');
                }
            }
            if (s.indexOf('width')<0) {
                var __p = new RegExp('width=(?:["])?([^">]+)(?:["])?', 'i');
                var __m = __p.exec(m[0]);
                if (__m && typeof(__m[0]) != "undefined" && typeof(__m[1]) != "undefined") {
                    s += 'width:'+__m[1];
                    if (__m[1].indexOf('%')<0) s += 'px;';
                }
            }
            if (s.length > 0) s = ' style="' + s + '"';
            content = content.replace(m[0], '<div class="image-wrapper"'+s+'>'+
                                            '<img class="image parsed-image" '+m[1]+'alt="'+m[2]+'"'+m[3]+'>'+
                                            '<div class="image-description">'+m[2]+
                                            '</div></div>');
            i++;
        }
        return content;
    };

    zira_parse_bind_lightbox = function() {
        $('.zira-lightbox, .image').each(function(){
            if ($(this).width() < 30 || $(this).height() < 20 || $(this).hasClass('lightbox-image')) return true;
            $(this).addClass('lightbox-image').click(zira_parse_lightbox);
        });
    };

    function zira_parse_lightbox() {
        if ($(this).attr('src')) {
            var title = $(this).attr('alt');
            if (typeof title == "undefined") title = '';
            $('body').append('<a href="'+this.src+'" title="'+title+'" data-lightbox="parsed_image_zoomer" id="parsed-image-zoomer-lightbox"></a>');
            $('#parsed-image-zoomer-lightbox').trigger('click');
            $('#parsed-image-zoomer-lightbox').remove();
        }
    }

    zira_parse_highlight = function() {
        $('pre.highlight').each(function(i, block) {
            hljs.highlightBlock(block);
        });
    };
    
    zira_parse_tables = function(selector) {
        $(selector).find('table').each(function(){
            var cellspacing = $(this).attr('cellspacing');
            var cellpadding = $(this).attr('cellpadding');
            if (typeof(cellspacing)!="undefined") {
                $(this).css({
                    'border-collapse': 'separate',
                    'border-spacing': parseInt(cellspacing) + 'px'
                });
            }
            if (typeof(cellpadding)!="undefined") {
                $(this).find('td').css('padding', parseInt(cellpadding)+'px');
            }
        });
    };

    function supportsSVG () {
        return !!document.createElementNS &&
                !!document.createElementNS('http://www.w3.org/2000/svg', "svg").createSVGRect &&
                !navigator.userAgent.match(/(android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini|mobi|palm)/i);
    }

    function preg_quote(str) {
        return (str+'').replace(/[.?*+^$[\]\\(){}|-]/g, "\\$&");
    }

    function zira_parse_load_highlighter() {
        try {
            var base = zira_base;
            if (base.substr(-1) == '/') {
                base = base.substr(0, base.length - 1);
            }
            var c = document.createElement('link');
            c.rel = 'stylesheet';
            c.type = 'text/css';
            c.href = base + '/assets/js/highlight/styles/darkula.css';
            document.head.appendChild(c);

            var s = document.createElement('script');
            s.async = 'async';
            s.onload = zira_parse_highlight;
            s.src = base + '/assets/js/highlight/highlight.pack.js';
            document.body.appendChild(s);
        } catch(err) {}
    }
})(jQuery);