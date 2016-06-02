<?php
/**
 * Zira project.
 * license.php
 * (c)2016 http://dro1d.ru
 */

if (!defined('ZIRA_INSTALL')) exit;

return array(
    'content' => Zira\Helper::tag('h2', Zira\Locale::t('Agreement')).
                Zira\Helper::tag_open('p').'&nbsp;'.Zira\Helper::tag_close('p').
                Zira\Helper::tag('p', Zira\Locale::t('By installing Zira CMS you are agree to the following terms and conditions:')).
                Zira\Helper::tag_open('p').'&nbsp;'.Zira\Helper::tag_close('p').
                Zira\Helper::tag('p', Zira\Locale::t('Zira CMS is a free software and can be used in both commercial and non-commercial projects without limitation.')).
                Zira\Helper::tag_open('p').Zira\Locale::t('You can modify system files for your needs, but Zira CMS\' copyright string with active link to %s must exists on every generated page.',Zira\Helper::tag('a','http://dro1d.ru', array('href'=>'http://dro1d.ru','target'=>'_blank'))).Zira\Helper::tag_close('p').
                Zira\Helper::tag_open('p').Zira\Locale::t('If this doesn\'t fit your needs, you may want to purchase a license. Please visit our %s for further details.',Zira\Helper::tag('a',Zira\Locale::t('website'), array('href'=>'http://dro1d.ru','target'=>'_blank'))).Zira\Helper::tag_close('p').
                Zira\Helper::tag_open('p').Zira\Locale::t('If you have any questions, feel free to %s.',Zira\Helper::tag('a',Zira\Locale::t('contact us'), array('href'=>'http://dro1d.ru/contact','target'=>'_blank'))).Zira\Helper::tag_close('p').
                Zira\Helper::tag('p', Zira\Locale::t('Don\'t forget to visit our forum and share your impressions with other users.')).
                Zira\Helper::tag_open('p').'&nbsp;'.Zira\Helper::tag_close('p').
                Zira\Helper::tag('p', Zira\Locale::t('Ready to proceed ?'))
);