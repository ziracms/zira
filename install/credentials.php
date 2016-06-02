<?php
/**
 * Zira project.
 * credentials.php
 * (c)2016 http://dro1d.ru
 */

if (!defined('ZIRA_INSTALL')) exit;

$form = new \Install\Forms\Credentials();
$form->setValues(Zira\Session::getArray());

return array(
    'content' => Zira\Helper::tag('p', Zira\Locale::t('Your website must have unique name and slogan!')).
                (string)$form
);