<?php
/**
 * Zira project.
 * record.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Record extends Form
{
    protected $_id = 'dash-record-form';

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
        $html .= $this->hidden('category_id');
        if (count(Zira\Config::get('languages'))<2) {
            $html .= $this->hidden('language');
        } else {
            $html .= $this->selectDropdown(Locale::t('Language').'*','language',Locale::getLanguagesArray());
        }
        $html .= $this->input(Locale::t('System name') . ' (' . Locale::t('URL') . ')*', 'name', array('placeholder'=>Locale::t('numbers and letters in lower case')));
        $html .= $this->input(Locale::t('Title') . '*', 'title');
        $html .= $this->checkbox(Locale::t('Publish'), 'published', null, false);
        $html .= $this->checkbox(Locale::t('Show on front page'), 'front_page', null, false);
        $html .= $this->checkbox(Locale::t('Enable comments'), 'comments_enabled', null, false);
        
        if ($this->getValue('image') || $this->getValue('thumb')) {
            $html .= $this->checkbox(Locale::t('Remove assigned image'), 'delete_image', null, false);
        }
        
        $html .= Zira\Helper::tag_open('div', array('id'=>'dashrecordform_access_button'));
        $html .= Zira\Helper::tag_open('div', array('class'=>'form-group'));
        $html .= Zira\Helper::tag_open('div', array('class'=>'col-sm-offset-4 col-sm-8'));
        $html .= Zira\Helper::tag_open('div', array('class'=>'checkbox checkbox-float'));
        $html .= Zira\Helper::tag('span', null, array('class'=>'glyphicon glyphicon-menu-right', 'style'=>'float:left;top:3px'));
        $html .= Zira\Helper::tag_open('label', array('class' => 'col-sm-4 control-label', 'style'=>'width:auto;padding-top:0;padding-left:7px;', 'id'=>'dashrecordform_access_label'));
        $html .= Locale::t('Restrict access');
        $html .= Zira\Helper::tag_close('label');
        $html .= Zira\Helper::tag_close('div');
        $html .= Zira\Helper::tag_close('div');
        $html .= Zira\Helper::tag_close('div');
        $html .= Zira\Helper::tag_close('div');
        $html .= Zira\Helper::tag_open('div', array('id'=>'dashrecordform_access_container', 'style'=>'display:none'));
        $html .= $this->checkbox(Locale::t('Restrict access to page'), 'access_check', null, false);
        $html .= $this->checkbox(Locale::t('Restrict access to gallery'), 'gallery_check', null, false);
        $html .= $this->checkbox(Locale::t('Restrict access to files'), 'files_check', null, false);
        $html .= $this->checkbox(Locale::t('Restrict access to audio'), 'audio_check', null, false);
        $html .= $this->checkbox(Locale::t('Restrict access to video'), 'video_check', null, false);
        $html .= Zira\Helper::tag_close('div');

        $html .= $this->close();
        return $html;
    }

    protected function _validate()
    {
        $validator = $this->getValidator();
        $validator->registerCustom(array(get_class(), 'checkLanguage'), 'language', Locale::t('An error occurred'));
        $validator->registerCustom(array(get_class(), 'checkCategory'), 'category_id', Locale::t('An error occurred'));
        $validator->registerString('name', null, 255, true, Locale::t('Invalid value "%s"',Locale::t('System name')));
        $validator->registerRegexp('name', Zira\Models\Record::REGEXP_NAME, Locale::t('Invalid value "%s"',Locale::t('System name')));
        $validator->registerCustom(array(get_class(), 'checkName'), array('name','category_id'), Locale::t('Invalid value "%s"',Locale::t('System name')));
        $validator->registerCustom(array(get_class(), 'checkExists'), array('id','name','category_id','language'), Locale::t('Record with such name already exists'));
        $validator->registerString('title', 0, 255, true, Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerUtf8('title', Locale::t('Invalid value "%s"',Locale::t('Title')));
    }

    public static function checkLanguage($language) {
        return in_array($language , Zira\Config::get('languages'));
    }

    public static function checkCategory($category_id) {
        if (!is_numeric($category_id)) return false;
        if ($category_id==Zira\Category::ROOT_CATEGORY_ID) return true;
        $category = new Zira\Models\Category($category_id);
        return $category->loaded();
    }

    public static function checkName($name,$category_id) {
        if (!empty($category_id)) {
//            $category = new Zira\Models\Category($category_id);
//            $category_co = Zira\Models\Category::getCollection()
//                            ->count()
//                            ->where('name','=',$category->name . '/' . $name)
//                            ->get('co');
//            if ($category_co!=0) return false;
//            else return true;
            return true;
        } else {
//            $category_co = Zira\Models\Category::getCollection()
//                ->count()
//                ->where('name', '=', $name)
//                ->get('co');
//            if ($category_co != 0) return false;
            if ($name == 'dash' || in_array($name, Zira\Config::get('languages'))) return false;
            return Zira\Router::isRouteAvailable($name);
        }
    }

    public static function checkExists($id, $name, $category_id, $language) {
        $id = intval($id);
        $query = Zira\Models\Record::getCollection();
        $query->count();
        $query->where('category_id','=',$category_id);
        $query->and_where('language','=',$language);
        $query->and_where('name','=',$name);
        if (!empty($id)) {
            $query->and_where('id','<>',$id);
        }
        return $query->get('co')==0;
    }
}