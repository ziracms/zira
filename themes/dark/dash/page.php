<div id="dashboard-wrapper">
<div id="dashboard-canvas-wrapper">
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
        });
    })(jQuery);
</script>
<?php layout_js_end(); ?>