<?php
/**
 * Zira project.
 * emoji.php
 * (c)2016 http://dro1d.ru
 */

namespace Emoji;

use Zira;

class Emoji {
    private static $_instance;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function onActivate() {
        Zira\Assets::registerCSSAsset('emoji/emoji.css');
        Zira\Assets::registerJSAsset('emoji/emoji.js');
    }

    public function onDeactivate() {
        Zira\Assets::unregisterCSSAsset('emoji/emoji.css');
        Zira\Assets::unregisterJSAsset('emoji/emoji.js');
    }

    public function beforeDispatch() {
        Zira\Assets::registerCSSAsset('emoji/emoji.css');
        Zira\Assets::registerJSAsset('emoji/emoji.js');
    }

    public function bootstrap() {
        Zira\View::addDefaultAssets();
        Zira\View::addStyle('emoji/emoji.css');
        Zira\View::addScript('emoji/emoji.js');

        $js = Zira\Helper::tag_open('script', array('type'=>'text/javascript'));
        $js .= 'var emoji_url = \''.Zira\Helper::url('emoji/load/typo').'\'; ';
        $js .= 'var emoji_size = '.Models\Emoji::SIZE.';';
        $js .= Zira\Helper::tag_close('script');
        //Zira\View::addHTML($js, Zira\View::VAR_HEAD_BOTTOM);
        Zira\View::addBodyBottomScript($js);

        Zira\View::addJsStrings(array(
            'Select emoji' => Zira\Locale::tm('Select emoji', 'emoji'),
            'Emoji' => Zira\Locale::tm('Emoji', 'emoji'),
            'Quote' => Zira\Locale::tm('Quote', 'emoji'),
            'Image' => Zira\Locale::tm('Image', 'emoji'),
            'Code' => Zira\Locale::tm('Code', 'emoji'),
            'Bold' => Zira\Locale::tm('Bold', 'emoji'),
            'Enter image URL address' => Zira\Locale::tm('Enter image URL address', 'emoji')
        ));
    }
}