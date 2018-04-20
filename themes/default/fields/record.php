<?php if (!empty($fields_groups)): ?>
<div class="record_fields_tabs_wrapper">
<ul class="nav nav-tabs" role="tablist">
<?php $co=0; ?>
<?php foreach ($fields_groups as $group_id=>$fields_group): ?>
<?php $group = $fields_group['group']; ?>
<?php $class = $co === 0 ? 'active' : ''; ?>
<li class="<?php echo $class ?>" role="presentation">
<a href="<?php echo '#record-field-group-'.$group['id'] ?>" aria-control="<?php echo 'record-field-group-'.$group['id'] ?>" role="tab" data-toggle="tab"><?php echo Zira\Helper::html(t($group['title'])) ?></a>
</li>
<?php $co++; ?>
<?php endforeach; ?>
</ul>
<!--tabs content-->
<div class="tab-content">
<?php $co=0; ?>
<?php foreach ($fields_groups as $group_id=>$fields_group): ?>
<?php $group = $fields_group['group']; ?>
<?php $fields = $fields_group['fields']; ?>
<?php $class = $co === 0 ? 'tab-pane active' : 'tab-pane'; ?>
<div role="tab-panel" id="<?php echo 'record-field-group-'.$group['id'] ?>" class="<?php echo $class ?>">
<div class="fields-tab-content-wrapper">
<?php $fco = 0; ?>
<?php foreach($fields as $field): ?>
<?php if (empty($field['value']) && $field['type']!='checkbox') continue; ?>
<?php $fco++ ?>
<div class="dl<?php echo ($fco%2==0 ? ' even' : ' odd'); ?>">
<div class="dt"><?php echo Zira\Helper::html(t($field['title'])) ?>: </div>
<div class="dd">
<?php if ($field['type']=='input'): ?>
<?php echo Zira\Helper::html(t($field['value'])); ?>
<?php endif; // input ?>
<?php if ($field['type']=='textarea'): ?>
<?php echo nl2br(Zira\Helper::html(t(str_replace("\r\n","\n",$field['value'])))); ?>
<?php endif; // textarea ?>
<?php if ($field['type']=='link'): ?>
<a href="<?php echo Zira\Helper::html($field['value']); ?>" target="_blank"><?php echo Zira\Helper::html($field['value']); ?></a>
<?php endif; // link ?>
<?php if ($field['type']=='html'): ?>
<?php echo $field['value']; ?>
<?php endif; // html ?>
<?php if (in_array($field['type'], array('radio', 'select'))): ?>
<?php $options = explode(',', $field['values']); ?>
<?php if (in_array($field['value'], $options)) echo Zira\Helper::html(t($field['value'])); ?>
<?php endif; // radio, select ?>
<?php if ($field['type']=='checkbox' && !empty($field['value'])): ?>
<span class="glyphicon glyphicon-ok-sign"></span>
<?php endif; // checkbox-checked ?>
<?php if ($field['type']=='checkbox' && empty($field['value'])): ?>
<span class="glyphicon glyphicon-minus-sign"></span>
<?php endif; // checkbox-unchecked ?>
<?php if ($field['type']=='multiple'): ?>
<?php $options = explode(',', $field['values']); ?>
<?php $values = explode(',', $field['value']); ?>
<?php if (count($values)>1): ?>
<ul class="options">
<?php foreach($values as $value): ?>
<?php if (!in_array($value, $options)) continue; ?>
<li><?php echo Zira\Helper::html(t($value)); ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<?php if (count($values)==1 && in_array($values[0], $options)): ?>
<?php echo Zira\Helper::html(t($value)); ?>
<?php endif; ?>
<?php endif; // multiple ?>
<?php if ($field['type']=='file'): ?>
<?php $values = explode(',', $field['value']); ?>
<ul class="fields-files">
<?php foreach($values as $value): ?>
<?php $title = rawurldecode(ltrim(substr($value, (int)strrpos($value, '/')),'/')); ?>
<li><a href="<?php echo Zira\Helper::html(Zira\Helper::baseUrl($value)) ?>" target="_blank"><?php echo Zira\Helper::html($title) ?></a></li>
<?php endforeach; ?>
</ul>
<?php endif; // file ?>
<?php if ($field['type']=='image'): ?>
<?php $values = explode(',', $field['value']); ?>
<ul class="fields-images">
<?php foreach($values as $value): ?>
<?php $title = rawurldecode(ltrim(substr($value, (int)strrpos($value, '/')),'/')); ?>
<?php $src = Zira\Helper::baseUrl(\Fields\Models\Value::getImageThumb($value)).'?t='.\Fields\Models\Value::getThumbTag($field['date_added']); ?>
<li><a data-lightbox="field-<?php echo Zira\Helper::html($field['id']) ?>" href="<?php echo Zira\Helper::html(Zira\Helper::baseUrl($value)) ?>"><img src="<?php echo Zira\Helper::html($src) ?>" alt="<?php echo Zira\Helper::html($title) ?>" width="<?php echo Zira\Config::get('thumbs_width'); ?>" height="<?php echo Zira\Config::get('thumbs_height'); ?>" /></a></li>
<?php endforeach; ?>
</ul>
<?php endif; // image ?>
</div><!--/.dd-->
</div><!--/.dl-->
<?php endforeach; ?>
</div><!--/.fields-tab-content-wrapper-->
</div>
<?php $co++; ?>
<?php endforeach; ?>
</div><!--/.tab-content-->
</div>
<?php endif; ?>