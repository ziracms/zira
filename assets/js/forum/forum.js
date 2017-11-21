(function($){
    $(document).ready(function(){
        if ($('#form-forum-message-form .forum-attach-input-icon .attach-icon').length>0) {
            zira_init_forum_attach_icon();
        }
        if ($('.forum-message-attaches .forum-message-attaches-title').length>0) {
            zira_init_forum_attaches();
        }
        if ($('.container #content form#form-forum-message-form input.forum-attaches').length>0) {
            zira_init_forum_attacher();
        }
        if ($('.container #content .forum-list a.forum-rating').length>0) {
            zira_init_forum_rating();
        }
        if ($('.container #content .messages-panel .reply-btn').length>0) {
            zira_init_forum_reply_btn();
        }
        if ($('.container #content .forum-list a.forum-reply-inline').length>0) {
            zira_init_forum_reply();
        }
        if ($('.container #content .forum-search-results-view-more-wrapper').length>0) {
            zira_init_forum_search_more();
        }
        if ($('.container #content .forum-list a.forum-edit-inline').length>0) {
            zira_init_forum_edit();
        }
    });

    zira_forum_form_submit_success = function(response) {
        var form = $('.container #content form#form-forum-message-form');
        if ($(form).length==0) return;
        $(form).get(0).reset();

        if ($(form).find('.forum-thumbs-wrapper').length>0) {
            $(form).find('.forum-thumbs-wrapper').html('');
        }

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

    zira_init_forum_reply_btn = function() {
        $('.container #content .messages-panel .reply-btn').click(function(){
            var form = $('.container #content form#form-forum-message-form');
            $(form).find('input[type=hidden].forum-edit-inline-id').val('');
            $(form).parents('.form-panel').find('.panel-title').text(t('Reply'));
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
                $(textarea).val('[quote][b]@'+user+':[/b]'+"\r\n"+content.replace(/[\r\n]/g,'').replace(/<br[^>]*?>/gi,"\r\n").replace(/<(\/)?q.*?>/gi,'[$1quote]').replace(/<(\/)?b.*?>/gi,'[$1b]').replace(/<(\/)?code.*?>/gi,'[$1code]').replace(/<img[^>]+?class[\x20]*[=][\x20]*["]emoji[^"]*["][^>]*?>/gi,'').replace(/<img[^>]+?src[\x20]*[=][\x20]*["]([^"]+)["][^>]*?>/gi,'[img]$1[/img]').replace(/<p.*?>([\s\S]*?)<\/p>/gi,'$1'+"\r\n").replace(/<([a-z]+).*?>[\s\S]*?<[\/]\1>/gi, '').replace(/<[a-z\/].*?>/gi, '')+'[/quote]'+"\r\n");
                $(textarea).get(0).focus();
            }
            
            $(form).find('input[type=hidden].forum-edit-inline-id').val('');
            $(form).parents('.form-panel').find('.panel-title').text(t('Reply'));
            
            var top = $(form).parents('.form-panel').offset().top;
            $('html, body').animate({'scrollTop': top}, 500);
        });
    };
    
    zira_init_forum_edit = function() {
        $('.container #content .forum-list a.forum-edit-inline').unbind('click').click(function (e) {
            e.stopPropagation();
            e.preventDefault();

            var editid = $(this).data('editid');
            if (typeof(editid)=="undefined") return;
            
            var user = $(this).parent('.list-title-wrapper').children('a[rel=author]').text();
            var content = $(this).parents('.list-item').find('.forum-message').html();

            var form = $('.container #content form#form-forum-message-form');
            var textarea = $(form).find('#message');
            var editable = $(form).find('#message-editable');

            if ($(editable).length>0) {
                $(editable).html(content);
                focusEditable($(editable).get(0));
            } else {
                $(textarea).val(content.replace(/[\r\n]/g,'').replace(/<br[^>]*?>/gi,"\r\n").replace(/<(\/)?q.*?>/gi,'[$1quote]').replace(/<(\/)?b.*?>/gi,'[$1b]').replace(/<(\/)?code.*?>/gi,'[$1code]').replace(/<img[^>]+?class[\x20]*[=][\x20]*["]emoji[^"]*["][^>]*?>/gi,'').replace(/<img[^>]+?src[\x20]*[=][\x20]*["]([^"]+)["][^>]*?>/gi,'[img]$1[/img]').replace(/<p.*?>([\s\S]*?)<\/p>/gi,'$1'+"\r\n").replace(/<([a-z]+).*?>[\s\S]*?<[\/]\1>/gi, '').replace(/<[a-z\/].*?>/gi, ''));
                $(textarea).get(0).focus();
            }
            
            $(form).find('input[type=hidden].forum-edit-inline-id').val(editid);
            $(form).parents('.form-panel').find('.panel-title').text(t('Edit message'));

            var top = $(form).parents('.form-panel').offset().top;
            $('html, body').animate({'scrollTop': top}, 500);
        });
    };

    zira_init_forum_search_more = function() {
        $('.container #content').on('click', '.forum-search-results-view-more', function(e){
            e.stopPropagation();
            e.preventDefault();

            var url = $(this).data('url');
            var text = $(this).data('text');
            var offset = $(this).data('offset');
            var forum_id = $(this).data('forum_id');

            if (typeof(url)=="undefined" ||
                typeof(text)=="undefined" ||
                typeof(offset)=="undefined" ||
                typeof(forum_id)=="undefined"
            ) {
                return;
            }

            $(this).attr('disabled','disabled');
            $(this).parent('.forum-search-results-view-more-wrapper').append('<div class="zira-loader-wrapper"><span class="zira-loader glyphicon glyphicon-refresh"></span> '+t('Please wait')+'...</div>');

            $.get(url, {
                'text': text,
                'offset': offset,
                'forum_id': forum_id,
                'ajax': 1
            }, zira_bind(this, function(response){
                $(this).parent('.forum-search-results-view-more-wrapper').replaceWith(response);
                if (navigator.userAgent.indexOf('MSIE')<0) {
                    $('.container #content .xhr-list').hide().slideDown().removeClass('xhr-list');
                } else {
                    $('.container #content .xhr-list').removeClass('xhr-list');
                }
            }),'html');
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