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

function check_url($error_to_get)
{
    global $url_back;

    if(strpos($_SERVER['HTTP_REFERER'], "?")) {
        $url_back = $_SERVER['HTTP_REFERER'] . "&error_spell=$error_to_get";
    } else {
        $url_back = $_SERVER['HTTP_REFERER'] . "?error_spell=$error_to_get";
    }
}

foreach ($HTTP_POST_VARS as $key => $value) {

    If($key === "submit" || $key === "Submit" || $key === "NF" || $key === "Paste") {
        continue;
    }

    $value_escaped = escapeshellcmd($value);

    if($value_escaped != $value) {
    
        check_url("error_not_allowed_char");

        import_request_variables('p', 'p_');
        import_request_variables('g', 'g_');

        if(file_exists('./setcookies.php')) {
            include_once './setcookies.php';
        } else {
            include_once '../setcookies.php';
        }

        header("Location:$url_back");
        echo(" ");

        }


    if($key === "user") {
    
        if(ereg("^[0-9]+", $value)) {
            
            check_url("error_not_allowed_char_u");

            import_request_variables('p', 'p_');
            import_request_variables('g', 'g_');

            if(file_exists('./setcookies.php')) {
                include_once './setcookies.php';
            } else {
                include_once '../setcookies.php';
            }

            header("Location:$url_back");
            echo(" ");

            }
        }


    if($key === "user" || $key === "local_domain") {
        
        if(strpos($value, "@") !== false || strpos($value, " ") !== false || strpos($value, ".") !== false) {
            
            check_url("error_not_allowed_char_ul");

            import_request_variables('p', 'p_');
            import_request_variables('g', 'g_');

            if(file_exists('./setcookies.php')) {
                include_once './setcookies.php';
            } else {
                include_once '../setcookies.php';
            }


            header("Location:$url_back");
            echo(" ");

            }
        }

    if($key === "domain") {
        
        if(strpos($value, "@") !== false || strpos($value, " ") !== false) {
            
            check_url("error_not_allowed_char_ud");

            import_request_variables('p', 'p_');
            import_request_variables('g', 'g_');

            if(file_exists('./setcookies.php')) {
                include_once './setcookies.php';
            } else {
                include_once '../setcookies.php';
            }

            header("Location:$url_back");
            echo(" ");

            }

        }

}

?>
