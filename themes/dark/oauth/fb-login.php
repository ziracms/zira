<?php if (!empty($app_id) && !empty($app_secret)): ?>
<script type="text/javascript">
    // used with standart button
    function oauth_fb_login() {
        FB.getLoginStatus(function(response) {
          oauth_fb_status_change_callback(response);
        });
    }
    // used with custom button
    function oauth_fb_btn() {
        if (typeof(oauth_fb_btn.activated)=="undefined" || !oauth_fb_btn.activated) return;

        FB.login(function(response) {
            oauth_fb_status_change_callback(response);
        }, {scope: 'public_profile,email'});
    }
    function oauth_fb_redirect() {
        var redirect = '<?php echo \Oauth\Oauth::getRedirectUrl(); ?>';
        var url = '<?php echo Zira\Helper::url('oauth/login/facebook'); ?>';
        if (redirect.length>0) url += '?redirect='+redirect;
        window.location.href=url;
    }
    function oauth_fb_status_change_callback(response) {
        if (response.status === 'connected') {
            // Logged into your app and Facebook.
            oauth_fb_redirect();
        } else if (response.status === 'not_authorized') {
            // The person is logged into Facebook, but not your app.
        } else {
            // The person is not logged into Facebook
        }
    }
</script>
<?php endif; ?>