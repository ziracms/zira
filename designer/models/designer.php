<?php
/**
 * Zira project.
 * designer.php
 * (c)2017 http://dro1d.ru
 */

namespace Designer\Models;

use Zira;
use Dash;
use Zira\Permission;

class Designer extends Dash\Models\Model {
    public function save($data) {
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }
        
        $id = (int)Zira\Request::post('item');
        if (!$id) return array('error' => Zira\Locale::t('An error occurred'));
        
        $content = trim(strip_tags(Zira\Request::post('content')));
        
        $style = new \Designer\Models\Style($id);
        if (!$style->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
        
        $style->content = $content;
        $style->save();
        
        Zira\Cache::clear();
        
        return array('message'=>Zira\Locale::t('Successfully saved'));
    }
}