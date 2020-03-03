<?php if (!empty($error)): ?>
<div class="alert alert-danger"><?php echo Zira\Helper::html($error) ?></div>
<?php endif; ?>
<?php layout_js_begin(); ?>
<script type="text/javascript">
jQuery(document).ready(function() {
    $('.container #content').on('click', '.fields-search-results-view-more', function(e){
        e.stopPropagation();
        e.preventDefault();

        var url = window.location.href;
        var offset = $(this).data('offset');

        if (typeof(offset)=="undefined") return;

        $(this).attr('disabled','disabled');
        $(this).parent('.fields-search-results-view-more-wrapper').append('<div class="zira-loader-wrapper"><span class="zira-loader glyphicon glyphicon-refresh"></span> '+t('Please wait')+'...</div>');

        $.get(url, {
            'offset': offset,
            'ajax': 1
        }, zira_bind(this, function(response){
            if ($(this).parent('.fields-search-results-view-more-wrapper').prev('ul').hasClass('grid-category-wrapper')) {
                var r = new RegExp('<ul[^>]*>([\\s\\S]+)</ul>([\\s\\S]*?)$','g');
                var m = r.exec(response);
                if (m) {
                    $(this).parent('.fields-search-results-view-more-wrapper').prev('ul').append(m[1]);
                    $(this).parent('.fields-search-results-view-more-wrapper').replaceWith(m[2]);
                    zira_init_grid();
                    window.setTimeout(zira_init_grid, 500);
                }
            } else {
                $(this).parent('.fields-search-results-view-more-wrapper').replaceWith(response);
                if (navigator.userAgent.indexOf('MSIE')<0) {
                    $('.container #content .xhr-list').hide().slideDown().removeClass('xhr-list');
                } else {
                    $('.container #content .xhr-list').removeClass('xhr-list');
                }
            }
        }),'html');
    });
});    
</script>
<?php layout_js_end(); ?>