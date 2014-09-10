<?php
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
