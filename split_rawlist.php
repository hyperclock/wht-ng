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
