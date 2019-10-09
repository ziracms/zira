<?php
/**
 * Zira project
 * file.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira;

class File {
    const WIDGET_CLASS = '\Zira\Widgets\Carousel';
    const WIDGETS_FOLDER = 'widgets';
    
    public static function getAbsolutePath($dir) {
        $uploads_dir = ROOT_DIR . DIRECTORY_SEPARATOR . UPLOADS_DIR;
        if (empty($dir)) return $uploads_dir;

        $dirs = explode(DIRECTORY_SEPARATOR, $dir);
        foreach($dirs as $folder) {
            if ($folder == '.' || $folder == '..') continue;
            $uploads_dir .= DIRECTORY_SEPARATOR . $folder;
            if (!file_exists($uploads_dir)) {
                mkdir($uploads_dir, 0777);
            }
        }

        return $uploads_dir;
    }

    public static function getFileName($name, $suffix = '') {
        if (is_int($suffix)) {
            if ($suffix==0) $suffix = '';
            else $suffix = '-'.$suffix;
        }
        if (!empty($suffix)) {
            $p = strrpos($name,'.');
            if ($p!==false) {
                $name = substr($name,0,$p).$suffix.substr($name,$p);
            } else {
                $name .= $suffix;
            }
        }

        return FILES_PREFIX . $name;
    }

    public static function save(array $file, $dir = null, &$refs = null) {
        if (empty($file) || empty($file['name']) || empty($file['tmp_name'])) return false;

        $files = array();
        if (is_array($file['name']) && is_array($file['tmp_name'])) {
            foreach($file['tmp_name'] as $i=>$tmp_name) {
                if (empty($tmp_name)) continue;
                if (!isset($file['name'][$i]) || empty($file['name'][$i])) continue;
                $files[$tmp_name] = $file['name'][$i];
            }
        } else if (is_string($file['name']) && is_string($file['tmp_name'])) {
            $files[$file['tmp_name']] = $file['name'];
        } else {
            return false;
        }

        if (empty($files)) return false;

        $savedir = self::getAbsolutePath($dir);

        $_files = array();
        foreach($files as $path=>$name) {
            $prefix = 0;
            do {
                $_f = self::getFileName($name, $prefix);
                $f = $savedir . DIRECTORY_SEPARATOR . $_f;
                $prefix++;
            } while(file_exists($f));

            $refs[$name] = $f;
            if (!copy($path, $f)) return false;
            $_files[$f] = $_f;
        }

        return $_files;
    }

    public static function getFileArray($path) {
        return array(
            'name' => Helper::basename($path),
            'tmp_name' => $path
        );
    }
}