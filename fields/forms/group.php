<?php
/**
 * Zira project.
 * group.php
 * (c)2018 http://dro1d.ru
 */

namespace Fields\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Group extends Form
{
    protected $_id = 'fields-group-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';
    protected $_select_wrapper_class = 'col-sm-8';

    protected $_checkbox_inline_label = true;

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
        $placeholders = \Fields\Models\Group::getPlaceholders();
        $categories = array(Zira\Category::ROOT_CATEGORY_ID => Locale::t('All pages')) + Zira\Models\Category::getArray();
        
        $html = $this->open();
        $html .= $this->input(Locale::t('Title').'*', 'title');
        $html .= $this->textarea(Locale::t('Description'), 'description');
        $html .= $this->select(Locale::t('Placeholder').'*','placeholder',$placeholders);
        if (count(Zira\Config::get('languages'))<2) {
            $html .= $this->hidden('language');
        } else {
            $languagesArr = array_merge(array(''=>Locale::t('All languages')), Locale::getLanguagesArray());
            $html .= $this->select(Locale::t('Language').'*','language',$languagesArr, array('class'=>'form-control language-select'));
        }
        $html .= $this->select(Locale::t('Category'),'category_id',$categories);
        $html .= $this->checkbox(Locale::tm('group is active','fields'), 'active', null, false);
        $html .= $this->hidden('id');
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $validator->registerString('title',null,255,true,Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerNoTags('title',Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerUtf8('title',Locale::t('Invalid value "%s"',Locale::t('Title')));

        $validator->registerNoTags('description',Locale::t('Invalid value "%s"',Locale::t('Description')));
        $validator->registerUtf8('description',Locale::t('Invalid value "%s"',Locale::t('Description')));
        
        $validator->registerString('placeholder', null, 255, true, Locale::t('Invalid value "%s"',Locale::t('Placeholder')));
        $validator->registerCustom(array(get_class(), 'checkPlaceholder'), 'placeholder', Locale::t('Invalid value "%s"',Locale::t('Placeholder')));

                
        $validator->registerCustom(array(get_class(), 'checkLanguage'), 'language', Locale::t('An error occurred'));
        $validator->registerCustom(array(get_class(), 'checkCategory'), 'category_id', Locale::t('An error occurred'));
    }
    
    public static function checkPlaceholder($placeholder) {
        $placeholders = \Fields\Models\Group::getPlaceholders();
        return array_key_exists($placeholder, $placeholders);
    }
    
    public static function checkLanguage($language) {
        if (empty($language)) return true;
        return in_array($language , Zira\Config::get('languages'));
    }
    
    public static function checkCategory($category_id) {
        if (!is_numeric($category_id)) return false;
        if ($category_id==Zira\Category::ROOT_CATEGORY_ID) return true;
        $category = new Zira\Models\Category($category_id);
        return $category->loaded();
    }
}