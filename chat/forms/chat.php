<?php
/**
 * Zira project.
 * chat.php
 * (c)2017 http://dro1d.ru
 */

namespace Chat\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Chat extends Form
{
    protected $_id = 'dash-chat-chat-form';

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
        $id = intval($this->getValue('id'));
        
        $html = $this->open();
        $html .= $this->hidden('id');
        if (empty($id)) {
            $placeholders = Zira\Models\Widget::getPlaceholders();
            $html .= $this->selectDropdown(Locale::t('Widget placeholder').'*','placeholder',$placeholders);
        }
        $html .= $this->input(Locale::t('Title').'*', 'title');
        $html .= $this->selectDropdown(Locale::tm('Visibility', 'chat'), 'visible_group', array_merge(array('0'=>Locale::tm('Visible for everybody', 'chat')), Zira\Models\Group::getArray()));
        $html .= $this->input(Locale::tm('Refresh timeout', 'chat').' ('.Locale::tm('sec.', 'chat').')'.'*', 'refresh_delay', array('placeholder'=>Locale::tm('in seconds', 'chat')));
        $html .= $this->checkbox(Locale::tm('Check authentication', 'chat'), 'check_auth', null, false);
        $html .= $this->close();
        return $html;
    }

    protected function _validate()
    {
        $validator = $this->getValidator();

        $validator->registerString('title', null, 255, true, Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerNoTags('title', Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerUtf8('title', Locale::t('Invalid value "%s"',Locale::t('Title')));

        $validator->registerNumber('refresh_delay', 1, null, true, Locale::t('Invalid value "%s"',Locale::tm('Refresh timeout', 'chat')));

        $validator->registerCustom(array(get_class(), 'checkGroup'), 'visible_group', Locale::t('Invalid value "%s"',Locale::tm('Visibility', 'chat')));
        
        $id = (int)$this->getValue('id');
        if (empty($id)) {
            $validator->registerString('placeholder', null, 255, true, Locale::t('Invalid value "%s"',Locale::t('Widget placeholder')));
            $validator->registerCustom(array(get_class(), 'checkPlaceholder'), 'placeholder', Locale::t('Invalid value "%s"',Locale::t('Widget placeholder')));
        }
    }

    public static function checkGroup($group_id) {
        if (!$group_id) return true;
        $group = new Zira\Models\Group($group_id);
        if (!$group->loaded()) return false;
        return true;
    }
    
    public static function checkPlaceholder($placeholder) {
        $placeholders = Zira\Models\Widget::getPlaceholders();
        return array_key_exists($placeholder, $placeholders);
    }
}