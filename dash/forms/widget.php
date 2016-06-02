<?php
/**
 * Zira project.
 * widget.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Widget extends Form
{
    protected $_id = 'dash-widget-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';
    protected $_select_wrapper_class = 'col-sm-8';

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
        $placeholders = Zira\Models\Widget::getPlaceholders();
        $categories = Zira\Models\Category::getArray();
        $_categories = array(
            -1 => Locale::t('All pages'),
            Zira\Category::ROOT_CATEGORY_ID => Locale::t('Home page')
        );
        $categories = $_categories + $categories;

        $filter_attr = array('class'=>'form-control filter-select');
        if ($this->getValue('category_id') == Zira\Category::ROOT_CATEGORY_ID) {
            $filter_attr['disabled'] = 'disabled';
        }

        $filters = array_merge(array(
            '' => Locale::t('Do not apply filter')
        ), Zira\Models\Widget::getFiltersArray());

        $html = $this->open();
        $html .= $this->hidden('id');
        $html .= $this->select(Locale::t('Placeholder').'*','placeholder',$placeholders);
        $html .= $this->select(Locale::t('Page').' / '.Locale::t('Category').'*','category_id',$categories, array('onchange'=>'if ($(this).val()==\'0\') $(this).parents(\'form\').find(\'.filter-select\').attr(\'disabled\',\'disabled\'); else $(this).parents(\'form\').find(\'.filter-select\').removeAttr(\'disabled\');'));
        if (count(Zira\Config::get('languages'))<2) {
            $html .= $this->hidden('language');
        } else {
            $languagesArr = array_merge(array(''=>Locale::t('All languages')), Locale::getLanguagesArray());
            $html .= $this->select(Locale::t('Language').'*','language',$languagesArr);
        }
        $html .= $this->select(Locale::t('Filter'),'filter',$filters, $filter_attr);
        $html .= $this->checkbox(Locale::t('active'),'active', null, false);
        $html .= $this->close();
        return $html;
    }

    protected function _validate()
    {
        $validator = $this->getValidator();
        $validator->registerCustom(array(get_class(), 'checkLanguage'), 'language', Locale::t('An error occurred'));
        $validator->registerString('placeholder', null, 255, true, Locale::t('Invalid value "%s"',Locale::t('Placeholder')));
        $validator->registerNumber('category_id',null,null,true, Locale::t('Invalid value "%s"',Locale::t('Page').' / '.Locale::t('Category')));
        $validator->registerCustom(array(get_class(), 'checkPlaceholder'), 'placeholder', Locale::t('Invalid value "%s"',Locale::t('Placeholder')));
        $validator->registerCustom(array(get_class(), 'checkCategory'), 'category_id', Locale::t('Invalid value "%s"',Locale::t('Page').' / '.Locale::t('Category')));
        $validator->registerCustom(array(get_class(), 'checkFilter'), 'filter', Locale::t('Invalid value "%s"',Locale::t('Filter')));
    }

    public static function checkLanguage($language) {
        if (empty($language)) return true;
        return in_array($language , Zira\Config::get('languages'));
    }

    public static function checkPlaceholder($placeholder) {
        $placeholders = Zira\Models\Widget::getPlaceholders();
        return array_key_exists($placeholder, $placeholders);
    }

    public static function checkCategory($category_id) {
        $category_id = intval($category_id);
        $categories = Zira\Models\Category::getArray();
        return $category_id===-1 || $category_id===0 || array_key_exists($category_id, $categories);
    }

    public static function checkFilter($filter) {
        if (empty($filter)) return true;
        return array_key_exists($filter, Zira\Models\Widget::getFiltersArray());
    }
}