<?php
/**
 * Zira project.
 * upload.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Dash\Forms;

use Dash\Windows\Files;
use Zira\Form;
use Zira\Locale;
use Zira\Permission;

class Upload extends Form {
    protected $_id = 'dash-upload-form';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $this->setMultipart(true);
        $this->setRenderPanel(false);
        $this->setWrapElements(false);
        $this->setFormClass('dashwindow-upload-form');
    }

    protected function _render() {
        $html = $this->open();
        $html .= $this->file(null, 'files', array('style'=>'width:100%;cursor:pointer'), true);
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();
        $validator->registerCustom(array(get_class(), 'checkPermission'), 'files', Locale::t('Permission denied'));
        if (Permission::check(Permission::TO_UPLOAD_FILES)) {
            $validator->registerFile('files',0,array(),true,Locale::t('Invalid file'));
        } else {
            $validator->registerImage('files',0,true,Locale::t('Invalid image'));
        }
        $validator->registerString('dirroot',0,0,true,Locale::t('Invalid directory'));
        $validator->registerCustom(array(get_class(), 'checkDirectory'), 'dirroot', Locale::t('Invalid directory'));
    }

    public static function checkPermission($files) {
        if (!Permission::check(Permission::TO_UPLOAD_FILES) && !Permission::check(Permission::TO_UPLOAD_IMAGES)) return false;
        if (is_array($files) && isset($files['name']) && is_array($files['name'])) {
            foreach($files['name'] as $name) {
                if (strtolower(substr($name, -4))=='.php') return false;
            }
        }
        return true;
    }

    public static function checkDirectory($dir) {
        if ($dir!=UPLOADS_DIR && strpos($dir, UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0) return false;
        if (strpos($dir, UPLOADS_DIR . DIRECTORY_SEPARATOR . Files::THUMBS_FOLDER)===0) return false;
        if (strpos($dir,'..')!==false) return false;
        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $dir) || !is_dir(ROOT_DIR . DIRECTORY_SEPARATOR . $dir)) return false;
        return true;
    }
}