<?php
/**
 * Zira project.
 * contact.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Forms;

use Zira\Form;
use Zira\Locale;
use Zira\User;

class Contact extends Form {
    const MIN_CHARS = 10;
    const MAX_CHARS = 1024;

    protected $_id = 'contact-form';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $this->setUrl('contact');
        $this->setTitle(Locale::t('Send message'));
        if (!User::isAuthorized()) {
            $this->setDescription(Locale::t('Please specify your name and Email address'));
        } else {
            $this->setDescription(Locale::t('Message should contain at least %s characters', self::MIN_CHARS));
        }
    }

    protected function _render() {
        $html = $this->open();
        if (!User::isAuthorized()) {
            $html .= $this->input(Locale::t('Name'), 'name');
            $html .= $this->input(Locale::t('Email'), 'email');
        }
        $html .= $this->textarea(Locale::t('Message').'*','message');
        $html .= $this->captcha(Locale::t('Anti-Bot').'*');
        $html .= $this->submit(Locale::t('Submit'));
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();
        $validator->registerCaptcha(Locale::t('Wrong CAPTCHA result'));
        $validator->registerString('name', 2, 255, false, Locale::t('Invalid name'));
        $validator->registerNoTags('name', Locale::t('Invalid name'));
        $validator->registerUtf8('name', Locale::t('Invalid name'));
        $validator->registerEmail('email', false, Locale::t('Invalid email'));
        $validator->registerText('message', self::MIN_CHARS, true, Locale::t('Message should contain at least %s characters', self::MIN_CHARS));
        $validator->registerString('message', null, self::MAX_CHARS, true, Locale::t('Sorry, your message is too big'));
        $validator->registerNoTags('message', Locale::t('Message contains bad character'));
        $validator->registerUtf8('message', Locale::t('Message contains bad character'));
    }
}