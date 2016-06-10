<?php
/**
 * Zira project.
 * edit.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Forms\User;

use Zira\Form;
use Zira\Locale;
use Zira\User;

class Edit extends Form {
    protected $_id = 'user-edit-form';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $this->initDatepicker('dob', 'years', date('Y-m-d'));
        $this->setTitle(Locale::t('Change profile'));
        $this->setDescription(Locale::t('Please enter your first name and second name'));
    }

    protected function _render() {
        $html = $this->open();
        $html .= $this->input(Locale::t('First name').'*','firstname');
        $html .= $this->input(Locale::t('Second name').'*','secondname');
        $html .= $this->input(Locale::t('Country'),'country');
        $html .= $this->input(Locale::t('City'),'city');
        $html .= $this->input(Locale::t('Street'),'street');
        $html .= $this->input(Locale::t('Phone'),'phone', array('placeholder'=>'+7...'));
        $html .= $this->datepicker(Locale::t('Date of birth'), 'dob');
        if (!User::isUserPasswordChecked()) {
            $html .= $this->password(Locale::t('Current password').'*','password-current');
        }
        $html .= $this->captchaLazy(Locale::t('Enter result').'*');
        $html .= $this->submit(Locale::t('Submit'));
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();
        $validator->registerCaptchaLazy($this->_id, Locale::t('Wrong CAPTCHA result'));
        $validator->registerString('firstname',0,0,true,Locale::t('Please enter your first name'));
        $validator->registerString('secondname',0,0,true,Locale::t('Please enter your second name'));
        $validator->registerNoTags('firstname', Locale::t('Invalid character specified'));
        $validator->registerUtf8('firstname', Locale::t('Invalid character specified'));
        $validator->registerNoTags('secondname', Locale::t('Invalid character specified'));
        $validator->registerUtf8('secondname', Locale::t('Invalid character specified'));
        $validator->registerNoTags('country', Locale::t('Invalid character specified'));
        $validator->registerUtf8('country', Locale::t('Invalid character specified'));
        $validator->registerNoTags('city', Locale::t('Invalid character specified'));
        $validator->registerUtf8('city', Locale::t('Invalid character specified'));
        $validator->registerNoTags('street', Locale::t('Invalid character specified'));
        $validator->registerUtf8('street', Locale::t('Invalid character specified'));
        $validator->registerPhone('phone', false, Locale::t('Phone should be specified in international format'));
        $validator->registerDate('dob', false, Locale::t('Invalid date format'));
        if (!User::isUserPasswordChecked()) {
            $validator->registerString('password-current',User::PASSWORD_MIN_CHARS,User::PASSWORD_MAX_CHARS,true,Locale::t('Invalid password'));
            $validator->registerCustom(array(get_class(), 'checkPassword'), 'password-current', Locale::t('Current password incorrect'));
        }

        $firstname = trim((string)$this->getValue('firstname'));
        $secondname = trim((string)$this->getValue('secondname'));
        $country = trim((string)$this->getValue('country'));
        $city = trim((string)$this->getValue('city'));
        $street = trim((string)$this->getValue('street'));
        $phone = trim((string)$this->getValue('phone'));
        if (!empty($phone)) {
            $phone = str_replace(' ','',$phone);
            $phone = str_replace('-','',$phone);
        }
        $this->updateValues(array(
            'firstname' => $firstname,
            'secondname' => $secondname,
            'country' => $country,
            'city' => $city,
            'street' => $street,
            'phone' => $phone
        ));
    }

    public static function checkPassword($password) {
        $user = User::getCurrent();
        if (!$user || !User::isAuthorized()) return false;
        $success = User::isPasswordCorrect($user->username, $password);
        if ($success) {
            User::setUserPasswordChecked();
        }
        return $success;
    }
}