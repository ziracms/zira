<?php
/**
 * Zira project.
 * parse.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Content;

use Zira;

class Parse {
    public static function bbcode($content) {
        if (strpos($content,'[')!==false && strpos($content,']')!==false) {
            while (stripos($content, '[b')!==false) {
                $content = preg_replace('/\[b\][\s]*(.+?)[\s]*\[\/b\]/si', '<b>$1</b>', $content, -1, $co);
                if ($co==0) break;
            }
            while(stripos($content, '[quote')!==false) {
                $content = preg_replace('/\[quote\][\s]*(.+?)[\s]*\[\/quote\]/si', '<q>$1</q>', $content, -1, $co);
                if ($co==0) break;
            }
            while (stripos($content, '[code')!==false) {
                $content = preg_replace('/\[code\][\s]*(.+?)[\s]*\[\/code\]/si', '<code>$1</code>', $content, -1, $co);
                if ($co==0) break;
            }
            if (stripos($content, '[img')!==false) {
                $content = preg_replace('/\[img[^\]]*?\][\s]*([^\[\]"]+?)[\s]*\[\/img\]/si', '<img src="$1" class="external-image" onerror="this.src=\''.Zira\Helper::imgUrl('noimage.jpg').'\';" />', $content);
            }
        }
        if (stripos($content, '&amp;#x')!==false) {
            $content = str_replace('&amp;#x', '&#x', $content);
        }
        if (stripos($content, '&amp;nbsp;')!==false) {
            $content = str_replace('&amp;nbsp;', '&nbsp;', $content);
        }
        return $content;
    }
}