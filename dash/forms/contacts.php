<?php
/**
 * Zira project.
 * contacts.php
 * (c)2016 https://github.com/ziracms/zira
 */

namespace Dash\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Contacts extends Form
{
    protected $_id = 'dash-contacts-form';

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
        $html .= $this->input(Locale::t('Full name / Company'), 'contact_name');
        $html .= $this->input(Locale::t('Address'), 'contact_address');
        $html .= $this->input(Locale::t('Picture'), 'contact_image', array('class'=>'form-control picture_option'));
        $html .= $this->input(Locale::t('Email'), 'feedback_email');
        $html .= $this->checkbox(Locale::t('Show Email'), 'contact_email_public', null, false);
        $html .= $this->input(Locale::t('Phone'), 'contact_phone');
        $html .= $this->textarea(Locale::t('Information'), 'contact_info');
        $html .= $this->input(Locale::t('Facebook'), 'contact_fb');
        $html .= $this->input(Locale::t('Google +'), 'contact_gp');
        $html .= $this->input(Locale::t('Twitter'), 'contact_tw');
        $html .= $this->input(Locale::t('Vkontakte'), 'contact_vk');
        $html .= $this->checkbox(Locale::t('Enable Google Map'), 'contact_google_map', null, false);
        $html .= $this->input(Locale::t('Google Maps API key'), 'google_map_key');
        $html .= $this->checkbox(Locale::t('Enable Yandex Map'), 'contact_yandex_map', null, false);
        $html .= $this->input(Locale::t('Yandex Maps API key'), 'yandex_map_key');
        $html .= $this->input(Locale::t('Latitude'), 'maps_latitude');
        $html .= $this->input(Locale::t('Longitude'), 'maps_longitude');
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $validator->registerString('contact_name',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Full name / Company')));
        $validator->registerNoTags('contact_name',Locale::t('Invalid value "%s"',Locale::t('Full name / Company')));
        $validator->registerUtf8('contact_name',Locale::t('Invalid value "%s"',Locale::t('Full name / Company')));

        $validator->registerString('contact_address',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Address')));
        $validator->registerNoTags('contact_address',Locale::t('Invalid value "%s"',Locale::t('Address')));
        $validator->registerUtf8('contact_address',Locale::t('Invalid value "%s"',Locale::t('Address')));

        $validator->registerEmail('feedback_email',false,Locale::t('Invalid value "%s"',Locale::t('Email')));

        $validator->registerString('contact_image',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Picture')));
        $validator->registerCustom(array(get_class(), 'checkImage'), 'contact_image',Locale::t('Invalid value "%s"',Locale::t('Picture')));

        $validator->registerString('contact_phone',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Phone')));
        $validator->registerNoTags('contact_phone',Locale::t('Invalid value "%s"',Locale::t('Phone')));
        $validator->registerUtf8('contact_phone',Locale::t('Invalid value "%s"',Locale::t('Phone')));

        $validator->registerString('contact_info',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Information')));
        $validator->registerNoTags('contact_info',Locale::t('Invalid value "%s"',Locale::t('Information')));
        $validator->registerUtf8('contact_info',Locale::t('Invalid value "%s"',Locale::t('Information')));

        $validator->registerString('contact_fb',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Facebook')));
        $validator->registerNoTags('contact_fb',Locale::t('Invalid value "%s"',Locale::t('Facebook')));
        $validator->registerUtf8('contact_fb',Locale::t('Invalid value "%s"',Locale::t('Facebook')));

        $validator->registerString('contact_gp',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Google +')));
        $validator->registerNoTags('contact_gp',Locale::t('Invalid value "%s"',Locale::t('Google +')));
        $validator->registerUtf8('contact_gp',Locale::t('Invalid value "%s"',Locale::t('Google +')));

        $validator->registerString('contact_tw',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Twitter')));
        $validator->registerNoTags('contact_tw',Locale::t('Invalid value "%s"',Locale::t('Twitter')));
        $validator->registerUtf8('contact_tw',Locale::t('Invalid value "%s"',Locale::t('Twitter')));

        $validator->registerString('contact_vk',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Vkontakte')));
        $validator->registerNoTags('contact_vk',Locale::t('Invalid value "%s"',Locale::t('Vkontakte')));
        $validator->registerUtf8('contact_vk',Locale::t('Invalid value "%s"',Locale::t('Vkontakte')));

        $validator->registerString('google_map_key',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Google Maps API key')));
        $validator->registerNoTags('google_map_key',Locale::t('Invalid value "%s"',Locale::t('Google Maps API key')));
        $validator->registerUtf8('google_map_key',Locale::t('Invalid value "%s"',Locale::t('Google Maps API key')));

        $validator->registerString('yandex_map_key',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Yandex Maps API key')));
        $validator->registerNoTags('yandex_map_key',Locale::t('Invalid value "%s"',Locale::t('Yandex Maps API key')));
        $validator->registerUtf8('yandex_map_key',Locale::t('Invalid value "%s"',Locale::t('Yandex Maps API key')));

        $validator->registerString('maps_latitude',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Latitude')));
        $validator->registerNoTags('maps_latitude',Locale::t('Invalid value "%s"',Locale::t('Latitude')));
        $validator->registerUtf8('maps_latitude',Locale::t('Invalid value "%s"',Locale::t('Latitude')));

        $validator->registerString('maps_longitude',null,255,false,Locale::t('Invalid value "%s"',Locale::t('Longitude')));
        $validator->registerNoTags('maps_longitude',Locale::t('Invalid value "%s"',Locale::t('Longitude')));
        $validator->registerUtf8('maps_longitude',Locale::t('Invalid value "%s"',Locale::t('Longitude')));

        $picture = (string)$this->getValue('contact_image');
        if (!empty($picture)) {
            $picture = trim($picture,'/');
        }
        $contact_address = (string)$this->getValue('contact_address');
        $contact_address = trim($contact_address);
        $google_map_key = (string)$this->getValue('google_map_key');
        $google_map_key = trim($google_map_key);
        $yandex_map_key = (string)$this->getValue('yandex_map_key');
        $yandex_map_key = trim($yandex_map_key);
        $maps_latitude = (string)$this->getValue('maps_latitude');
        $maps_latitude = trim($maps_latitude);
        $maps_longitude = (string)$this->getValue('maps_longitude');
        $maps_longitude = trim($maps_longitude);
        $this->updateValues(array(
            'contact_image' => $picture,
            'contact_address' => $contact_address,
            'google_map_key' => $google_map_key,
            'yandex_map_key' => $yandex_map_key,
            'maps_latitude' => $maps_latitude,
            'maps_longitude' => $maps_longitude
        ));
    }

    public static function checkImage($picture) {
        if (empty($picture)) return true;
        if (strpos($picture,'..')!==false) return false;

        $p = strrpos($picture, '.');
        if ($p===false) return false;
        $ext = substr($picture, $p+1);
        if (!in_array(strtolower($ext), array('jpg','jpeg','gif','png'))) return false;

        if (!file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . $picture)) return false;

        $size = @getimagesize(ROOT_DIR . DIRECTORY_SEPARATOR . $picture);
        if (!$size) return false;

        return true;
    }
}