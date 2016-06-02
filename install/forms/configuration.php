<?php
/**
 * Zira project.
 * configuration.php
 * (c)2016 http://dro1d.ru
 */

namespace Install\Forms;

use Zira\Form;
use Zira\Helper;
use Zira\Locale;

class Configuration extends Form {
    protected $_id = 'install-configuration-form';

    public function __construct() {
        parent::__construct($this->_id);
    }

    protected function _init() {
        $this->setTitle(Locale::t('Configuration'));
        $this->setDescription(Locale::t('Please fill out form fields'));
    }

    protected function _render() {
        $html = $this->open();
        $html .= Helper::tag_open('div', array('class'=>'form-group'));
        $html .= Helper::tag('label', Locale::t('Database').':', array('class'=>'col-sm-3 control-label'));
        $html .= Helper::tag_close('div');

        $html .= $this->select(Locale::t('Type').'*', 'db_type', array('mysql'=>'MySQL', 'sqlite'=>'SQLite'), array('onchange'=>'zira_install_database_select()', 'id'=>'zira-install-db-type-select'));

        $html .= Helper::tag_open('div', array('id'=>'zira-install-mysql-credentials'));
        $html .= $this->input(Locale::t('Server').'*', 'db_host');
        $html .= $this->input(Locale::t('Port').'*', 'db_port');
        $html .= $this->input(Locale::t('Name').'*', 'db_name');
        $html .= $this->input(Locale::t('User').'*', 'db_username');
        $html .= $this->input(Locale::t('Password'), 'db_password');
        $html .= Helper::tag_close('div');

        $html .= Helper::tag_open('div', array('id'=>'zira-install-sqlite-credentials'));
        $html .= $this->input(Locale::t('File').'*', 'db_file');
        $html .= Helper::tag_close('div');

        $html .= $this->input(Locale::t('Table prefix').'*', 'db_prefix', array('title'=>Locale::t('change to unique string')));
        $html .= Helper::tag('div', null, array('style'=>'margin: 40px 0px'));
        $html .= Helper::tag_open('div', array('class'=>'form-group'));
        $html .= Helper::tag('label', Locale::t('Website').':', array('class'=>'col-sm-3 control-label'));
        $html .= Helper::tag_close('div');
        $html .= $this->input(Locale::t('Document root').'*', 'root_dir', array('title'=>Locale::t('change only if a problem occurres')));
        $html .= $this->input(Locale::t('Base URL').'*', 'base_url', array('title'=>Locale::t('change only if it was detected incorrectly')));
        $html .= $this->input(Locale::t('Console password'), 'console_password', array('placeholder'=>Locale::t('min. %s chars',8), 'title'=>Locale::t('leave blank if you\'re not planning to use console')));
        $html .= $this->hidden('clean_url', array('id'=>'zira_install_clean_url_input'));
        $html .= $this->close();
        return $html;
    }

    protected function _validate() {
        $validator = $this->getValidator();

        $db_type = $this->getValue('db_type');
        if ($db_type == 'mysql') {
            $validator->registerString('db_host', null, 255, true, Locale::t('Please fill out form fields'));
            $validator->registerNumber('db_port', 1, null, true, Locale::t('Please fill out form fields'));
            $validator->registerString('db_name', null, 255, true, Locale::t('Please fill out form fields'));
            $validator->registerString('db_username', null, 255, true, Locale::t('Please fill out form fields'));
        } else if ($db_type == 'sqlite') {
            $validator->registerString('db_file', null, 255, true, Locale::t('Please fill out form fields'));
        }

        $validator->registerString('db_prefix', null, 255, true, Locale::t('Please fill out form fields'));
        $validator->registerString('root_dir', null, 255, true, Locale::t('Please fill out form fields'));
        $validator->registerString('base_url', null, 255, true, Locale::t('Please fill out form fields'));
        $validator->registerString('console_password', 8, 255, false, Locale::t('Console password is too short'));
    }
}