<div class="widget-category-wrapper<?php if (!empty($grid)) echo ' grid-category-wrapper' ?>">
<?php if (!empty($records)) Zira\View::renderView(array(
    'class' => 'records',
    'records' => $records,
    'settings' => array()
), 'zira/list'); ?>
</div>
