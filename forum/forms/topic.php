<?php
/**
 * Zira project.
 * topic.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Topic extends Form
{
    protected $_id = 'dash-forum-topic-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';
    protected $_select_wrapper_class = 'col-sm-8';

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
        $html .= $this->hidden('id');
        if (count(Zira\Config::get('languages'))<2) {
            $html .= $this->hidden('language');
        } else {
            $html .= $this->selectDropdown(Locale::t('Language').'*','language',array_merge(array(''=>Locale::t('All languages')), Locale::getLanguagesArray()));
        }
        $html .= $this->hidden('category_id');
        $html .= $this->input(Locale::t('Title').'*', 'title');
        $html .= $this->textarea(Locale::t('Description'), 'description');
        $html .= $this->input(Locale::t('Window title'), 'meta_title');
        $html .= $this->input(Locale::t('Keywords'), 'meta_keywords');
        $html .= $this->input(Locale::tm('Meta description', 'forum'), 'meta_description');
        $html .= $this->textarea(Locale::tm('Information message', 'forum'), 'info');
        $id = $this->getValue('id');
        $category_id = $this->getValue('category_id');
        if ($id && $category_id) {
            $html .= $this->select(Locale::tm('Forum', 'forum').'*', 'forum_id', \Forum\Models\Forum::getArray($category_id));
        } else {
            $html .= $this->hidden('forum_id');
        }
        $html .= $this->select(Locale::tm('Status','forum'), 'status', \Forum\Models\Topic::getStatuses());
        $html .= $this->checkbox(Locale::t('Thread is open', 'forum'), 'active', null, false);
        $html .= $this->checkbox(Locale::t('Thread is sticky', 'forum'), 'sticky', null, false);
        $html .= $this->close();
        return $html;
    }

    protected function _validate()
    {
        $validator = $this->getValidator();

        $validator->registerNumber('forum_id', 1, null, true, Locale::t('An error occurred'));
        $validator->registerNumber('category_id', 1, null, true, Locale::t('An error occurred'));

        $validator->registerString('title', null, 255, true, Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerNoTags('title', Locale::t('Invalid value "%s"',Locale::t('Title')));
        $validator->registerUtf8('title', Locale::t('Invalid value "%s"',Locale::t('Title')));

        $validator->registerNoTags('description', Locale::t('Invalid value "%s"',Locale::t('Description')));
        $validator->registerUtf8('description', Locale::t('Invalid value "%s"',Locale::t('Description')));

        $validator->registerString('meta_title', null, 255, false, Locale::t('Invalid value "%s"',Locale::t('Window title')));
        $validator->registerNoTags('meta_title', Locale::t('Invalid value "%s"',Locale::t('Window title')));
        $validator->registerUtf8('meta_title', Locale::t('Invalid value "%s"',Locale::t('Window title')));

        $validator->registerString('meta_keywords', null, 255, false, Locale::t('Invalid value "%s"',Locale::t('Keywords')));
        $validator->registerNoTags('meta_keywords', Locale::t('Invalid value "%s"',Locale::t('Keywords')));
        $validator->registerUtf8('meta_keywords', Locale::t('Invalid value "%s"',Locale::t('Keywords')));

        $validator->registerString('meta_description', null, 255, false, Locale::t('Invalid value "%s"',Locale::tm('Meta description', 'forum')));
        $validator->registerNoTags('meta_description', Locale::t('Invalid value "%s"',Locale::tm('Meta description', 'forum')));
        $validator->registerUtf8('meta_description', Locale::t('Invalid value "%s"',Locale::tm('Meta description', 'forum')));

        $validator->registerNoTags('info', Locale::t('Invalid value "%s"',Locale::t('Information message')));
        $validator->registerUtf8('info', Locale::t('Invalid value "%s"',Locale::t('Information message')));

        $validator->registerCustom(array(get_class(), 'checkStatus'), 'status', Locale::t('An error occurred'));

        $validator->registerCustom(array(get_class(), 'checkForum'), array('category_id', 'forum_id'), Locale::t('An error occurred'));
        $validator->registerCustom(array(get_class(), 'checkLanguage'), 'language', Locale::t('An error occurred'));                
    }

    public static function checkStatus($status) {
        $statuses = \Forum\Models\Topic::getStatuses();
        return array_key_exists($status, $statuses);
    }

    public static function checkForum($category_id, $forum_id) {
        $forum = new \Forum\Models\Forum($forum_id);
        return ($forum->loaded() && $forum->category_id == $category_id);
    }

    public static function checkLanguage($language) {
        if (empty($language)) return true;
        return in_array($language , Zira\Config::get('languages'));
    }
}