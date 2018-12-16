<?php
/**
 * Zira project.
 * holiday.php
 * (c)2018 http://dro1d.ru
 */

namespace Holiday\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Holiday extends Form
{
    protected $_id = 'holiday-holiday-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';
    protected $_select_wrapper_class = 'col-sm-4';

    protected $_checkbox_inline_label = true;
    
    protected static function getClasses() {
        return array(
            '' => Locale::tm('Please select', 'holiday'),
            'holiday-red' => Locale::tm('Red', 'holiday'),
            'holiday-orange' => Locale::tm('Orange', 'holiday'),
            'holiday-yellow' => Locale::tm('Yellow', 'holiday'),
            'holiday-green' => Locale::tm('Green', 'holiday'),
            'holiday-blue' => Locale::tm('Blue', 'holiday'),
            'holiday-purple' => Locale::tm('Purple', 'holiday'),
            'holiday-pink' => Locale::tm('Pink', 'holiday')
        );
    }

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
        $days = array('0' => ' - ');
        for($i=1;$i<=31;$i++) {
            $days[$i] = $i;
        }
        $months = array(
            '0' => Locale::tm('Please select', 'holiday'),
            '1' => Locale::tm('January', 'holiday'),
            '2' => Locale::tm('February', 'holiday'),
            '3' => Locale::tm('March', 'holiday'),
            '4' => Locale::tm('April', 'holiday'),
            '5' => Locale::tm('May', 'holiday'),
            '6' => Locale::tm('June', 'holiday'),
            '7' => Locale::tm('July', 'holiday'),
            '8' => Locale::tm('August', 'holiday'),
            '9' => Locale::tm('September', 'holiday'),
            '10' => Locale::tm('October', 'holiday'),
            '11' => Locale::tm('November', 'holiday'),
            '12' => Locale::tm('December', 'holiday')
        );
        $html = $this->open();
        $html .= $this->select(Locale::tm('Month','holiday').'*', 'month', $months, array('class'=>'form-control holiday_month_select'));
        $select_wrapper_class = $this->_select_wrapper_class;
        $this->_select_wrapper_class = 'col-sm-2';
        $html .= $this->select(Locale::tm('Day','holiday').'*', 'day', $days, array('class'=>'form-control holiday_day_select'));
        $this->_select_wrapper_class = $select_wrapper_class;
        $html .= $this->hidden('cdate', array('class'=>'holiday_date_hidden'));
        $html .= $this->input(Locale::t('Title').'*', 'title');
        $html .= $this->input(Locale::t('Description'), 'description');
        $html .= $this->input(Locale::t('Image'), 'image', array('class'=>'form-control image_input'));
        $html .= $this->input(Locale::t('Audio'), 'audio', array('class'=>'form-control audio_input'));
        $html .= $this->select(Locale::t('Class'), 'cls', self::getClasses());
        $html .= $this->checkbox(Locale::t('active'), 'active', null, false);
        $html .= $this->hidden('id');
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $validator->registerString('cdate',null,255,true,Locale::t('Invalid value "%s"',Locale::tm('Day','holiday').' / '.Locale::tm('Month','holiday')));
        $validator->registerNoTags('cdate',Locale::t('Invalid value "%s"',Locale::tm('Day','holiday').' / '.Locale::tm('Month','holiday')));
        $validator->registerUtf8('cdate',Locale::t('Invalid value "%s"',Locale::tm('Day','holiday').' / '.Locale::tm('Month','holiday')));
        $validator->registerCustom(array(get_class(), 'checkDay'), 'cdate', Locale::t('Invalid value "%s"',Locale::tm('Day','holiday').' / '.Locale::tm('Month','holiday')));
        
        $validator->registerString('title',null,255,true,Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerNoTags('title',Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerUtf8('title',Locale::t('Invalid value "%s"',Locale::t('Title')));
        
        $validator->registerString('description',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Description')));
        $validator->registerNoTags('description',Locale::t('Invalid value "%s"',Locale::t('Description')));
        $validator->registerUtf8('description',Locale::t('Invalid value "%s"',Locale::t('Description')));
        
        $validator->registerString('image',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Image')));
        $validator->registerNoTags('image',Locale::t('Invalid value "%s"',Locale::t('Image')));
        $validator->registerUtf8('image',Locale::t('Invalid value "%s"',Locale::t('Image')));
        
        $validator->registerString('audio',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Audio')));
        $validator->registerNoTags('audio',Locale::t('Invalid value "%s"',Locale::t('Audio')));
        $validator->registerUtf8('audio',Locale::t('Invalid value "%s"',Locale::t('Audio')));
        
        $validator->registerString('cls',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Class')));
        $validator->registerNoTags('cls',Locale::t('Invalid value "%s"',Locale::t('Class')));
        $validator->registerUtf8('cls',Locale::t('Invalid value "%s"',Locale::t('Class')));
        $validator->registerCustom(array(get_class(), 'checkClass'), 'cls', Locale::t('Invalid value "%s"',Locale::t('Class')));
    }
    
    public static function checkDay($cdate) {
        if (strpos($cdate, '.')===false) return false;
        $parts = explode('.', $cdate);
        if (count($parts)!=2) return false;
        if (!is_numeric($parts[0])) return false;
        if ($parts[0]<1 || $parts[0]>31) return false;
        if (!is_numeric($parts[1])) return false;
        if ($parts[1]<1 || $parts[1]>12) return false;
        if ($parts[1]==2 && $parts[0]>29) return false;
        if (($parts[1]==4 || $parts[1]==6 || $parts[1]==9 || $parts[1]==11) && $parts[0]>30) return false;
        return true;
    }
    
    public static function checkClass($class) {
        if (empty($class)) return true;
        $classes = self::getClasses();
        return array_key_exists($class, $classes);
    }
}