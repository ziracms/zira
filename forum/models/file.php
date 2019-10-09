<?php
/**
 * Zira project.
 * file.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Forum\Models;

use Zira;
use Zira\Orm;

class File extends Orm {
    public static $table = 'forum_files';
    public static $pk = 'id';
    public static $alias = 'frm_fls';

    const SUBDIR = 'forum';
    const DEFAULT_MAX_SIZE = 1024;
    const DEFAULT_ALLOWED_EXTENSIONS = 'jpg, jpeg, png, gif, txt, rtf, doc, docx, xls, xlsx, pdf, zip';
    const BAD_EXTENSIONS = 'php, pl, sh, exe, bat, jar, jsp, asp, html, htm, js';
    const MAX_FILES_COUNT = 10;

    public static function getFields() {
        return array(
            'id',
            'message_id',
            'owner_id',
            'path1',
            'path2',
            'path3',
            'path4',
            'path5',
            'path6',
            'path7',
            'path8',
            'path9',
            'path10',
            'date_created'
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
            Message::getClass() => 'message_id',
            Zira\Models\User::getClass() => 'owner_id'
        );
    }

    public static function getFileFolder() {
        return self::SUBDIR . DIRECTORY_SEPARATOR . Zira\User::getCurrent()->id;
    }

    public static function storeFiles($files, &$refs = null) {
        if (empty($files) || !is_array($files)) return false;
        if (!Zira\User::isAuthorized()) return false;
        if (empty($files['name']) || !is_array($files['name'])) return false;
        if (empty($files['tmp_name']) || !is_array($files['tmp_name'])) return false;
        if (count($files['name'])>self::MAX_FILES_COUNT || count($files['tmp_name'])>self::MAX_FILES_COUNT) {
            $files['name'] = array_slice($files['name'], 0, self::MAX_FILES_COUNT);
            $files['tmp_name'] = array_slice($files['tmp_name'], 0, self::MAX_FILES_COUNT);
        }
        return Zira\File::save($files, self::getFileFolder(), $refs);
    }

    public static function saveFiles($files, $message_id) {
        if (empty($files) || !is_array($files)) return false;
        if (!Zira\User::isAuthorized()) return false;
        $existing = self::getCollection()->where('message_id', '=', $message_id)->get(0);
        
        if ($existing) {
            $row = new self($existing->id);
        } else {
            $row = new self();
            $row->message_id = $message_id;
            $row->owner_id = Zira\User::getCurrent()->id;
            $row->date_created = date('Y-m-d H:i:s');
        }
        
        $folder = self::getFileFolder();
        $co=1;
        if ($existing) {
            for ($z=1; $z<=self::MAX_FILES_COUNT; $z++) {
                if (!empty($existing->{'path'.$z})) $co=$z+1;
            }
        }
        if ($co<=self::MAX_FILES_COUNT) {
            foreach($files as $path=>$name) {
                $row->{'path'.$co} = $folder . DIRECTORY_SEPARATOR . $name;
                $co++;
                if ($co>self::MAX_FILES_COUNT) break;
            }
        }
        $row->save();
    }

    public static function parseContentFiles($files, &$content) {
        if (!is_array($files)) return;
        foreach($files as $name=>$path) {
            $search = '['.$name.']';
            if (strpos($content, $search)!==false && @getimagesize($path)) {
                $url = $path;
                if (strpos($url, ROOT_DIR)===0) $url = substr($url, strlen(ROOT_DIR));
                $url = str_replace(DIRECTORY_SEPARATOR, '/', $url);
                $content = str_replace($search, '[img]'.$url.'[/img]', $content);
            }
        }
    }

    public static function extractItemFiles($item, $field_prefix='', $type = 'images') {
        $files = array();
        for ($i=1; $i<=self::MAX_FILES_COUNT; $i++) {
            $field = $field_prefix.'path'.$i;
            if ($item->{$field}) {
                $p = strrpos($item->{$field}, '.');
                if ($p!==false) $ext = substr($item->{$field}, $p+1);
                else $ext = '';
                if (in_array($ext, array('jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG'))) $is_image = true;
                else $is_image = false;
                if ($is_image && $type != 'images') continue;
                if (!$is_image && $type == 'images') continue;
                $_p = strrpos($item->{$field}, DIRECTORY_SEPARATOR);
                if ($_p!==false) $filename = substr($item->{$field}, $_p+1);
                else $filename = $item->{$field};
                $files[$item->{$field}] = $filename;
            }
        }
        return $files;
    }

    public static function deleteFiles($message_id) {
        $row = self::getCollection()
                    ->where('message_id', '=', $message_id)
                    ->get(0);

        if (!$row) return;

        for ($i=1; $i<=\Forum\Models\File::MAX_FILES_COUNT; $i++) {
            if ($row->{'path'.$i}) {
                $path = ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR . DIRECTORY_SEPARATOR . $row->{'path'.$i};
                if (file_exists($path)) @unlink($path);
            }
        }

        self::getCollection()
                    ->delete()
                    ->where('message_id', '=', $message_id)
                    ->execute();
    }
}