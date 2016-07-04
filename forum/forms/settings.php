<?php
/**
 * Zira project.
 * settings.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Settings extends Form
{
    protected $_id = 'dash-forum-settings-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';

    protected $_checkbox_inline_label = false;

    public function __construct()
    {
        parent::__construct($this->_id);
    }

    protected function _init()
    {
        $this->setRenderPanel(false);
        $this->setFormClass('form-horizontal dash-window-form');
    }

    protected function _render()
    {
        $html = $this->open();
        $html .= $this->selectDropdown(Locale::t('Layout'),'forum_layout',array_merge(array(Locale::t('Default layout')),Zira\View::getLayouts()));
        $html .= $this->input(Locale::t('Title'), 'forum_title');
        $html .= $this->input(Locale::t('Description'), 'forum_description');
        $html .= $this->input(Locale::t('Window title'), 'forum_meta_title');
        $html .= $this->input(Locale::t('Keywords'), 'forum_meta_keywords');
        $html .= $this->input(Locale::tm('Meta description', 'forum'), 'forum_meta_description');
        $html .= $this->input(Locale::t('Records limit'), 'forum_limit');
        $html .= $this->input(Locale::tm('Message min. length', 'forum'), 'forum_min_chars');
        $html .= $this->checkbox(Locale::t('Moderation'), 'forum_moderate', null, false);
        $html .= $this->input(Locale::t('Notification Email'), 'forum_notify_email');
        $html .= $this->checkbox(Locale::tm('Allow file uploads', 'forum'), 'forum_file_uploads', null, false);
        $html .= $this->input(Locale::tm('File max. size', 'forum').' (kB)', 'forum_file_max_size');
        $html .= $this->input(Locale::tm('Allowed file extensions', 'forum'), 'forum_file_ext');
        $html .= $this->close();
        return $html;
    }

    protected function _validate()
    {
        $validator = $this->getValidator();

        $validator->registerString('forum_title', null, 255, false, Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerNoTags('forum_title', Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerUtf8('forum_title', Locale::t('Invalid value "%s"',Locale::t('Title')));

        $validator->registerString('forum_description', null, 255, false, Locale::t('Invalid value "%s"',Locale::t('Description')));
        $validator->registerNoTags('forum_description', Locale::t('Invalid value "%s"',Locale::t('Description')));
        $validator->registerUtf8('forum_description', Locale::t('Invalid value "%s"',Locale::t('Description')));

        $validator->registerString('forum_meta_title', null, 255, false, Locale::t('Invalid value "%s"',Locale::t('Window title')));
        $validator->registerNoTags('forum_meta_title', Locale::t('Invalid value "%s"',Locale::t('Window title')));
        $validator->registerUtf8('forum_meta_title', Locale::t('Invalid value "%s"',Locale::t('Window title')));

        $validator->registerString('forum_meta_keywords', null, 255, false, Locale::t('Invalid value "%s"',Locale::t('Keywords')));
        $validator->registerNoTags('forum_meta_keywords', Locale::t('Invalid value "%s"',Locale::t('Keywords')));
        $validator->registerUtf8('forum_meta_keywords', Locale::t('Invalid value "%s"',Locale::t('Keywords')));

        $validator->registerString('forum_meta_description', null, 255, false, Locale::t('Invalid value "%s"',Locale::tm('Meta description', 'forum')));
        $validator->registerNoTags('forum_meta_description', Locale::t('Invalid value "%s"',Locale::tm('Meta description', 'forum')));
        $validator->registerUtf8('forum_meta_description', Locale::t('Invalid value "%s"',Locale::tm('Meta description', 'forum')));

        $validator->registerNumber('forum_limit', 1, null, false, Locale::t('Invalid value "%s"',Locale::t('Records limit')));
        $validator->registerNumber('forum_min_chars', 1, null, false, Locale::t('Invalid value "%s"',Locale::tm('Message min. length', 'forum')));
        $validator->registerEmail('forum_notify_email',false,Locale::t('Invalid value "%s"',Locale::t('Notification Email')));
        $validator->registerNumber('forum_file_max_size', 1, null, false, Locale::t('Invalid value "%s"',Locale::tm('File max. size', 'forum')));
        $validator->registerString('forum_file_ext', null, 255, false, Locale::t('Invalid value "%s"',Locale::tm('Allowed file extensions', 'forum')));

        $validator->registerCustom(array(get_class(), 'checkLayout'), 'forum_layout', Locale::t('Invalid value "%s"',Locale::t('Layout')));
    }

    public static function checkLayout($layout) {
        if (empty($layout)) return true;
        $layouts = Zira\View::getLayouts();
        return array_key_exists($layout, $layouts);
    }
}