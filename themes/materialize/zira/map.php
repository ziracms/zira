<div id="sitemap-wrapper">
<nav>
<ul class="menu">
<li class="menu-item odd"><a href="<?php echo Zira\Helper::url('') ?>" title="<?php echo Zira\Helper::html(t('Home')) ?>" class="menu-link"><span class="glyphicon glyphicon-home"></span> <?php echo Zira\Helper::html(t('Home')) ?></a></li>
<?php $co=1; ?>
<?php if (!empty($categories)): ?>
<?php foreach($categories as $category): ?>
<?php $parts = explode('/',$category->name); ?>
<li class="menu-item <?php echo $co++%2==0 ? 'odd' : 'even'; ?><?php if (count($parts)>1) echo  ' menu-item-offset menu-item-offset-'.(count($parts)-1); ?>">
<a href="<?php echo Zira\Helper::url(Zira\Helper::html(Zira\Page::generateCategoryUrl($category->name))) ?>" title="<?php echo Zira\Helper::html(t($category->title)) ?>" class="menu-link">
<span class="<?php echo (count($parts)>1) ? 'glyphicon glyphicon-chevron-right' : 'glyphicon glyphicon-link'; ?>"></span> <?php echo Zira\Helper::html(t($category->title)) ?>
</a>
</li>
<?php endforeach; ?>
<?php endif; ?>
<li class="menu-item <?php echo $co++%2==0 ? 'odd' : 'even'; ?>"><a href="<?php echo Zira\Helper::url('search') ?>" title="<?php echo Zira\Helper::html(t('Search')) ?>" class="menu-link"><span class="glyphicon glyphicon-search"></span> <?php echo Zira\Helper::html(t('Search')) ?></a></li>
<li class="menu-item <?php echo $co++%2==0 ? 'odd' : 'even'; ?>"><a href="<?php echo Zira\Helper::url('contact') ?>" title="<?php echo Zira\Helper::html(t('Contacts')) ?>" class="menu-link"><span class="glyphicon glyphicon-envelope"></span> <?php echo Zira\Helper::html(t('Contacts')) ?></a></li>
</ul>
</nav>
</div>