<?php
/**
 * Zira project.
 * category.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Forum\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Category extends Form
{
    protected $_id = 'dash-forum-category-form';

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
        if (count(Zira\Config::get('languages'))<2) {
            $html .= $this->hidden('language');
        } else {
            $html .= $this->selectDropdown(Locale::t('Language').'*','language',array_merge(array(''=>Locale::t('All languages')), Locale::getLanguagesArray()));
        }
        $html .= $this->input(Locale::t('Title').'*', 'title');
        $html .= $this->selectDropdown(Locale::t('Layout'),'layout',array_merge(array(Locale::t('Default layout')),Zira\View::getLayouts()));
        $html .= $this->textarea(Locale::t('Description'), 'description');
        $html .= $this->input(Locale::t('Window title'), 'meta_title');
        $html .= $this->input(Locale::t('Keywords'), 'meta_keywords');
        $html .= $this->input(Locale::tm('Meta description', 'forum'), 'meta_description');
        $html .= $this->checkbox(Locale::t('Restrict access'), 'access_check', null, false);
        $html .= $this->close();
        return $html;
    }

    protected function _validate()
    {
        $validator = $this->getValidator();

        $validator->registerString('title', null, 255, true, Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerNoTags('title', Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerUtf8('title', Locale::t('Invalid value "%s"',Locale::t('Title')));

        $validator->registerNoTags('description', Locale::t('Invalid value "%s"',Locale::t('Description')));
        $validator->registerUtf8('description', Locale::t('Invalid value "%s"',Locale::t('Description')));

        $validator->registerString('meta_title', null, 255, false, Locale::t('Invalid value "%s"',Locale::t('Window title')));
        $validator->registerNoTags('meta_title', Locale::t('Invalid value "%s"',Locale::t('Window title')));
        $validator->registerUtf8('meta_title', Locale::t('Invalid value "%s"',Locale::t('Window title')));

        $validator->registerString('meta_keywords', null, 255, false, Locale::t('Invalid value "%s"',Locale::t('Keywords')));
        $validator->registerNoTags('meta_keywords', Locale::t('Invalid value "%s"',Locale::t('Keywords')));
        $validator->registerUtf8('meta_keywords', Locale::t('Invalid value "%s"',Locale::t('Keywords')));

        $validator->registerString('meta_description', null, 255, false, Locale::t('Invalid value "%s"',Locale::tm('Meta description', 'forum')));
        $validator->registerNoTags('meta_description', Locale::t('Invalid value "%s"',Locale::tm('Meta description', 'forum')));
        $validator->registerUtf8('meta_description', Locale::t('Invalid value "%s"',Locale::tm('Meta description', 'forum')));

        $validator->registerCustom(array(get_class(), 'checkLayout'), 'layout', Locale::t('Invalid value "%s"',Locale::t('Layout')));
        $validator->registerCustom(array(get_class(), 'checkLanguage'), 'language', Locale::t('An error occurred'));
    }

    public static function checkLayout($layout) {
        if (empty($layout)) return true;
        $layouts = Zira\View::getLayouts();
        return array_key_exists($layout, $layouts);
    }

    public static function checkLanguage($language) {
        if (empty($language)) return true;
        return in_array($language , Zira\Config::get('languages'));
    }
}