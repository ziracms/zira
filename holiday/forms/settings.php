<?php
/**
 * Zira project.
 * settings.php
 * (c)2018 http://dro1d.ru
 */

namespace Holiday\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Settings extends Form
{
    protected $_id = 'dash-forum-settings-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';

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
        $html .= $this->checkbox(Locale::tm('Enable New Year mode', 'holiday'), 'holiday_ny_mode', null, false);
        $html .= $this->checkbox(Locale::tm('Enable snow', 'holiday'), 'holiday_snow', null, false);
        $html .= $this->close();
        return $html;
    }

    protected function _validate()
    {
        $validator = $this->getValidator();

        
    }
}