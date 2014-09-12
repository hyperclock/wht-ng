#!/usr/bin/php -q
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

echo("type your domain name (where WHT-NG is installed): ");

$version = "wht-ng";

$stdin = fopen('php://stdin', 'r');

$domain_name = fgets($stdin);

$domain_name = substr($domain_name, 0, strlen($domain_name) - 1);

echo("CGI directory (usually /var/www/cgi-bin): ");

$cgi_dir = fgets($stdin);

$cgi_dir = substr($cgi_dir, 0, strlen($cgi_dir) - 1);


fclose($stdin);


$file = "cgi-bin/wht_handler.cgi.c";
$fp = fopen($file, "r");

while(!feof($fp)) {
    $line = fgets($fp, 1024);

    if($line == "//wht\n") {
        $output .= "printf(\"<script type=\\\"text/javascript\\\" src=\\\"http://$domain_name/$version/wht_advertise.php?domain=\");\n";
        $output .= "printf(getenv(\"PATH_TRANSLATED\"));";
        $output .= "\nprintf(\"\\\"> </script>\");";
    } else {
        $output .= $line;
    }
}

fclose($fp);



$file = "cgi-bin/wht_handler.cgi.out.c";

$fp = fopen($file, "w+") or die("Can't open ".$file);

fwrite($fp, $output);

fclose($fp);

system("g++ -o $cgi_dir/wht_handler.cgi cgi-bin/wht_handler.cgi.out.c");
?>
