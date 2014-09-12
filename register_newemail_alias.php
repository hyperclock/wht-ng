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

require_once './conf_inc.php';
require_once './errors_inc.php';
require_once './execute_cmd.php';
require_once './check_posted.php';

session_start();
session_cache_limiter('nocache');

import_request_variables('p', 'p_');

error_reporting($error_reporting);

@mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
@mysql_select_db($database) or die($error_selectdb);



$query = "select domain_id from emails where email='$p_email'";
$result = mysql_query($query) or die($error_select);


$row_emails = mysql_fetch_array($result);


$query = "select user_id from domains where ID='$row_emails[domain_id]'";
$result = mysql_query($query) or die($error_select);


$row_domains = mysql_fetch_array($result);


$query = "select user from users where ID='$row_domains[user_id]'";
$result = mysql_query($query) or die($error_select);


$row_users = mysql_fetch_array($result);


if($row_users['user'] === $_SESSION['user']) {

    if($p_email != "" && $p_alias != "") {

        $exec_cmd = "$valias $p_email -i $p_alias";
        $result = execute_cmd("$exec_cmd");



        if(strstr($result[0], "Error") == false) {
            $query = "insert into email_aliases (ID, email, alias) values('NULL', '$p_email', '$p_alias' )";
            mysql_query($query) or die($error_query);

            header("Location:emails.php");
        } else {
            echo($result[0]);
        }
    }
} else {
    echo("Nice Try");
}
