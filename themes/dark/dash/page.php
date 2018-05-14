<div id="dashboard-wrapper">
<div id="dashboard-canvas-wrapper"<?php if (Zira\Config::get('dash_bg')) echo ' data-bg="'.Zira\Helper::html(Zira\Config::get('dash_bg')).'"' ?>>
    <div id="dashboard-sidebar">
        <div id="remote-clock-wrapper">
            <canvas id="dashboard_remote_clock" width="230" height="230"></canvas>
        </div>
        <?php if (isset($settings)): ?>
        <div id="dashboard_stats">
            <h3><span class="glyphicon glyphicon-stats"></span> <?php echo t('Statistics').':'; ?></h3>
            <ul>
            <?php if (isset($settings['records'])): ?>
            <li><?php echo t('Records: %s', $settings['records']) ?></li>
            <?php endif; ?>
            <?php if (isset($settings['comments'])): ?>
            <li><?php echo t('Comments: %s', $settings['comments']) ?></li>
            <?php endif; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
    <div id="memory-stick-wrapper">
        <textarea rows="10" cols="30" maxlength="255" name="memory-stick"><?php echo Zira\Helper::html(Zira\Config::get('memory_stick')) ?></textarea>
        <div id="memory-stick-save"><span class="glyphicon glyphicon-floppy-disk"></span></div>
    </div>
    <?php if (isset($content)) echo $content; ?>
</div>
</div>
<?php layout_js_begin(); ?>
<script type="text/javascript">
    (function($) {
        $(document).ready(function(){
            $('#remote-clock-wrapper').show();
            var date = new Date();
            dashboard_remote_clock.start_timestamp = Math.floor(date.getTime() / 1000);
            dashboard_remote_clock.remote_timestamp = <?php echo Zira\Datetime::getOffsetTime(); ?>;
            dashboard_clock();
            window.setInterval(dashboard_clock, 1000);

            $('textarea[name=memory-stick]').keydown(function(){
                $('#memory-stick-save').show();
            });
            $('#memory-stick-save').click(function(){
                var data = $('textarea[name=memory-stick]').val();
                $.post('<?php echo Zira\Helper::url('dash/system/stick') ?>',{'content':data, 'token':'<?php echo Dash\Dash::getToken() ?>'}, function(response){
                    if (response && response.ok) {
                        $('#memory-stick-save').hide();
                        desk_timeout_message('<?php echo t('Successfully saved'); ?>');
                    }
                },'json');
            });

            Desk.dock_open = Dock.show;
            Desk.dock_close = Dock.hide;
            Desk.dock_update = Dock.update;
            Desk.dock_update_focus = Dock.updateFocus;
            Desk.dock_position = Dock.position;
            Desk.dock_reset = Dock.reset;
            Dock.click = Desk.dock_click;
            Dock.init();
            
            dashboard_init_background_setter(function(url){
                $.post('<?php echo Zira\Helper::url('dash/system/bg') ?>',{'url':url, 'token':'<?php echo Dash\Dash::getToken() ?>'}, function(response){
                    if (response && response.ok && url.length>0) {
                        desk_timeout_message('<?php echo t('Successfully saved'); ?>');
                    }
                },'json');
            });
        });
    })(jQuery);
</script>
<?php layout_js_end(); ?>