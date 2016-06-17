<?php
/**
 * Zira project.
 * eformfields.php
 * (c)2016 http://dro1d.ru
 */

namespace Eform\Models;

use Zira;
use Dash;
use Eform;
use Zira\Permission;

class Eformfields extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new Eform\Forms\Eformfield();
        if ($form->isValid()) {
            $id = (int)$form->getValue('id');
            $eform_id = (int)$form->getValue('eform_id');

            if ($id) {
                $eformfield = new Eform\Models\Eformfield($id);
                if (!$eformfield->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            } else {
                $max_order = Eform\Models\Eformfield::getCollection()->max('sort_order')->get('mx');

                $eformfield = new Eform\Models\Eformfield();
                $eformfield->eform_id = $eform_id;
                $eformfield->sort_order = ++$max_order;
            }
            $eformfield->field_type = $form->getValue('field_type');
            $eformfield->label = $form->getValue('label');
            $description = $form->getValue('description');
            $eformfield->description = !empty($description) ? $description : null;
            $eformfield->required = (int)$form->getValue('required') ? 1 : 0;

            $form_field_values = $form->getValue('form_field_values');
            if (is_array($form_field_values) && !empty($form_field_values)) {
                $_field_values = '';
                foreach($form_field_values as $form_field_value) {
                    if (empty($form_field_value)) continue;
                    if (strlen($_field_values)>0) $_field_values .= ',';
                    $_field_values .= $form_field_value;
                }
                if (empty($_field_values)) $_field_values = null;
                $eformfield->field_values = $_field_values;
            } else {
                $eformfield->field_values = null;
            }

            $eformfield->save();

            return array('message'=>Zira\Locale::t('Successfully saved'), 'close'=>true);
        } else {
            return array('error'=>$form->getError());
        }
    }

    public function delete($data) {
        if (empty($data) || !is_array($data)) return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        foreach($data as $eformfield_id) {
            $eformfield = new Eform\Models\Eformfield($eformfield_id);
            if (!$eformfield->loaded()) {
                return array('error' => Zira\Locale::t('An error occurred'));
            };
            $eformfield->delete();
        }

        return array('reload' => $this->getJSClassName());
    }

    public function drag($eform_id, $fields, $orders) {
        if (empty($eform_id)) return array('error' => Zira\Locale::t('An error occurred'));
        if (empty($fields) || !is_array($fields) || count($fields)<2 || empty($orders) || !is_array($orders) || count($orders)<2 || count($fields)!=count($orders)) {
            return array('error' => Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $_fields = array();
        $_orders = array();
        foreach($fields as $id) {
            $_field = new Eform\Models\Eformfield($id);
            if (!$_field->loaded() || $_field->eform_id != $eform_id) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            $_fields []= $_field;
            $_orders []= $_field->sort_order;
        }
        foreach($orders as $order) {
            if (!in_array($order, $_orders)) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
        }
        foreach($_fields as $index=>$field) {
            $field->sort_order = intval($orders[$index]);
            $field->save();
        }

        return array('reload'=>$this->getJSClassName());
    }
}