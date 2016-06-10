<?php
/**
 * Zira project.
 * block.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Block extends Form
{
    protected $_id = 'dash-block-form';

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
        $id = intval($this->getValue('id'));

        $html = $this->open();
        $html .= $this->hidden('id');
        if (empty($id)) {
            $placeholders = Zira\Models\Widget::getPlaceholders();
            $html .= $this->selectDropdown(Locale::t('Widget placeholder').'*','placeholder',$placeholders);
        }
        $html .= $this->input(Locale::t('Title') . '*', 'name');
        $html .= $this->textarea(Locale::t('Content'), 'content', array('rows'=>10));
        $html .= $this->checkbox(Locale::t('Use template'), 'tpl', null, false);
        $html .= $this->close();
        return $html;
    }

    protected function _validate()
    {
        $validator = $this->getValidator();
        $validator->registerString('name', null, 255, true, Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerUtf8('name', Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerUtf8('content', Locale::t('Invalid value "%s"',Locale::t('Content')));

        $id = (int)$this->getValue('id');
        if (empty($id)) {
            $validator->registerString('placeholder', null, 255, true, Locale::t('Invalid value "%s"',Locale::t('Widget placeholder')));
            $validator->registerCustom(array(get_class(), 'checkPlaceholder'), 'placeholder', Locale::t('Invalid value "%s"',Locale::t('Widget placeholder')));
        }
    }

    public static function checkPlaceholder($placeholder) {
        $placeholders = Zira\Models\Widget::getPlaceholders();
        return array_key_exists($placeholder, $placeholders);
    }
}