<?php
if (!empty($logo) || !empty($title) || !empty($slogan)) {
    echo '<div id="site-logo-wrapper">';
    if (!empty($logo) || !empty($title)) {
        echo '<a id="site-logo" href="'.Zira\Helper::url('').'" title="'.Zira\Helper::html($title).'">';
        if (!empty($logo)) echo '<img src="'.Zira\Helper::html(Zira\Helper::baseUrl($logo)).'" alt="'.Zira\Helper::html($title).'"'.(!empty($logo_size) ? ' width="'.\Zira\Helper::html($logo_size[0]).'" height="'.\Zira\Helper::html($logo_size[1]).'"' : '').' />';
        if (!empty($title)) echo '<span>'.Zira\Helper::html($title).'</span>';
        echo '</a>';
    }
    if (!empty($slogan)) echo '<p id="site-slogan">'.Zira\Helper::html($slogan).'</p>';
    else echo '<p id="site-slogan">&nbsp;</p>';
    echo '</div>';
}