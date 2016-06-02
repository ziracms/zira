(function($) {
    $(document).ready(function () {
        zira_parse_content();
    });

    $(window).load(function(){
        zira_parse_bind_lightbox();
    });

    zira_parse_content = function() {
        zira_parse('.parse-content');
    };

    zira_parse = function(selector) {
        $(selector).each(function(){
            if ($(this).hasClass('parsed-content')) return true;
            var content = $(this).html();
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

        var emojiBase = zira_base+'assets/images/emoji';
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
        var p = new RegExp('(^|\\s|>)(http(?:s)?[:][\/][\/][^\\s,\\!\\(\\)\\[\\]"\'<]+?)([,\\.\\!\\(\\)\\[\\]])?($|\\s|<)','g');
        content = content.replace(p, '$1<a class="external-url" href="$2" rel="nofollow" target="_blank">$2</a>$3$4');
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
        while (m=p.exec(content)) {
            if (typeof(m[0])=="undefined" || typeof(m[1])=="undefined") continue;
            var tab = '&nbsp;&nbsp;&nbsp;&nbsp;';
            m[1] = m[1].replace(/[\x20]{4}/g, tab);
            m[1] = m[1].replace(/[\t]/g, tab);
            content = content.replace(m[0], '<code class="parsed-code-tag">'+m[1]+'</code>');
        }
        return content;
    };

    zira_parse_pre_tags = function(content) {
        var p = new RegExp('<pre>(?:<br>)?([\\s\\S]+?)(?:<br>)?</pre>','gi');
        var m;
        while (m=p.exec(content)) {
            if (typeof(m[0])=="undefined" || typeof(m[1])=="undefined") continue;
            content = content.replace(m[0], '<pre class="highlight">'+m[1]+'</pre>');
            zira_parse.highlight = true;
        }
        return content;
    };

    zira_parse_images = function(content) {
        var p = new RegExp('<img ([^>]*)alt="([^"]+)"([^>]*)>','gi');
        var m;
        while (m=p.exec(content)) {
            if (typeof(m[0])=="undefined" ||
                typeof(m[1])=="undefined" ||
                typeof(m[2])=="undefined" ||
                typeof(m[3])=="undefined"
            ) continue;
            if (m[0].indexOf(' class="image"')<0 || m[0].indexOf(' class="image parsed-image"')>0) continue;
            content = content.replace(m[0], '<div class="image-wrapper">'+
                                            '<img class="image parsed-image" '+m[1]+'alt="'+m[2]+'"'+m[3]+'>'+
                                            '<div class="image-description">'+m[2]+
                                            '</div></div>');
        }
        return content;
    };

    zira_parse_bind_lightbox = function() {
        $('.zira-lightbox, .parsed-image').each(function(){
            if ($(this).width() < 300 || $(this).height() < 200 || $(this).hasClass('lightbox-image')) return true;
            $(this).addClass('lightbox-image').click(zira_parse_lightbox);
        });
    };

    function zira_parse_lightbox() {
        if ($(this).attr('src')) {
            $('body').append('<a href="'+this.src+'" title="'+$(this).attr('alt')+'" data-lightbox="parsed_image_zoomer" id="parsed-image-zoomer-lightbox"></a>');
            $('#parsed-image-zoomer-lightbox').trigger('click');
            $('#parsed-image-zoomer-lightbox').remove();
        }
    }

    zira_parse_highlight = function() {
        $('pre.highlight').each(function(i, block) {
            hljs.highlightBlock(block);
        });
    };

    function supportsSVG () {
        return !!document.createElementNS &&
                !!document.createElementNS('http://www.w3.org/2000/svg', "svg").createSVGRect;
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