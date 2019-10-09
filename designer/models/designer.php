<?php
/**
 * Zira project.
 * designer.php
 * (c)2017 https://github.com/ziracms/zira
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
        if (empty($content)) return array('error' => Zira\Locale::t('Style has no changes'));
        
        $style = new \Designer\Models\Style($id);
        if (!$style->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
        
        $bo = preg_replace('/[^{]/','', $content);
        $bc = preg_replace('/[^}]/','', $content);
        if (strlen($bo) != strlen($bc)) {
            return array('error'=>Zira\Locale::t('Syntax error'));
        }
        
        $style->content = $this->_prepareCode($content);
        $style->save();
        
        Zira\Models\Option::raiseVersion();
        
        return array('message'=>Zira\Locale::t('Successfully saved'));
    }
    
    protected function _prepareCode($code) {
        $code = preg_replace('/\/\*[\s\S]*?\*\//', '', $code); // removing comments
        $code = preg_replace('/\s[;]\s/', '', "\r\n".$code."\r\n"); // removing lines with semicolon only
        $code = preg_replace('/\s*([{,])\s*/','$1', $code); // removing whitespaces
        $code = preg_replace('/([^{};,\r\n\t])[\x20\t]*([\r\n]+)/', '$1;', $code); // adding semicolon
        $code = preg_replace('/\s*([{};:,])\s*/','$1', $code); // removing whitespaces
        $code = preg_replace('/([\(])\s*/','$1', $code); // removing whitespaces after brackets
        $code = preg_replace('/\s*([\)])/','$1', $code); // removing whitespaces before brackets
        $code = preg_replace('/[\x20]+/',' ', $code); // removing multiple whitespaces
        $code = preg_replace('/[;][\x20]+[;]/',';', $code); // removing spaces between semicolons
        $code = preg_replace('/[;]+/',';', $code); // removing multiple semicolons
        $code = trim($code);
        return $code;
    }
}