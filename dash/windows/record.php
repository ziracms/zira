<?php
/**
 * Zira project.
 * record.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Record extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-file';
    protected static $_title = 'Record';

    protected $_help_url = 'zira/help/record';

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
                'desk_call(dash_record_load, this);'
            )
        );
        
        $this->includeJS('dash/record');
    }

    public function load() {
        if (!empty($this->item)) $this->item=intval($this->item);
        if ((!Permission::check(Permission::TO_CREATE_RECORDS) && !Permission::check(Permission::TO_EDIT_RECORDS)) ||
            (empty($this->item) && !Permission::check(Permission::TO_CREATE_RECORDS)) ||
            (!empty($this->item) && !Permission::check(Permission::TO_EDIT_RECORDS))
        ) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $root = trim((string)Zira\Request::post('root'),'/');
        $language = Zira\Request::post('language');
        if (!empty($language) && !in_array($language, Zira\Config::get('languages'))) {
            $language = '';
        }

        if (!empty($root)) {
            $category = Zira\Models\Category::getCollection()
                ->where('name', '=', $root)
                ->get(0);

            if (!$category) {
                return array('error' => Zira\Locale::t('An error occurred'));
            }
            $category_id = $category->id;
        } else {
            $category_id = Zira\Category::ROOT_CATEGORY_ID;
        }
        $this->setData(array(
            'root'=>$root,
            'items'=>$this->item ? array($this->item) : null
        ));

        $form = new \Dash\Forms\Record();

        if (!empty($this->item)) {
            $record = new Zira\Models\Record($this->item);
            if (!$record->loaded()) {
                return array('error'=>Zira\Locale::t('An error occurred'));
            }
            $this->setTitle(Zira\Locale::t('Record: %s', $record->title));
            $recordArray = $record->toArray();
            $recordArray['published'] = $record->published == Zira\Models\Record::STATUS_PUBLISHED ? 1 : 0;
            $recordArray['front_page'] = $record->front_page == Zira\Models\Record::STATUS_FRONT_PAGE ? 1 : 0;
            if ($recordArray['comments_enabled']===null) $recordArray['comments_enabled'] = isset($category) && $category->comments_enabled!==null ? $category->comments_enabled : Zira\Config::get('comments_enabled', 1);

            $form->setValues($recordArray);
        } else {
            $this->setTitle(Zira\Locale::t('New record'));
            $form->setValues(array(
                'category_id' => $category_id,
                'language' => !empty($language) ? $language : Zira\Config::get('language'),
                'comments_enabled' => isset($category) && $category->comments_enabled!==null ? $category->comments_enabled : Zira\Config::get('comments_enabled', 1)
            ));
        }

        $this->setBodyContent($form);
    }
}