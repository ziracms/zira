<?php
/**
 * Zira project.
 * settings.php
 * (c)2016 http://dro1d.ru
 */

namespace Oauth\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class Settings extends Form
{
    protected $_id = 'oauth-settings-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';
    protected $_input_offset_wrap_class = 'col-sm-offset-4 col-sm-8';
    protected $_select_wrapper_class = 'col-sm-8';

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

        $html .= $this->input(Locale::tm('Facebook App ID','oauth'), 'oauth_fb_app_id');
        $html .= $this->input(Locale::tm('Facebook App Secret','oauth'), 'oauth_fb_app_secret');
        $html .= $this->checkbox(Locale::tm('Enable Facebook authentication','oauth'), 'oauth_fb_on', null, false);
        $html .= $this->input(Locale::tm('Facebook Page URL','oauth'), 'oauth_fb_page_url');

        $html .= $this->input(Locale::tm('Vkontakte App ID','oauth'), 'oauth_vk_app_id');
        $html .= $this->input(Locale::tm('Vkontakte App Secret','oauth'), 'oauth_vk_app_secret');
        $html .= $this->checkbox(Locale::tm('Enable Vkontakte authentication','oauth'), 'oauth_vk_on', null, false);
        $html .= $this->input(Locale::tm('Vkontakte Group ID','oauth'), 'oauth_vk_group_id');
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $validator->registerString('oauth_fb_app_id',null,255,false,Locale::t('Invalid value "%s"',Locale::tm('Facebook App ID','oauth')));
        $validator->registerNoTags('oauth_fb_app_id',Locale::t('Invalid value "%s"',Locale::tm('Facebook App ID','oauth')));
        $validator->registerString('oauth_fb_app_secret',null,255,false,Locale::t('Invalid value "%s"',Locale::tm('Facebook App Secret','oauth')));
        $validator->registerNoTags('oauth_fb_app_secret',Locale::t('Invalid value "%s"',Locale::tm('Facebook App Secret','oauth')));
        $validator->registerString('oauth_fb_page_url',null,255,false,Locale::t('Invalid value "%s"',Locale::tm('Facebook Page URL','oauth')));
        $validator->registerNoTags('oauth_fb_page_url',Locale::t('Invalid value "%s"',Locale::tm('Facebook Page URL','oauth')));

        $validator->registerString('oauth_vk_app_id',null,255,false,Locale::t('Invalid value "%s"',Locale::tm('Vkontakte App ID','oauth')));
        $validator->registerNoTags('oauth_vk_app_id',Locale::t('Invalid value "%s"',Locale::tm('Vkontakte App ID','oauth')));
        $validator->registerString('oauth_vk_app_secret',null,255,false,Locale::t('Invalid value "%s"',Locale::tm('Vkontakte App Secret','oauth')));
        $validator->registerNoTags('oauth_vk_app_secret',Locale::t('Invalid value "%s"',Locale::tm('Vkontakte App Secret','oauth')));
        $validator->registerString('oauth_vk_group_id',null,255,false,Locale::t('Invalid value "%s"',Locale::tm('Vkontakte Group ID','oauth')));
        $validator->registerNoTags('oauth_vk_group_id',Locale::t('Invalid value "%s"',Locale::tm('Vkontakte Group ID','oauth')));

    }
}