<?php
/**
 * Zira project.
 * user.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Dash\Forms;

use Zira;
use Zira\Form;
use Zira\Locale;

class User extends Form
{
    protected $_id = 'dash-user-form';

    protected $_label_class = 'col-sm-4 control-label';
    protected $_input_wrap_class = 'col-sm-8';

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
        $id = (int)$this->getValue('id');

        $html = $this->open();
        $html .= $this->hidden('id');
        $html .= $this->hidden('image', array('class'=>'form-control dashwindow-user-image'));
        $html .= $this->selectDropdown(Locale::t('Group') . '*', 'group_id', Zira\Models\Group::getArray());
        $html .= $this->input(Locale::t('Username') . '*', 'username');
        $html .= $this->input(Locale::t('Email') . '*', 'email');
        if (empty($id)) {
            $html .= $this->password(Locale::t('Password') . '*', 'password');
        } else {
            $html .= $this->password(Locale::t('Password'), 'password', array('placeholder' => Locale::t('hidden')));
        }
        $html .= $this->input(Locale::t('First name').'*','firstname');
        $html .= $this->input(Locale::t('Second name').'*','secondname');
        $html .= $this->input(Locale::t('Country'),'country');
        $html .= $this->input(Locale::t('City'),'city');
        $html .= $this->input(Locale::t('Street'),'address');
        $html .= $this->input(Locale::t('Phone'),'phone', array('placeholder'=>'+7...'));
        $html .= $this->datepicker(Locale::t('Date of birth'), 'dob', array('class'=>'form-control dash-datepicker'));
        $html .= $this->hidden('verified', array('class'=>'form-control dashwindow-user-verified'));
        $html .= $this->hidden('active', array('class'=>'form-control dashwindow-user-active'));
        $html .= $this->close();
        return $html;
    }

    protected function _validate()
    {
        $validator = $this->getValidator();
        $validator->registerCustom(array(get_class(), 'checkUser'), 'id', Locale::t('User not found'));
        $validator->registerNumber('group_id', null, null, true, Locale::t('Group not found'));
        $validator->registerCustom(array(get_class(), 'checkGroup'), 'group_id', Locale::t('Group not found'));
        $validator->registerString('username', Zira\User::LOGIN_MIN_CHARS, Zira\User::LOGIN_MAX_CHARS, true, Locale::t('Invalid username'));
        $validator->registerRegexp('username', Zira\User::REGEXP_LOGIN, Locale::t('Login must contain only letters and numbers'));
        $validator->registerCustom(array(get_class(), 'checkUsernameExists'), array('id','username'), Locale::t('Specified login already exists'));
        $validator->registerEmail('email', true, Locale::t('Invalid email'));
        $validator->registerCustom(array(get_class(), 'checkEmailExists'), array('id','email'), Locale::t('Specified email already exists'));
        $validator->registerString('firstname', 0, 0, true, Locale::t('Please enter first name'));
        $validator->registerString('secondname', 0, 0, true, Locale::t('Please enter second name'));
        $validator->registerNoTags('firstname', Locale::t('Invalid character specified'));
        $validator->registerUtf8('firstname', Locale::t('Invalid character specified'));
        $validator->registerNoTags('secondname', Locale::t('Invalid character specified'));
        $validator->registerUtf8('secondname', Locale::t('Invalid character specified'));
        $validator->registerNoTags('country', Locale::t('Invalid character specified'));
        $validator->registerUtf8('country', Locale::t('Invalid character specified'));
        $validator->registerNoTags('city', Locale::t('Invalid character specified'));
        $validator->registerUtf8('city', Locale::t('Invalid character specified'));
        $validator->registerNoTags('address', Locale::t('Invalid character specified'));
        $validator->registerUtf8('address', Locale::t('Invalid character specified'));
        $validator->registerPhone('phone', false, Locale::t('Phone should be specified in international format'));
        $validator->registerDate('dob', false, Locale::t('Invalid date format'));

        $id = (int)$this->getValue('id');
        $password = (string)$this->getValue('password');
        if (empty($id) || !empty($password)) {
            $validator->registerString('password',Zira\User::PASSWORD_MIN_CHARS,Zira\User::PASSWORD_MAX_CHARS,true,Locale::t('Invalid password'));
            $validator->registerRegexp('password', Zira\User::REGEXP_PASSWORD, Locale::t('Password contain bad characters'));
        }

        $firstname = trim((string)$this->getValue('firstname'));
        $secondname = trim((string)$this->getValue('secondname'));
        $country = trim((string)$this->getValue('country'));
        $city = trim((string)$this->getValue('city'));
        $street = trim((string)$this->getValue('address'));
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
            'address' => $street,
            'phone' => $phone
        ));
    }

    public static function checkUser($id) {
        if (empty($id)) return true;
        $user = new Zira\Models\User($id);
        if (!$user->loaded()) return false;
        return true;
    }

    public static function checkGroup($group_id) {
        $group = new Zira\Models\Group($group_id);
        if (!$group->loaded()) return false;
        return true;
    }

    public static function checkUsernameExists($id,$username) {
        if (empty($id)) $id=0;
        $co=Zira\Models\User::getCollection()
            ->count()
            ->where('username','=',$username)
            ->and_where('id','<>',$id)
            ->get('co');
        return $co==0;
    }

    public static function checkEmailExists($id,$email) {
        if (empty($id)) $id=0;
        $co=Zira\Models\User::getCollection()
            ->count()
            ->where('email','=',$email)
            ->and_where('id','<>',$id)
            ->get('co');
        return $co==0;
    }
}