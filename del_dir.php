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
