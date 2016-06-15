<?php if (count($options)>0): ?>
<?php if (!$is_ajax): ?>
<div class="vote-results-wrapper block">
<?php if ($is_sidebar): ?>
<h2><?php echo tm('Vote','vote') ?></h2>
<?php else: ?>
<div class="page-header">
<h2><?php echo tm('Vote','vote') ?></h2>
</div>
<?php endif; ?>
<p class="subject"><?php echo t(Zira\Helper::html($subject)) ?></p>
<?php endif; ?>
<ul class="vote-results">
<?php foreach($options as $option): ?>
<li>
<?php echo '<div>'.t(Zira\Helper::html($option->content)).' &mdash; <span class="vote-result">'.Zira\Helper::html($option->count).'</span></div>' ?>
<div class="vote-results-line" style="width:<?php echo round(($option->count/$total)*100) ?>%"></div>
</li>
<?php endforeach; ?>
</ul>
<?php if (!$is_ajax): ?>
</div>
<?php endif; ?>
<?php endif; ?>