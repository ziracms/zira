<?php
$languages = Zira\Config::get('languages');
if (!empty($languages) && count($languages)>1) {
echo '<ul id="language-switcher">';
    Zira\Helper::setAddingLanguageToUrl(false);
    foreach($languages as $language) {
        $url = Zira\Helper::html($language);
        $class = '';
        if ($language == Zira\Config::get('language')) $url='/';
        if ($language == Zira\Locale::getLanguage()) $class=' class="active"';
        echo '<li><a href="'.Zira\Helper::url($url).'"'.$class.'>'.Zira\Helper::html(t(ucfirst($language))).'</a></li>';
    }
    Zira\Helper::setAddingLanguageToUrl(true);
echo '</ul>';
}
