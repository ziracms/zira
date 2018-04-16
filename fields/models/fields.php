<?php
/**
 * Zira project.
 * fields.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Models;

use Zira;
use Dash;
use Zira\Permission;

class Fields extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Fields\Forms\Field();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');

            if ($id) {
                $field = new \Fields\Models\Field($id);
                if (!$field->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            } else {
                $max_order = \Fields\Models\Field::getCollection()->max('sort_order')->get('mx');
                $field = new \Fields\Models\Field();
                $field->sort_order = ++$max_order;
            }
            $field->title = $form->getValue('title');
            $field->description = $form->getValue('description');
            $field->field_group_id = $form->getValue('field_group_id');
            $field->field_type = $form->getValue('field_type');
            
            $form_field_values = $form->getValue('form_field_values');
            if (is_array($form_field_values) && !empty($form_field_values)) {
                $_field_values = '';
                foreach($form_field_values as $form_field_value) {
                    if (empty($form_field_value)) continue;
                    if (strlen($_field_values)>0) $_field_values .= ',';
                    $_field_values .= $form_field_value;
                }
                if (empty($_field_values)) $_field_values = null;
                $field->field_values = $_field_values;
            } else {
                $field->field_values = null;
            }
            
            $field->preview = (int)$form->getValue('preview') ? 1 : 0;
            $field->active = (int)$form->getValue('active') ? 1 : 0;

            $field->save();

            return array('message'=>Zira\Locale::t('Successfully saved'), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }

    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($data as $field_id) {
            $field = new \Fields\Models\Field($field_id);
            if (!$field->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            // deleting active field is not allowed
            if ($field->active) {
                return array('error' => Zira\Locale::tm('Cannot delete active field', 'fields'));
            }
            $field->delete();

            \Fields\Models\Value::getCollection()
                                ->where('field_item_id','=',$field_id)
                                ->delete()
                                ->execute();
        }

        return array('reload' => $this->getJSClassName());
    }
    
    public function drag($items, $orders) {
        if (empty($items) || !is_array($items) || count($items)<2 || empty($orders) || !is_array($orders) || count($orders)<2 || count($items)!=count($orders)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $_items = array();
        $_orders = array();
        foreach($items as $id) {
            $_item = new \Fields\Models\Field($id);
            if (!$_item->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            $_items []= $_item;
            $_orders []= $_item->sort_order;
        }
        foreach($orders as $order) {
            if (!in_array($order, $_orders)) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
        }
        foreach($_items as $index=>$item) {
            $item->sort_order = intval($orders[$index]);
            $item->save();
        }

        return array('reload'=>$this->getJSClassName());
    }
}