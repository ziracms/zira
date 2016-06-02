<?php
/**
 * Zira project.
 * logs.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Logs extends Model {
    public function info($file) {
        if (!Permission::check(Permission::TO_VIEW_FILES) || !Permission::check(Permission::TO_EXECUTE_TASKS)) {
            return array();
        }
        $file = trim($file, DIRECTORY_SEPARATOR);
        if (empty($file) || strpos($file,'..')!==false) return array();
        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . LOG_DIR . DIRECTORY_SEPARATOR . $file)) return array();
        $is_dir = is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . LOG_DIR . DIRECTORY_SEPARATOR . $file);

        $info = array();
        //$info[]='<span class="glyphicon glyphicon-tag"></span> '.Zira\Helper::html($file);
        $info[]='<span class="glyphicon glyphicon-time"></span> '.date(Zira\Config::get('date_format'),filemtime(ROOT_DIR . DIRECTORY_SEPARATOR . LOG_DIR . DIRECTORY_SEPARATOR . $file));
        if (!$is_dir) {
            $info[]='<span class="glyphicon glyphicon-hdd"></span> '.number_format(filesize(ROOT_DIR . DIRECTORY_SEPARATOR . LOG_DIR . DIRECTORY_SEPARATOR . $file) / 1024, 2).' kB';
        }
        return $info;
    }

    public function delete($data) {
        if (empty($data) || !is_array($data)) return array();
        if (!Permission::check(Permission::TO_DELETE_FILES) || !Permission::check(Permission::TO_EXECUTE_TASKS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $dirs = array();
        $files = array();
        foreach($data as $item) {
            $item = trim((string)$item,DIRECTORY_SEPARATOR);
            if (strpos($item,'..')!==false) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            $path = ROOT_DIR . DIRECTORY_SEPARATOR . LOG_DIR . DIRECTORY_SEPARATOR . $item;
            if (!file_exists($path)) return array('error' => Zira\Locale::t('An error occurred'));
            if (is_dir($path)) {
                $dirs[]=$path;
                $stack = array(LOG_DIR . DIRECTORY_SEPARATOR . $item);
                while(count($stack)>0) {
                    $d = array_shift($stack);
                    $_files = scandir(ROOT_DIR . DIRECTORY_SEPARATOR . $d);
                    foreach($_files as $file) {
                        if ($file=='.' || $file=='..') continue;
                        if (is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . $d . DIRECTORY_SEPARATOR . $file)) {
                            $dirs[]=ROOT_DIR . DIRECTORY_SEPARATOR . $d . DIRECTORY_SEPARATOR . $file;
                            $stack[]=$d . DIRECTORY_SEPARATOR . $file;
                            continue;
                        }
                        $files[]=ROOT_DIR . DIRECTORY_SEPARATOR . $d . DIRECTORY_SEPARATOR . $file;
                    }
                }
            } else {
                $files[]=$path;
            }
        }
        $dirs=array_reverse($dirs);
        $paths = array_merge($files, $dirs);
        foreach($paths as $path) {
            if (is_dir($path)) {
                if (!@rmdir($path)) return array('error' => Zira\Locale::t('An error occurred'));
            } else {
                if (!@unlink($path)) return array('error' => Zira\Locale::t('An error occurred'));
            }
        }
        return array('reload' => $this->getJSClassName());
    }
}