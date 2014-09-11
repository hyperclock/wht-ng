#!/usr/bin/php
<?php
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

echo("<script type=\"text/javascript\" src=\"http://$host_name/$version/wht_advertise.php?domain=$path_translated\"> </script>");
//echo("<script type=\"text/javascript\">\n");
//require("$DocumentRoot/$version/wht_advertise.php");
//echo("\n</script>");

?>
