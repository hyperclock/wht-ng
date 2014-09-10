<?php
function directory_list($result)
{
    $i = 0;

    while($result[$i]) {
        $item = split("[ ]+", $result[$i], 9);

        if($result[$i][0] === "d") {
            $directory_list['directory']['permitions'][] = $item[0];
            $directory_list['directory']['uid'][] = $item[2];
            $directory_list['directory']['gid'][] = $item[3];
            $directory_list['directory']['size'][] = $item[4];
            $directory_list['directory']['last_modified'][] = "$item[5] $item[6] $item[7]";
            $directory_list['directory']['name'][] = $item[8];
        } else {
            $directory_list['file']['permitions'][] = $item[0];
            $directory_list['file']['uid'][] = $item[2];
            $directory_list['file']['gid'][] = $item[3];
            $directory_list['file']['size'][] = $item[4];
            $directory_list['file']['last_modified'][] = "$item[5] $item[6] $item[7]";
            $directory_list['file']['name'][] = $item[8];
        }

        $i++;
    }
    return($directory_list);
}
?>
