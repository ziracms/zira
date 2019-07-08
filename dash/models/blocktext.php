<?php
/**
 * Zira project.
 * blocktext.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Models;

use Zira;
use Zira\Permission;

class Blocktext extends Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        if (!isset($data['item']) || !isset($data['content'])) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }

        $item = $data['item'];
        $content = $data['content'];

        $block = new Zira\Models\Block($item);
        if (!$block->loaded()) {
            return array('error'=>Zira\Locale::t('An error occurred'));
        }

        $block->content = str_replace("\r\n","\n",$content);
        $block->save();

        Zira\Cache::clear();

        return array('message' => Zira\Locale::t('Successfully saved'));
    }
}