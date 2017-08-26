<?php
/**
 * Zira project.
 * categorysettings.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Categorysettings extends Form
{
    protected $_id = 'dash-categorysettings-form';

    protected $_label_class = 'col-sm-6 control-label';
    protected $_input_wrap_class = 'col-sm-6';
    protected $_input_offset_wrap_class = 'col-sm-offset-6 col-sm-6';

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
        $html .= $this->hidden('id');
        $html .= $this->checkbox(Locale::t('Show slider'), 'slider_enabled', null, false);
        $html .= $this->checkbox(Locale::t('Show gallery'), 'gallery_enabled', null, false);
        $html .= $this->checkbox(Locale::t('Show files'), 'files_enabled', null, false);
        $html .= $this->checkbox(Locale::t('Show audio'), 'audio_enabled', null, false);
        $html .= $this->checkbox(Locale::t('Show video'), 'video_enabled', null, false);
        $html .= $this->checkbox(Locale::t('Enable rating'), 'rating_enabled', null, false);
        $html .= $this->checkbox(Locale::t('Display author'), 'display_author', null, false);
        $html .= $this->checkbox(Locale::t('Display date'), 'display_date', null, false);
        $html .= $this->checkbox(Locale::t('Display records list'), 'records_list', null, false);
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        //$validator = $this->getValidator();

    }
}