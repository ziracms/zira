<?php
/**
 * Zira project.
 * eforms.php
 * (c)2016 http://dro1d.ru
 */

namespace Eform\Windows;

use Dash;
use Zira;
use Eform;
use Zira\Permission;

class Eforms extends Dash\WIndows\Window {
    protected static $_icon_class = 'glyphicon glyphicon-send';
    protected static $_title = 'Email forms';


    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setViewSwitcherEnabled(true);
        $this->setSelectionLinksEnabled(true);
        $this->setBodyViewListVertical(true);

        $this->setCreateActionWindowClass(Eform\Windows\Eform::getClass());
        $this->setEditActionWindowClass(Eform\Windows\Eform::getClass());

        $this->setDeleteActionEnabled(true);
    }

    public function create() {
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::tm('Form fields','eform'), 'glyphicon glyphicon-list', 'desk_call(dash_eform_fields, this);', 'edit', true, array('typo'=>'fields'))
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownSeparator()
        );
        $this->addDefaultMenuDropdownItem(
            $this->createMenuDropdownItem(Zira\Locale::t('Open page'), 'glyphicon glyphicon-new-window', 'desk_call(dash_eform_page, this);', 'edit', true, array('typo'=>'page'))
        );

        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::tm('Form fields','eform'), 'glyphicon glyphicon-list', 'desk_call(dash_eform_fields, this);', 'edit', true, array('typo'=>'fields'))
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuSeparator()
        );
        $this->addDefaultContextMenuItem(
            $this->createContextMenuItem(Zira\Locale::t('Open page'), 'glyphicon glyphicon-new-window', 'desk_call(dash_eform_page, this);', 'edit', true, array('typo'=>'page'))
        );

        $this->setSidebarContent('<div class="eform-infobar" style="white-space:nowrap;text-overflow: ellipsis;width:100%;overflow:hidden"></div>');

        $this->setOnSelectJSCallback(
            $this->createJSCallback(
                'desk_call(dash_eform_select, this);'
            )
        );

        $this->addDefaultOnLoadScript('desk_call(dash_eform_load, this);');

        $this->addVariables(array(
            'dash_eform_route' => Eform\Eform::ROUTE,
            'dash_eform_fields_wnd' => Dash\Dash::getInstance()->getWindowJSName(Eformfields::getClass())
        ));

        $this->includeJS('eform/dash');
    }

    public function load() {
        if (!Permission::check(Permission::TO_CHANGE_OPTIONS)) {
            $this->setBodyItems(array());
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $eforms = Eform\Models\Eform::getCollection()->get();

        $items = array();
        foreach($eforms as $eform) {
            $items[]=$this->createBodyFileItem($eform->title, $eform->name, $eform->id, 'desk_call(dash_eform_fields,this);', false, array('type'=>'txt','page'=>$eform->name,'inactive'=>$eform->active ? 0 : 1));
        }
        $this->setBodyItems($items);
    }
}