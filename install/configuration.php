<?php
/**
 * Zira project.
 * configuration.php
 * (c)2016 http://dro1d.ru
 */

if (!defined('ZIRA_INSTALL')) exit;

$infoFields = array(
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

$form = new \Install\Forms\Credentials();

foreach($infoFields as $field) {
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
    foreach($infoFields as $field) {
        Zira\Session::set($field, $form->getValue($field));
    }
}

$form = new \Install\Forms\Configuration();
$form->setValues(Zira\Session::getArray());
if (!Zira\Session::get('db_type')) $form->setValue('db_type', 'mysql');
if (!Zira\Session::get('db_host')) $form->setValue('db_host', 'localhost');
if (!Zira\Session::get('db_port')) $form->setValue('db_port', '3306');
if (!Zira\Session::get('db_file')) $form->setValue('db_file', '..' . DIRECTORY_SEPARATOR . 'zira.db');
if (!Zira\Session::get('db_prefix')) $form->setValue('db_prefix', 'zira_');
if (!Zira\Session::get('root_dir')) $form->setValue('root_dir', '.');
if (!Zira\Session::get('base_url')) {
    $uri = trim($_SERVER['REQUEST_URI'],'/');
    $uri = preg_replace('/^([^\?]+).*$/', '$1', $uri);
    $uri_parts = explode('/', $uri);
    if ($uri_parts[count($uri_parts)-1] == 'index.php') array_pop($uri_parts);
    if ($uri_parts[count($uri_parts)-1] == 'install') array_pop($uri_parts);
    $base_url = '/' . implode('/', $uri_parts);
    $form->setValue('base_url', $base_url);
}

return array(
    'content' => Zira\Helper::tag('p', Zira\Locale::t('Zira CMS stores data in database. Currently, MySQL 5 and SQLite 3 are supported.')).
                (string)$form,
    'script' => 'if (zira_install_clean_url) {'.
                '$(\'#zira_install_clean_url_input\').val(1);'.
                '} else {'.
                '$(\'#zira_install_clean_url_input\').val(0);'.
                '}'.
                'zira_install_database_select = function() {'.
                'var zira_init_db_type = $(\'#zira-install-db-type-select\').val();'.
                'if (zira_init_db_type == \'mysql\') {'.
                '$(\'#zira-install-mysql-credentials\').show();'.
                '$(\'#zira-install-sqlite-credentials\').hide();'.
                '} else if (zira_init_db_type == \'sqlite\') {'.
                '$(\'#zira-install-mysql-credentials\').hide();'.
                '$(\'#zira-install-sqlite-credentials\').show();'.
                '}'.
                '};'.
                'zira_install_database_select();'
);