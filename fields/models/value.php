<?php
/**
 * Zira project.
 * value.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Models;

use Zira;
use Zira\Orm;

class Value extends Orm {
    public static $table = 'field_values';
    public static $pk = 'id';
    public static $alias = 'fld_val';
    
    const THUMBS_SUBDIR = 'fields';

    public static function getFields() {
        return array(
            'id',
            'record_id',
            'field_item_id',
            'field_group_id',
            'content',
            'mark',
            'date_added'
        );
    }
    
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
            Zira\Models\Record::getClass() => 'record_id',
            Field::getClass() => 'field_item_id',
            Group::getClass() => 'field_group_id'
        );
    }
    
    public static function clearRecordValues($record_id, array $field_ids = null) {
        $query = self::getCollection()
                    ->delete()
                    ->where('record_id', '=', $record_id)
                    ;
        
        if ($field_ids !== null) {
            $query->and_where('field_item_id', 'in', $field_ids);
        }
        
        return $query->execute();
    }
    
    public static function getRecordValues($record_id, array $field_ids = null) {
        $query = self::getCollection()
                    ->where('record_id', '=', $record_id)
                    ;
        
        if ($field_ids !== null) {
            $query->and_where('field_item_id', 'in', $field_ids);
        }
        
        return $query->get();
    }
    
    public static function getRecordsValues(array $record_ids, array $field_ids = null) {
        $query = self::getCollection()
                    ->where('record_id', 'in', $record_ids)
                    ;
 
        if ($field_ids !== null) {
            $query->and_where('field_item_id', 'in', $field_ids);
        }
        
        return $query->get();
    }
    
    public static function loadRecordValues($record_id) {
        $values = array();
        $_values = self::getRecordValues($record_id);
        foreach($_values as $value) {
            $values[$value->field_item_id] = $value;
        }
        return $values;
    }
    
    public static function loadRecordsValues(array $record_ids, array $field_ids = null) {
        $values = array();
        $_values = self::getRecordsValues($record_ids, $field_ids);
        foreach($_values as $value) {
            if (!array_key_exists($value->record_id, $values)) $values[$value->record_id] = array();
            $values[$value->record_id][$value->field_item_id] = $value;
        }
        return $values;
    }
    
    public static function createImageThumb($url, $recreate = false) {
        $path = str_replace('/', DIRECTORY_SEPARATOR, rawurldecode($url));
        if (strpos($path, UPLOADS_DIR)!==0) return false;
        $src_path = ROOT_DIR . DIRECTORY_SEPARATOR . $path;
        $_path = substr($path, 0, (int)strrpos($path, DIRECTORY_SEPARATOR));
        $_path = substr($_path, strlen(UPLOADS_DIR . DIRECTORY_SEPARATOR));
        $savedir = THUMBS_DIR . DIRECTORY_SEPARATOR . self::THUMBS_SUBDIR;
        if (!empty($_path)) $savedir .= DIRECTORY_SEPARATOR . $_path;
        $save_path = Zira\File::getAbsolutePath($savedir);
        $name = ltrim(substr($path, (int)strrpos($path, DIRECTORY_SEPARATOR)), DIRECTORY_SEPARATOR);
        if (file_exists($save_path . DIRECTORY_SEPARATOR . $name) && filesize($save_path . DIRECTORY_SEPARATOR . $name)>0 && !$recreate) {
            return UPLOADS_DIR . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $savedir) . '/' . $name;
        } else if (file_exists($save_path . DIRECTORY_SEPARATOR . $name)) {
            @unlink($save_path . DIRECTORY_SEPARATOR . $name);
        }
        if (file_exists($src_path) && Zira\Image::createThumb($src_path, $save_path . DIRECTORY_SEPARATOR . $name, Zira\Config::get('thumbs_width'), Zira\Config::get('thumbs_height'))) {
            return $save_path . DIRECTORY_SEPARATOR . $name;
        } else {
            return false;
        }
    }
    
    public static function getImageThumb($url) {
        $path = str_replace('/', DIRECTORY_SEPARATOR, rawurldecode($url));
        if (strpos($path, UPLOADS_DIR)!==0) return false;
        $src_path = ROOT_DIR . DIRECTORY_SEPARATOR . $path;
        $_path = substr($path, 0, (int)strrpos($path, DIRECTORY_SEPARATOR));
        $_path = substr($_path, strlen(UPLOADS_DIR . DIRECTORY_SEPARATOR));
        $savedir = THUMBS_DIR . DIRECTORY_SEPARATOR . self::THUMBS_SUBDIR;
        if (!empty($_path)) $savedir .= DIRECTORY_SEPARATOR . $_path;
        $save_path = ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR . DIRECTORY_SEPARATOR . $savedir;
        $name = ltrim(substr($path, (int)strrpos($path, DIRECTORY_SEPARATOR)), DIRECTORY_SEPARATOR);
        if (file_exists($save_path . DIRECTORY_SEPARATOR . $name)) {
            return Zira\Helper::urlencode(UPLOADS_DIR . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $savedir) . '/' . $name);
        } else {
            return $url;
        }
    }
    
    public static function getThumbTag($date_added) {
        return strtotime($date_added);
    }
}