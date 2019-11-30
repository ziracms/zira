<?php
/**
 * Zira project.
 * index.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Push\Controllers;

use Zira;
use Push;

class Index extends Zira\Controller {
    public function index() {
        if (!Zira\Request::isPost()) Zira\Response::notFound();
        if (!Zira\User::checkToken(Zira\Request::post('token'))) Zira\Response::forbidden();
        $action = Zira\Request::post('action');
        switch ($action) {
            case 'create':
                Push\Models\Subscription::createSubscription(
                    Zira\Request::post('endpoint'),
                    Zira\Request::post('publicKey'),
                    Zira\Request::post('authToken'),
                    Zira\Request::post('contentEncoding')
                );
                break;
            case 'delete':
                Push\Models\Subscription::deleteSubscription(
                    Zira\Request::post('endpoint')
                );
                break;
        }
    }
}