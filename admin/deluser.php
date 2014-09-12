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

require_once '../execute_cmd.php';


$query = "select ID from users where user='$g_deluser'";
$result = mysql_query($query) or die($error_select);

$res_deluser = mysql_fetch_array($result);


$query = "select ID, domain, subdomain, zone from domains where user_id='$res_deluser[ID]'";
$result_domdel = mysql_query($query) or die($error_select);

while($row = mysql_fetch_array($result_domdel)) {
    $res_domdel[] = $row;
}


for($i = 0; $i < sizeof($res_domdel); $i++) {
    $g_domdel = $res_domdel[$i]['domain'];
    $domdel_id = $res_domdel[$i]['ID'];

    $query = "select email from emails where domain_id='$domdel_id'";
    $result_emails = mysql_query($query) or die($error_select);

    while($row_emails = mysql_fetch_array($result_emails)) {
        $query = "delete from email_aliases where email='$row_emails[email]'";
        mysql_query($query);
    }

    $query = "delete from emails where domain_id='$domdel_id'";
    mysql_query($query);

    $exec_cmd = "$vdeldomain $g_domdel";
    execute_cmd("$exec_cmd");

    $domdel_subdomain = $res_domdel[$i]['subdomain'];
    $domdel_zone = $res_domdel[$i]['zone'];

    $query = "insert into deleted (ID, domain, subdomain, zone) values(NULL, '$g_domdel', '$domdel_subdomain', '$domdel_zone');";
    mysql_query($query) or die($error_insert);

    $query = "delete from domains where domain='$g_domdel';";
    mysql_query($query) or die($error_delete);

}

$exec_cmd = "$delusercmd -r $g_deluser";
$res_deluser = execute_cmd("$exec_cmd");

$exec_cmd = "$rmdircmd $crond_spool/$g_deluser";
$res_deluser = execute_cmd("$exec_cmd");

echo($res_deluser[0]);

$query = "delete from users where user='$g_deluser';";
mysql_query($query) or die($error_delete);

mysql_select_db("mysql") or die($error_selectdb);

$query = "select Db from db where User='$g_deluser'";
$result_drop = mysql_query($query) or die($error_select);

while($row_drop = mysql_fetch_array($result_drop)) {
    $query = " DROP DATABASE $row_drop[Db]";
    mysql_query($query) or die("Cant drop database");
}

$query = "delete from db where User='$g_deluser'";
mysql_query($query) or die($error_delete);


$query = "delete from user where User='$g_deluser'";
mysql_query($query) or die($error_delete);

$query = "FLUSH PRIVILEGES;";
mysql_query($query) or die("Cant FLUSH PRIVILEGES");

mysql_select_db($database) or die($error_selectdb);

?>
