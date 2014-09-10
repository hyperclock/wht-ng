<?php
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

