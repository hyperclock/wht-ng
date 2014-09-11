#!/usr/bin/php -q
<?php
$stdin = fopen('php://stdin', 'r');

while(!feof($stdin)) {
    $line = fgets($stdin);
    echo($line);
}

echo("<script type=\"text/javascript\" src=\"http://wht.org/wht/wht_advertise.php?domain="
. $HTTP_SERVER_VARS['argv'][1] . "> </script>");

fclose($stdin);

?>
