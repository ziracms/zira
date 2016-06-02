<?php
/**
 * Zira project.
 * database.php
 * (c)2016 http://dro1d.ru
 */

if (!defined('ZIRA_INSTALL')) exit;

$constants = array(
    'db_type',
    'db_host',
    'db_port',
    'db_name',
    'db_username',
    'db_password',
    'db_file',
    'db_prefix',
    'root_dir',
    'base_url',
    'console_password'
);

$form = new \Install\Forms\Configuration();

foreach($constants as $field) {
    $_field = Zira\Form\Form::getFieldName($form->getToken(), $field);
    if (Zira\Request::post($_field)===null && Zira\Session::get($field)) {
        Zira\Request::setPost($_field, Zira\Session::get($field));
    }
}

// checking previous form
if (!$form->isValid()) {
    return array(
        'error' => $form->getError()
    );
} else {
    // saving form data
    foreach($constants as $field) {
        Zira\Session::set($field, $form->getValue($field));
    }
}

$data = array();
$data['clean_url'] = (bool)$form->getValue('clean_url');
$error = false;

// checking collected data before installation
$info = array(
    'site_name',
    'site_slogan',
    'email_from',
    'secret',
    'firstname',
    'secondname',
    'username',
    'password',
    'email'
);
foreach($info as $field) {
    if (!Zira\Session::get($field)) {
        $error = Zira\Locale::t('Please check the specified data once again.');
        break;
    }
    $data[$field] = Zira\Session::get($field);
}
// checking constants once again
if (!$error) {
    foreach ($constants as $field) {
        if (Zira\Session::get($field)===null) {
            $error = Zira\Locale::t('Please check the specified data once again.');
            break;
        }
        $data[$field] = Zira\Session::get($field);
    }
}
// checking root_dir

if (!$error && $data['root_dir']!='.') {
    $data['root_dir'] = rtrim($data['root_dir'], '/\\');
    if (!file_exists($data['root_dir'] . DIRECTORY_SEPARATOR . 'zira.php')) {
        $error = Zira\Locale::t('Incorrect document root is specified.');
    }
}
// checking base url
if (!$error && $data['base_url']!='/') {
    $data['base_url'] = '/'.trim($data['base_url'],'/');
    $asset_path = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . rtrim(str_repeat('..' . DIRECTORY_SEPARATOR, count(explode('/',$data['base_url']))), DIRECTORY_SEPARATOR) . str_replace('/', DIRECTORY_SEPARATOR, rtrim($data['base_url'], '/') . '/assets/images/zira.png');
    if (!file_exists($asset_path)) {
        $error = Zira\Locale::t('Incorrect base url is specified.');
    }
}
// trying to connect to db
if (!$error) {
    foreach($constants as $field) {
        if (!isset($data[$field])) continue;
        $const = strtoupper($field);
        if ($field == 'db_file' && !empty($data[$field]) && substr($data[$field], 0, 3)=='../') {
            $data[$field] = '../'.$data[$field];
        }
        if (!defined($const)) define($const, $data[$field]);
    }
    try {
        Zira\Db\Loader::initialize();
        Zira\Db\Db::open();
        Zira\Db\Db::close();
    } catch(\Exception $e) {
        if ($data['db_type']=='sqlite') {
            $error = Zira\Locale::t('Failed to create database file.');
        } else {
            $error = Zira\Locale::t('Failed to connect to database.');
        }
    }
}

if (!$error) {
    // ready to install
    Zira\Session::set('zira_data', $data);
    return array(
        'content' => Zira\Helper::tag('p', Zira\Locale::t('Please wait...')).
                    Zira\Helper::tag_open('p').'&nbsp;'.Zira\Helper::tag_close('p').
                    Zira\Helper::tag('p', '- '.Zira\Locale::t('Creating database tables')),
        'script' => '$(\'#zira-install-backward-btn\').attr(\'disabled\', \'disabled\');'.
                    '$(\'#zira-install-forward-btn\').attr(\'disabled\', \'disabled\');'.
                    '$(\'#zira-install-pager\').remove();'.
                    'zira_modal_progress(\''.Zira\Locale::t('Installation').'\');'.
                    'zira_process_page++;'.
                    'window.setTimeout(\'zira_install_request();\', 1000);'
    );
} else {
    return array(
        'error' => Zira\Locale::t('An error occurred').'. '.$error
    );
}