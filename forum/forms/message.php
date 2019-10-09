<?php
/**
 * Zira project.
 * message.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Forum\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Message extends Form
{
    protected $_id = 'dash-forum-message-form';

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
        $html = $this->open();
        $html .= $this->hidden('id');
        $html .= $this->hidden('topic_id');
        $html .= $this->textarea(Locale::t('Message').'*', 'content', array('rows'=>8));
        $html .= $this->select(Locale::tm('Status','forum'), 'status', \Forum\Models\Message::getStatuses());
        $html .= $this->close();
        return $html;
    }

    protected function _validate()
    {
        $validator = $this->getValidator();

        $validator->registerNumber('topic_id', 1, null, true, Locale::t('An error occurred'));

        $validator->registerText('content', null, true, Locale::t('Invalid value "%s"',Locale::t('Message')));
        $validator->registerNoTags('content', Locale::t('Invalid value "%s"',Locale::t('Message')));
        $validator->registerUtf8('content', Locale::t('Invalid value "%s"',Locale::t('Message')));

        $validator->registerCustom(array(get_class(), 'checkStatus'), 'status', Locale::t('An error occurred'));
    }

    public static function checkStatus($status) {
        $statuses = \Forum\Models\Message::getStatuses();
        return array_key_exists($status, $statuses);
    }
}