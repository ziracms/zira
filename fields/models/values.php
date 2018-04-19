<?php
/**
 * Zira project.
 * values.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Models;

use Zira;
use Dash;
use Zira\Permission;

class Values extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_EDIT_RECORDS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Fields\Forms\Value();
        $record_id = (int) Zira\Request::post($form->getFieldName('record_id'));
        $record = new Zira\Models\Record($record_id);
        if (!$record->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
        $form->loadFields($record);
        if ($form->isValid()) {
            \Fields\Models\Value::clearRecordValues($record->id);
            
            $name_prefix = $form->getNamePrefix();
            $fields = $form->getFields();
            $preview_fields = array();
            $preview_groups = array();
            foreach ($fields as $group_id=>$fields_group) {
                $group = $fields_group['group'];
                foreach($fields_group['fields'] as $field) {
                    $name = $name_prefix.$field->field_id;
                    $value = $form->getValue($name);
                    $content = '';
                    if ($field->field_type == 'input' || 
                        $field->field_type == 'textarea' || 
                        $field->field_type == 'link' ||
                        $field->field_type == 'html'
                    ) {
                        $content = $value;
                    } else if ($field->field_type == 'checkbox') {
                        $content = $value ? 1 : null;
                    } else if ($field->field_type == 'radio') { 
                        $value_titles = explode(',', $field->field_values);
                        $content = in_array($value, $value_titles) ? $value : null;
                    } else if ($field->field_type == 'select') { 
                        $value_titles = explode(',', ','.$field->field_values);
                        $content = $value && in_array($value, $value_titles) ? $value : null;
                    } else if ($field->field_type == 'multiple') {
                        $value = (array)$value;
                        $value_titles = explode(',', $field->field_values);
                        $content = array();
                        foreach($value as $val) {
                            if (in_array($val, $value_titles)) $content []= $val;
                        }
                        $content = implode(',', $content);
                    } else if ($field->field_type == 'file' || 
                              $field->field_type == 'image'
                    ) {
                        $value = (array)$value;
                        $content = array();
                        foreach($value as $val) {
                            if (!empty($val)) $content []= $val;
                        }
                        $content = implode(',', $content);
                    }
                    
                    //if (empty($content)) $content = null;
                    if (empty($content)) continue;
                    
                    $valueObj = new \Fields\Models\Value();
                    $valueObj->record_id = $record->id;
                    $valueObj->field_item_id = $field->field_id;
                    $valueObj->field_group_id = $field->group_id;
                    $valueObj->content = $content;
                    $valueObj->date_added = date('Y-m-d H:i:s');
                    $valueObj->mark = $field->field_type;
                    $valueObj->save();
                    
                    if ($field->field_preview) {
                        if (!array_key_exists($field->group_id, $preview_fields)) {
                            $preview_fields[$field->group_id] = array(
                                'group' => array(
                                    'id' => $field->group_id,
                                    'title' => $field->group_title
                                ),
                                'fields' => array()
                            );
                        }
                        $preview_fields[$field->group_id]['fields'][]=array(
                            'id' => $field->field_id,
                            'title' => $field->field_title,
                            'content' => $content
                        );
                    }
                }
                
                if (!empty($preview_fields)) {
                    $preview_description = serialize($preview_fields);
                    $record->extradata = $preview_description;
                    $record->save();
                }
            }

            return array('message'=>Zira\Locale::t('Successfully saved'), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }
}