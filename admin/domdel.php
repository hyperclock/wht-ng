<?php
if($ftp_server === "proftpd") {
    $www = "www/";
} else {
    $www = "";
}

if($g_domdel === "." || $g_domdel === ".." || false !== strstr($g_domdel, "/")) {
    echo("Don't do this!");
} else {
    $query = "select ID, user_id, subdomain, sub, zone, quota from domains where domain='$g_domdel'";
    $result = mysql_query($query) or die($error_select);

    $res_domdel = mysql_fetch_array($result);

    $query = "select user, quota from users where ID='$res_domdel[user_id]'";
    $result_user = mysql_query($query) or die($error_select);

    $res_user = mysql_fetch_array($result_user);

    if($res_user['user'] === "admin") {
        $query = "delete from domains where domain='$g_domdel';";
        mysql_query($query) or die($error_delete);

        require_once '../execute_cmd.php';

        $exec_cmd = "$vdeldomain $g_domdel";
        execute_cmd("$exec_cmd");

    } else {
        if($res_domdel['sub'] != "y" && $res_domdel['subdomain'] == "") {
            $query = "select subdomain from domains where zone='$res_domdel[zone]'";
            $result = mysql_query($query) or die($error_select);

            if(mysql_num_rows($result) > 1) {
                die($error_exist_subdomain);
            }
        }


        $query = "select email from emails where domain_id='$res_domdel[ID]'";
        $result_emails = mysql_query($query) or die($error_select);

        while($row_emails = mysql_fetch_array($result_emails)) {
            $query = "delete from email_aliases where email='$row_emails[email]'";
            mysql_query($query);
        }


        $query = "delete from emails where domain_id='$res_domdel[ID]'";
        mysql_query($query);

        require_once '../execute_cmd.php';

        $exec_cmd = "$vdeldomain $g_domdel";
        execute_cmd("$exec_cmd");

        $domdel_user = $res_user['user'];

        $exec_cmd = "$rmdircmd -rf /home/$domdel_user/$www$g_domdel";
        $result_exec = execute_cmd("$exec_cmd");

        $exec_cmd = "$rmdircmd -rf /home/$domdel_user/$www$g_domdel"."_cgi-bin";
        $result_exec = execute_cmd("$exec_cmd");

        $query = "insert into deleted (ID, domain, subdomain, zone) values(NULL, '$g_domdel', '$res_domdel[subdomain]', '$res_domdel[zone]');";
        mysql_query($query) or die($error_insert);

        $quota_soft = $res_user['quota'] - $res_domdel['quota'];
        $quota_hard = $quota_soft + 20;

        $exec_cmd = "$setquotacmd -u $res_user[user] $quota_soft $quota_hard 0 0 -a $partition_used";
        execute_cmd("$exec_cmd");

        $query = "update users set quota='$quota_soft' where ID='$res_domdel[user_id]'";
        $result = mysql_query($query) or die($error_update);

        $query = "delete from domains where domain='$g_domdel';";
        mysql_query($query) or die($error_delete);
    }
}

?>
