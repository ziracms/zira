<?php
/**
 * Zira project.
 * user.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira\Controllers;

use Zira;

class User extends Zira\Controller {
    public function _before() {
        parent::_before();
    }

    public function index($id) {
        $id = intval($id);
        if (!Zira\Config::get(Zira\User::CONFIG_ALLOW_VIEW_PROFILE, true) &&
            !Zira\User::isAuthorized()
        ) {
            if ($id>0) {
                Zira\Response::redirect('user/login'.'?redirect=user/'.$id);
            } else {
                Zira\Response::redirect('user/login');
            }
            return;
        }
        if (!$id) {
            $category = Zira\Category::current();
            if ($category && $category->name==Zira\Router::getController()) {
                Zira\Content\Category::content();
            } else if (Zira\User::isAuthorized()) {
                Zira\Response::redirect('user/profile');
            } else {
                Zira\Response::redirect('user/login');
            }
            return;
        }
        $user = Zira\Models\User::findUser($id);
        if (!$user || !$user->active) {
            Zira\Response::notFound();
            return;
        }

        Zira\Page::addTitle(Zira\Locale::t('User profile'));
        Zira\Page::addTitle(Zira\User::getProfileName($user));
        Zira\Page::addBreadcrumb(Zira\User::getProfileUrlPath($user),Zira\Locale::t('User profile'));

        Zira\View::addLightbox();
        Zira\View::render(array(
            'id' => $user->id,
            'title' => Zira\User::getProfileName($user),
            'email' => Zira\User::getProfileEmail($user),
            'phone' => Zira\User::getProfilePhone($user),
            'photo' => Zira\User::getProfilePhoto($user),
            'thumb' => Zira\User::getProfilePhotoThumb($user),
            'location' => Zira\User::getProfileLocation($user),
            'dob' => Zira\User::getProfileDob($user),
            'group' => Zira\User::getProfileGroup($user),
            'date_created' => Zira\User::getProfileSignupDate($user),
            'date_logged' => Zira\User::getProfileLoginDate($user),
            'comments' => Zira\User::getProfileComments($user),
            'is_owner' => Zira\User::isSelf($user),
            'verified' => Zira\User::isProfileVerified($user),
            'user' => $user
        ),'zira/user/profile');
    }

    public function signup() {
        if (!Zira\Config::get(Zira\User::CONFIG_ALLOW_SIGNUP, true)) {
            Zira\Response::forbidden();
            return;
        }
        if (Zira\User::isAuthorized()) {
            Zira\Response::redirect('user/profile');
        }
        $form = new Zira\Forms\User\Register();

        if (Zira\Request::isPost() && $form->isValid()) {
            $user = new Zira\Models\User();
            $user->firstname = $form->getValue('firstname');
            $user->secondname = $form->getValue('secondname');
            $user->email = $form->getValue('email');
            $user->username = $form->getValue('username');
            $user->password = Zira\User::generatePasswordHash($form->getValue('password'));
            $user->group_id = Zira\User::GROUP_USER;
            $user->date_created = date('Y-m-d H:i:s');
            $user->date_logged = date('Y-m-d H:i:s');
            $user->verified = Zira\Models\User::STATUS_NOT_VERIFIED;
            $user->active = Zira\Models\User::STATUS_ACTIVE;
            $vcode = Zira\User::generateEmailConfirmationCode();
            $user->vcode = Zira\User::getHashedConfirmationCode($vcode);
            $user->code = Zira\User::generateRememberCode($user->username, $user->email);

            try {
                $user->save();
                if (Zira\Config::get(Zira\User::CONFIG_VERIFY_EMAIL, true)) {
                    Zira\User::sendConfirmEmail($user->email, Zira\User::getProfileName($user), $vcode);
                    Zira\User::rememberConfirmEmail($user->email);
                    Zira\Response::redirect('user/confirm');
                } else {
                    Zira\Response::redirect('user/login');
                }
            } catch(\Exception $e) {
                Zira::getInstance()->exception($e);
                $form->setError($e->getMessage());
            }
        }

        Zira\Page::addTitle(Zira\Locale::t('User Signup'));
        Zira\Page::addBreadcrumb('user/signup',Zira\Locale::t('Sign Up'));

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_CONTENT=>$form
        ));
    }

    public function login() {
        if (Zira\User::isAuthorized()) {
            Zira\Response::redirect('user/profile');
        }
        $form = new Zira\Forms\User\Login();

        $email = Zira\User::getRememberedConfirmEmail();
        if (!empty($email)) {
            $form->setValues(array('login'=>$email));
        }

        if (Zira\Request::isPost() && $form->isValid()) {
            if (Zira\User::isAllowedToLogin()) {
                Zira\User::onUserLogin($form->getValue('rememberme'));
                $redirect = Zira\Request::get('redirect');
                if (!empty($redirect) && strpos($redirect,'//')===false && strpos($redirect, '.')===false) {
                    if ($redirect=='dash') Zira\Helper::setAddingLanguageToUrl(false);
                    Zira\Response::redirect($redirect);
                } else {
                    Zira\Response::redirect('user/profile');
                }
            } else {
                Zira\Response::redirect('user/confirm');
            }
        }

        Zira\Page::addTitle(Zira\Locale::t('Sign In'));
        Zira\Page::addBreadcrumb('user/login',Zira\Locale::t('Sign In'));

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_CONTENT=>$form
        ));
    }

    public function logout() {
        if (Zira\User::isAuthorized()) {
            Zira\User::forgetAuthorizedUserId();
            Zira\User::forgetUser();
            Zira\User::onUserLogout();
        }
        Zira\Response::redirect('user/login');
    }

    public function confirm() {
        if (Zira\User::isVerified()) {
            Zira\Response::redirect('user/profile');
            return;
        }
        $form = new Zira\Forms\User\Confirm();

        if (!Zira\User::isAuthorized()) {
            $email = Zira\User::getRememberedConfirmEmail();
            if (!empty($email)) {
                $form->setValues(array('login'=>$email));
            }
        }

        if (Zira\Request::isPost() && $form->isValid()) {
            if (!Zira\User::isAuthorized()) {
                $current = Zira\User::getCurrent();
                $user = new Zira\Models\User($current->id);
            } else {
                $user = Zira\User::getCurrent();
            }
            $user->verified = Zira\Models\User::STATUS_VERIFIED;
            $user->vcode = '';

            try {
                $user->save();
                if (!Zira\User::isAuthorized()) {
                    Zira\Response::redirect('user/login');
                } else {
                    Zira\Response::redirect('user/profile');
                }
            } catch(\Exception $e) {
                Zira::getInstance()->exception($e);
                $form->setError($e->getMessage());
            }
        }

        Zira\Page::addTitle(Zira\Locale::t('Email confirmation'));
        if (Zira\User::isAuthorized()) {
            Zira\Page::addBreadcrumb('user/profile',Zira\Locale::t('Profile'));
        }
        Zira\Page::addBreadcrumb('user/confirm',Zira\Locale::t('Verification'));

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_CONTENT=>$form
        ));
    }

    public function send() {
        if (Zira\User::isVerified()) {
            Zira\Response::redirect('user/profile');
            return;
        }
        $form = new Zira\Forms\User\Send();

        if (!Zira\User::isAuthorized()) {
            $email = Zira\User::getRememberedConfirmEmail();
            if (!empty($email)) {
                $form->setValues(array('login'=>$email));
            }
        } else {
            $current = Zira\User::getCurrent();
            $form->setValues(array('login'=>$current->email));
        }

        if (Zira\Request::isPost() && $form->isValid()) {
            if (!Zira\User::isAuthorized()) {
                $current = Zira\User::getCurrent();
                $user = new Zira\Models\User($current->id);
            } else {
                $user = Zira\User::getCurrent();
            }
            $vcode = Zira\User::generateEmailConfirmationCode();
            $user->vcode = Zira\User::getHashedConfirmationCode($vcode);

            try {
                $user->save();
                Zira\User::sendConfirmEmail($user->email, Zira\User::getProfileName($user), $vcode);
                Zira\Response::redirect('user/confirm');
            } catch(\Exception $e) {
                Zira::getInstance()->exception($e);
                $form->setError($e->getMessage());
            }
        }

        Zira\Page::addTitle(Zira\Locale::t('Send confirmation code'));
        if (Zira\User::isAuthorized()) {
            Zira\Page::addBreadcrumb('user/profile',Zira\Locale::t('Profile'));
        }
        Zira\Page::addBreadcrumb('user/send',Zira\Locale::t('Verification'));

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_CONTENT=>$form
        ));
    }

    public function recover() {
        if (Zira\User::isAuthorized()) {
            Zira\Response::redirect('user/profile');
        }
        $form = new Zira\Forms\User\Recover();

        $email = Zira\User::getRememberedConfirmEmail();
        if (!empty($email)) {
            $form->setValues(array('login'=>$email));
        }

        if (Zira\Request::isPost() && $form->isValid()) {
            $current = Zira\User::getCurrent();
            $user = new Zira\Models\User($current->id);
            $vcode = Zira\User::generatePasswordRecoveryCode();
            $user->vcode = Zira\User::getHashedPasswordRecoveryCode($vcode);

            try {
                $user->save();
                Zira\User::sendRecoverEmail($user->email, Zira\User::getProfileName($user), $vcode);
                Zira\User::rememberConfirmEmail($user->email);
                Zira\Response::redirect('user/password');
            } catch(\Exception $e) {
                Zira::getInstance()->exception($e);
                $form->setError($e->getMessage());
            }
        }

        Zira\Page::addTitle(Zira\Locale::t('Password recovery'));
        Zira\Page::addBreadcrumb('user/recover',Zira\Locale::t('Recovery'));

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_CONTENT=>$form
        ));
    }

    public function password() {
        if (Zira\User::isAuthorized()) {
            Zira\Response::redirect('user/pwd');
        }
        $form = new Zira\Forms\User\Password();

        $email = Zira\User::getRememberedConfirmEmail();
        if (!empty($email)) {
            $form->setValues(array('login'=>$email));
        }

        if (Zira\Request::isPost() && $form->isValid()) {
            $current = Zira\User::getCurrent();
            $user = new Zira\Models\User($current->id);
            $user->vcode = '';
            $password = Zira\User::generateUserToken();
            $user->password = Zira\User::getHashedUserToken($password);

            try {
                $user->save();
                Zira\User::sendPasswordEmail($user->email, Zira\User::getProfileName($user), $password);
                Zira\Response::redirect('user/login');
            } catch(\Exception $e) {
                Zira::getInstance()->exception($e);
                $form->setError($e->getMessage());
            }
        }

        Zira\Page::addTitle(Zira\Locale::t('Password recovery'));
        Zira\Page::addBreadcrumb('user/password',Zira\Locale::t('Recovery'));

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_CONTENT=>$form
        ));
    }

    public function pwd() {
        if (!Zira\User::isAuthorized()) {
            Zira\Response::redirect('user/recover');
        }
        $form = new Zira\Forms\User\Pwd();

        if (Zira\Request::isPost() && $form->isValid()) {
            $user = Zira\User::getCurrent();
            $password = $form->getValue('password');
            $user->password = Zira\User::getHashedUserToken($password);

            try {
                $user->save();
                $form->setMessage(Zira\Locale::t('Successfully saved'));
            } catch(\Exception $e) {
                Zira::getInstance()->exception($e);
                $form->setError($e->getMessage());
            }
        }

        Zira\Page::addTitle(Zira\Locale::t('Change password'));
        Zira\Page::addBreadcrumb('user/profile',Zira\Locale::t('Profile'));
        Zira\Page::addBreadcrumb('user/pwd',Zira\Locale::t('Password'));

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_CONTENT=>$form
        ));
    }

    public function email() {
        if (!Zira\User::isAuthorized()) {
            Zira\Response::redirect('user/login');
        }
        $form = new Zira\Forms\User\Email();

        $user = Zira\User::getCurrent();

        if (Zira\Request::isPost() && $form->isValid()) {
            $e_updated = false;
            if ($user->email != $form->getValue('email')) {
                $user->email = $form->getValue('email');
                $user->verified = Zira\Models\User::STATUS_NOT_VERIFIED;
                $vcode = Zira\User::generateEmailConfirmationCode();
                $user->vcode = Zira\User::getHashedConfirmationCode($vcode);
                $e_updated = true;
            }
            $s_updated = false;
            $subscribed = $form->getValue('subscribed') ? Zira\Models\User::STATUS_SUBSCRIBED : Zira\Models\User::STATUS_NOT_SUBSCRIBED;
            if ($user->subscribed != $subscribed) {
                $user->subscribed = $subscribed;
                $s_updated = true;
            }
            try {
                if ($e_updated || $s_updated) {
                    $user->save();
                }
                if ($e_updated) {
                    Zira\User::sendConfirmEmail($user->email, Zira\User::getProfileName($user), $vcode);
                }
                Zira\Response::redirect('user/confirm');
            } catch(\Exception $e) {
                Zira::getInstance()->exception($e);
                $form->setError($e->getMessage());
            }
        } else {
            $form->setValues(array(
                'email'=> $user->email,
                'subscribed'=>$user->subscribed
            ));
        }

        Zira\Page::addTitle(Zira\Locale::t('Change email'));
        Zira\Page::addBreadcrumb('user/profile',Zira\Locale::t('Profile'));
        Zira\Page::addBreadcrumb('user/email',Zira\Locale::t('Email'));

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_CONTENT=>$form
        ));
    }

    public function name() {
        if (!Zira\Config::get(Zira\User::CONFIG_ALLOW_LOGIN_CHANGE, true)) {
            Zira\Response::forbidden();
            return;
        }
        if (!Zira\User::isAuthorized()) {
            Zira\Response::redirect('user/login');
        }
        $form = new Zira\Forms\User\Name();

        $user = Zira\User::getCurrent();
        $form->setValue('login', $user->username);

        if (Zira\Request::isPost() && $form->isValid()) {
            $user->username = $form->getValue('login');

            try {
                $user->save();
                $form->setMessage(Zira\Locale::t('Successfully saved'));
            } catch(\Exception $e) {
                Zira::getInstance()->exception($e);
                $form->setError($e->getMessage());
            }
        }

        Zira\Page::addTitle(Zira\Locale::t('Change username'));
        Zira\Page::addBreadcrumb('user/profile',Zira\Locale::t('Profile'));
        Zira\Page::addBreadcrumb('user/name',Zira\Locale::t('Username'));

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_CONTENT=>$form
        ));
    }

    public function edit() {
        if (!Zira\User::isAuthorized()) {
            Zira\Response::redirect('user/login');
        }
        $form = new Zira\Forms\User\Edit();

        $user = Zira\User::getCurrent();

        $form->setValues(array(
            'firstname' => $user->firstname,
            'secondname' => $user->secondname,
            'country' => $user->country,
            'city' => $user->city,
            'street' => $user->address,
            'phone' => $user->phone,
            'dob' => $user->dob ? $form->prepareDatepickerDate($user->dob) : ''
        ));

        if (Zira\Request::isPost() && $form->isValid()) {
            $user->firstname = $form->getValue('firstname');
            $user->secondname = $form->getValue('secondname');
            $user->country = $form->getValue('country');
            $user->city = $form->getValue('city');
            $user->address = $form->getValue('street');
            $user->phone = $form->getValue('phone');
            $dob = $form->getValue('dob');
            if (!empty($dob)) $user->dob = $form->parseDatepickerDate($dob);
            else $user->dob = null;
            try {
                $user->save();
                $form->setMessage(Zira\Locale::t('Successfully saved'));
            } catch(\Exception $e) {
                Zira::getInstance()->exception($e);
                $form->setError($e->getMessage());
            }
        }

        Zira\Page::addTitle(Zira\Locale::t('Change profile'));
        Zira\Page::addBreadcrumb('user/profile',Zira\Locale::t('Profile'));
        Zira\Page::addBreadcrumb('user/edit',Zira\Locale::t('Information'));

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_CONTENT=>$form
        ));
    }

    public function photo() {
        if (!Zira\User::isAuthorized()) {
            Zira\Response::redirect('user/login');
        }
        $form = new Zira\Forms\User\Photo();

        $user = Zira\User::getCurrent();

        if (Zira\Request::isPost() && $form->isValid()) {
            try {
                $image = Zira\User::savePhoto($user, $form->getValue('photo'));
                if (!$image) {
                    $form->setError(Zira\Locale::t('An error occurred'));
                } else {
                    $user->image = $image;
                    $user->save();
                    Zira\Response::redirect('user/avatar');
                }
            } catch(\Exception $e) {
                Zira::getInstance()->exception($e);
                $form->setError($e->getMessage());
            }
        }

        Zira\Page::addTitle(Zira\Locale::t('Change photo'));
        Zira\Page::addBreadcrumb('user/profile',Zira\Locale::t('Profile'));
        Zira\Page::addBreadcrumb('user/photo',Zira\Locale::t('Photo'));

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_CONTENT=>$form
        ));
    }

    public function avatar() {
        if (!Zira\User::isAuthorized()) {
            Zira\Response::redirect('user/login');
        }

        $user = Zira\User::getCurrent();
        if (!$user->image) {
            Zira\Response::redirect('user/photo');
        }

        $form = new Zira\Forms\User\Avatar();

        $form->setValue('image', $user->image);

        if (Zira\Request::isPost() && $form->isValid()) {
            $width = floatval($form->getValue('cropper_w'));
            $height = floatval($form->getValue('cropper_h'));
            $left = floatval($form->getValue('cropper_x'));
            $top = floatval($form->getValue('cropper_y'));

            try {
                $image = Zira\User::saveAvatar($user, $width, $height, $left, $top);
                if (!$image) {
                    $form->setError(Zira\Locale::t('An error occurred'));
                } else {
                    $user->image = $image;
                    $user->save();
                    Zira\Response::redirect('user/profile');
                }
            } catch(\Exception $e) {
                Zira::getInstance()->exception($e);
                $form->setError($e->getMessage());
            }
        }

        Zira\Page::addTitle(Zira\Locale::t('Change avatar'));
        Zira\Page::addBreadcrumb('user/profile',Zira\Locale::t('Profile'));
        Zira\Page::addBreadcrumb('user/avatar',Zira\Locale::t('Avatar'));

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_CONTENT=>$form
        ));
    }

    public function nophoto() {
        if (!Zira\User::isAuthorized()) {
            Zira\Response::redirect('user/login');
        }

        $user = Zira\User::getCurrent();
        if (!$user->image) {
            Zira\Response::redirect('user/profile');
        }

        $form = new Zira\Forms\User\Nophoto();

        if (Zira\Request::isPost() && $form->isValid()) {
            try {
                Zira\User::deletePhoto($user);
                $user->image = '';
                $user->save();
                Zira\Response::redirect('user/profile');
            } catch(\Exception $e) {
                Zira::getInstance()->exception($e);
                $form->setError($e->getMessage());
            }
        }

        Zira\Page::addTitle(Zira\Locale::t('Remove photo'));
        Zira\Page::addBreadcrumb('user/profile',Zira\Locale::t('Profile'));
        Zira\Page::addBreadcrumb('user/nophoto',Zira\Locale::t('Photo'));

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_CONTENT=>$form
        ));
    }

    public function profile() {
        if (!Zira\User::isAuthorized()) {
            Zira\Response::redirect('user/login');
        }

        Zira\Page::addTitle(Zira\Locale::t('User profile'));
        Zira\Page::addTitle(Zira\User::getProfileName());
        Zira\Page::addBreadcrumb('user/profile',Zira\Locale::t('Profile'));

        Zira\View::addLightbox();
        Zira\View::render(array(
            'title' => Zira\User::getProfileName(),
            'email' => Zira\User::getProfileEmail(),
            'phone' => Zira\User::getProfilePhone(),
            'photo' => Zira\User::getProfilePhoto(),
            'thumb' => Zira\User::getProfilePhotoThumb(),
            'location' => Zira\User::getProfileLocation(),
            'dob' => Zira\User::getProfileDob(),
            'group' => Zira\User::getProfileGroup(),
            'date_created' => Zira\User::getProfileSignupDate(),
            'date_logged' => Zira\User::getProfileLoginDate(),
            'comments' => Zira\User::getProfileComments(),
            'is_owner' => true,
            'verified' => Zira\User::isVerified(),
            'user' => Zira\User::getCurrent()
        ));
    }

    public function message($recipient_id) {
        if (!Zira\User::isAuthorized()) {
            Zira\Response::redirect('user/login');
        }

        if (empty($recipient_id)) {
            Zira\Response::notFound();
        }
        if ($recipient_id == Zira\User::getCurrent()->id) {
            Zira\Response::forbidden();
        }
        $recipient = new Zira\Models\User($recipient_id);
        if (!$recipient->loaded() || !$recipient->active) {
            Zira\Response::notFound();
        }

        $form = new Zira\Forms\User\Conversation($recipient);

        if (Zira\Request::isPost() && $form->isValid()) {
            try {
                if (!Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) &&
                    ($blocked = Zira\User::isCurrentBlocked($recipient->id, true))
                ) {
                    $form->setError(Zira\Locale::t('Sorry, you were added to black list. Reason: %s', Zira\Helper::html($blocked->message)));
                } else {
                    $conversation_id = Zira\Models\Conversation::createConversation(Zira\User::getCurrent()->id, $recipient->id, $form->getValue('subject'));
                    if ($conversation_id) {
                        $message = new Zira\Models\Message();
                        $message->conversation_id = $conversation_id;
                        $message->user_id = Zira\User::getCurrent()->id;
                        $message->content = Zira\Helper::utf8Entity(html_entity_decode($form->getValue('content')));
                        $message->creation_date = date('Y-m-d H:i:s');
                        $message->save();
                    }
                    Zira\User::increaseMessagesCount($recipient);
                    try {
                        Zira\Models\Message::notify($recipient, Zira\User::getCurrent());
                    } catch (\Exception $e) {
                        Zira\Log::exception($e);
                    }
                    $form->setMessage(Zira\Locale::t('Message sent'));
                    $form->setFill(false);
                }
            } catch(\Exception $e) {
                Zira::getInstance()->exception($e);
                $form->setError($e->getMessage());
            }
        }

        Zira\Page::addTitle(Zira\Locale::t('New message'));
        Zira\Page::addBreadcrumb('user/profile',Zira\Locale::t('Profile'));
        Zira\Page::addBreadcrumb('user/messages',Zira\Locale::t('Messages'));

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_CONTENT=>$form
        ));
    }

    public function compose() {
        if (!Zira\User::isAuthorized()) {
            Zira\Response::redirect('user/login');
        }

        $form = new Zira\Forms\User\Compose();

        if (Zira\Request::isPost() && $form->isValid()) {
            try {
                $users = $form->getUsers();
                $recipients = array();
                foreach($users as $recipient) {
                    if (!Zira\Permission::check(Zira\Permission::TO_ACCESS_DASHBOARD) &&
                        ($blocked = Zira\User::isCurrentBlocked($recipient->id, true))
                    ) {
                        $form->setError(Zira\Locale::t('Sorry, you are in %s\'s black list', Zira\User::getProfileName($recipient)));
                        $recipients = array();
                        break;
                    } else {
                        $recipients []= $recipient->id;
                    }
                }
                if (!empty($recipients)) {
                    $conversation_id = Zira\Models\Conversation::createGroupConversation(Zira\User::getCurrent()->id, $recipients, $form->getValue('subject'));
                    if ($conversation_id) {
                        $message = new Zira\Models\Message();
                        $message->conversation_id = $conversation_id;
                        $message->user_id = Zira\User::getCurrent()->id;
                        $message->content = Zira\Helper::utf8Entity(html_entity_decode($form->getValue('content')));
                        $message->creation_date = date('Y-m-d H:i:s');
                        $message->save();

                        foreach($users as $recipient) {
                            Zira\User::increaseMessagesCount($recipient);
                            try {
                                Zira\Models\Message::notify($recipient, Zira\User::getCurrent());
                            } catch (\Exception $e) {
                                Zira\Log::exception($e);
                            }
                        }
                        $form->setMessage(Zira\Locale::t('Message sent'));
                        $form->setFill(false);
                    }
                }
            } catch(\Exception $e) {
                Zira::getInstance()->exception($e);
                $form->setError($e->getMessage());
            }
        }

        Zira\Page::addTitle(Zira\Locale::t('New message'));
        Zira\Page::addBreadcrumb('user/profile',Zira\Locale::t('Profile'));
        Zira\Page::addBreadcrumb('user/messages',Zira\Locale::t('Messages'));

        Zira\Page::render(array(
            Zira\Page::VIEW_PLACEHOLDER_CONTENT=>$form
        ));
    }

    public function messages($conversation_id) {
        if (!Zira\User::isAuthorized()) {
            Zira\Response::redirect('user/login');
        }

        Zira\Page::addTitle(Zira\Locale::t('Messages'));
        Zira\Page::addBreadcrumb('user/profile',Zira\Locale::t('Profile'));
        Zira\Page::addBreadcrumb('user/messages',Zira\Locale::t('Messages'));

        $limit = 10;
        if (empty($conversation_id)) {
            $total = Zira\Models\Conversation::getCollection()
                                ->count()
                                ->where('user_id','=',Zira\User::getCurrent()->id)
                                ->get('co');

            $page = (int)Zira\Request::get('page');
            $pages = ceil($total/$limit);
            if ($page>$pages) $page = $pages;
            if ($page<1) $page = 1;

            $rows = Zira\Models\Conversation::getCollection()
                                ->where('user_id','=',Zira\User::getCurrent()->id)
                                ->order_by('modified_date','desc')
                                ->limit($limit, $limit * ($page-1))
                                ->get();

            $pagination = new Zira\Pagination();
            $pagination->setLimit($limit);
            $pagination->setTotal($total);
            $pagination->setPages($pages);
            $pagination->setPage($page);

            Zira\Page::setView('zira/user/conversations');
            Zira\Page::render(array(
                'items'=>$rows,
                'pagination'=>$pagination
            ));
        } else {
            $_conversation = Zira\Models\Conversation::getCollection()
                                ->where('conversation_id','=',$conversation_id)
                                ->and_where('user_id','=',Zira\User::getCurrent()->id)
                                ->get(0, true);
            if (!$_conversation) {
                Zira\Response::forbidden();
            }

            $conversation = new Zira\Models\Conversation();
            $conversation->loadFromArray($_conversation);
            if ($conversation->highlight) {
                $conversation->highlight = 0;
                $conversation->save();
                Zira\User::decreaseMessagesCount();
            }

            $form = new Zira\Forms\User\Message();

            if (Zira\Request::isPost() && $form->isValid()) {
                try {
                    $message = new Zira\Models\Message();
                    $message->conversation_id = $conversation_id;
                    $message->user_id = Zira\User::getCurrent()->id;
                    $message->content = Zira\Helper::utf8Entity(html_entity_decode($form->getValue('content')));
                    $message->creation_date = date('Y-m-d H:i:s');
                    $message->save();

                    $user_conversations = Zira\Models\Conversation::getCollection()
                                ->where('conversation_id','=',$conversation_id)
                                ->get(null, true);

                    foreach($user_conversations as $user_conversation) {
                        if ($user_conversation['highlight']) continue;
                        $_user_conversation = new Zira\Models\Conversation();
                        $_user_conversation->loadFromArray($user_conversation);
                        if ($_user_conversation->user_id!=Zira\User::getCurrent()->id) {
                            $_user_conversation->highlight = 1;
                            $recipient = new Zira\Models\User($_user_conversation->user_id);
                            if (!$recipient->loaded()) continue;
                            Zira\User::increaseMessagesCount($recipient);
                            try {
                                Zira\Models\Message::notify($recipient, Zira\User::getCurrent());
                            } catch(\Exception $e) {
                                Zira\Log::exception($e);
                            }
                        }
                        $_user_conversation->modified_date = date('Y-m-d H:i:s');
                        $_user_conversation->save();
                    }

                    $form->setFill(false);

                    if (Zira\View::isAjax()) {
                        $form->setMessage(Zira\Locale::t('Message sent'));
                    }
                } catch(\Exception $e) {
                    Zira::getInstance()->exception($e);
                    $form->setError($e->getMessage());
                }
            }

            if (Zira\Request::isPost() && Zira\View::isAjax()) {
                Zira\Page::render(array(
                    Zira\Page::VIEW_PLACEHOLDER_CONTENT => $form
                ));
                return;
            }

            $total = Zira\Models\Message::getCollection()
                                ->count()
                                ->where('conversation_id','=',$conversation_id)
                                ->get('co');

            $page = (int)Zira\Request::get('page');
            $pages = ceil($total/$limit);
            if ($page>$pages) $page = $pages;
            if ($page<1) $page = 1;

            $rows = Zira\Models\Message::getCollection()
                                ->select('id', 'user_id', 'content', 'creation_date')
                                ->where('conversation_id','=',$conversation_id)
                                ->left_join(Zira\Models\User::getClass(), array('username', 'firstname', 'secondname', 'image'))
                                ->order_by('id','desc')
                                ->limit($limit, $limit * ($page-1))
                                ->get();

            $users = Zira\Models\Conversation::getCollection()
                                ->where('conversation_id','=',$conversation_id)
                                ->and_where('user_id','<>',Zira\User::getCurrent()->id)
                                ->join(Zira\Models\User::getClass(),array('id','username','firstname','secondname','image'))
                                ->get();

            $pagination = new Zira\Pagination();
            $pagination->setLimit($limit);
            $pagination->setTotal($total);
            $pagination->setPages($pages);
            $pagination->setPage($page);

            Zira\View::addParser();
            Zira\Page::setView('zira/user/messages');
            Zira\Page::render(array(
                'conversation' => $conversation,
                'users' => $users,
                'items'=>$rows,
                'pagination'=>$pagination,
                'form'=>$form
            ));
        }
    }

    public function ajax() {
        Zira\View::setAjax(true);
        if (!Zira\User::isAuthorized()) return;
        $response = array();
        if (Zira\Request::isPost() && Zira\User::checkToken(Zira\Request::post('token'))) {
            $action = Zira\Request::post('action');
            if ($action == 'conversation-mark-read') {
                $items = Zira\Request::post('items');
                if (!empty($items) && is_array($items)) {
                    $updated = array();
                    foreach($items as $item) {
                        $conversation = new Zira\Models\Conversation($item);
                        if (!$conversation->loaded()) continue;
                        if ($conversation->user_id != Zira\User::getCurrent()->id) continue;
                        if (!$conversation->highlight) continue;
                        $conversation->highlight = 0;
                        $conversation->save();
                        Zira\User::getCurrent()->messages--;
                        $updated []= $conversation->id;
                    }
                    if (Zira\User::getCurrent()->messages<0) Zira\User::getCurrent()->messages = 0;
                    Zira\User::getCurrent()->save();
                    $response['items'] = $updated;
                }
            } else if ($action == 'conversation-mark-all-read') {
                Zira\Models\Conversation::getCollection()
                        ->update(array('highlight'=>0))
                        ->where('user_id','=',Zira\User::getCurrent()->id)
                        ->execute();
                Zira\User::getCurrent()->messages = 0;
                Zira\User::getCurrent()->save();
            } else if ($action == 'conversation-delete') {
                $items = Zira\Request::post('items');
                if (!empty($items) && is_array($items)) {
                    $updated = array();
                    foreach($items as $item) {
                        $conversation = new Zira\Models\Conversation($item);
                        if (!$conversation->loaded()) continue;
                        if ($conversation->user_id != Zira\User::getCurrent()->id) continue;
                        $conversation->delete();
                        if ($conversation->highlight) {
                            Zira\User::getCurrent()->messages--;
                        }
                        $updated []= $conversation->id;
                    }
                    if (Zira\User::getCurrent()->messages<0) Zira\User::getCurrent()->messages = 0;
                    Zira\User::getCurrent()->save();
                    $response['items'] = $updated;
                }
            } else if ($action == 'black-list') {
                $user_id = Zira\Request::post('user_id');
                $user = new Zira\Models\User($user_id);
                if ($user->loaded() && $user->active && !Zira\User::isSelf($user)) {
                    $row = Zira\User::isUserBlocked($user_id, true);
                    if (!$row) {
                        $blocked = new Zira\Models\Blacklist();
                        $blocked->user_id = Zira\User::getCurrent()->id;
                        $blocked->blocked_user_id = $user_id;
                        $blocked->message = Zira\Request::post('message');
                        $blocked->creation_date = date('Y-m-d H:i:s');
                        $blocked->save();
                    } else {
                        Zira\Models\Blacklist::getCollection()
                            ->delete()
                            ->where('id', '=', $row->id)
                            ->execute();
                    }
                    $response['success'] = 1;
                } else {
                    $response['success'] = 0;
                }
            }
        } else {
            $response['error'] = Zira\Locale::t('Invalid token');
        }
        Zira\Page::render($response);
    }

    public function autocomplete() {
        Zira\View::setAjax(true);
        if (!Zira\User::isAuthorized()) return;
        $response = array();
        if (Zira\Request::isPost() && Zira\User::checkToken(Zira\Request::post('token'))) {
            $items = array();
            $text = Zira\Request::post('text');
            $text = trim(preg_replace('/[\x20]+/',' ', $text));
            if (strlen($text)>0) {
                $rows = false;
                if (is_numeric($text)) {
                    $user = new Zira\Models\User($text);
                    if ($user->loaded() && $user->active) {
                        $rows = array($user->toArray());
                    }
                } else if (strpos($text, ' ')>0) {
                    $parts = explode(' ', $text);
                    if (count($parts)==2) {
                        $rows = Zira\Models\User::getCollection()
                            ->where('firstname', 'like', $parts[0].'%')
                            ->and_where('secondname', 'like', $parts[1].'%')
                            ->and_where('active', '=', Zira\Models\User::STATUS_ACTIVE)
                            ->order_by('id','asc')
                            ->limit(10)
                            ->get(null, true);
                    }
                } else {
                    $rows = Zira\Models\User::getCollection()
                                ->open_where()
                                ->where('username','like',$text.'%')
                                ->or_where('firstname','like',$text.'%')
                                ->or_where('secondname','like',$text.'%')
                                ->close_where()
                                ->and_where('active','=',Zira\Models\User::STATUS_ACTIVE)
                                ->order_by('id','asc')
                                ->limit(10)
                                ->get(null, true);
                }

                if ($rows) {
                    foreach($rows as $row) {
                        $user = new Zira\Models\User();
                        $user->loadFromArray($row);
                        if (Zira\User::isSelf($user)) continue;
                        $items[$user->id] = Zira\User::getProfileName($user);
                    }
                }
            }
            $response['items'] = $items;
        } else {
            $response['error'] = Zira\Locale::t('Invalid token');
        }
        Zira\Page::render($response);
    }
}