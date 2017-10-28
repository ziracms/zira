<?php
/**
 * Zira project.
 * designer.php
 * (c)2017 http://dro1d.ru
 */

namespace Designer\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Designer extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-picture';
    protected static $_title = 'Style editor';

    //protected $_help_url = 'zira/help/designer';

    public $item;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);
        $this->setMaximized(true);
        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->addDefaultToolbarItem(
            $this->createToolbarButton(Zira\Locale::t('Code'), Zira\Locale::t('CSS code'), 'glyphicon glyphicon-list-alt', 'desk_call(designer_designer_code, this);', 'code', false, true)
        );
        
        $this->setOnOpenJSCallback(
            $this->createJSCallback(
                'desk_call(designer_designer_open, this);'
            )
        );
        
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_call(designer_designer_load, this);'
            )
        );
        
        $this->setOnUpdateContentJSCallback(
            $this->createJSCallback(
                'desk_call(designer_designer_onsave, this);'
            )
        );
        
        $this->setOnCloseJSCallback(
            $this->createJSCallback(
                'desk_call(designer_designer_close, this);'
            )
        );
    }

    public function load() {
        if (!empty($this->item)) $this->item=intval($this->item);
        else return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $style = new \Designer\Models\Style($this->item);
        if (!$style->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
        $this->setTitle(Zira\Locale::tm(self::$_title,'designer').' - '.$style->title);

        $content = '';

        $this->setBodyContent($content);
    }
}