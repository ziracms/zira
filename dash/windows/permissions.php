<?php
/**
 * Zira project.
 * permissions.php
 * (c)2015 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Permissions extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-flag';
    protected static $_title = 'Permissions';

    protected $_help_url = 'zira/help/permissions';

    public $item;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->setOnLoadJSCallback(
            $this->createJSCallback(
                'desk_window_form_init(this);'
            )
        );
    }

    public function load() {
        if (!empty($this->item)) $this->item = intval($this->item);
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        if (empty($this->item)) return array('error'=>Zira\Locale::t('An error occurred'));
        if ($this->item==Zira\User::GROUP_SUPERADMIN) return array('error' => Zira\Locale::t('Permission denied'));

        $group = new Zira\Models\Group($this->item);
        if (!$group->loaded()) return array('error'=>Zira\Locale::t('An error occurred'));

        $this->setTitle(Zira\Locale::t('Permissions of group "%s"', Zira\Locale::t($group->name)));

        $permissions = Zira\Models\Permission::getCollection()
                        ->select('name', 'allow', 'module')
                        ->where('group_id','=',$this->item)
                        ->order_by('id', 'ASC')
                        ->get()
                        ;

        $form = new \Dash\Forms\Permissions();
        $form->setValues(array(
            'group_id' => $this->item,
            'permissions' => $permissions
        ));

        $this->setBodyContent($form);
    }
}