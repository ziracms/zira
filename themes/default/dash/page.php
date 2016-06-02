<div id="dashboard-wrapper">
<div id="dashboard-canvas-wrapper">
    <div id="dashboard-sidebar">
        <div id="remote-clock-wrapper">
            <canvas id="dashboard_remote_clock" width="230" height="230"></canvas>
        </div>
    </div>
    <div id="memory-stick-wrapper">
        <textarea rows="10" cols="30" maxlength="255" name="memory-stick"><?php echo Zira\Helper::html(Zira\Config::get('memory_stick')) ?></textarea>
        <div id="memory-stick-save"><span class="glyphicon glyphicon-floppy-disk"></span></div>
    </div>
    <?php if (isset($content)) echo $content; ?>
</div>
</div>
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
        });
    })(jQuery);
</script>