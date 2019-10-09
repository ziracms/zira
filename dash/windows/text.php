<?php
/**
 * Zira project.
 * text.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Text extends Editor {
    public function init() {
        parent::init();

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        parent::create();

        $this->addVariables(array(
            'dash_text_wnd' => $this->getJSClassName()
        ));

        $this->includeJS('dash/text');
    }

    protected function getTextOnLoadJs() {
        return 'desk_call(dash_text_load, this);'.
                parent::getTextOnLoadJs()
                ;
    }

    public function load() {
        if (!Permission::check(Permission::TO_VIEW_FILES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $file = (string)Zira\Request::post('file');

        $file = trim($file, DIRECTORY_SEPARATOR);
        if (empty($file) || strpos($file,'..')!==false) return array('error'=>Zira\Locale::t('An error occurred'));
        if (strpos($file,UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0 && strpos($file,LOG_DIR.DIRECTORY_SEPARATOR)!==0) return array('error'=>Zira\Locale::t('An error occurred'));
        if (strpos($file,LOG_DIR.DIRECTORY_SEPARATOR)===0 && !Permission::check(Permission::TO_EXECUTE_TASKS)) return array('error'=>Zira\Locale::t('Permission denied'));

        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($path) || !is_readable($path)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $content = file_get_contents($path);

        $this->setBodyFullContent(
            $this->getBodyContent($content, 'file', $file, (string)Zira\Request::post('id'))
        );

        $this->setData(array(
            'content' => null,
            'file' => $file
        ));
    }
}