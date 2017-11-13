<?php
/**
 * Zira project.
 * style.php
 * (c)2017 http://dro1d.ru
 */

namespace Designer\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Style extends Form
{
    protected $_id = 'designer-style-form';

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
        $categories = Zira\Models\Category::getArray();
        $_categories = array(
            -1 => Locale::t('All pages'),
            -2 => Locale::t('Choose page...'),
            -3 => Locale::t('Set URL...'),
            Zira\Category::ROOT_CATEGORY_ID => Locale::t('Home page')
        );
        $categories = $_categories + $categories;

        $filter_attr = array('class'=>'form-control filter-select');

        $filters = array_merge(array(
            '' => Locale::t('Do not apply filter')
        ), \Designer\Models\Style::getFiltersArray());
        
        $html = $this->open();
        $html .= $this->hidden('id');
        $html .= $this->input(Locale::t('Title') . '*', 'title');
        $html .= $this->checkbox(Locale::tm('main style', 'designer'), 'main', array('class'=>'form-control dash_form_designer_main_style_checkbox'), false);
        $html .= $this->select(Locale::t('Page').' / '.Locale::t('Category'),'category_id',$categories, array('class'=>'form-control dash_form_designer_record_select'));
        
        $html .= $this->hidden('record_id', array('class'=>'dash_form_designer_record_hidden'));
        $html .= Zira\Helper::tag_open('div', array('class'=>'dash_form_designer_record_container'));
        $html .= $this->input(Locale::t('Choose page'),'record',array('class'=>'form-control dash_form_designer_record_input', 'placeholder'=>Zira\Locale::t('Enter page title or system name')));
        $html .= Zira\Helper::tag_close('div');
        
        $html .= Zira\Helper::tag_open('div', array('class'=>'dash_form_designer_url_container'));
        $html .= $this->input(Locale::t('URL address'),'url',array('class'=>'form-control dash_form_designer_url_input'));
        $html .= Zira\Helper::tag_close('div');
        
        if (count(Zira\Config::get('languages'))<2) {
            $html .= $this->hidden('language');
        } else {
            $languagesArr = array_merge(array(''=>Locale::t('All languages')), Locale::getLanguagesArray());
            $html .= $this->select(Locale::t('Language'),'language',$languagesArr, array('class'=>'form-control language-select'));
        }
        $html .= $this->select(Locale::t('Filter'),'filter',$filters, $filter_attr);
        $html .= $this->checkbox(Locale::t('active'), 'active', null, false);
        $html .= $this->close();
        return $html;
    }

    protected function _validate()
    {
        $validator = $this->getValidator();
        $validator->registerString('title', null, 255, true, Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerUtf8('title', Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerCustom(array(get_class(), 'checkLanguage'), 'language', Locale::t('An error occurred'));
        $validator->registerNumber('category_id',null,null,false, Locale::t('Invalid value "%s"',Locale::t('Page').' / '.Locale::t('Category')));
        $validator->registerCustom(array(get_class(), 'checkCategory'), 'category_id', Locale::t('Invalid value "%s"',Locale::t('Page').' / '.Locale::t('Category')));
        $validator->registerCustom(array(get_class(), 'checkFilter'), 'filter', Locale::t('Invalid value "%s"',Locale::t('Filter')));
        $validator->registerNumber('record_id',null,null,false, Locale::t('Invalid value "%s"',Locale::t('Page').' / '.Locale::t('Category')));
        $validator->registerString('url', null, 255, false, Locale::t('Invalid value "%s"',Locale::t('Page').' / '.Locale::t('Category')));
    }
    
    public static function checkLanguage($language) {
        if (empty($language)) return true;
        return in_array($language , Zira\Config::get('languages'));
    }

    public static function checkCategory($category_id) {
        if (empty($category_id)) return true;
        $category_id = intval($category_id);
        $categories = Zira\Models\Category::getArray();
        return $category_id===-1 || $category_id===-2 || $category_id===-3 || $category_id===0 || array_key_exists($category_id, $categories);
    }

    public static function checkFilter($filter) {
        if (empty($filter)) return true;
        return array_key_exists($filter, \Designer\Models\Style::getFiltersArray());
    }
}