<?php
require_once '../conf_inc.php';
require_once "../errors_inc.php";
require_once "../execute_cmd.php";

error_reporting($error_reporting);

import_request_variables('g', 'g_');

@mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
@mysql_select_db($database) or die($error_selectdb);

$query = "select domain, user_id, free, status from domains where ID='$g_conf'";
$result = mysql_query($query) or die($error_query);

$row_dom = mysql_fetch_array($result);

if($row_dom['free'] === 'y' && $row_dom['status'] != 1) {
    $custom = $row_dom['domain'];

    $query = "update domains set domaincheck=NULL, status='1' where domain='$custom';";
    $result = mysql_query($query) or die($error_query);


    $query = "update users set status='1' where ID='$row_dom[user_id]';";
    $result = mysql_query($query) or die($error_query);

    $query = "select user, password, quota from users where ID='$row_dom[user_id]'";
    $result = mysql_query($query) or die($error_query);

    $row_user = mysql_fetch_array($result);

    $quota_soft = $row_user['quota'];
    $quota_hard = $quota_soft + 20;

    $passencrypt = crypt($row_user['password'], $row_user['password']);
    $exec_cmd = "$addusercmd -m -d $userhomedir/$row_user[user] -p $passencrypt $row_user[user] -s /bin/bash";
    $result_exec = execute_cmd("$exec_cmd");

    $exec_cmd = "$chgrpcmd $httpd_group ~$row_user[user]";
    $result_exec = execute_cmd("$exec_cmd");

    $exec_cmd = "$chmod 750 ~$row_user[user]";
    $result_exec = execute_cmd("$exec_cmd");

    if($email_home === "vpopmail") {
        $exec_cmd = "$vadddomain $custom $row_user[password]";
    } else {
        $exec_cmd = "$vadddomain -u $row_user[user] $custom $row_user[password]";
    }
    
    execute_cmd("$exec_cmd");


    $exec_cmd = "$setquotacmd -u $row_user[user] $quota_soft $quota_hard 0 0 -a $partition_used";
    execute_cmd("$exec_cmd");

    $ftp_server_ip = "127.0.0.1";

    $conn_id = ftp_connect($ftp_server_ip, 21, 5);

    // login with username and password
    $login_result = ftp_login($conn_id,$row_user['user'],$row_user['password']); 

    // check connection
    if((!$conn_id) || (!$login_result)) { 
        echo "FTP connection has failed!";
        echo "Attempted to connect to $ftp_server_ip for user $row_user[user]"; 
        die; 
    } else {
        ftp_mkdir($conn_id, "$custom");
        ftp_put($conn_id, "$custom/index.html", "$DocumentRoot/$version/templates/index.html", FTP_ASCII);
    }

    ftp_close($conn_id);

    require_once '../templates/return_newuser.php';
} else {
    require_once '../templates/already_registered.php';
}
?>
