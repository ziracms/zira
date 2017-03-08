<?php
/**
 * Zira project.
 * operate.php
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

$total_processes = 6;
$error = false;

try {
    if (empty($process)) throw new \Exception('What to do ?');
    if ($process>$total_processes) throw new \Exception('Unknown task');

    $data = Zira\Session::get('zira_data');
    if (empty($data)) throw new \Exception('No data recieved');

    // defining constants
    foreach ($constants as $field) {
        if (!isset($data[$field])) throw new \Exception('No data recieved');
        $const = strtoupper($field);
        if (!defined($const)) define($const, $data[$field]);
    }

    // connecting to database
    Zira\Db\Loader::initialize();
    Zira\Db\Db::open();

    if ($process == 1) {
        // creating db tables
        $dir = ROOT_DIR . DIRECTORY_SEPARATOR . 'zira' . DIRECTORY_SEPARATOR . 'install';
        $tables = array();
        $d = opendir($dir);
        while (($f = readdir($d)) !== false) {
            if ($f == '.' || $f == '..' || is_dir($dir . DIRECTORY_SEPARATOR . $f)) continue;
            if (!preg_match('/^([a-zA-Z0-9]+)\.php$/', $f, $matches)) continue;
            $class = '\\Zira\\Install\\' . ucfirst($matches[1]);
            try {
                if (class_exists($class)) {
                    $table = new $class;
                    if ($table instanceof Zira\Db\Table) {
                        $tables [] = $table;
                    } else {
                        unset($table);
                    }
                }
            } catch (\Exception $e) {

            }
        }
        closedir($d);

        if (!empty($tables)) {
            foreach ($tables as $table) {
                $table->install();
            }
        }

        $message = Zira\Locale::t('Creating user');
    } else if ($process == 2) {
        // creating user
        if (empty($data['username']) ||
            empty($data['password']) ||
            empty($data['email']) ||
            empty($data['firstname']) ||
            empty($data['secondname'])
        ) {
            throw new \Exception('No user data');
        }
        $user = new Zira\Models\User();

        $user->group_id = Zira\User::GROUP_SUPERADMIN;
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = Zira\User::getHashedUserToken($data['password']);
        $user->firstname = $data['firstname'];
        $user->secondname = $data['secondname'];
        $user->verified = Zira\Models\User::STATUS_VERIFIED;
        $user->active = Zira\Models\User::STATUS_ACTIVE;
        $user->date_created = date('Y-m-d H:i:s');
        $user->date_logged = date('Y-m-d H:i:s');
        $user->code = Zira\User::generateRememberCode($user->username, $user->email);
        $user->save();

        $message = Zira\Locale::t('Creating home page');
    } else if ($process == 3) {
        // creating home page
        $record = new Zira\Models\Record();
        $record->category_id = 0;
        $record->name = Zira\Locale::t('home');
        $record->title = Zira\Locale::t('Home page');
        $record->description = Zira\Locale::t('Welcome to Zira CMS!');
        $record->content = Zira\Helper::tag('p', Zira\Locale::t('Welcome to Zira CMS!')).
                            Zira\Helper::tag_open('p').'&nbsp;'.Zira\Helper::tag_close('p').
                            Zira\Helper::tag('p', Zira\Locale::t('Zira CMS is a lightweight, flexible and easy to use content management system.')).
                            Zira\Helper::tag('p', Zira\Locale::t('Installing Zira CMS, you get the most commonly used features right out of the box.')).
                            Zira\Helper::tag('p', Zira\Locale::t('No need for extra downloads and plugins setup.')).
                            Zira\Helper::tag('p', Zira\Locale::t('Zira CMS brings desktop experience to your website - no web development skills required!')).
                            Zira\Helper::tag_open('p').'&nbsp;'.Zira\Helper::tag_close('p').
                            Zira\Helper::tag_open('p').Zira\Locale::t('If you have any questions, feel free to %s.',Zira\Helper::tag('a',Zira\Locale::t('contact us'), array('href'=>'http://dro1d.ru/contact','target'=>'_blank'))).Zira\Helper::tag_close('p').
                            Zira\Helper::tag('p', Zira\Locale::t('Don\'t forget to visit our forum and share your impressions with other users.'))
                            ;
        $record->language = $language;
        $record->access_check = 0;
        $record->published = Zira\Models\Record::STATUS_PUBLISHED;
        $record->front_page = Zira\Models\Record::STATUS_NOT_FRONT_PAGE;
        $record->author_id = 1;
        $record->creation_date = date('Y-m-d H:i:s');
        $record->modified_date = date('Y-m-d H:i:s');
        $record->save();

        // settings
        if (!empty($data['site_name'])) {
            $optionObj = new Zira\Models\Option();
            $optionObj->name = 'home_window_title';
            $optionObj->value = $data['site_name'];
            $optionObj->module = 'zira';
            $optionObj->save();
        }

        $optionObj = new Zira\Models\Option();
        $optionObj->name = 'home_layout';
        $optionObj->value = Zira\View::LAYOUT_RIGHT_SIDEBAR;
        $optionObj->module = 'zira';
        $optionObj->save();

        $optionObj = new Zira\Models\Option();
        $optionObj->name = 'home_record_name';
        $optionObj->value = Zira\Locale::t('home');
        $optionObj->module = 'zira';
        $optionObj->save();
        $message = Zira\Locale::t('Creating news category');
    } else if ($process == 4) {
        // creating news category
        $category = new Zira\Models\Category();
        $category->name = Zira\Locale::t('news');
        $category->title = Zira\Locale::t('News');
        $category->layout = Zira\View::LAYOUT_RIGHT_SIDEBAR;
        $category->parent_id = 0;
        $category->access_check = 0;
        $category->display_author = 1;
        $category->display_date = 1;
        $category->rating_enabled = 1;
        $category->records_list = 1;
        $category->save();

        $record = new Zira\Models\Record();
        $record->category_id = $category->id;
        $record->name = Zira\Locale::t('launch');
        $record->title = Zira\Locale::t('Our new website is now open!');
        $record->description = Zira\Locale::t('Our website is now open for visitors.');
        $record->content = Zira\Helper::tag('p', Zira\Locale::t('Our website is now open for visitors.')).Zira\Helper::tag('p', Zira\Locale::t('This is an example page created by installer.'));
        $record->language = $language;
        $record->access_check = 0;
        $record->published = Zira\Models\Record::STATUS_PUBLISHED;
        $record->front_page = Zira\Models\Record::STATUS_NOT_FRONT_PAGE;
        $record->author_id = 1;
        $record->creation_date = date('Y-m-d H:i:s');
        $record->modified_date = date('Y-m-d H:i:s');
        $record->save();

        $max_order = Zira\Models\Widget::getCollection()->max('sort_order')->get('mx');

        $widget = new Zira\Models\Widget();
        $widget->name = Zira\Models\Category::WIDGET_CLASS;
        $widget->module = 'zira';
        $widget->placeholder = Zira\Models\Category::WIDGET_PLACEHOLDER;
        $widget->params = $category->id;
        $widget->category_id = 0;
        $widget->sort_order = ++$max_order;
        $widget->active = Zira\Models\Widget::STATUS_ACTIVE;
        $widget->save();

        $message = Zira\Locale::t('Creating menu');
    } else if ($process == 5) {
        // creating menu
        $menuItem = new Zira\Models\Menu();
        $menuItem->menu_id = Zira\Menu::MENU_PRIMARY;
        $menuItem->parent_id = 0;
        $menuItem->sort_order = 1;
        $menuItem->url = '/';
        $menuItem->title = 'Home';
        $menuItem->external = 0;
        $menuItem->active = Zira\Models\Menu::STATUS_ACTIVE;
        $menuItem->save();

        $menuItem = new Zira\Models\Menu();
        $menuItem->menu_id = Zira\Menu::MENU_PRIMARY;
        $menuItem->parent_id = 0;
        $menuItem->sort_order = 2;
        $menuItem->url = Zira\Locale::t('news');
        $menuItem->language = $language;
        $menuItem->title = Zira\Locale::t('News');
        $menuItem->external = 0;
        $menuItem->active = Zira\Models\Menu::STATUS_ACTIVE;
        $menuItem->save();

        $menuItem = new Zira\Models\Menu();
        $menuItem->menu_id = Zira\Menu::MENU_FOOTER;
        $menuItem->parent_id = 0;
        $menuItem->sort_order = 3;
        $menuItem->url = '/';
        $menuItem->title = 'Home';
        $menuItem->external = 0;
        $menuItem->active = Zira\Models\Menu::STATUS_ACTIVE;
        $menuItem->save();

        $menuItem = new Zira\Models\Menu();
        $menuItem->menu_id = Zira\Menu::MENU_FOOTER;
        $menuItem->parent_id = 0;
        $menuItem->sort_order = 4;
        $menuItem->url = 'contact';
        $menuItem->title = 'Contacts';
        $menuItem->external = 0;
        $menuItem->active = Zira\Models\Menu::STATUS_ACTIVE;
        $menuItem->save();

        $menuItem = new Zira\Models\Menu();
        $menuItem->menu_id = Zira\Menu::MENU_FOOTER;
        $menuItem->parent_id = 0;
        $menuItem->sort_order = 5;
        $menuItem->url = 'sitemap';
        $menuItem->title = 'Site map';
        $menuItem->external = 0;
        $menuItem->active = Zira\Models\Menu::STATUS_ACTIVE;
        $menuItem->save();

        $message = Zira\Locale::t('Writing settings to %s', 'config.php');
    } else if ($process == 6) {
        // writing config
        if (empty($data['root_dir']) || empty($data['base_url'])) {
            throw new \Exception('No data');
        }
        $head = '/**'."\r\n".
                ' * Zira CMS'."\r\n".
                ' * config.php'."\r\n".
                ' * (c)'.date('Y').' http://dro1d.ru'."\r\n".
                ' */'."\r\n";
        $info = '/**'."\r\n".
                ' * Defined during installation on '.date('Y-m-d')."\r\n".
                ' */'."\r\n";
        $config = 'const ROOT_DIR = \''.$data['root_dir'].'\';'."\r\n".
                    'const BASE_URL = \''.$data['base_url'].'\';'."\r\n".
                    'const SECRET = \''.$data['secret'].'\';'."\r\n".
                    'const DB_TYPE = \''.DB_TYPE.'\';'."\r\n";
        if (DB_TYPE == 'mysql') {
            $config .=
                'const DB_HOST = \'' . DB_HOST . '\';' . "\r\n" .
                'const DB_PORT = ' . DB_PORT . ';' . "\r\n" .
                'const DB_NAME = \'' . DB_NAME . '\';' . "\r\n" .
                'const DB_USERNAME = \'' . DB_USERNAME . '\';' . "\r\n" .
                'const DB_PASSWORD = \'' . DB_PASSWORD . '\';' . "\r\n";
        } else if (DB_TYPE == 'sqlite') {
            $db_file = DB_FILE;
            if (substr($db_file, 0, 6) == '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) {
                $db_file = substr($db_file, 3);
            }
            $config .= 'const DB_FILE = \'' . $db_file . '\';' . "\r\n";
        }
        $config .= 'const DB_PREFIX = \''.DB_PREFIX.'\';'."\r\n".
                    'const CONSOLE_PASSWORD = \''.(CONSOLE_PASSWORD ? md5(rawurlencode(CONSOLE_PASSWORD)) : '').'\';'."\r\n";

        if (!isset($data['clean_url']) ||
            empty($data['email_from']) ||
            empty($data['site_name']) ||
            empty($data['site_slogan'])
        ) {
            throw new \Exception('No site info');
        }
        $settings = @include('default.php');
        if (empty($settings) || !is_array($settings)) throw new \Exception('Bad defaults');
        $settings['clean_url'] = $data['clean_url'];
        $settings['language'] = $language;
        $settings['languages'] = array($language);
        $settings['email_from'] = $data['email_from'];
        $settings['email_from_name'] = $data['site_name'];
        $settings['feedback_email'] = $data['email_from'];
        $settings['site_name'] = $data['site_name'];
        $settings['site_title'] = $data['site_name'];
        $settings['site_slogan'] = $data['site_slogan'];

        $defaults = '/**'."\r\n".
                    ' * System default settings'."\r\n".
                    ' */'."\r\n".
                    'return array('."\r\n";
        $default_strs = array();
        foreach ($settings as $key => $value) {
            if (is_int($value)) {
                $default_strs []= "\t'".$key."' => ".$value;
            } else if (is_bool($value)) {
                $default_strs []= "\t'".$key."' => ".($value ? 'true' : 'false');
            } else if (is_string($value)) {
                $default_strs []= "\t'".$key."' => '".$value."'";
            } else if (is_array($value)) {
                $_value = 'array(';
                if (!empty($value)) {
                    $_value .= "'".implode("', '", $value)."'";
                }
                $_value .= ')';
                $default_strs []= "\t'".$key."' => ".$_value;
            } else {
                throw new \Exception('Unknown type');
            }
        }
        $defaults .= implode(",\r\n", $default_strs)."\r\n";
        $defaults .= ');'."\r\n";

        file_put_contents(ROOT_DIR . DIRECTORY_SEPARATOR . 'config.php', '<?php'."\r\n" . $head . "\r\n\r\n" . $info . $config . "\r\n" . $defaults, FILE_TEXT);
    }

    Zira\Db\Db::close();
} catch (\Exception $e) {
    $error = true;
}

if (!$error) {
    $percent = floor(($process / $total_processes) * 100);
    if ($process<$total_processes) {
        // next task
        if (!isset($message)) $message = '';
        return array(
            'script' => 'zira_modal_progress_update(' . $percent . ');' .
                '$(\'#zira-install-container\').append(\''.Zira\Helper::tag('p', '- '.$message).'\');'.
                'zira_process_page++;' .
                'window.setTimeout(\'zira_install_request();\', 1000);'
        );
    } else {
        // Done!
        return array(
            'content' => Zira\Helper::tag('h2', Zira\Locale::t('Congratulations!')).
                        Zira\Helper::tag_open('p').'&nbsp;'.Zira\Helper::tag_close('p').
                        Zira\Helper::tag('p', Zira\Locale::t('Zira CMS is successfully installed.')).
                        Zira\Helper::tag_open('p').'&nbsp;'.Zira\Helper::tag_close('p').
                        Zira\Helper::tag('p', Zira\Locale::t('For security reasons turn off write permissions of %s file.', 'config.php')).
                        Zira\Helper::tag_open('p').Zira\Locale::t('Please visit our %s for detailed information.', Zira\Helper::tag('a', Zira\Locale::t('website'), array('href'=>'http://dro1d.ru','target'=>'_blank'))).Zira\Helper::tag_close('p').
                        Zira\Helper::tag_open('p').'&nbsp;'.Zira\Helper::tag_close('p').
                        Zira\Helper::tag_open('p').Zira\Locale::t('Go to your new %s!', Zira\Helper::tag('a', Zira\Locale::t('website'), array('href'=>$data['base_url']))).Zira\Helper::tag_close('p'),
            'script' => 'zira_modal_progress_hide();'
        );
    }
} else {
    // something went wrong
    return array(
        'error' => Zira\Locale::t('An error occurred')
    );
}