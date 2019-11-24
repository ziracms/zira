<?php
/**
 * Zira project.
 * recordmeta.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Recordmeta extends Window {
    protected static $_icon_class = 'glyphicon glyphicon-search';
    protected static $_title = 'SEO tags';

    public $item;

    public function init() {
        $this->setIconClass(self::$_icon_class);
        $this->setTitle(Zira\Locale::t(self::$_title));
        $this->setSidebarEnabled(false);

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        $this->addDefaultOnLoadScript(
            'desk_call(dash_recordmeta_load, this);'
        );

        $this->addVariables(array(
            'dash_recordmeta_tags_autocomplete_url' => Zira\Helper::url('dash/records/autocompletetag')
        ));

        $this->includeJS('dash/recordmeta');
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

        $form = new \Dash\Forms\Recordmeta();

        $record = new Zira\Models\Record($this->item);
        if (!$record->loaded()) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        $tags_str = '';
        $tagsArr = Zira\Models\Tag::getCollection()->where('record_id', '=', $record->id)->get();
        $co = 0;
        foreach($tagsArr as $tagObj) {
            if ($co > 0) $tags_str .= ',';
            $tags_str .= $tagObj->tag;
            $co++;
        }
        $this->setTitle(Zira\Locale::t(self::$_title) .' - ' . $record->title);
        $form->setValues(array_merge($record->toArray(), array('record_tags' => $tags_str)));

        $this->setBodyContent($form);

        $this->setData(array(
            'items' => array($this->item)
        ));
    }
}