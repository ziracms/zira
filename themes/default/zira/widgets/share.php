<?php if (isset($url) && isset($title)): ?>
<div class="share-btn-wrapper">
<a class="share-btn vk" href="http://vk.com/share.php?url=<?php echo urlencode($url) ?>&title=<?php echo urlencode($title) ?>&description=&image=" target="_blank" rel="nofollow"></a>
<a class="share-btn fb" href="https://www.facebook.com/sharer.php?src=sp&u=<?php echo urlencode($url) ?>" target="_blank" rel="nofollow"></a>
<a class="share-btn ok" href="https://connect.ok.ru/dk?st.cmd=WidgetSharePreview&st.shareUrl=<?php echo urlencode($url) ?>" target="_blank" rel="nofollow"></a>
<a class="share-btn gp" href="https://plus.google.com/share?url=<?php echo urlencode($url) ?>" target="_blank" rel="nofollow"></a>
<a class="share-btn tw" href="https://twitter.com/intent/tweet?text=<?php echo urlencode($title) ?>&url=<?php echo urlencode($url) ?>" target="_blank" rel="nofollow"></a>
</div>
<?php endif; ?>