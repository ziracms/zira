<?php if (!empty($group_id)): ?>
<div id="vk_groups" class="block noframe"></div>
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
                color1: '878B92',
                color2: 'eeeeee',
                color3: '51545D'
            }, <?php echo Zira\Helper::html($group_id) ?>
        );
        })(jQuery);
});
</script>
<?php endif; ?>