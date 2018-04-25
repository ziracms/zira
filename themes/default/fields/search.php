<?php if (isset($form)): ?>
<?php $sid = uniqid('fieldsearch'); ?>
<div class="field-search-form-wrapper block">
<div class="panel-group" role="tablist" aria-multiselectable="true">
<div class="form-panel panel panel-default">
<div class="panel-heading" role="tab">
<h4 class="panel-title">
<span class="glyphicon glyphicon-search pull-left visible-lg"></span> &nbsp;
<a class="pull-right form-expander" role="button" data-toggle="collapse" href="#<?php echo $sid ?>" aria-controls="<?php echo $sid ?>">
<span class="fields-group-title visible-lg"><?php echo Zira\Helper::html($form->getDescription()) ?></span>
<span class="glyphicon glyphicon-search hidden-lg"></span> 
</a>
</h4>
</div>
<div id="<?php echo $sid ?>" class="panel-collapse collapse<?php if (!empty($expand)) echo ' in'; ?>" role="tabpanel">
<div class="panel-body">
<?php echo $form; ?>
</div>
</div>
</div>
</div>
</div>
<?php endif; ?>