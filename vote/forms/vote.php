<?php
/**
 * Zira project.
 * vote.php
 * (c)2016 http://dro1d.ru
 */

namespace Vote\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Vote extends Form
{
    protected $_id = 'vote-vote-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';
    protected $_select_wrapper_class = 'col-sm-8';

    protected $_checkbox_inline_label = false;

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

        $id = $this->getValue('id');
        if (empty($id)) {
            $placeholders = Zira\Models\Widget::getPlaceholders();
            $html .= $this->selectDropdown(Locale::t('Widget placeholder').'*','placeholder',$placeholders);
        }
        $html .= $this->input(Locale::tm('Subject','vote'), 'subject');
        $html .= $this->checkbox(Locale::tm('Multiple selection','vote'), 'multiple', null, false);
        $html .= $this->hidden('id');
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $validator->registerString('subject',null,255,false,Locale::t('Invalid value "%s"',Locale::tm('Subject','vote')));
        $validator->registerNoTags('subject',Locale::t('Invalid value "%s"',Locale::tm('Subject','vote')));
        $validator->registerUtf8('subject',Locale::t('Invalid value "%s"',Locale::tm('Subject','vote')));

        $id = $this->getValue('id');
        if (empty($id)) {
            $validator->registerString('placeholder', null, 255, true, Locale::t('Invalid value "%s"',Locale::tm('Widget placeholder','vote')));
            $validator->registerCustom(array(get_class(), 'checkPlaceholder'), 'placeholder', Locale::t('Invalid value "%s"',Locale::tm('Widget placeholder','vote')));
        }
    }

    public static function checkPlaceholder($placeholder) {
        $placeholders = Zira\Models\Widget::getPlaceholders();
        return array_key_exists($placeholder, $placeholders);
    }
}