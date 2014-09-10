#!/usr/bin/php -q
<?php
error_reporting($error_reporting);

$arg = $HTTP_SERVER_VARS['argv'];

$size = 0;

$dir = $arg[1];

function size_dir($dir)
{
    $size = 0;

    if($directory = opendir($dir)) {
        while(($file = readdir($directory)) !== false) {
            if($file === "." || $file === "..") {
                continue;
            }
            
            if(is_dir($dir . "/" . $file)) {
                $size += size_dir($dir . "/" . $file);
            } else {
                $size += filesize($dir . "/" . $file);
            }
        }
        closedir($directory);
    }

    return $size;

}

$size = size_dir($dir);

echo($size);

?>
