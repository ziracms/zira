<?php
/**
 * Zira project.
 * eform.php
 * (c)2016 http://dro1d.ru
 */

namespace Eform\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Eform extends Form
{
    protected $_id = 'eform-eform-form';

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
        $html = $this->open();
        $html .= $this->input(Locale::t('System name').'*', 'name',array('placeholder'=>'[a-z]'));
        $html .= $this->input(Locale::t('Email').'*', 'email');
        $html .= $this->input(Locale::t('Title').'*', 'title');
        $html .= $this->textarea(Locale::t('Description'), 'description');
        $html .= $this->checkbox(Locale::tm('form is active','eform'), 'active', null, false);
        $html .= $this->hidden('id');
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $validator->registerString('name',null,255,true,Locale::t('Invalid value "%s"',Locale::t('System name')));
        $validator->registerNoTags('name',Locale::t('Invalid value "%s"',Locale::t('System name')));
        $validator->registerUtf8('name',Locale::t('Invalid value "%s"',Locale::t('System name')));
        $validator->registerRegexp('name','/^[a-z]+$/i',Locale::t('Invalid value "%s"',Locale::t('System name')));

        $id = $this->getValue('id');
        if (empty($id)) {
            $validator->registerExists('name', \Eform\Models\Eform::getClass(), 'name', Locale::tm('Specified system name already exists','eform'));
        } else {
            $validator->registerCustom(array(get_class(), 'checkName'), array('name', 'id'), Locale::tm('Specified system name already exists','eform'));
        }

        $validator->registerEmail('email', true, Locale::t('Invalid value "%s"',Locale::t('Email')));

        $validator->registerString('title',null,255,true,Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerNoTags('title',Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerUtf8('title',Locale::t('Invalid value "%s"',Locale::t('Title')));

        $validator->registerNoTags('description',Locale::t('Invalid value "%s"',Locale::t('Description')));
        $validator->registerUtf8('description',Locale::t('Invalid value "%s"',Locale::t('Description')));
    }

    public static function checkName($name, $id) {
        $co = \Eform\Models\Eform::getCollection()
                            ->count()
                            ->where('name','=',$name)
                            ->and_where('id','<>',$id)
                            ->get('co');
        return $co == 0;
    }
}