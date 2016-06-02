<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach($urls as $index=>$url): ?>
<url>
<loc><?php echo Zira\Helper::html($url) ?></loc>
<changefreq>weekly</changefreq>
<priority>0.8</priority>
</url>
<?php endforeach; ?>
</urlset>
        
