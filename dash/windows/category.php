<?php
/**
 * Zira project.
 * category.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Category extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-book';
    protected static $_title = 'Category';

    protected $_help_url = 'zira/help/category';

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
        if (!empty($this->item)) $this->item=intval($this->item);
        if ((!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) ||
            (empty($this->item) && !Permission::check(Permission::TO_CREATE_RECORDS)) ||
            (!empty($this->item) && !Permission::check(Permission::TO_EDIT_RECORDS))
        ) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $root = (string)Zira\Request::post('root');

        $this->setData(array(
            'root'=>$root,
            'items'=>$this->item ? array($this->item) : null
        ));

        $form = new \Dash\Forms\Category();

        if (!empty($this->item)) {
            $category = new Zira\Models\Category($this->item);
            if (!$category->loaded()) {
                return array('error'=>Zira\Locale::t('An error occurred'));
            }
            $this->setTitle(Zira\Locale::t('Category: %s', $category->name));
            $categoryArray = $category->toArray();
            if (!empty($root)) {
                $categoryArray['name'] = substr($categoryArray['name'], strrpos($categoryArray['name'], '/') + 1);
            }
            $form->setValues($categoryArray);
        } else {
            $this->setTitle(Zira\Locale::t('New category'));
        }

        $form->setValue('root', $root);

        $this->setBodyContent($form);
    }
}