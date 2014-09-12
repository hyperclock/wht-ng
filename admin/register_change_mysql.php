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

require_once "../conf_inc.php";
require_once "../errors_inc.php";

session_start();
session_cache_limiter('nocache');

error_reporting($error_reporting);

import_request_variables('p', 'p_');

require_once "../check_correct.php";

if($_SESSION['login'] === "yes") {
    mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
    mysql_select_db($database) or die($error_selectdb);


    $query = "select db, password from users where user='$p_user'";
    $result = mysql_query($query) or die($error_select);

    $row = mysql_fetch_array($result);

    if($row['db'] === "on" && $p_db == "") {
        mysql_select_db("mysql") or die($error_selectdb);

        $query = "delete from user where User='$p_user'";
        mysql_query($query) or die($error_delete);

        $query = "FLUSH PRIVILEGES;";
        mysql_query($query) or die("Cant FLUSH PRIVILEGES");

    }


    if($row['db'] === "" && $p_db == "on") {
        mysql_select_db("mysql") or die($error_selectdb);

        $query = "GRANT USAGE ON *.* TO $p_user@localhost IDENTIFIED BY '$row[password]';";
        mysql_query($query) or die("Cant create user $p_user");

        $query = "FLUSH PRIVILEGES;";
        mysql_query($query) or die("Cant FLUSH PRIVILEGES");

    }

    mysql_select_db($database) or die($error_selectdb);

    $query = "update users  set db='$p_db', db_expday='$p_day', db_expmonth='$p_month', db_expyear='$p_year' where user='$p_user'";
    $result = mysql_query($query) or die($error_update);

}

header("Location:change_properties.php?user=$p_user");
?>

