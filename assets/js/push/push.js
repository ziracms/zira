(function($){
    function enableSubscribeButton() {
        $('.push-subscription-button').removeAttr('disabled');
    }
    
    function disableSubscribeButton() {
        $('.push-subscription-button').attr('disabled', 'disabled');
    }

    function setSubscribeButton() {
        $('.push-subscription-button').text(t('Subscribe to notifications'));
        $('.push-subscription-button').removeClass('unsubscribe');
    }
    
    function setUnsubscribeButton() {
        $('.push-subscription-button').text(t('Unsubscribe from notifications'));
        $('.push-subscription-button').addClass('unsubscribe');
    }
    
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
        
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    function checkNotificationPermission() {
        return new Promise((resolve, reject) => {
            if (Notification.permission === 'denied') {
                return reject(new Error('Push messages are blocked.'));
            }
            if (Notification.permission === 'granted') {
                return resolve();
            }
            if (Notification.permission === 'default') {
                return Notification.requestPermission().then(result => {
                    if (result !== 'granted') {
                        reject(new Error('Bad permission result'));
                    }
                    resolve();
                });
            }
        });
    }

    function subscribe() {
        return checkNotificationPermission()
            .then(() => navigator.serviceWorker.ready)
            .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(zira_push_pub_key),
                })
            )
            .then(subscription => {
                return send(subscription, 'create');
            }).then(subscription => subscription && setUnsubscribeButton())
            .catch(e => {
                if (Notification.permission === 'denied') {
                    disableSubscribeButton();
                } else {
                    // error
                    disableSubscribeButton();
                }
            });
    }

    function unsubscribe() {
        navigator.serviceWorker.ready
            .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
            .then(subscription => {
                if (!subscription) return setSubscribeButton();
                return send(subscription, 'delete');
            })
            .then(subscription => subscription.unsubscribe())
            .then(setSubscribeButton())
            .catch(e => {
                // error
            });
    }

    function update() {
        navigator.serviceWorker.ready
            .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
            .then(subscription => {
                if (!subscription) {
                    setSubscribeButton();
                    if (Notification.permission === 'default' && typeof zira_push_request_onload_on != "undefined" && zira_push_request_onload_on) {
                        subscribe();
                    }
                    return;
                }
                if (typeof zira_push_subscription_disabled != "undefined" && zira_push_subscription_disabled) {
                    return unsubscribe();
                }
                return subscription;
            })
            .then(subscription => subscription && setUnsubscribeButton())
            .catch(e => {
                // error
            });
    }

    function send(subscription, action) {
        const key = subscription.getKey('p256dh');
        const token = subscription.getKey('auth');
        const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];
        let data = new FormData();
        data.append('endpoint', subscription.endpoint);
        data.append('publicKey', key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null);
        data.append('authToken', token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null);
        data.append('contentEncoding', contentEncoding);
        data.append('action', action);
        data.append('token', zira_push_token);
        return fetch(zira_push_controller_url, {
            method: 'POST',
            body: data,
        }).then(() => subscription);
    }
    
    $(document).ready(function(){
        if (window.location.protocol != 'https:') return;
        if (typeof zira_push_service_worker_url == "undefined" || zira_push_service_worker_url.length == 0) return;
        if (typeof zira_push_controller_url == "undefined" || zira_push_controller_url.length == 0) return;
        if (typeof zira_push_pub_key == "undefined" || zira_push_pub_key.length == 0) return;
        if (typeof zira_push_token == "undefined" || zira_push_token.length == 0) return;
        
        if (!('serviceWorker' in navigator)) return;
        if (!('PushManager' in window)) return;
        if (!('showNotification' in ServiceWorkerRegistration.prototype)) return;
        if (Notification.permission === 'denied') return setSubscribeButton();

        navigator.serviceWorker.register(zira_push_service_worker_url).then(
            () => {
                enableSubscribeButton();
                update();
                $('.push-subscription-button').click(function(){
                    if (!$(this).hasClass('unsubscribe')) {
                        subscribe();
                    } else {
                        unsubscribe();
                    }
                });
            },
            e => {
                // error
            }
        );
    });
})(jQuery);