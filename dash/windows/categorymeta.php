<?php
/**
 * Zira project.
 * categorymeta.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Categorymeta extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-search';
    protected static $_title = 'SEO tags';

    protected $_help_url = 'zira/help/category-seo';

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
        if (empty($this->item) || !is_numeric($this->item)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        if ((!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) ||
            (empty($this->item) && !Permission::check(Permission::TO_CREATE_RECORDS)) ||
            (!empty($this->item) && !Permission::check(Permission::TO_EDIT_RECORDS))
        ) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $form = new \Dash\Forms\Categorymeta();

        $category = new Zira\Models\Category($this->item);
        if (!$category->loaded()) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $this->setTitle(Zira\Locale::t(self::$_title) .' - ' . $category->title);
        $form->setValues($category->toArray());

        $this->setBodyContent($form);

        $this->setData(array(
            'items' => array($this->item)
        ));
    }
}