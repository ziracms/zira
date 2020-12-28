<div class="featured-page-wrapper<?php if (!empty($grid)) echo ' grid-category-wrapper grid-col-'.(intval($grid)+1) ?>">
<?php if (!empty($records)) Zira\View::renderView(array(
    'class' => 'records',
    'records' => $records,
    'settings' => array()
), 'zira/list'); ?>
</div>
