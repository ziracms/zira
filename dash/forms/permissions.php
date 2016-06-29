<?php
/**
 * Zira project.
 * permissions.php
 * (c)2015 http://dro1d.ru
 */

namespace Dash\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Permissions extends Form
{
    protected $_id = 'dash-permissions-form';

    protected $_input_offset_wrap_class = 'col-sm-12';

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
        $permissions = $this->getValue('permissions');

        $html = $this->open();
        $html .= $this->hidden('group_id');
        if ($permissions && count($permissions)>0) {
            foreach ($permissions as $permission) {
                $this->setValue(\Dash\Models\Permissions::getFieldName($permission->name), $permission->allow);
                if ($permission->module == 'zira') {
                    $label = Locale::t($permission->name);
                } else {
                    $label = Locale::tm($permission->name, $permission->module);
                }
                $html .= $this->checkbox($label, \Dash\Models\Permissions::getFieldName($permission->name), null, false);
            }
        }
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $validator->registerNumber('group_id',null,null,true,Locale::t('An error occurred'));
        $validator->registerCustom(array(get_class(), 'checkGroup'), 'group_id', Locale::t('Group not found'));

        $permissions = $this->getValue('permissions');
        if ($permissions && count($permissions)>0) {
            foreach ($permissions as $permission) {
                $validator->registerNumber(\Dash\Models\Permissions::getFieldName($permission->name),0,1,false,Locale::t('An error occurred'));
            }
        }
    }

    public static function checkGroup($group_id) {
        $group = new Zira\Models\Group($group_id);
        if (!$group->loaded()) return false;
        return true;
    }
}