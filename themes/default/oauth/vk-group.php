<?php if (!empty($group_id)): ?>
<div id="vk_groups" class="block noframe"></div>
<?php layout_js_begin(); ?>
<script type="text/javascript">
if (typeof(vk_open_api_init_callbacks)=="undefined") {
    vk_open_api_init_callbacks = [];
}
vk_open_api_init_callbacks.push(function() {
    (function($) {
        var w = $('#vk_groups').parent().width();
        VK.Widgets.Group("vk_groups", {
                mode: 0,
                width: w,
                height: "200",
                color1: 'f9f9f9',
                color2: '333333',
                color3: '53708C'
            }, <?php echo Zira\Helper::html($group_id) ?>
        );
        })(jQuery);
});
</script>
<?php layout_js_end(); ?>
<?php endif; ?>