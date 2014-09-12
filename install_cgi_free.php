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

echo("type your domain name (where WHT is installed): ");

$version = "wht";

$stdin = fopen('php://stdin', 'r');

$domain_name = fgets($stdin);

$domain_name = substr($domain_name, 0, strlen($domain_name) - 1);

fclose($stdin);


$file = "cgi-bin/wht-ng_ext_filter.c";
$fp = fopen($file, "r");

while(!feof($fp)) {
    $line = fgets($fp, 1024);

    if($line == "//wht\n") {
        $output .= "printf(\"<script type=\\\"text/javascript\\\" src=\\\"http://$domain_name/$version/wht-ng_advertise.php?domain=\");\n";
        $output .= "printf(argv[1]);";
        $output .= "\nprintf(\"\\\"> </script>\");";
    } else {
        $output .= $line;
    }
}

fclose($fp);



$file = "cgi-bin/wht-ng_ext_filter.out.c";

$fp = fopen($file, "w+") or die("Can't open " . $file);

fwrite($fp, $output);

fclose($fp);

system("g++ -o /bin/wht-ng_ext_filter cgi-bin/wht-ng_ext_filter.out.c");
?>
