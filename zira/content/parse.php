<?php
/**
 * Zira project.
 * parse.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Content;

use Zira;

class Parse {
    public static function bbcode($content) {
        if (strpos($content,'[')!==false && strpos($content,']')!==false) {
            if (stripos($content, '[b')!==false) {
                $content = preg_replace('/\[b[^\]]*?\]([^\[\]]+?)[\s]*\[\/b\]/i', '<b>$1</b>', $content);
            }
            if (stripos($content, '[quote')!==false) {
                $content = preg_replace('/\[quote[^\]]*?\]([^\[\]]+?)[\s]*\[\/quote\]/i', '<q>$1</q>', $content);
            }
            if (stripos($content, '[code')!==false) {
                $content = preg_replace('/\[code[^\]]*?\]([^\[\]]+?)[\s]*\[\/code\]/i', '<code>$1</code>', $content);
            }
            if (stripos($content, '[img')!==false) {
                $content = preg_replace('/\[img[^\]]*?\][\s]*([^\[\]"]+?)[\s]*\[\/img\]/i', '<img src="$1" class="external-image" onerror="this.src=\''.Zira\Helper::imgUrl('noimage.jpg').'\';" />', $content);
            }
        }
        return $content;
    }
}