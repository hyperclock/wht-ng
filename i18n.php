<?php
/**
*    Web Hosting Toolkit - Next Generation (WHT-NG)
*    Copyright (C) 2014  Jimmy M. Coleman <hyperclock@ok.de>
*    Copyright (C) 2003  Nikolay Ivanov <nivanov@email.com> (GPLv2)
*
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

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
