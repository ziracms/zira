<?php
/**
 * Zira project.
 * message.php
 * (c)2016 http://dro1d.ru
 */

namespace Forum\Forms;

use Zira\Config;
use Zira\Form;
use Zira\Helper;
use Zira\Locale;
use Zira\View;

class Reply extends Form {
    protected $_id = 'forum-message-form';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        View::addParser();
        $this->setAjax(true);
        if (\Zira\Config::get('forum_file_uploads')) {
            $this->setMultipart(true);
        }
        $this->setTitle(Locale::t('Reply'));
        $this->setDescription(Locale::t('Message should contain at least %s characters', Config::get('forum_min_chars', 10)));
        $script = Helper::tag_open('script', array('type'=>'text/javascript'));
        $script .= 'jQuery(document).ready(function(){';
        $script .= 'jQuery(\'#'.$this->getId().'\').bind(\'xhr-submit-success\', function(e, response){ ';
        $script .= 'zira_forum_form_submit_success(response);';
        $script .= '});';
        $script .= '});';
        $script .= Helper::tag_close('script');
        View::addHTML($script, View::VAR_BODY_BOTTOM);
    }

    protected function _render() {
        $html = $this->open();
        $extra_items = \Zira\Hook::run(\Zira\Page::USER_TEXTAREA_HOOK);
        if (!empty($extra_items)) {
            $html .= Helper::tag_open('div',array('class'=>'user-text-form-extra-items'));
            foreach($extra_items as $item) {
                $html .= Helper::tag_open('div',array('class'=>'user-text-form-extra-item'));
                $html .= $item;
                $html .= Helper::tag_close('div');
            }
            $html .= Helper::tag_close('div');
        }

        $html .= $this->textarea(Locale::t('Message').'*','message', array('class'=>'form-control user-rich-input', 'rows'=>6));
        if (\Zira\Config::get('forum_file_uploads')) {
            $html .= Helper::tag_open('div',array('class'=>'forum-attach-input-icon'));
            $html .= Helper::tag_open('div',array('class'=>'col-sm-3'));
            $html .= Helper::tag_close('div');
            $html .= Helper::tag_open('div',array('class'=>'col-sm-9'));
            $html .= Helper::tag('span', null, array('class'=>'glyphicon glyphicon-paperclip attach-icon', 'title'=>Locale::tm('Attach files', 'forum')));
            $html .= Helper::tag_close('div');
            $html .= Helper::tag_close('div');
            $html .= Helper::tag_open('div',array('class'=>'forum-attach-input-wrapper'));
            $html .= $this->fileButton(Locale::tm('Attach files', 'forum'), 'attaches', array('class'=>'forum-attaches', 'title'=>Locale::tm('File max. size: %s', 'forum', \Forum\Models\File::DEFAULT_MAX_SIZE).' kB'), true);
            $html .= Helper::tag_close('div');
        }
        $html .= $this->captcha(Locale::t('Enter result').'*');
        $html .= $this->submit(Locale::t('Submit'));
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();
        $validator->registerCaptcha(Locale::t('Wrong CAPTCHA result'));
        $validator->registerCustom(array(get_class(), 'checkMessageMinLength'), 'message', Locale::t('Message should contain at least %s characters', Config::get('forum_min_chars', 10)));
        $validator->registerNoTags('message', Locale::t('Message contains bad character'));
        //$validator->registerUtf8('message', Locale::t('Message contains bad character'));

        if (\Zira\Config::get('forum_file_uploads')) {
            $max_size = (int)\Zira\Config::get('forum_file_max_size', \Forum\Models\File::DEFAULT_MAX_SIZE) * 1024;
            $ext = \Zira\Config::get('forum_file_ext', \Forum\Models\File::DEFAULT_ALLOWED_EXTENSIONS);
            $exts = explode(',', $ext);
            $exts_i = array();
            for($i=0; $i<count($exts); $i++) {
                $exts[$i] = trim(strtolower($exts[$i]));
                $exts_i []= trim(strtoupper($exts[$i]));
            }
            $validator->registerFile('attaches', $max_size, array_merge($exts, $exts_i), false, Locale::tm('Invalid file', 'forum'));
            $validator->registerCustom(array(get_class(), 'checkAttaches'), 'attaches', Locale::tm('Invalid file', 'forum'));
        }
    }

    public static function checkMessageMinLength($message) {
        return (mb_strlen(html_entity_decode($message), CHARSET)>=Config::get('forum_min_chars', 10));
    }

    public static function checkAttaches($files) {
        if (empty($files) || !is_array($files) || empty($files['name']) || empty($files['tmp_name'])) return true;
        $bad_exts = explode(',', \Forum\Models\File::BAD_EXTENSIONS);
        $bad_exts_i = array();
        for($i=0; $i<count($bad_exts); $i++) {
            $bad_exts_i []= trim(strtolower($bad_exts[$i]));
            $bad_exts_i []= trim(strtoupper($bad_exts[$i]));
        }
        $names = array();
        if (is_array($files['name'])) {
            foreach($files['name'] as $name) {
                $names []= $name;
            }
        } else {
            $names []= $files['name'];
        }
        foreach($names as $file) {
            $p = strrpos($file, '.');
            if ($p !== false) $ext = substr($file, $p+1);
            else $ext = '';
            if (in_array($ext, $bad_exts_i)) return false;
        }
        return true;
    }
}