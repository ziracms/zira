<?php
/**
 * Zira project
 * form.php
 * (c)2015 https://github.com/ziracms/zira
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
        $captcha_image_id = uniqid(CAPTCHA_NAME.'-image');
        
        $html = Helper::tag_open('div',array('class'=>$image_wrapper_class));
        $html .= Helper::tag_short('img',array('src'=>Helper::url('captcha').'?token='.$token.'&t='.time(),'width'=>CAPTCHA_WIDTH,'height'=>CAPTCHA_HEIGHT,'id'=>$captcha_image_id,'alt'=>Zira\Locale::t('CAPTCHA')));
        $html .= Helper::tag_close('div');

        $html .= Helper::tag_open('div',array('class'=>$input_wrapper_class));
        $html .= self::input($token,CAPTCHA_NAME, null, array('class'=>$input_class,'id'=>uniqid(CAPTCHA_NAME),'autocomplete'=>'off'), false);

        $html .= Helper::tag_open('div',array('class'=>$refresh_wrapper_class));
        $html .= Helper::tag_open('a',array('href'=>'javascript:void(0)','class'=>'captcha-refresh-btn','data-id'=>$captcha_image_id));
        $html .= $refresh_value;
        $html .= Helper::tag_close('a');
        $html .= Helper::tag_close('div');

        $html .= Helper::tag_close('div');

        return $html;
    }

    public static function recaptcha($site_key, $wrapper_class='recaptcha') {
        $html = Helper::tag_open('div',array('class'=>$wrapper_class));
        $html .= Helper::tag('div', null, array(
            'class' => 'g-recaptcha', 
            'data-sitekey' => $site_key, 
            'data-size' => Zira\Request::isMobile() ? 'compact' : 'normal'
        ));
        $html .= Helper::tag_close('div');
        return $html;
    }
    
    public static function recaptcha3($site_key, $action, $wrapper_class='recaptcha3') {
        $html = Helper::tag_open('div',array('class'=>$wrapper_class));
        $html .= Helper::tag('div', null, array(
            'class' => 'g-recaptcha3', 
            'data-sitekey' => $site_key,
            'data-action' => $action
        ));
        $html .= Helper::tag('div', Zira\Locale::t('Anti-Bot is not active.').' '.Zira\Locale::t('Please wait').'...', array('data-error'=>Zira\Locale::t('Anti-Bot is not active.'),'data-success'=>Zira\Locale::t('Anti-Bot is active.'),'class'=>'g-recaptcha3-message'));
        $html .= Helper::tag_close('div');
        return $html;
    }

    public static function generateCaptcha() {
        $token = Request::get('token');
        if (!$token) return;

        $image = imagecreatetruecolor(CAPTCHA_WIDTH,CAPTCHA_HEIGHT);
        $image_copy = imagecreatetruecolor(CAPTCHA_WIDTH,CAPTCHA_HEIGHT);

        $captcha_font = CAPTCHA_FONT;
        $size = CAPTCHA_HEIGHT*.4;
        putenv('GDFONTPATH=' . realpath(ROOT_DIR . DIRECTORY_SEPARATOR . ASSETS_DIR . DIRECTORY_SEPARATOR . FONTS_DIR));

        $bg = imagecolorallocate($image, rand(160,255), rand(160,255), rand(160,255));
        
        $ttf = true;
        try {
            imagettftext($image,$size,0,0,0,$bg,$captcha_font,' ');
        } catch(\Exception $e) {
            $ttf = false;
        }
        
        imagefill($image,0,0,$bg);
        
        if ($ttf && Zira\Locale::getLanguage()=='ru') {
            $chars = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я');
            $chars = array_merge($chars, range(0,9));
        } else {
            $chars = array_merge(range('A', 'Z'), range(0,9));
        }

        for ($i=0;$i<15;$i++) {
            $color= imagecolorallocate($image, rand(160,255), rand(160,255), rand(160,255));
            imagefilledellipse($image,rand(0,CAPTCHA_WIDTH),rand(0,CAPTCHA_HEIGHT),CAPTCHA_HEIGHT/3,CAPTCHA_HEIGHT/3,$color);
        }
        
        imagecopy($image_copy, $image, 0, 0, 0, 0, CAPTCHA_WIDTH, CAPTCHA_HEIGHT);
        
        $str_co = 5;
        $result = '';
        for ($i=0; $i<$str_co; $i++) {
            $char = $chars[rand(0, count($chars)-1)];
            $result .= $char;
            $color= imagecolorallocate($image, rand(0,155), rand(0,155), rand(0,155));
            try {
                imagettftext($image,$size,rand(-5,5),CAPTCHA_WIDTH/$str_co*$i+(CAPTCHA_WIDTH/$str_co-$size)/2,CAPTCHA_HEIGHT-(CAPTCHA_HEIGHT-$size)/2,$color,$captcha_font,$char);
            } catch(\Exception $e) {
                imagestring($image,$size,CAPTCHA_WIDTH/$str_co*$i+(CAPTCHA_WIDTH/$str_co-$size)/2,(CAPTCHA_HEIGHT-$size)/2+$size/4,$char,$color);
            }
        }
        
        Session::set(self::getFieldName($token, CAPTCHA_NAME),$result);
        
        for ($i=0;$i<15;$i++) {
            $color= imagecolorallocate($image, rand(160,255), rand(160,255), rand(160,255));
            imagefilledellipse($image,rand(0,CAPTCHA_WIDTH),rand(0,CAPTCHA_HEIGHT),CAPTCHA_HEIGHT/10,CAPTCHA_HEIGHT/10,$color);
        }
        
        if ($ttf) {
            imagecopymerge($image_copy, $image, 2, 0, 0, 1, CAPTCHA_WIDTH-2, CAPTCHA_HEIGHT/4-1, 50);
            imagecopymerge($image_copy, $image, 4, CAPTCHA_HEIGHT/4, 0, CAPTCHA_HEIGHT/4+1, CAPTCHA_WIDTH-4, CAPTCHA_HEIGHT/4-1, 50);
            imagecopymerge($image_copy, $image, 2, CAPTCHA_HEIGHT/2, 0, CAPTCHA_HEIGHT/2+1, CAPTCHA_WIDTH-2, CAPTCHA_HEIGHT/4-1, 100);
            imagecopymerge($image_copy, $image, 4, CAPTCHA_HEIGHT/4*3, 0, CAPTCHA_HEIGHT/4*3+1, CAPTCHA_WIDTH-4, CAPTCHA_HEIGHT/4-1, 50);
        } else {
            imagecopy($image_copy, $image, 1, 0, 0, 0, CAPTCHA_WIDTH-1, CAPTCHA_HEIGHT);
        }
        imagedestroy($image);
        imagejpeg($image_copy,null,50);
    }

    public static function isCaptchaValid($token,$method=Request::POST) {
        $value = self::getValue($token,CAPTCHA_NAME,$method);
        if (!$value) return false;
        $captcha = Session::get(self::getFieldName($token, CAPTCHA_NAME));
        if (!$captcha) return false;
        Session::remove(self::getFieldName($token, CAPTCHA_NAME));
        return mb_strtolower($value, CHARSET) == mb_strtolower($captcha, CHARSET);
    }

    public static function isRecaptchaValid($secret_key, $response_value) {
        if (!$secret_key || !$response_value) return false;
        $data = http_build_query(array(
            'secret' => $secret_key,
            'response' => $response_value
        ));
        $options = array(
            'http' => array(
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/x-www-form-urlencoded',
                        'content' => $data
                    )
        );
        $context  = stream_context_create($options);
        try {
            $result = file_get_contents(Zira\Models\Captcha::RECAPTCHA_VALIDATE_URL, false, $context);
            if (!$result) throw new \Exception('An error occurred');
            $result_data = json_decode($result, true);
            if (empty($result_data) || !array_key_exists('success', $result_data)) throw new \Exception('An error occurred');
        } catch (\Exception $e) {
            return false;
        }
        return $result_data['success'];
    }
    
    public static function isRecaptcha3Valid($secret_key, $response_value, $action, $min_score) {
        if (!$secret_key || !$response_value || !$action) return false;
        $data = http_build_query(array(
            'secret' => $secret_key,
            'response' => $response_value
        ));
        $options = array(
            'http' => array(
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/x-www-form-urlencoded',
                        'content' => $data
                    )
        );
        $context  = stream_context_create($options);
        try {
            $result = file_get_contents(Zira\Models\Captcha::RECAPTCHA_VALIDATE_URL, false, $context);
            if (!$result) throw new \Exception('An error occurred');
            $result_data = json_decode($result, true);
            if (empty($result_data) || !array_key_exists('success', $result_data)) throw new \Exception('An error occurred');
            if (!array_key_exists('score', $result_data) || !array_key_exists('action', $result_data)) throw new \Exception('An error occurred');
        } catch (\Exception $e) {
            return false;
        }
        return $result_data['success'] && $result_data['action']==$action && floatval($result_data['score'])>=floatval($min_score);
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