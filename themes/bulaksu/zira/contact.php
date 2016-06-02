<div class="page-header">
<h1><?php echo t('Contacts') ?></h1>
</div>
<div class="contacts-wrapper">
<?php if (!empty($image)): ?>
<img class="contact-image" src="<?php echo Zira\Helper::html(Zira\Helper::baseUrl($image)); ?>" alt="<?php if (!empty($name)) echo Zira\Helper::html($name); ?>" />
<?php endif; ?>
<div class="contact-details">
<?php if (!empty($name)): ?>
<h2 class="contact-name"><?php echo Zira\Helper::html($name); ?></h2>
<?php endif; ?>
<?php if (!empty($address)): ?>
<address class="contact-address"><span class="glyphicon glyphicon-map-marker"></span> <?php echo Zira\Helper::html($address); ?></address>
<?php endif; ?>
<?php if (!empty($email)): ?>
<div><span class="glyphicon glyphicon-envelope"></span> <a href="mailto:<?php echo Zira\Helper::html($email); ?>"><?php echo Zira\Helper::html($email); ?></a></div>
<?php endif; ?>
<?php if (!empty($phone)): ?>
<div><span class="glyphicon glyphicon-earphone"></span> <?php echo Zira\Helper::html($phone); ?></div>
<?php endif; ?>
<?php if (!empty($facebook) || !empty($google) || !empty($twitter) || !empty($vkontakte)): ?>
<div class="social-btn-wrapper social-contacts">
<?php if (!empty($facebook)): ?>
<a class="social-btn fb" href="<?php echo Zira\Helper::html($facebook); ?>" title="<?php echo t('Facebook') ?>" target="_blank"></a>
<?php endif; ?>
<?php if (!empty($google)): ?>
<a class="social-btn gp" href="<?php echo Zira\Helper::html($google); ?>" title="<?php echo t('Google +') ?>" target="_blank"></a>
<?php endif; ?>
<?php if (!empty($twitter)): ?>
<a class="social-btn tw" href="<?php echo Zira\Helper::html($twitter); ?>" title="<?php echo t('Twitter') ?>" target="_blank"></a>
<?php endif; ?>
<?php if (!empty($vkontakte)): ?>
<a class="social-btn vk" href="<?php echo Zira\Helper::html($vkontakte); ?>" title="<?php echo t('Vkontakte') ?>" target="_blank"></a>
<?php endif; ?>
</div><!--/social-btn-wrapper-->
<?php endif; ?>
<?php if (!empty($info)): ?>
<p class="contact-info"><?php echo Zira\Helper::nl2br(Zira\Helper::html($info)); ?></p>
<?php endif; ?>
</div><!--/contact-details-->
</div><!--/contact-wrapper-->
<?php if (!empty($google_map) && !empty($address)) Zira\View::renderView(array('address'=>$address, 'name'=>!empty($name) ? $name : ''), 'zira/google-map'); ?>
<?php if (!empty($yandex_map) && !empty($address)) Zira\View::renderView(array('address'=>$address, 'name'=>!empty($name) ? $name : ''), 'zira/yandex-map'); ?>
<?php if (!empty($form)) echo $form; ?>