<?php
/**
 * Zira project.
 * eform.php
 * (c)2016 http://dro1d.ru
 */

namespace Eform\Models;

use Zira;
use Zira\Orm;

class Eform extends Orm {
    const WIDGET_CLASS = '\Eform\Widgets\Eform';
    const BUTTON_CLASS = '\Eform\Widgets\Button';
    
    public static $table = 'eforms';
    public static $pk = 'id';
    public static $alias = 'efm';

    public static function getTable() {
        return self::$table;
    }

    public static function getPk() {
        return self::$pk;
    }

    public static function getAlias() {
        return self::$alias;
    }

    public static function getReferences() {
        return array(

        );
    }

    public static function sendEmail($eform, $fields, $form) {
        $message = Zira\Locale::t($eform->title)."\r\n\r\n";
        $files = array();
        $name_prefix = $form->getNamePrefix();
        foreach ($fields as $field) {
            $label = Zira\Locale::t($field->label);
            $name = $name_prefix.$field->id;
            $value = $form->getValue($name);
            if ($field->field_type == 'email' ||
                $field->field_type == 'input' ||
                $field->field_type == 'datepicker' ||
                $field->field_type == 'textarea'
            ) {
                if (empty($value)) $value = '-';
                $message .= $label .': '.$value."\r\n";
            } else if ($field->field_type == 'file') {
                if (is_array($value) && ($saved = Zira\File::save($value, TMP_DIR))!==false) {
                    $value = '';
                    foreach ($saved as $path => $name) {
                        $files [] = $path;
                        $value .= $name.' ';
                    }
                } else {
                    $value = '-';
                }
                $message .= $label .': '.$value."\r\n";
            } else if ($field->field_type == 'checkbox') {
                $value = $value ? Zira\Locale::tm('Yes', 'eform') : Zira\Locale::tm('No', 'eform');
                $message .= $label .': '.$value."\r\n";
            } else if ($field->field_type == 'radio') {
                $value_titles = explode(',', $field->field_values);
                $value = array_key_exists($value, $value_titles) ? $value_titles[$value] : Zira\Locale::tm('No', 'eform');
                $message .= $label .': '.$value."\r\n";
            } else if ($field->field_type == 'select') {
                $options = explode(',', Zira\Locale::tm('No', 'eform').','.$field->field_values);
                $value = array_key_exists($value, $options) ? $options[$value] : Zira\Locale::tm('No', 'eform');
                $message .= $label .': '.$value."\r\n";
            }
        }
        $message .= "\r\n".Zira\Locale::tm('Sent on %s', 'eform', date(Zira\Config::get('date_format')));

        Zira\Mail::send($eform->email, Zira\Locale::t($eform->title), Zira\Helper::html($message), !empty($files) ? $files : null);

        if (!empty($files)) {
            foreach($files as $file) {
                if (!file_exists($file)) continue;
                @unlink($file);
            }
        }
    }
}