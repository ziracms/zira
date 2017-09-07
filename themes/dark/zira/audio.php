<?php if ((!empty($urls) || !empty($embeds)) && !empty($access_allowed)): ?>
<?php if (!empty($urls) && !empty($container_id) && !empty($player_id)): ?>
<?php if (count($urls)==1) $disabled_attr = ' disabled="disabled"'; else $disabled_attr = ''; ?>
<div class="jplayer-audio-wrapper">
<div id="<?php echo Zira\Helper::html($player_id) ?>" class="jp-jplayer"></div>
<div id="<?php echo Zira\Helper::html($container_id) ?>" class="jp-audio" role="application" aria-label="media player">
    <div class="jp-type-playlist">
        <div class="jp-gui jp-interface">
            <div class="jp-controls-holder">
                <div class="jp-volume-controls">
                    <button class="jp-mute" role="button" tabindex="0"></button>
                    <button class="jp-volume-max" role="button" tabindex="0"></button>
                    <div class="jp-volume-bar">
                        <div class="jp-volume-bar-value"></div>
                    </div>
                </div>
                <div class="jp-controls">
                    <button class="jp-previous" role="button" tabindex="0"<?php echo $disabled_attr; ?>></button>
                    <button class="jp-play" role="button" tabindex="0"></button>
                    <button class="jp-stop" role="button" tabindex="0"></button>
                    <button class="jp-next" role="button" tabindex="0"<?php echo $disabled_attr; ?>></button>
                </div>
                <div class="jp-toggles">
                    <button class="jp-repeat" role="button" tabindex="0"<?php echo $disabled_attr; ?>></button>
                    <button class="jp-shuffle" role="button" tabindex="0"<?php echo $disabled_attr; ?>></button>
                </div>
            </div>
            <div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
            <div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
            <div class="jp-progress">
                <div class="jp-seek-bar">
                    <div class="jp-play-bar"></div>
                </div>
            </div>
        </div>
        <div class="jp-playlist">
            <ul>
                <li>&nbsp;</li>
            </ul>
        </div>
        <div class="jp-no-solution">
            <?php echo t('To play the media you will need to either update your browser to a recent version or update your flash plugin.'); ?>
        </div>
    </div>
</div>
</div>
<?php endif; ?>
<?php if (!empty($embeds)): ?>
<div class="audio-wrapper">
<?php foreach($embeds as $file): ?>
    <div class="audio-wrapper-item">
    <?php if (!empty($file->description)): ?>
    <p><?php echo Zira\Helper::html($file->description) ?></p>
    <?php endif; ?>
    <?php echo $file->embed; ?>
    </div>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php endif; ?>

<?php if (empty($urls) && empty($embeds) && empty($access_allowed)): ?>
<div class="alert alert-warning alert-dark" role="alert">
    <?php if (!Zira\User::isAuthorized()): ?>
    <?php echo t('%s to listen to audio', '<a href="'.Zira\Helper::url('user/login?redirect='.Zira\Page::getRedirectUrl()).'">'.t('Login').'</a>') ?>
    <?php else: ?>
    <?php echo t('You do not have permission to listen to audio'); ?>
    <?php endif; ?>
</div>
<?php endif; ?>
