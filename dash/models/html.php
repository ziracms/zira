<?php
/**
 * Zira project.
 * html.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Html extends Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_UPLOAD_FILES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        if (!isset($data['file']) || !isset($data['content'])) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }

        $file = $data['file'];
        $content = $data['content'];

        if (strlen($content)>2 && substr($content,0,2)=='#!') {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $file = trim($file, DIRECTORY_SEPARATOR);
        if (empty($file) || strpos($file,'..')!==false || strpos($file,UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        if (substr($file,-5)!='.html') {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($path) || !is_writable($path)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }

        if (file_put_contents($path, $content)===false) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }

        return array('message' => Zira\Locale::t('Successfully saved'));
    }
}