<div id="vk_comments" class="comments-wrapper"></div>
<?php layout_js_begin(); ?>
<script type="text/javascript">
if (typeof(vk_open_api_init_callbacks) == "undefined") {
    vk_open_api_init_callbacks = [];
}
vk_open_api_init_callbacks.push(function () {
    (function($) {
        var w = $('#vk_comments').parent().width();
        VK.Widgets.Comments("vk_comments", {limit: 10, width: w, attach: "*"});
    })(jQuery);
});
</script>
<?php layout_js_end(); ?>