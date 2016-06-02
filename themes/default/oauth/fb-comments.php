<?php if (!empty($page_url)): ?>
<div class="fb-comments comments-wrapper"
     data-width="100%"
     data-href="<?php echo Zira\Helper::html($page_url); ?>"
     data-numposts="5">
</div>
<?php endif; ?>