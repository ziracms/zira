<?php if (!empty($app_id) && !empty($app_secret)): ?>
<script type="text/javascript">
function oauth_vk_login() {
    var url = '<?php echo \Oauth\Oauth::getVkontakteAuthUrl(); ?>';
    var w = 650;
    var h = 450;
    var l,t;
    try {
        l = Math.floor((window.screen.width - w) / 2);
        t = Math.floor((window.screen.height - h) / 2);
    } catch(err) {
        l = 0;
        t = 0;
    }
    window.open(url, 'vkAuthWindow', 'width='+w+',height='+h+',left='+l+',top='+t+',scrollbars=yes,toolbar=no,menubar=no');
}
function oauth_vk_on_response(code) {
    if (typeof(code)=="undefined") code = '';
    oauth_vk_redirect(code);
}
function oauth_vk_redirect(code) {
    var redirect = '<?php echo \Oauth\Oauth::getRedirectUrl(); ?>';
    var url = '<?php echo Zira\Helper::url('oauth/login/vkontakte'); ?>';
    url += '?code='+code;
    if (redirect.length>0) url += '&redirect='+redirect;
    window.location.href=url;
}
</script>
<?php endif; ?>