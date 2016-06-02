<?php
/**
 * Zira project.
 * contact.php
 * (c)2016 http://dro1d.ru
 */

namespace Zira\Controllers;

use Zira;

class Contact extends Zira\Controller {
    /**
     * Contact page
     */
    public function index() {
        $contacts = array();

        Zira\Page::addTitle(Zira\Locale::t('Contacts'));
        Zira\Page::addBreadcrumb('contact', Zira\Locale::t('Contacts'));

        if (Zira\Config::get('feedback_email')) {
            $form = new Zira\Forms\Contact();
            if (Zira\Request::isPost() && $form->isValid()) {
                if (Zira\User::isAuthorized()) {
                    $name = Zira\User::getProfileName();
                    $email = Zira\User::getProfileEmail();
                    $replyTo = $email;
                } else {
                    $name = $form->getValue('name');
                    if (empty($name)) $name= Zira\Locale::t('not specified');
                    $email = $form->getValue('email');
                    if (empty($email)) {
                        $email = Zira\Locale::t('not specified');
                        $replyTo = null;
                    } else {
                        $replyTo = $email;
                    }
                }
                $message = $form->getValue('message');

                $message_tpl = Zira\Config::get('feedback_message');
                if (!$message_tpl || strlen(trim($message_tpl)) == 0) {
                    $message_tpl = Zira\Locale::t('Message') . ':' . "\r\n";
                    $message_tpl .= '$message' . "\r\n\r\n";
                    $message_tpl .= Zira\Locale::t('Name: %s', '$name') . "\r\n";
                    $message_tpl .= Zira\Locale::t('Email: %s', '$email') . "\r\n\r\n";
                    $message_tpl .= Zira\Locale::t('You recieved this message, because your Email address is specified as a contact email on %s', '$site');
                } else {
                    $message_tpl = Zira\Locale::t($message_tpl);
                }
                $message_tpl = str_replace('$name', $name, $message_tpl);
                $message_tpl = str_replace('$email', $email, $message_tpl);
                $message_tpl = str_replace('$message', $message, $message_tpl);
                $message_tpl = str_replace('$site', Zira\Helper::url('/', true, true), $message_tpl);

                try {
                    Zira\Mail::send(Zira\Config::get('feedback_email'), Zira\Locale::t('Feedback'), Zira\Helper::html($message_tpl), null, $replyTo);
                    $form->setMessage(Zira\Locale::t('Thank you. Your message was sent'));
                    $form->setFill(false);
                } catch (\Exception $e) {
                    $form->setError(Zira\Locale::t('Sorry, could not send your message. Try later'));
                }
            }
            if (Zira\Config::get('contact_email_public')) {
                $contacts['email'] = Zira\Config::get('feedback_email');
            }
        } else {
            $form = '';
        }

        $contact_name = Zira\Config::get('contact_name');
        if ($contact_name) $contacts['name'] = $contact_name;
        $contact_address = Zira\Config::get('contact_address');
        if ($contact_address) $contacts['address'] = $contact_address;
        $contact_image = Zira\Config::get('contact_image');
        if ($contact_image) $contacts['image'] = $contact_image;
        $contact_phone = Zira\Config::get('contact_phone');
        if ($contact_phone) $contacts['phone'] = $contact_phone;
        $contact_info = Zira\Config::get('contact_info');
        if ($contact_info) $contacts['info'] = $contact_info;
        $contact_fb = Zira\Config::get('contact_fb');
        if ($contact_fb) $contacts['facebook'] = $contact_fb;
        $contact_gp= Zira\Config::get('contact_gp');
        if ($contact_gp) $contacts['google'] = $contact_gp;
        $contact_tw= Zira\Config::get('contact_tw');
        if ($contact_tw) $contacts['twitter'] = $contact_tw;
        $contact_vk= Zira\Config::get('contact_vk');
        if ($contact_vk) $contacts['vkontakte'] = $contact_vk;
        $contact_yandex_map = Zira\Config::get('contact_yandex_map');
        if ($contact_yandex_map && $contact_address) $contacts['yandex_map'] = $contact_yandex_map;
        $contact_google_map = Zira\Config::get('contact_google_map');
        if ($contact_google_map && $contact_address) $contacts['google_map'] = $contact_google_map;

        if (empty($form) && empty($contacts)) {
            Zira\Page::render(array(
                Zira\Page::VIEW_PLACEHOLDER_TITLE => Zira\Locale::t('Contacts'),
                Zira\Page::VIEW_PLACEHOLDER_CONTENT => Zira\Locale::t('Sorry, contacts are not specified')
            ));
        } else if (!empty($form) && empty($contacts)) {
            Zira\Page::render(array(
                Zira\Page::VIEW_PLACEHOLDER_TITLE => Zira\Locale::t('Contacts'),
                Zira\Page::VIEW_PLACEHOLDER_CONTENT => $form
            ));
        } else {
            if (!empty($form)) $contacts['form'] = $form;
            Zira\Page::setView('zira/contact');
            Zira\Page::render($contacts);
        }
    }
}