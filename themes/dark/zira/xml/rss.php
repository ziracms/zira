<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<title><?php echo Zira\Helper::html($title); ?></title>
<link><?php echo Zira\Helper::html($url); ?></link>
<description><?php echo Zira\Helper::html($description) ?></description>
<?php if (!empty($logo)): ?>
<image>
<url><?php echo Zira\Helper::html($logo); ?></url>
<title><?php echo Zira\Helper::html($title); ?></title>
<link><?php echo Zira\Helper::html($url); ?></link>
</image>
<?php endif; ?>
<generator>Zira CMS</generator>
<atom:link href="<?php echo Zira\Helper::html($channel_url) ?>" rel="self" type="application/rss+xml" />
<?php foreach ($items as $item): ?>
<item>
<title><?php echo Zira\Helper::html($item['title']) ?></title>
<link><?php echo Zira\Helper::html($item['url']) ?></link>
<description><?php echo Zira\Helper::html($item['description']) ?></description>
<?php if (!empty($item['image']) && is_array($item['image'])): ?>
<enclosure url="<?php echo Zira\Helper::html($item['image']['url']) ?>" length="<?php echo Zira\Helper::html($item['image']['length']) ?>" type="<?php echo Zira\Helper::html($item['image']['type']) ?>"/>
<?php endif; ?>
<?php if (!empty($item['category'])): ?>
<category><?php echo Zira\Helper::html($item['category']); ?></category>
<?php endif; ?>
<guid isPermaLink="true"><?php echo Zira\Helper::html($item['url']) ?></guid>
<pubDate><?php echo Zira\Helper::html(date('r',$item['date'])) ?></pubDate>
</item>
<?php endforeach; ?>
</channel>
</rss>