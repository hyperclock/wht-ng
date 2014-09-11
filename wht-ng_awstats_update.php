#!/usr/bin/php -q
<?php
require_once './conf_inc.php';
require_once './errors_inc.php';

error_reporting($error_reporting);

if($enable_awstats === "on") {
    mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
    mysql_select_db($database) or die($error_selectdb);

    $query = "select domain from domains where user_id!='1';";
    $result = mysql_query($query) or die($error_select);

    if(mysql_num_rows($result) != 0) {
        while($row = mysql_fetch_array($result)) {
            system("$awstats_update -config=$row[domain] -update");
        }
    }
}
?>
