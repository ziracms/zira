<?php
/**
 * Zira project.
 * editor.php
 * (c)2017 http://dro1d.ru
 */

namespace Designer\Windows;

use Dash;
use Zira;
use Zira\Permission;

class Editor extends Dash\Windows\Editor {
    public $item;

    public function init() {
        parent::init();

        $this->setSaveActionEnabled(true);
    }

    public function create() {
        parent::create();
    }

    public function load() {
        if (!empty($this->item)) $this->item=intval($this->item);
        else return array('error' => Zira\Locale::t('An error occurred'));
        if (!Permission::check(Permission::TO_CHANGE_LAYOUT)) {
            return array('error' => Zira\Locale::t('Permission denied'));
        }

        $style = new \Designer\Models\Style($this->item);
        if (!$style->loaded()) return array('error' => Zira\Locale::t('An error occurred'));
        $this->setTitle(Zira\Locale::tm(self::$_title,'designer').' - '.$style->title);

        $content = $style->content;
        $content = str_replace(';', ';'."\r\n\t", $content);
        $content = str_replace('{', '{'."\r\n\t", $content);
        $content = str_replace("\t".'}', '}', $content);
        $content = str_replace('}', '}'."\r\n", $content);
        
        do {
            $count = 0;
            $content = preg_replace('/([,])([^}\r\n]+[{])/', '$1'."\r\n".'$2', $content, -1, $count);
        } while($count>0);
        
        if (preg_match_all('/([{][^}]+[{])(.+?)([}][^{]+[}])/s', $content, $m)) {
            for($i=0; $i<count($m[0]); $i++) {
                $content = str_replace($m[0][$i], $m[1][$i].str_replace("\r\n", "\r\n\t", $m[2][$i]).$m[3][$i], $content);
            }
        }
        
        $this->setBodyFullContent(
            $this->getBodyContent($content, 'item', $this->item, (string)Zira\Request::post('id'))
        );
        
        $this->setData(array(
            'content' => null,
            'items' => array($this->item),
            'highlight_mode' => 'css'
        ));
    }
}