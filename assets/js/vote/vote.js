(function($){
    $(document).ready(function(){
        $('.vote-options-wrapper .vote-submit').click(function(){
            var vote_id = $(this).data('vote_id');
            var url = $(this).data('url');
            var token = $(this).data('token');
            if (typeof(vote_id)=="undefined" || typeof(url)=="undefined" || typeof(token)=="undefined") return;

            var opts = $(this).parent('.vote-options-wrapper').find('input:checked');
            if ($(opts).length==0) return;

            var o = [];
            $(opts).each(function(){
                o.push($(this).val());
            });

            $(this).attr('disabled','disabled');
            $(this).parent('.vote-options-wrapper').append('<div class="zira-loader-wrapper"><span class="zira-loader glyphicon glyphicon-refresh"></span></div>');

            $.post(url, {
                'vote_id': vote_id,
                'options': o,
                'token': token
            }, zira_bind(this, function(response){
                $(this).parent('.vote-options-wrapper').children('ul').replaceWith(response);
                $(this).parent('.vote-options-wrapper').children('.zira-loader-wrapper').remove();
                $(this).remove();
            }),'html');
        });
    });
})(jQuery);