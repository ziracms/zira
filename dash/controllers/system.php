<?php
/**
 * Zira project.
 * system.php
 * (c)2016 http://dro1d.ru
 */

namespace Dash\Controllers;

use Zira;
use Dash;

class System extends Dash\Controller {
    public function _before() {
        parent::_before();
        Zira\View::setAjax(true);
    }

    public function theme() {
        if (Zira\Request::isPost()) {
            $theme = Zira\Request::post('theme');
            $model = new Dash\Models\Themes(new Dash\Windows\Themes());
            $response = $model->activate($theme);
            Zira\Page::render($response);
        }
    }

    public function module() {
        if (Zira\Request::isPost()) {
            $module = Zira\Request::post('module');
            $active = Zira\Request::post('active');
            $install = Zira\Request::post('install');
            $model = new Dash\Models\Modules(new Dash\Windows\Modules());
            $response = array();
            if ($install!==null && $install) {
                $response = $model->install($module);
            } else if ($install!==null && !$install) {
                $response = $model->uninstall($module);
            } else if ($active!==null && $active) {
                $response = $model->activate($module);
            } else if ($active!==null && !$active) {
                $response = $model->deactivate($module);
            }
            Zira\Page::render($response);
        }
    }

    public function info() {
        if (Zira\Request::isPost()) {
            $type = Zira\Request::post('type');
            $file = Zira\Request::post('file');
            $response = array();
            if ($type=='logs') {
                $model = new Dash\Models\Logs(new Dash\Windows\Logs());
                $response = $model->info($file);
            } else if ($type=='cache') {
                $model = new Dash\Models\Cache(new Dash\Windows\Cache());
                $response = $model->info($file);
            }
            Zira\Page::render($response);
        }
    }

    public function dump() {
        if (Zira\Permission::check(Zira\Permission::TO_EXECUTE_TASKS)) {
            header("Content-type: application/octet-stream");
            if (DB_TYPE == 'sqlite') {
                header("Content-Disposition: attachment; filename=" . DB_TYPE . '-' . date('Y-m-d') . '.sql');
            } else {
                header("Content-Disposition: attachment; filename=" . DB_NAME . '-' . date('Y-m-d') . '.sql');
            }

            $model = new Dash\Models\System(new Dash\Windows\System());
            $model->dump();
        } else {
            Zira\Response::forbidden();
        }
    }

    public function tree() {
        if (Zira\Request::isPost()) {
            $model = new Dash\Models\System(new Dash\Windows\System());
            $response = $model->tree();
            Zira\Page::render($response);
        }
    }

    public function cache() {
        if (Zira\Request::isPost()) {
            $class = Zira\Request::post('class');
            $window = new Dash\Windows\Cache();
            if ($class == $window->getJSClassName()) {
                $model = new Dash\Models\Cache($window);
                $response = $model->clear();
            } else {
                $model = new Dash\Models\System(new Dash\Windows\System());
                $response = $model->cache();
            }
            Zira\Page::render($response);
        }
    }

    public function stick() {
        if (Zira\Request::isPost()) {
            $content = Zira\Request::post('content');
            Zira\Models\Option::write('memory_stick', $content);
            Zira\Page::render(array('ok'=>1));
        }
    }

    public function blocks() {
        if (Zira\Request::isPost()) {
            $blocks = Zira\Request::post('blocks');
            $model = new Dash\Models\Blocks(new Dash\Windows\Blocks());
            $response = $model->install($blocks);
            Zira\Page::render($response);
        }
    }

    public function description() {
        if (Zira\Request::isPost()) {
            $description = Zira\Request::post('description');
            $id = Zira\Request::post('item');
            $type = Zira\Request::post('type');
            $response = array();
            if ($type == 'category') {
                $model = new Dash\Models\Records(new Dash\Windows\Records());
                $response = $model->setCategoryDescription($id, $description);
            }
            Zira\Page::render($response);
        }
    }

    public function mailing() {
         if (Zira\Request::isPost()) {
             $model = new Dash\Models\Mailing(new Dash\Windows\Mailing());
             $response = $model->mail();
             Zira\Page::render($response);
         }
    }

    public function homedrag() {
        if (Zira\Request::isPost()) {
            $items = Zira\Request::post('items');
            $orders = Zira\Request::post('orders');
            $model = new Dash\Models\Homecategories(new Dash\Windows\Homecategories());
            $response = $model->drag($items, $orders);
            Zira\Page::render($response);
        }
    }
}