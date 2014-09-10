<?php
require_once '../conf_inc.php';
require_once '../errors_inc.php';

session_start();
session_cache_limiter('nocache');

error_reporting($error_reporting);

import_request_variables('p', 'p_');


if($_SESSION['login'] === "yes") {
    mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
    mysql_select_db($database) or die($error_selectdb);

    $timestamp = time();

    $today = getdate();
    $year = $today['year'];
    $month = $today['mon'];
    $day = $today['mday'];


    if($month === 2 && $day > 28) {
        $expday = 28;
    } else {
        $expday = $day;
    }
    
    $expmonth = $month + $p_months;

    if($expmonth > 12) {
        $expyear = $year + (int)(($expmonth - 1) / 12);
        $expmonth = $expmonth - 12 * (int)(($expmonth - 1) / 12);
    } else {
        $expyear = $year;
    }

    $query = "update users set db='on', db_expday='$expday', db_expmonth='$expmonth', db_expyear='$expyear' where user='$p_user'";
    $result = mysql_query($query) or die($error_update);

}

header("Location:change_properties.php?user=$p_user");
?>
