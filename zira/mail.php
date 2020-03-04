<?php
/**
 * Zira project
 * mail.php
 * (c)2015 https://github.com/ziracms/zira
 */

namespace Zira;

use Zira\Phpmailer\Phpmailer;
use Zira\Phpmailer\Smtp;

class Mail {
    protected static $_mailer;
    public static $_is_html = false;

    public static function send($email, $subject, $body, $filename = null, $replyTo = null) {
        self::$_mailer = new Phpmailer();

        if (defined('DEBUG') && DEBUG) {
            ob_start();
            self::$_mailer->SMTPDebug = Smtp::DEBUG_SERVER;
        }

        if (Config::get('use_smtp')) {
            self::sendSMTP($email, $subject, $body, $filename, $replyTo);
        } else {
            self::sendMail($email, $subject, $body, $filename, $replyTo);
        }

        self::$_mailer = null;
    }

    protected static function sendSMTP($email, $subject, $body, $filename = null, $replyTo = null) {
        self::$_mailer->isSMTP();
        self::$_mailer->Host = Config::get('smtp_host');
        self::$_mailer->SMTPAuth = true;
        self::$_mailer->Username = Config::get('smtp_username');
        self::$_mailer->Password = Config::get('smtp_password');
        self::$_mailer->SMTPSecure = Config::get('smtp_secure');
        self::$_mailer->Port = Config::get('smtp_port');

        self::sendMail($email, $subject, $body, $filename, $replyTo);
    }

    protected static function sendMail($email, $subject, $body, $filename = null, $replyTo = null) {
        self::$_mailer->CharSet = CHARSET;

        self::$_mailer->From = Config::get('email_from');
        self::$_mailer->FromName = Config::get('email_from_name');

        if (!empty($replyTo)) {
            self::$_mailer->addReplyTo($replyTo);
        }

        self::$_mailer->addAddress($email);

        if (!empty($filename) && !is_array($filename) && file_exists($filename)) {
            self::$_mailer->addAttachment($filename);
        } else if (!empty($filename) && is_array($filename)) {
            foreach($filename as $_filename) {
                if (!file_exists($_filename)) continue;
                self::$_mailer->addAttachment($_filename);
            }
        }

        self::$_mailer->isHTML(self::$_is_html);

        self::$_mailer->Subject = $subject;
        self::$_mailer->Body    = $body;

        $success = self::$_mailer->send();

        if (defined('DEBUG') && DEBUG) {
            $debug = ob_get_clean();
            if (!empty($debug)) Log::write($debug);
        }
        
        if(!$success) {
            throw new \Exception(self::$_mailer->ErrorInfo);
        }
    }
}