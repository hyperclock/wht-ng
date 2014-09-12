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

function copy_directory($ftp_conn_id, $source, $destination)
{
    global $userhomedir;
    global $ftp_server;
    static $copied_dirs;

    if($ftp_server === "proftpd") {
        $www = "/www";
    } else {
        $www = "";
    }

    for($i = 0; $i < sizeof($copied_dirs); $i++) {
    
        if($copied_dirs[$i] === $source || "/" . $$copied_dirs[$i] === $source) {
            return(0);
        }

    }

    /*
    foreach ($copied_dirs as $key => $value)
        {
        if($value===$source || "/".$value===$source)
            return(0);

        }
    */
    
    $directory_array  = explode("/", $source);
    $directory_name = $directory_array[sizeof($directory_array)-1];

    ftp_mkdir($ftp_conn_id, $destination . "/" . $directory_name);
    $copied_dirs[] = "/" . $destination . "/" . $directory_name;

    $paste_files = ftp_nlist($ftp_conn_id, $source);

    if(sizeof($paste_files) == 1 && $paste_files[0] == "") {
        return(0);
    }

    for($i = 0; $i < sizeof($paste_files); $i++) {
    
        $paste_files_clean = explode("/", $paste_files[$i]);

        if(!is_dir($userhomedir . "/" . $_SESSION['user'] . $www . $paste_files[$i])
        && ($userhomedir . "/" . $_SESSION['user'] . $www . $paste_files[$i] === "."
        || $userhomedir. "/" . $_SESSION['user'] . $www . $paste_files[$i] === "..")) {
        
        // Do nothing
        
        } elseif(!is_dir($userhomedir . "/" . $_SESSION['user'] . $www . $paste_files[$i])) {

            ftp_put($ftp_conn_id, $destination."/".$directory_name."/".$paste_files_clean[sizeof($paste_files_clean)-1], $userhomedir."/".$_SESSION[user].$www.$paste_files[$i], FTP_BINARY);
        } else {
        
            copy_directory($ftp_conn_id, $paste_files[$i], $destination."/".$directory_name);
        }
    }
}

?>
