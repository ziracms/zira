<?php
/**
 * Zira project.
 * editor.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Windows;

use Zira;
use Zira\Permission;

class Blocktext extends Editor {
    public $item;

    public function init() {
        parent::init();
        $this->setSaveActionEnabled(true);
    }

    public function load() {
        if (empty($this->item) || !is_numeric($this->item)) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $block = new Zira\Models\Block($this->item);
        if (!$block->loaded()) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }

        $this->setBodyFullContent(
            $this->getBodyContent($block->content, 'item', $this->item, (string)Zira\Request::post('id'))
        );

        $this->setTitle(Zira\Locale::t(self::$_title).' - '.$block->name);

        $this->setData(array(
            'content' => null,
            'items' => array($this->item)
        ));
    }
}