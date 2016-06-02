<?php
/**
 * Zira project.
 * avatar.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Forms\User;

use Zira\Config;
use Zira\File;
use Zira\Form;
use Zira\Helper;
use Zira\Locale;
use Zira\User;
use Zira\View;

class Avatar extends Form {
    protected $_id = 'user-avatar-form';
    protected $_cropper_id = 'avatar-cropper';
    protected $_previewer_id = 'avatar-preview';

    protected $_cropper_width = 250;

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $w = intval(Config::get('user_thumb_width'));
        $h = intval(Config::get('user_thumb_height'));

        View::addCropper($this->_cropper_id, array(
            'dst_w' => $w,
            'dst_h' => $h,
            'sel_w' => $w,
            'sel_h' => $h,
            'sel_mw' => $w,
            'sel_mh' => $h,
            'fixed' => true,
            'preview' => true,
            'previewer' => $this->_previewer_id,
            'input_w'=>'cropper_w',
            'input_h'=>'cropper_h',
            'input_x'=>'cropper_x',
            'input_y'=>'cropper_y'
        ));
        $this->setTitle(Locale::t('Change avatar'));
        $this->setDescription(Locale::t('Select your desired area'));
    }

    protected function _render() {
        $image = $this->getValue('image');
        $filename = User::getUserPhotoFilename($image);
        $url = Helper::baseUrl(UPLOADS_DIR . '/' . USERS_DIR . '/' . $filename);
        $size = @getimagesize(File::getAbsolutePath(USERS_DIR). DIRECTORY_SEPARATOR . $filename);
        if ($size[0]>$size[1]) {
            $height = $this->_cropper_width;
            $width = round($height * $size[0] / $size[1]);
        } else {
            $width = $this->_cropper_width;
            $height = round($width * $size[1] / $size[0]);
        }

        $html = $this->open();
        $html .= Helper::tag_open('div',array('class'=>$this->_group_class));
        $html .= Helper::tag('label', Locale::t('Photo'), array('class'=>$this->_label_class));
        $html .= Helper::tag_open('div',array('class'=>$this->_input_wrap_class));
        $html .= Helper::tag_short('img', array('src'=>$url,'width'=>$width,'height'=>$height,'id'=>$this->_cropper_id));
        $html .= Helper::tag_close('div');
        $html .= Helper::tag_close('div');
        $html .= Helper::tag_open('div',array('class'=>$this->_group_class));
        $html .= Helper::tag('label', Locale::t('Avatar'), array('class'=>$this->_label_class));
        $html .= Helper::tag_open('div',array('class'=>$this->_input_wrap_class));
        $html .= Helper::tag('div', null, array('id'=>$this->_previewer_id));
        $html .= Helper::tag_close('div');
        $html .= Helper::tag_close('div');
        $html .= $this->hidden('cropper_w');
        $html .= $this->hidden('cropper_h');
        $html .= $this->hidden('cropper_x');
        $html .= $this->hidden('cropper_y');
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
        if (!User::isUserPasswordChecked()) {
            $validator->registerString('password-current',User::PASSWORD_MIN_CHARS,User::PASSWORD_MAX_CHARS,true,Locale::t('Invalid password'));
            $validator->registerCustom(array(get_class(), 'checkPassword'), 'password-current', Locale::t('Current password incorrect'));
        }
        $width = floatval($this->getValue('cropper_w'));
        $height = floatval($this->getValue('cropper_h'));
        $left = floatval($this->getValue('cropper_x'));
        $top = floatval($this->getValue('cropper_y'));

        $this->updateValues(array(
            'cropper_w' => $width,
            'cropper_h' => $height,
            'cropper_x' => $left,
            'cropper_y' => $top
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