#!/usr/bin/php -q
<?php
echo("type your domain name (where WHT is installed): ");

$version = "wht";

$stdin = fopen('php://stdin', 'r');

$domain_name = fgets($stdin);

$domain_name = substr($domain_name, 0, strlen($domain_name) - 1);

fclose($stdin);


$file = "cgi-bin/wht_ext_filter.c";
$fp = fopen($file, "r");

while(!feof($fp)) {
    $line = fgets($fp, 1024);

    if($line == "//wht\n") {
        $output .= "printf(\"<script type=\\\"text/javascript\\\" src=\\\"http://$domain_name/$version/wht_advertise.php?domain=\");\n";
        $output .= "printf(argv[1]);";
        $output .= "\nprintf(\"\\\"> </script>\");";
    } else {
        $output .= $line;
    }
}

fclose($fp);



$file = "cgi-bin/wht_ext_filter.out.c";

$fp = fopen($file, "w+") or die("Can't open " . $file);

fwrite($fp, $output);

fclose($fp);

system("g++ -o /bin/wht_ext_filter cgi-bin/wht_ext_filter.out.c");
?>
