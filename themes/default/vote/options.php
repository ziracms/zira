<?php if (count($options)>0): ?>
<div class="vote-options-wrapper block">
<?php if ($is_sidebar): ?>
<div class="page-header">
<h2 class="widget-title"><?php echo tm('Vote','vote') ?></h2>
</div>
<?php else: ?>
<div class="page-header">
<h2><?php echo tm('Vote','vote') ?></h2>
</div>
<?php endif; ?>
<p class="subject"><?php echo t(Zira\Helper::html($subject)) ?></p>
<ul>
<?php foreach($options as $option): ?>
<?php $id = 'vote-'.$vote_id.'-option-'.$option->id; ?>
<li>
<?php if ($multiple): ?>
<?php echo Zira\Form\Form::checkbox($token, 'vote_options', $option->id, array('id'=>$id)) ?>
<?php else: ?>
<?php echo Zira\Form\Form::radio($token, 'vote_options', $option->id, array('id'=>$id)) ?>
<?php endif; ?>
<?php echo '<label for="'.Zira\Helper::html($id).'">'.t(Zira\Helper::html($option->content)).'</label>' ?>
</li>
<?php endforeach; ?>
</ul>
<button class="btn btn-primary vote-submit" data-token="<?php echo Zira\Helper::html($token); ?>" data-vote_id="<?php echo Zira\Helper::html($vote_id); ?>" data-url="<?php echo Zira\Helper::url('vote/index/index'); ?>"><?php echo tm('Vote!','vote') ?></button>
</div>
<?php endif; ?>
