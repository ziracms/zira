<?php if (!empty($slider)) render($slider, 'zira/slider'); ?>
<?php if (!empty($videos)) render($videos, 'zira/videos'); ?>
<main>
<article>
<?php if (!empty($image)): ?>
<img class="image zira-lightbox" src="<?php echo Zira\Helper::urlencode(Zira\Helper::baseUrl($image)) ?>" alt="<?php echo (!empty($title) ? Zira\Helper::html($title) : '') ?>" />
<?php endif; ?>
<?php if (!empty($title)): ?>
<div class="page-header">
<h1><?php if (isset($admin_icons)) echo $admin_icons; ?><?php echo Zira\Helper::html($title) ?></h1>
</div>
<?php endif; ?>
<?php if (!empty($description)): ?>
<p class="description">
<?php echo Zira\Helper::html($description); ?>
</p>
<?php endif; ?>
<?php if (!empty($date) || !empty($author)): ?>
<div class="article-info">
<?php if (!empty($date)): ?>
<div class="datetime">
<?php echo t('Last updated').': ' ?>
<time datetime="<?php echo date(DATE_W3C, strtotime($date)) ?>" itemprop="dateModified"><?php echo date(Zira\Config::get('date_format'), strtotime($date)) ?></time>
</div>
<?php endif ?>
<?php if (!empty($author)): ?>
<div class="author">
<?php echo t('Author').': ' ?>
<?php echo $author; ?>
</div>
<?php endif; ?>
</div>
<?php endif; // $date || $author ?>
<?php if (!empty($content)): ?>
<div class="article<?php if (!empty($class)) echo ' '.$class ?>">
<?php echo $content; ?>
</div>
<?php endif; ?>
<?php if (!empty($contentView) && isset($contentView['data']) && isset($contentView['view'])) render($contentView['data'], $contentView['view']); ?>
<?php if (isset($rating)): ?>
<div id="rating" class="rating">
<a href="javascript:void(0)" class="like" data-value="1" data-type="record" data-id="<?php echo Zira\Page::getRecordId() ?>" data-token="<?php echo Zira\User::getToken() ?>" data-url="<?php echo Zira\Helper::url('poll') ?>">
<span class="glyphicon glyphicon-thumbs-up"></span> <span class="rating-value"><?php echo Zira\Helper::html($rating) ?></span>
</a>
<?php if (isset($title) && isset($url)): ?>
<div class="share-wrapper"><?php Zira\View::renderView(array('url'=>Zira\Helper::url(strip_tags($url),true, true),'title'=>strip_tags($title)), 'zira/widgets/share') ?></div>
<?php endif; ?>
</div>
<?php endif; ?>
<?php if (isset($pagination)) echo $pagination; ?>
</article>
</main>
<?php if (!empty($gallery)) render($gallery, 'zira/gallery'); ?>
<?php if (!empty($audio)) render($audio, 'zira/audio'); ?>
<?php if (!empty($files)) render($files, 'zira/files'); ?>
<?php if (!empty($comments)) render($comments, 'zira/comments'); ?>