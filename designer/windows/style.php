<?php
/**
 * Zira project.
 * style.php
 * (c)2017 http://dro1d.ru
 */

namespace Designer\Windows;

use Zira;
use Dash;
use Zira\Permission;

class Style extends Dash\Windows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-picture';
    protected static $_title = 'Style';

    //protected $_help_url = 'zira/help/style';

    public $item;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_call(designer_style_load, this);'
            )
        );
    }

    public function load() {
        if (!empty($this->item)) $this->item=intval($this->item);
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $form = new \Designer\Forms\Style();
        if (!empty($this->item)) {
            $style = new \Designer\Models\Style($this->item);
            if (!$style->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
            
            $values = $style->toArray();
            
            if ($values['category_id']===null) {
                $values['category_id'] = -1;
            }
            if ($values['record_id']>0) {
                $values['category_id'] = -2;
                $record = new Zira\Models\Record($values['record_id']);
                if ($record->loaded()) $values['record'] = $record->title;
            } else if (strlen($values['url'])>0) {
                $values['category_id'] = -3;
            }
            if ($values['language']!==null && !in_array($values['language'],Zira\Config::get('languages'))) {
                $values['language'] = null;
            }
            
            $form->setValues($values);
            $this->setTitle(Zira\Locale::tm(self::$_title,'designer').' - '.$style->title);
        } else {
            $form->setValues(array(
                'category_id' => -1,
                'active' => 0
            ));
            $this->setTitle(Zira\Locale::tm('New style','designer'));
        }

        $this->setBodyContent($form);
    }
}