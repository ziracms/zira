<?php
/**
 * Zira project.
 * license.php
 * (c)2016 http://dro1d.ru
 */

if (!defined('ZIRA_INSTALL')) exit;

return array(
    'content' => Zira\Helper::tag('h2', Zira\Locale::t('WORKERS OF THE WORLD, UNITE!'), array('style'=>'text-align:center;margin:-20px 0px 1px;color:#f7d71c;font-weight:bold;background:#720f04;padding:15px 0px;')).
                Zira\Helper::tag_short('img', array('src'=>'assets/ussr.jpg','width'=>'100%','alt'=>Zira\Locale::t('My homeland is the USSR!'),'title'=>Zira\Locale::t('My homeland is the USSR!'))).
                Zira\Helper::tag('p', Zira\Locale::t('In the victory of the immortal ideas of communism we see the future of our country!'), array('style'=>'text-align:center;font-size:18px;margin:0px;color:#f7d71c;background:#b81e08;padding:10px 0px;')).
                Zira\Helper::tag('p', Zira\Locale::t('Dedicated to the memory of the USSR.'), array('style'=>'text-align:center;font-size:18px;margin:1px 0px 20px;color:#f7d71c;background:#720f04;padding:10px 0px;')),
    'script' => '$(\'body\').css(\'backgroundColor\',\'#f9f9f9\');'.
                '$(\'#site-logo img\').attr(\'src\',\'assets/cccp.png\');'.
                '$(\'#site-logo span\').text(\'\');'.
                '$(\'#site-logo\').attr(\'title\',\''.Zira\Locale::t('My homeland is the USSR!').'\');'.
                '$(\'.page-header\').css(\'borderColor\',\'#ffbeb4\');'.
                '$(\'.page-header h1\').css(\'color\',\'#530801\');'.
                '$(\'header\').css({\'background\':\'#cc0000 url(assets/emblem.png) no-repeat 50% 50%\',\'border\':\'none\',\'minHeight\':\'180px\'});'.
                '$(\'#language-switcher li a.active\').css({\'backgroundColor\':\'#b81e08\'});'
);