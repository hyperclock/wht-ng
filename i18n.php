<?php
if(file_exists('./include/class/gettext.php')) {
    require_once './include/class/gettext.php';
} else {
    require_once '../include/class/gettext.php';
}

// <<>>    Language characteristics    <<>>

$languages['Bulgarian']['charset'] = 'koi8-r';
$languages['Bulgarian']['code'] = 'bg_BG';
$languages['Bulgarian']['lang'] = 'bg';

$languages['English']['charset'] = 'iso-8859-1';
$languages['English']['code'] = 'en';
$languages['English']['lang'] = 'en';

$languages['Spanish']['charset'] = 'iso-8859-1';
$languages['Spanish']['code'] = 'es_ES';
$languages['Spanish']['lang'] = 'es';

if(IsSet($HTTP_COOKIE_VARS['lang'])) {
    if(IsSet($HTTP_POST_VARS['language'])) {
        $languageSel = $HTTP_POST_VARS['language'];
    } else {
        $languageSel = $HTTP_COOKIE_VARS['lang'];
    }
} elseif(IsSet($HTTP_POST_VARS['language'])) {
    $languageSel = $HTTP_POST_VARS['language'];
} else {
    $languageSel = $languageDefault;
}

$langObject = new Language($languages[$languageSel]['lang'],
$languages[$languageSel]['charset'], $languages[$languageSel]['code']);

$lang = $langObject->lang;
$charset = $langObject->charset;
$stylesheet = "darkred";

header('Content-Type: text/html; charset=' . $charset);

?>
