<?php
function del_dir($dir)
{
    global $conn_id;

    $result = ftp_rawlist($conn_id, $dir);

    if(sizeof($result) != 0) {
        for($i = 0; $i < sizeof($result); $i++) {
            $directory = split("[ ]+", $result[$i], 9);

            if(substr($result[$i], 0, 1) == "-") {
                ftp_delete ($conn_id, $dir . "/" . $directory[sizeof($directory) - 1]);
            } else {
                del_dir($dir . "/" . $directory[sizeof($directory) - 1]);
            }

        }
    }

    if(ftp_rmdir($conn_id, $dir) === TRUE) {
        return TRUE;
    } else {
        return FALSE;
    }

}
