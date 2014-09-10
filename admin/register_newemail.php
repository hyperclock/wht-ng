<?php
require_once '../conf_inc.php';
require_once '../errors_inc.php';
require_once '../execute_cmd.php';
require_once '../check_posted.php';

session_start();
session_cache_limiter('nocache');

import_request_variables('p', 'p_');

error_reporting($error_reporting);

if(IsSet($p_email)) {
    @mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
    @mysql_select_db($database) or die($error_selectdb);

    if($p_email != "" && $p_password != "") {

        $exec_cmd = "$vadduser $p_email@$p_domain $p_password -q $email_quota";
        $result = execute_cmd("$exec_cmd");



        if(strstr($result[0], "Error") == false) {
            $query="insert into emails (ID, domain_id, email, password) values('NULL', '$p_domain_id', '$p_email@$p_domain', '$p_password')";
            mysql_query($query) or die($error_insert);

            echo("<script type=\"text/javascript\"> window.opener.num($p_num); window.close(); </script>");
        } else {
            echo($result[0]);
        }
    }
}
