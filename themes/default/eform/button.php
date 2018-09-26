<div class="block">
    <button class="eform-button btn btn-primary" data-toggle="modal" data-target="#zira-eform-dialog-<?php echo $eform->id ?>" data-backdrop="false"><?php echo t($eform->title) ?></button>
</div>
<div class="zira-eform-modal zira-modal modal fade" id="zira-eform-dialog-<?php echo $eform->id ?>" tabindex="-1" role="dialog" aria-labelledby="zira-eform-dialog-label-<?php echo $eform->id ?>">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo t('Close') ?>"><span aria-hidden="true">×</span></button>
            <h4 class="modal-title" id="zira-eform-dialog-label-<?php echo $eform->id ?>"><?php echo $form->getTitle() ?></h4>
        </div>
        <div class="modal-body">
            <p class="modal-description alert alert-info"><?php echo $form->getDescription() ?></p>
            <div class="modal-form-wrapper"><?php echo $form; ?></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo t('Close') ?></button>
        </div>
    </div>
</div>
</div>