<?php
/**
 * Zira project.
 * push.php
 * (c)2019 https://github.com/ziracms/zira
 */

namespace Push\Models;

use Zira;
use Dash;
use Zira\Permission;

class Push extends Dash\Models\Model {
    public function generateKeys() {
        if (!Permission::check(Permission::TO_EXECUTE_TASKS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }

        $keys = \Push\Push::createVapidKeys();
        return $keys;
    }

    public function send() {
        if (!Permission::check(Permission::TO_EXECUTE_TASKS)) {
            return array('error'=>Zira\Locale::t('Permission denied'));
        }
        $form = new \Push\Forms\Send();
        if ($form->isValid()) {
            $offset = (int)$form->getValue('offset');
            $language = $form->getValue('language');
            return $this->send_notifications($form->getValue('title'), $form->getValue('description'), $form->getValue('image'), $form->getValue('url'), $offset, $language);
        } else {
            return array('error'=>$form->getError());
        }
    }
    
    protected function send_notifications($title, $body, $image, $url, $page, $language='') {
        $total_q = \Push\Models\Subscription::getCollection()
                                                ->count()
                                                ;
        
        if (!empty($language)) {
            $total_q->where('language', '=', $language);
        }
        
        $total = $total_q->get('co');
        
        $offset = \Push\Windows\Push::LIMIT * ($page);
        $left = 0;
        $sent = 0;
        
        $subscribers_q = \Push\Models\Subscription::getCollection();
        
        if (!empty($language)) {
            $subscribers_q->where('language', '=', $language);
        }
        
        $subscribers = $subscribers_q->limit(\Push\Windows\Push::LIMIT, $offset)
                                    ->order_by('id','asc')
                                    ->get();

        $sent = 0;
        foreach ($subscribers as $subscription) {
            if (!$subscription->active) continue;
            try {
                if (!\Push\Push::pushNotification($subscription->endpoint, $subscription->pub_key, $subscription->auth_token, $subscription->encoding, $title, $body, $image, $url)) {
                    \Push\Models\Subscription::disableSubscription($subscription->endpoint);
                } else {
                    $sent++;
                }
            } catch (\Exception $e) {
                Zira\Log::exception($e);
            }
        }
        
        $progress = $offset + count($subscribers);
        $left = $total - $progress;
        
        return array(
            'total' => $total,
            'progress' => $progress,
            'sent' => $sent,
            'left' => $left,
            'offset' => $page
        );
    }
}