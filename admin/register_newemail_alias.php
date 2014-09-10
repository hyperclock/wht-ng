<?php
require_once '../conf_inc.php';
require_once '../errors_inc.php';
require_once '../execute_cmd.php';
require_once '../check_posted.php';

session_start();
session_cache_limiter('nocache');

import_request_variables('p', 'p_');

error_reporting($error_reporting);


@mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
@mysql_select_db($database) or die($error_selectdb);

if($_SESSION['login'] === "yes") {

    if($p_email != "" && $p_alias != "") {

        $exec_cmd = "$valias $p_email -i $p_alias";
        $result = execute_cmd("$exec_cmd");

        if(strstr($result[0], "Error") == false) {
            $query = "insert into email_aliases (ID, email, alias) values('NULL', '$p_email', '$p_alias' )";
            mysql_query($query) or die($error_query);

            echo("<script type=\"text/javascript\"> window.opener.num($p_num); window.close(); </script>");
        } else {
            echo($result[0]);
        }
    }
} else {
    echo("Nice Try");
}
