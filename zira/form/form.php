<?php
/**
 * Zira project
 * form.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Form;

use Zira;
use Zira\Helper;
use Zira\Request;
use Zira\Session;

class Form {
    protected static function generateId($key) {
        return 'form-'.md5($key.SECRET);
    }

    protected static function generateToken($key, $unique = false) {
        if (!$unique) return 'form-'.$key.'-'.Zira\User::getToken();
        else return 'form-'.$key.'-'.Zira::randomSecureString(8);
    }

    public static  function getToken($key, $unique = false) {
        $id = self::generateId($key);
        $exist = Session::get($id);
        if ($exist) return $exist;

        $token = self::generateToken($key, $unique);
        Session::set($id,$token);

        return $token;
    }

    public static function getFieldName($token, $name) {
        return $_name = $token . '-' . $name;
    }

    public static function open($url,$method, $multipart = false, array $attributes = null) {
        if (!$attributes) $attributes = array();
        $attributes['action'] = Helper::url($url);
        $attributes['method'] = $method;
        if ($multipart) $attributes['enctype'] = 'multipart/form-data';

        return Helper::tag_open('form',$attributes);
    }

    public static function close() {
        return Helper::tag_close('form');
    }

    public static function label($text, $for = null, array $attributes = null) {
        if (empty($text)) return '';
        if (!$attributes) $attributes = array();
        if ($for!==null) $attributes['for'] = $for;
        if (strpos($text, '*')===false) {
            return Helper::tag('label',$text,$attributes);
        } else {
            $text = str_replace('*','<span class="required">*</span>',Helper::html($text));
            $html = Helper::tag_open('label',$attributes);
            $html .= $text;
            $html .= Helper::tag_close('label');
            return $html;
        }
    }

    public static function input($token, $name, $value=null, array $attributes = null, $fill=Request::POST) {
        $_name = self::getFieldName($token, $name);
        if (!$attributes) $attributes = array();
        if (!isset($attributes['type'])) $attributes['type'] = 'text';
        if (!isset($attributes['id'])) $attributes['id'] = $name;
        $attributes['name'] = $_name;

        if ($fill) {
            if ($fill == Request::POST && Request::isPost()) $_value = Request::post($_name);
            else if($fill == Request::GET) $_value = Request::get($_name);
        }

        if (isset($_value)) $attributes['value'] = $_value;
        else if ($value!==null) $attributes['value'] = $value;
        else if (!isset($attributes['value'])) $attributes['value'] = '';
        return Helper::tag_short('input',$attributes);
    }

    public static function password($token, $name, $value=null, array $attributes = null) {
        if (!$attributes) $attributes = array();
        $attributes['type'] = 'password';
        return self::input($token,$name,$value,$attributes,false);
    }

    public static function hidden($token, $name, $value=null, array $attributes = null, $fill=Request::POST) {
        if (!$attributes) $attributes = array();
        $attributes['type'] = 'hidden';
        return self::input($token,$name,$value,$attributes,$fill);
    }

    public static function token($value, array $attributes = null) {
        if (!$attributes) $attributes = array();
        $attributes['name'] = 'token';
        $attributes['type'] = 'hidden';
        $attributes['value'] = $value;
        return Helper::tag_short('input',$attributes);
    }

    public static function file($token, $name, array $attributes = null, $multiple = false) {
        if (!$attributes) $attributes = array();
        $attributes['type'] = 'file';
        if ($multiple) {
            $attributes['multiple'] = 'multiple';
            if (!isset($attributes['id'])) $attributes['id'] = $name;
            $name .= '[]';
        }

        return self::input($token,$name,null,$attributes,false);
    }

    public static function checkbox($token, $name, $value=null, array $attributes = null, $fill=Request::POST) {
        if (!$attributes) $attributes = array();
        $attributes['type'] = 'checkbox';
        if ($value===null && !isset($attributes['value'])) $attributes['value'] = 1;

        if ($fill) {
            $_name = $token . '-' . $name;
            if ($fill == Request::POST && Request::isPost()) $_value = Request::post($_name, 0);
            else if ($fill == Request::GET) $_value = Request::get($_name, 0);

            if (isset($_value) && !empty($_value)) {
                $attributes['checked'] = 'checked';
            } else if (isset($_value) && empty($_value) && isset($attributes['checked'])) {
                unset($attributes['checked']);
            }
            $fill = false;
        }

        return self::input($token,$name,$value,$attributes,$fill);
    }

    public static function radio($token, $name, $value=null, array $attributes = null, $fill=Request::POST) {
        if (!$attributes) $attributes = array();
        $attributes['type'] = 'radio';
        if ($value===null && isset($attributes['value'])) $value = $attributes['value'];

        if ($fill) {
            $_name = $token . '-' . $name;
            if ($fill == Request::POST && Request::isPost()) $_value = Request::post($_name);
            else if ($fill == Request::GET) $_value = Request::get($_name);

            if (isset($_value) && $_value == $value) {
                $attributes['checked'] = 'checked';
            } else if (isset($_value) && $_value != $value && isset($attributes['checked'])) {
                unset($attributes['checked']);
            }
            $fill = false;
        }

        return self::input($token,$name,$value,$attributes,$fill);
    }

    public static function textarea($token, $name, $value=null, array $attributes = null, $fill=Request::POST) {
        $_name = self::getFieldName($token, $name);
        if (!$attributes) $attributes = array();
        if (!isset($attributes['id'])) $attributes['id'] = $name;
        $attributes['name'] = $_name;

        if ($fill) {
            if ($fill == Request::POST && Request::isPost()) $_value = Request::post($_name);
            else if($fill == Request::GET) $_value = Request::get($_name);
        }

        if (isset($_value)) $value = $_value;

        return Helper::tag('textarea',$value,$attributes);
    }

    public static function select($token, $name, array $options=null, $selected = null, array $attributes = null, $fill=Request::POST) {
        $_name = self::getFieldName($token, $name);
        if (!$options) $options = array();
        if (!$attributes) $attributes = array();
        if (!isset($attributes['id'])) $attributes['id'] = $name;
        $attributes['name'] = $_name;

        if ($fill) {
            if ($fill == Request::POST && Request::isPost()) $_value = Request::post($_name);
            else if ($fill == Request::GET) $_value = Request::get($_name);
        }

        if (isset($_value)) $selected = $_value;

        $html = Helper::tag_open('select',$attributes);
        foreach($options as $k=>$v) {
            $_attributes = array();
            $_attributes['value'] = $k;
            if ($k == $selected) $_attributes['selected'] = 'selected';
            $html .= Helper::tag('option',$v,$_attributes);
        }
        $html .= Helper::tag_close('select');
        return $html;
    }

    public static function button($label, array $attributes = null) {
        return Helper::tag('button',$label,$attributes);
    }

    public static function submit($label, array $attributes = null) {
        if (!$attributes) $attributes = array();
        $attributes['type'] = 'submit';
        $attributes['value'] = $label;

        return Helper::tag_short('input',$attributes);
    }

    public static function captcha($token,$image_wrapper_class='captcha_image',$input_wrapper_class='captcha_input',$input_class='captcha',$refresh_wrapper_class='captcha_refresh',$refresh_value = 'Reload') {
        $html = Helper::tag_open('div',array('class'=>$image_wrapper_class));
        $html .= Helper::tag_short('img',array('src'=>Helper::url('captcha').'?token='.$token.'&t='.time(),'width'=>CAPTCHA_WIDTH,'height'=>CAPTCHA_HEIGHT,'id'=>$token.'-'.CAPTCHA_NAME.'-image','alt'=>Zira\Locale::t('CAPTCHA')));
        $html .= Helper::tag_close('div');

        $html .= Helper::tag_open('div',array('class'=>$input_wrapper_class));
        $html .= self::input($token,CAPTCHA_NAME, null, array('class'=>$input_class,'autocomplete'=>'off'), false);

        $html .= Helper::tag_open('div',array('class'=>$refresh_wrapper_class));
        $html .= Helper::tag_open('a',array('href'=>'javascript:void(0)','onclick'=>'document.getElementById(\''.$token.'-'.CAPTCHA_NAME.'-image'.'\').src+=Math.floor(Math.random()*10);'));
        $html .= $refresh_value;
        $html .= Helper::tag_close('a');
        $html .= Helper::tag_close('div');

        $html .= Helper::tag_close('div');

        return $html;
    }

    public static function generateCaptcha() {
        $token = Request::get('token');
        if (!$token) return;

        $digit1 = rand(1,9);
        $digit2 = rand(1,9);

        $result = $digit1 + $digit2;
        Session::set(self::getFieldName($token, CAPTCHA_NAME),$result);

        $image = imagecreatetruecolor(CAPTCHA_WIDTH,CAPTCHA_HEIGHT);

        $size = CAPTCHA_HEIGHT*.8;
        putenv('GDFONTPATH=' . realpath(ROOT_DIR . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . FONTS_DIR));

        $bg = imagecolorallocate($image, rand(160,255), rand(160,255), rand(160,255));
        imagefill($image,0,0,$bg);

        for ($i=0;$i<30;$i++) {
            $color= imagecolorallocate($image, rand(160,255), rand(160,255), rand(160,255));
            imagefilledellipse($image,rand(0,CAPTCHA_WIDTH),rand(0,CAPTCHA_HEIGHT),CAPTCHA_HEIGHT/3,CAPTCHA_HEIGHT/3,$color);
        }

        $captcha_font = CAPTCHA_FONT;

        $color= imagecolorallocate($image, rand(0,155), rand(0,155), rand(0,155));
        try {
			imagettftext($image,$size,rand(-15,15),CAPTCHA_WIDTH/5+(CAPTCHA_WIDTH/5-$size)/2,CAPTCHA_HEIGHT-(CAPTCHA_HEIGHT-$size)/2,$color,$captcha_font,$digit1);
		} catch(\Exception $e) {
			imagestring($image,$size,CAPTCHA_WIDTH/5+(CAPTCHA_WIDTH/5-$size)/2,(CAPTCHA_HEIGHT-$size)/2+$size/4,$digit1,$color);
		}

        $sign = '+';
        $color= imagecolorallocate($image, rand(0,155), rand(0,155), rand(0,155));
		try {
			imagettftext($image,$size,rand(-5,5),CAPTCHA_WIDTH*2/5+(CAPTCHA_WIDTH/5-$size)/2,CAPTCHA_HEIGHT-(CAPTCHA_HEIGHT-$size)/2,$color,$captcha_font,$sign);
		} catch(\Exception $e) {
			imagestring($image,$size,CAPTCHA_WIDTH*2/5+(CAPTCHA_WIDTH/5-$size)/2,(CAPTCHA_HEIGHT-$size)/2+$size/4,$sign,$color);
		}

        $color= imagecolorallocate($image, rand(0,155), rand(0,155), rand(0,155));
		try {
			imagettftext($image,$size,rand(-15,15),CAPTCHA_WIDTH*3/5+(CAPTCHA_WIDTH/5-$size)/2,CAPTCHA_HEIGHT-(CAPTCHA_HEIGHT-$size)/2,$color,$captcha_font,$digit2);
		} catch(\Exception $e) {
			imagestring($image,$size,CAPTCHA_WIDTH*3/5+(CAPTCHA_WIDTH/5-$size)/2,(CAPTCHA_HEIGHT-$size)/2+$size/4,$digit2,$color);
		}

        $sign = '=';
        $color= imagecolorallocate($image, rand(0,155), rand(0,155), rand(0,155));
		try {
			imagettftext($image,$size,rand(-5,5),CAPTCHA_WIDTH*4/5+(CAPTCHA_WIDTH/5-$size)/2,CAPTCHA_HEIGHT-(CAPTCHA_HEIGHT-$size)/2,$color,$captcha_font,$sign);
		} catch(\Exception $e) {
			imagestring($image,$size,CAPTCHA_WIDTH*4/5+(CAPTCHA_WIDTH/5-$size)/2,(CAPTCHA_HEIGHT-$size)/2+$size/4,$sign,$color);
		}

        imagejpeg($image,null,90);
    }

    public static function isCaptchaValid($token,$method=Request::POST) {
        $value = self::getValue($token,CAPTCHA_NAME,$method);
        if (!$value) return false;
        $captcha = Session::get(self::getFieldName($token, CAPTCHA_NAME));
        if (!$captcha) return false;
        return $value == $captcha;
    }

    public static function getValue($token,$name,$method=Request::POST) {
        $_name = self::getFieldName($token, $name);
        if ($method == Request::POST) {
            return Request::post($_name);
        } else if ($method == Request::FILES) {
            return Request::file($_name);
        } else if ($method == Request::GET) {
            return Request::get($_name);
        } else {
            return null;
        }
    }
}