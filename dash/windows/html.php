<?php
/**
 * Zira project.
 * html.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Html extends Editor {
    public function init() {
        parent::init();
        $this->setWysiwyg(true);

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        parent::create();

        $this->addVariables(array(
            'dash_html_wnd' => $this->getJSClassName()
        ));

        $this->includeJS('dash/html');
    }

    protected function getHtmlOnLoadJs() {
        return 'desk_call(dash_html_load, this);'.
                parent::getHtmlOnLoadJs()
                ;
    }

    public function load() {
        if (!Permission::check(Permission::TO_VIEW_FILES)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $id = (string)Zira\Request::post('id');
        $file = (string)Zira\Request::post('file');

        $file = trim($file, DIRECTORY_SEPARATOR);
        if (empty($file) || strpos($file,'..')!==false || strpos($file,UPLOADS_DIR.DIRECTORY_SEPARATOR)!==0) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        if (substr($file,-5)!='.html') {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($path) || !is_readable($path)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $content = file_get_contents($path);

        $this->setBodyFullContent(
            $this->getBodyContent($content, 'file', $file, $id)
        );

        $this->setData(array(
            'content' => null,
            'file' => $file
        ));
    }
}