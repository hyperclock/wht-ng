<?php
require_once '../conf_inc.php';
require_once '../i18n.php';
require_once '../errors_inc.php';
require_once '../execute_cmd.php';
require_once '../check_posted.php';

session_start();
session_cache_limiter('nocache');

if($_SESSION['login'] === "yes") {
    import_request_variables('p', 'p_');

    require_once '../check_correct.php';

    error_reporting($error_reporting);


    if(IsSet($p_traffic) && IsSet($p_year) && IsSet($p_month) && IsSet($p_year)) {

        @mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
        @mysql_select_db($database) or die($error_selectdb);

        $query = "select subdomain, zone, script, ssl, user_id, months, quota, expday, expmonth, expyear, free from domains where domain='$p_domain';";
        $result = mysql_query($query) or die($error_select);

        $row = mysql_fetch_array($result);

        $query = "select user, quota from users where ID='$row[user_id]';";
        $result = mysql_query($query) or die($error_select);

        $row_user = mysql_fetch_array($result);

        if($p_free == "on") {
            $free_y = "y";
        }

        if($p_script == "on") {
            $script = 1;
        } else {
            $script = 0;
        }


        $year_c = $p_year - $row['expyear'];
        $month_c = $p_month - $row['expmonth'];
        $day_c = $p_day - $row['expday'];

        $months = 0;

        if($day_c > 15) {
            $months++;
        }
        
        if($day_c < (-15)) {
            $months--;
        }

        $months += $year_c * 12;

        $months += $month_c;

        $months = $row['months'] + $months;

        $quota = $p_quota;

        if($p_script != $row['script'] || $free_y != $row['free']) {
            $query = "insert into deleted (ID, domain, subdomain, zone, modified) values(NULL, '$p_domain', '$row[subdomain]', '$row[zone]', 'y');";
            mysql_query($query) or die($error_insert);

            $query = "update domains set num_emails='$p_num_emails', script='$p_script', ssl='$p_ssl', months='$months', quota='$quota', traffic='$p_traffic', expday='$p_day', expmonth='$p_month', expyear='$p_year', free='$free_y', domaincheck=NULL, category='$p_category' where domain='$p_domain';";

        } else {
            $query = "update domains set num_emails='$p_num_emails', script='$p_script', ssl='$p_ssl', months='$months', quota='$quota', traffic='$p_traffic', expday='$p_day', expmonth='$p_month', expyear='$p_year', category='$p_category' where domain='$p_domain';";
        }

        $result = mysql_query($query) or die($error_update);

        if($p_quota != $row['quota']) {
            $quota_soft = $p_quota - $row['quota'];
            $quota_soft = $row_user['quota'] + $quota_soft;
            $quota_hard = $quota_soft + 20;

            $exec_cmd = "$setquotacmd -u $row_user[user] $quota_soft $quota_hard 0 0 -a $partition_used";
            execute_cmd("$exec_cmd");

            $query = "update users set quota='$quota_soft' where ID='$row[user_id]'";
            $result = mysql_query($query) or die($error_update);

        }


        if($row['script'] !== "on" && $p_script === "on") {

            $query = "select user, password from users where ID='$row[user_id]';";
            $result = mysql_query($query) or die($error_select);

            $row = mysql_fetch_array($result);

            $ftp_server_ip = "127.0.0.1";

            $conn_id = ftp_connect($ftp_server_ip, 21, 5);

            $login_result = ftp_login($conn_id, $row['user'], $row['password']); 

            if ((!$conn_id) || (!$login_result)) { 
                echo "FTP connection has failed!";
                echo "Attempted to connect to $ftp_server_ip for user $row[user]"; 
                die; 
            } else  {
                ftp_mkdir ($conn_id, $p_domain . "_cgi-bin");
            }
            
        ftp_close($conn_id); 
        }

    }
}
header("Location:modify_domain.php?domain=$p_domain");
?>
