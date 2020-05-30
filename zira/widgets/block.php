<?php
/**
 * Zira project.
 * block.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Zira\Widgets;

use Zira;

class Block extends Zira\Widget {
    protected $_title = 'Block';
    protected static $_titles;

    protected function _init() {
        $this->setDynamic(true);
        $this->setCaching(true);
        $this->setOrder(0);
        $this->setPlaceholder(Zira\View::VAR_BODY_BOTTOM);
    }

    protected function getTitles() {
        if (self::$_titles===null) {
            self::$_titles = array();
            $rows = Zira\Models\Block::getCollection()->get();
            foreach($rows as $row) {
                self::$_titles[$row->id] = $row->name;
            }
        }
        return self::$_titles;
    }

    public function getTitle() {
        $id = $this->getData();
        if (is_numeric($id)) {
            $titles = $this->getTitles();
            if (empty($titles) || !array_key_exists($this->getData(), $titles)) return parent::getTitle();
            return Zira\Locale::t('Block') . ' - ' . $titles[$id];
        } else if (preg_match('/^\[file=(.+)\]$/',$id, $m) && file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $m[1])) {
            return Zira\Locale::t('File') . ' - [' . $m[1] . ']';
        } else {
            return parent::getTitle();
        }
    }

    protected function getKey() {
        $id = $this->getData();
        $suffix = '';
        if (!empty($id) && is_numeric($id)) $suffix = '.'.$id;
        else if (!empty($id)) $suffix = '.'.md5($id);
        return parent::getKey().$suffix;
    }

    protected function _render() {
        $id = $this->getData();
        if (!is_numeric($id)) {
            if (strpos($id, '..')!==false) return;
            if (preg_match('/^\[file=(.+)\]$/',$id, $m)) {
                if (file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $m[1])) {
                    $p = strrpos($m[1], '.');
                    $ext = strtolower(substr($m[1],$p+1));

                    if ($ext == 'txt' || $ext=='html') {
                        echo file_get_contents(ROOT_DIR . DIRECTORY_SEPARATOR . $m[1]);
                    } else if ($ext=='jpg' || $ext=='jpeg' || $ext=='png' || $ext=='gif') {
                        $size = getimagesize(ROOT_DIR . DIRECTORY_SEPARATOR . $m[1]);
                        if (!$size) return;
                        echo '<img src="'.Zira\Helper::urlencode(Zira\Helper::baseUrl('').$m[1]).'" '.$size[3].' class="block block-image" />';
                    }
                }
            } else {
                echo $id;
            }
        } else {
            $block = new Zira\Models\Block($id);
            if (!$block->loaded()) return;

            if (strpos($block->content, '[str]')===false || strpos($block->content, '[/str]')===false) {
                $block->content = Zira\Locale::t($block->content);
            } else if (preg_match_all('/\[str\](.+?)\[\/str\]/', $block->content, $matches)) {
                foreach($matches[1] as $index=>$match) {
                    $block->content = str_replace($matches[0][$index],Zira\Locale::t($match), $block->content);
                }
            }

            if (strpos($block->content, '[url]')!==false &&
                strpos($block->content, '[/url]')!==false &&
                preg_match_all('/\[url\](.+?)\[\/url\]/', $block->content, $matches)
            ) {
                foreach($matches[1] as $index=>$match) {
                    $block->content = str_replace($matches[0][$index],Zira\Helper::url($match), $block->content);
                }
            }

            if ((strpos($block->content, '<') === false ||
                strpos($block->content, '>') === false) &&
                $this->getPlaceholder() != Zira\View::VAR_HEAD_BOTTOM
            ) {
                $block->content = Zira\Helper::html($block->content);
                $parts = explode("\n", $block->content);
                $block->content = '<p>' . implode('</p><p>', $parts) . '</p>';
            }

            if (!$block->tpl) {
                echo $block->content;
            } else {
                Zira\View::renderView(array(
                    'title' => Zira\Locale::t($block->name),
                    'content' => $block->content
                ), 'zira/widgets/block');
            }
        }
    }
}