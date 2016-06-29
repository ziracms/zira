(function($){
    $(document).ready(function(){
        zira_init_forum_attach_icon();
        zira_init_forum_attaches();
        zira_init_forum_attacher();
        zira_init_forum_rating();
        zira_init_forum_reply();
    });

    zira_forum_form_submit_success = function(response) {
        var form = $('.container #content form#form-forum-message-form');
        if ($(form).length==0) return;
        $(form).get(0).reset();

        if (typeof(response)!="undefined" &&
            typeof(response.redirect)!="undefined" &&
            response.redirect.indexOf('http')<0
        ) {
            window.location.href=response.redirect;
        }
    };

    zira_init_forum_attach_icon = function() {
        $('#form-forum-message-form .forum-attach-input-icon .attach-icon').click(function(){
            $(this).parents('#form-forum-message-form').find('.forum-attach-input-wrapper').show();
            $(this).parents('.forum-attach-input-icon').remove();
        });
    };

    zira_init_forum_attaches = function() {
        $('.forum-message-attaches .forum-message-attaches-title').click(function(){
            var is_ie = navigator.userAgent.indexOf('MSIE') >= 0;
            var content = $(this).parent().children('.forum-message-attaches-content');
            if ($(content).css('display')=='none') {
                $(this).children('.attach-arrow-down').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
                if (!is_ie) {
                    $(content).slideDown();
                } else {
                    $(content).show();
                }
            } else {
                $(this).children('.attach-arrow-down').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
                if (!is_ie) {
                    $(content).slideUp();
                } else {
                    $(content).hide();
                }
            }
        });
    };

    zira_init_forum_attacher = function() {
        var form = $('.container #content form#form-forum-message-form');

        if ($(form).length==0) return;
        var input = $(form).find('input.forum-attaches');
        var textarea = $(form).find('#message');
        if ($(input).length==0) return;
        $(textarea).bind('drop', function(e){
            e.stopPropagation();
            e.preventDefault();
        });
        if ($(input).parents('.form-group').children('.forum-thumbs-wrapper').length==0) {
            $(input).parents('.form-group').prepend('<div class="forum-thumbs-wrapper"></div>');
        }
        $(input).change(function(e){
            $(input).parents('.form-group').children('.forum-thumbs-wrapper').html('<div class="col-sm-3"></div><div class="col-sm-9 forum-thumbs"></div>');
            $(input).parents('.form-group').children('.forum-thumbs-wrapper').find('.forum-thumbs').append('<span></span>');
            try {
                var files = e.originalEvent.target.files;
            } catch (err) {
                return;
            }
            var editable = $(form).find('#message-editable');
            var tag;
            for (var i = 0, f; f = files[i]; i++) {
                if (!f.type.match('image.*')) {
                    tag = '<a class="forum-link" href="javascript:void(0)">' + encodeURIComponent(f.name) + '</a>';
                    $(input).parents('.form-group').children('.forum-thumbs-wrapper').find('.forum-thumbs').append(tag);
                    $(form).find('.forum-link').bind('dragstart', function(e){
                        e.stopPropagation();
                        e.preventDefault();
                    });
                    continue;
                }
                var reader = new FileReader();
                reader.onload = (function(file) {
                    return function(e) {
                        var tag = '<img class="forum-thumb" src="' + e.target.result + '" title="' + file.name + '"/>';
                        $(input).parents('.form-group').children('.forum-thumbs-wrapper').find('.forum-thumbs').append(tag);

                        $(form).find('.forum-thumb').bind('dragstart', function(e){
                            e.originalEvent.dataTransfer.effectAllowed = 'copy';
                            $(editable).addClass('dragging');
                        });
                        $(form).find('.forum-thumb').bind('dragend', function(e){
                            $(editable).removeClass('dragging');
                        });
                        $(editable).unbind('dragover').bind('dragover', function(){
                            $(editable).addClass('dragover');
                        });
                        $(editable).unbind('dragleave').bind('dragleave', function(){
                            $(editable).removeClass('dragover');
                        });
                        $(editable).unbind('drop').bind('drop', function(e){
                            $(editable).removeClass('dragging');
                            $(editable).removeClass('dragover');
                        });
                        $(form).find('.forum-thumb').unbind('click').click(function(){
                            if ($(editable).length==0) {
                                var bb = '['+$(this).attr('title')+']';
                                $(textarea).get(0).focus();
                                var sel = getTextareaSelection($(textarea).get(0));
                                if (sel!==false) {
                                    var val = $(textarea).val();
                                    $(textarea).val(val.substr(0, sel.start) + bb + val.substr(sel.start));
                                } else {
                                    $(textarea).val($(textarea).val() + bb);
                                }
                            } else {
                                $(editable).get(0).focus();
                                var html = '<img src="' + $(this).get(0).src + '" title="' + $(this).attr('title') + '" />';
                                if (!pasteAtEditableSelection(html)) {
                                    $(editable).append(html);
                                }
                            }
                        });
                    };
                })(f);
                reader.readAsDataURL(f);
            }
        });
    };

    zira_init_forum_rating = function() {
        $('.container #content .forum-list a.forum-rating').unbind('click').click(function(e){
            e.stopPropagation();
            e.preventDefault();
            if ($(this).hasClass('active')) return;
            zira_poll.call(this, function(response){
                if (typeof(response.rating)=="undefined") return;
                $(this).addClass('active');
                var rating = response.rating;
                if (rating>0) rating = '<span class="positive-rating">+'+rating+'</span>';
                else if (rating<0) rating = '<span class="negative-rating">'+rating+'</span>';
                $(this).parent().children('.forum-rating-value').html(rating);
            }, function(){
                zira_error(t('An error occurred'));
            });
        });
    };

    zira_init_forum_reply = function() {
        $('.container #content .forum-list a.forum-reply-inline').unbind('click').click(function (e) {
            e.stopPropagation();
            e.preventDefault();

            var user = $(this).parent('.list-title-wrapper').children('a[rel=author]').text();
            var content = $(this).parents('.list-item').find('.forum-message').html();

            var form = $('.container #content form#form-forum-message-form');
            var textarea = $(form).find('#message');
            var editable = $(form).find('#message-editable');

            if ($(editable).length>0) {
                $(editable).html('<q><b>@'+user+':</b><br />'+content+'</q>'+'<span>&#x200c;</span>');
                focusEditable($(editable).get(0));
            } else {
                $(textarea).val('[quote][b]@'+user+':[/b]'+"\r\n"+content.replace(/<([a-z]+).*?>[\s\S]*?<[\/]\1>/gi, '').replace(/<[a-z\/].*?>/gi, '')+'[/quote]'+"\r\n");
                $(textarea).get(0).focus();
            }

            var top = $(form).parents('.form-panel').offset().top;
            $('html, body').animate({'scrollTop': top}, 500);
        });
    };

    function getTextareaSelection(input) {
        if ("selectionStart" in input && document.activeElement == input) {
            return {
                start: input.selectionStart,
                end: input.selectionEnd
            };
        } else if (input.createTextRange) {
            var sel = document.selection.createRange();
            if (sel.parentElement() === input) {
                var rng = input.createTextRange();
                rng.moveToBookmark(sel.getBookmark());
                for (var len = 0;
                         rng.compareEndPoints("EndToStart", rng) > 0;
                         rng.moveEnd("character", -1)) {
                    len++;
                }
                rng.setEndPoint("StartToStart", input.createTextRange());
                for (var pos = { start: 0, end: len };
                         rng.compareEndPoints("EndToStart", rng) > 0;
                         rng.moveEnd("character", -1)) {
                    pos.start++;
                    pos.end++;
                }
                return pos;
            }
        }
        return false;
    }

    function focusEditable(el) {
        el.focus();
        if (typeof window.getSelection != "undefined" && typeof document.createRange != "undefined") {
            var range = document.createRange();
            range.selectNodeContents(el);
            range.collapse(false);
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        } else if (typeof document.body.createTextRange != "undefined") {
            var textRange = document.body.createTextRange();
            textRange.moveToElementText(el);
            textRange.collapse(false);
            textRange.select();
        }
    }

    function pasteAtEditableSelection(html) {
        var sel, range;
        if (window.getSelection) {
            // IE9 and non-IE
            sel = window.getSelection();
            if (sel.getRangeAt && sel.rangeCount) {
                range = sel.getRangeAt(0);
                range.deleteContents();
                var el = document.createElement("div");
                el.innerHTML = html;
                var frag = document.createDocumentFragment(), node, lastNode;
                while ( (node = el.firstChild) ) {
                    lastNode = frag.appendChild(node);
                }
                range.insertNode(frag);
                // setting carret
                if (lastNode) {
                    range = range.cloneRange();
                    range.selectNodeContents(lastNode);
                    range.collapse(false);
                    sel.removeAllRanges();
                    sel.addRange(range);
                }
            }
            return true;
        } else if (document.selection && document.selection.type != "Control") {
            // IE < 9
            //document.selection.createRange().pasteHTML(html);

            // paste and set caret
            var id = "marker_" + ("" + Math.random()).slice(2);
            html += '<span id="' + id + '"></span>';
            var textRange = document.selection.createRange();
            textRange.pasteHTML(html);
            var markerSpan = document.getElementById(id);
            textRange.moveToElementText(markerSpan);
            textRange.select();
            markerSpan.parentNode.removeChild(markerSpan);
            return true;
        } else {
            return false;
        }
    }
})(jQuery);