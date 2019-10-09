<?php
/**
 * Zira project.
 * settings.php
 * (c)2018 https://github.com/ziracms/zira
 */

namespace Fields\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Settings extends Form
{
    protected $_id = 'dash-fields-settings-form';

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
        $search_types = \Fields\Models\Search::getSearchTypes();
        $html = $this->open();
        $html .= $this->radioButton(Locale::tm('Search type', 'fields'), 'fields_search_type', $search_types);
        $html .= $this->checkbox(Locale::tm('Expand extra fields search form', 'fields'), 'fields_search_expand', null, false);
        $html .= $this->checkbox(Locale::tm('Enable records description extra fields', 'fields'), 'fields_enable_previews', null, false);
        $html .= $this->checkbox(Locale::tm('Display description extra fields in widgets', 'fields'), 'fields_display_widgets_previews', null, false);
        $html .= $this->close();
        return $html;
    }

    protected function _validate()
    {
        $validator = $this->getValidator();
        
        $validator->registerCustom(array(get_class(), 'checkType'), 'fields_search_type', Locale::t('An error occurred'));
    }
    
    public static function checkType($type) {
        $types = \Fields\Models\Search::getSearchTypes();
        return array_key_exists($type, $types);
    }
}