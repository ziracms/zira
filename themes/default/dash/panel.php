<?php
if (!empty($panelItems)) {
    echo '<div id="dashpanel-container">'."\r\n";
    echo '<nav class="navbar navbar-default">'."\r\n";
    echo '<div class="navbar-header">';
    echo '<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#dashpanel" aria-expanded="false">';
    echo '<span class="sr-only">'.t('Dashboard').'</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>';
    echo '</button>';
    echo '<a class="navbar-brand" href="http://dro1d.ru" target="_blank"><img src="'.Zira\Helper::assetUrl('images/zira.png').'" width="16" height="16" alt="Zira" class="glyphicon" />Zira</a>';
    echo '</div>'."\r\n";
    echo '<div id="dashpanel" class="dashpanel-wrapper collapse navbar-collapse">'."\r\n";
    $stack = array();
    $stackIndexes = array();
    $stack[]=$panelItems;
    $stackIndexes[]=0;
    while(count($stack)>0){
        $items = $stack[count($stack)-1];
        if ($stackIndexes[count($stackIndexes)-1]==0) {
            if (count($stack)==1) $class='dashpanel-menu nav navbar-nav';
            else $class='dropdown-menu';
            echo str_repeat("\t", count($stack)).'<ul class="'.$class.'">'."\r\n";
        } else {
            echo str_repeat("\t", count($stack)+1).'</li>'."\r\n";
        }
        while(count($items)>$stackIndexes[count($stackIndexes)-1]){
            $item = $items[$stackIndexes[count($stackIndexes)-1]];
            $stackIndexes[count($stackIndexes)-1]++;
            if (isset($item['label'])) $item['label'] = \Zira\Helper::html($item['label']);
            if (!empty($item['icon_class']) && isset($item['label'])) $item['label'] = '<span class="'.$item['icon_class'].'"></span>&nbsp;'.$item['label'];
            if (!empty($item['type']) && $item['type']=='separator') {
                echo str_repeat("\t", count($stack)+1).'<li role="separator" class="divider"></li>'."\r\n";
            } else if (is_array($item['rel'])){
                echo str_repeat("\t", count($stack)+1).'<li><a id="'.$item['id'].'" href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">'.$item['label'].'&nbsp;<span class="caret"></span></a>'."\r\n";
                $stack[]=$item['rel'];
                $stackIndexes[]=0;
                break;
            } else if (empty($item['rel'])) {
                echo str_repeat("\t", count($stack)+1).'<li><a id="'.$item['id'].'" href="javascript:void(0)">'.$item['label'].'</a></li>'."\r\n";
            } else {
                echo str_repeat("\t", count($stack)+1).'<li><a id="'.$item['id'].'" href="'.Zira\Helper::html($item['rel']).'">'.$item['label'].'</a></li>'."\r\n";
            }

        }
        if (count($items)==$stackIndexes[count($stackIndexes)-1]){
            echo str_repeat("\t", count($stack)).'</ul>'."\r\n";
            array_pop($stack);
            array_pop($stackIndexes);
        }
    }

    if (!empty($userMenu)) {
        echo "\t" . '<ul class="nav navbar-nav navbar-right">';
        echo '<li class="dropdown">';
        echo '<a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">' . Zira\User::getProfileName() . '&nbsp;<span class="caret"></span></a>';
        echo '<ul class="dropdown-menu">';
        echo '<li><a href="' . Zira\Helper::url('user/profile') . '"><span class="glyphicon glyphicon-user"></span>&nbsp;' . t('Profile') . '</a></li>';
        echo '<li role="separator" class="divider"></li>';
        echo '<li><a href="' . Zira\Helper::url('user/logout') . '"><span class="glyphicon glyphicon-log-out"></span>&nbsp;' . t('Logout') . '</a></li>';
        echo '</ul>';
        echo '</li>';
        echo '</ul>'."\r\n";
    } else {
        echo "\t" . '<ul class="nav navbar-nav navbar-right">';
        echo '<li>';
        echo '<a href="'.Zira\Helper::url('user/logout').'"><span class="glyphicon glyphicon-log-out"></span> ' . Zira\Locale::t('Logout') . '</a>';
        echo '</li>';
        echo '</ul>'."\r\n";
    }

    echo '</div>'."\r\n";
    echo '</nav>'."\r\n";
    echo '</div>';
}