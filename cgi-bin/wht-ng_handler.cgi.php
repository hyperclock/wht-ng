#!/usr/bin/php
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

require_once '/var/www/html/wht/conf_inc.php';

$path_info = getenv("PATH_INFO");
$path_translated = getenv("PATH_TRANSLATED");

$file = fopen ($path_translated, "r");
if(!$file) {
    echo "<p>Unable to open file.\n";
    exit;
}

while (!feof ($file)) {
    $line = fgets($file, 1024);
    echo($line);
}

fclose($file);

echo("<script type=\"text/javascript\" src=\"http://$host_name/$version/wht-ng_advertise.php?domain=$path_translated\"> </script>");
//echo("<script type=\"text/javascript\">\n");
//require("$DocumentRoot/$version/wht-ng_advertise.php");
//echo("\n</script>");

?>
