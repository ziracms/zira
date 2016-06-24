(function($){
    $(document).ready(function(){
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
})(jQuery);