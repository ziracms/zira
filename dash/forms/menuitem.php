<?php
/**
 * Zira project.
 * menuitem.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Menuitem extends Form
{
    protected $_id = 'dash-menu-item-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';

    public function __construct()
    {
        parent::__construct($this->_id);
    }

    protected function _init()
    {
        $this->setRenderPanel(false);
        $this->setFormClass('form-horizontal dash-window-form');
    }

    protected function _render()
    {
        $html = $this->open();
        $html .= $this->hidden('id');
        $html .= $this->hidden('menu_id');
        $html .= $this->hidden('parent_id');
        if (count(Zira\Config::get('languages'))<2) {
            $html .= $this->hidden('language');
        } else {
            $languagesArr = array_merge(array(''=>Locale::t('All languages')), Locale::getLanguagesArray());
            $html .= $this->selectDropdown(Locale::t('Language').'*','language',$languagesArr);
        }
        $html .= $this->input(Locale::t('URL') . '*', 'url');
        $html .= $this->input(Locale::t('Title') . '*', 'title');
        $html .= $this->input(Locale::t('Class'), 'class', array('title'=>Zira\Locale::t('Predefined classes: %s','menu-primary, menu-success, menu-info, menu-warning, menu-danger, menu-default')));
        $html .= $this->checkbox(Locale::t('Display item'), 'active', null, false);
        $html .= $this->checkbox(Locale::t('Open in new tab'), 'external', null, false);
        $html .= $this->close();
        return $html;
    }

    protected function _validate()
    {
        $validator = $this->getValidator();
        $validator->registerCustom(array(get_class(), 'checkLanguage'), 'language', Locale::t('An error occurred'));
        $validator->registerCustom(array(get_class(), 'checkMenu'), 'menu_id', Locale::t('An error occurred'));
        $validator->registerCustom(array(get_class(), 'checkParent'), 'parent_id', Locale::t('An error occurred'));
        $validator->registerString('url', null, 255, true, Locale::t('Invalid value "%s"',Locale::t('URL')));
        $validator->registerNoTags('url', Locale::t('Invalid value "%s"',Locale::t('URL')));
        $validator->registerUtf8('url', Locale::t('Invalid value "%s"',Locale::t('URL')));
        $validator->registerString('title', null, 255, true, Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerNoTags('title', Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerUtf8('title', Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerString('class', null, 255, false, Locale::t('Invalid value "%s"',Locale::t('Class')));
        $validator->registerNoTags('class', Locale::t('Invalid value "%s"',Locale::t('Class')));
        $validator->registerUtf8('class', Locale::t('Invalid value "%s"',Locale::t('Class')));
    }

    public static function checkLanguage($language) {
        if (empty($language)) return true;
        return in_array($language , Zira\Config::get('languages'));
    }

    public static function checkMenu($menu_id) {
        //return $menu_id == Zira\Menu::MENU_PRIMARY || $menu_id == Zira\Menu::MENU_SECONDARY || $menu_id == Zira\Menu::MENU_FOOTER;
        return is_numeric($menu_id) && $menu_id>0;
    }

    public static function checkParent($parent_id) {
        if ($parent_id) {
            $parent = new Zira\Models\Menu($parent_id);
            return $parent->loaded();
        } else {
            return true;
        }
    }
}