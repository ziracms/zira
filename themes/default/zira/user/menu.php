<?php if (!empty($items)): ?>
<ul id="user-menu" class="<?php if (!empty($class)) echo $class; ?>">
<?php foreach($items as $index=>$item): ?>
<?php if ($index>0): ?>
<li class="menu-item menu-item-separator"></li>
<?php endif; ?>
<li class="menu-item">
<?php $icon = !empty($item['icon']) ? '<span class="'.Zira\Helper::html($item['icon']).'"></span>' : ''; ?>
<?php if (empty($item['dropdown'])): ?>
<a href="<?php echo Zira\Helper::html(Zira\Helper::url($item['url'])) ?>" class="menu-link"><?php echo $icon ?> <?php echo Zira\Helper::html($item['title']) ?></a>
<?php else: ?>
<a href="<?php echo Zira\Helper::html(Zira\Helper::url($item['url'])) ?>" id="user-menu-<?php echo $index ?>-dropdown"  class="menu-link dropdown-toggle" data-toggle="dropdown"><?php echo $icon ?> <?php echo Zira\Helper::html($item['title']) ?> <span class="caret"></span></a>
<ul class="dropdown-menu" aria-labelledby="user-menu-<?php echo $index ?>-dropdown">
<?php foreach($item['dropdown'] as $_item): ?>
<?php if (array_key_exists('type', $_item) && $_item['type']=='separator'): ?>
<li role="separator" class="divider"></li>
<?php else: ?>
<?php $_icon = !empty($_item['icon']) ? '<span class="'.Zira\Helper::html($_item['icon']).'"></span>' : ''; ?>
<li><a href="<?php echo Zira\Helper::html(Zira\Helper::url($_item['url'])) ?>"><?php echo $_icon ?> <?php echo Zira\Helper::html($_item['title']) ?></a></li>
<?php endif; ?>
<?php endforeach; ?>
</ul>
<?php endif; ?>
</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>