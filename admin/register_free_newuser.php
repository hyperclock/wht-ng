<?php
require_once '../conf_inc.php';
require_once '../i18n.php';
require_once '../errors_inc.php';
require_once '../check_posted.php';
require_once '../execute_cmd.php';

session_start();
session_cache_limiter('nocache');

import_request_variables('p', 'p_');
import_request_variables('g', 'g_');

error_reporting($error_reporting);

require_once '../check_correct.php';

if($_SESSION['login'] === "yes") {
    if($p_user != "" && ($p_local_domain != "" || $p_domain != "") && $p_password != "" && $p_confpass != "" && $p_email != "") {
        $p_months = 12;

        if($enable_cgi_free == "on") {
            $p_db = "on";
        }

        if(strlen($p_password) < 8) {
            header("Location:free_newuser.php?error=error_short_password");
            exit;
        }

        mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);

        if($p_db === "on") {
            mysql_select_db("mysql") or die($error_selectdb);

            $query = "select user from user where user='$p_user'";
            $result = mysql_query($query) or die($error_select);

            $db_user = mysql_num_rows($result);

        }

        mysql_select_db($database) or die($error_selectdb);

        $query = "lock tables users write, domains write;";
        $result = mysql_query($query) or die($error_query);

        $query = "select user from users where user='$p_user'";
        $result = mysql_query($query) or die($error_select);

        $check_user = execute_cmd("finger \"$p_user\"");

        if(mysql_num_rows($result) == 0 && sizeof($check_user) < 2 && $db_user == 0) {

            if($p_domain != "") {
                $domain_insert = $p_domain;
                $zone = $p_domain;
            } else {
                $domain_insert = $p_local_domain . "." . $p_sel_domain;
                $zone = $p_sel_domain;
                $subdomain = $p_local_domain;
            }

            $query = "select domain from domains where domain='$domain_insert';";
            $result = mysql_query($query) or die($error_select);

            if(mysql_num_rows($result)==0) {
            
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
                
                $expyear += 100;


                $quota = $p_quota * 1024 + 100;

                if($p_db === "on") {
                    $query = "insert into users (ID, user, password, quota, email, db, db_expday, db_expmonth, db_expyear, status, timestamp) values(NULL, '$p_user', '$p_password', '$quota', '$p_email', '$p_db', '$expday', '$expmonth', '$expyear', '1', '$timestamp')";
                    mysql_query($query) or die($error_insert);
                } else {
                    $query = "insert into users (ID, user, password, quota, email, db, status, timestamp) values(NULL, '$p_user', '$p_password', '$quota', '$p_email', '$p_db', '1', '$timestamp')";
                    mysql_query($query) or die($error_insert);
                }

                $query = "select ID from users where user='$p_user'";
                $result = mysql_query($query) or die($error_select);

                while($row = mysql_fetch_array($result)) {
                    $res_insert[] = $row;
                }

                $user_id = $res_insert[0]['ID'];

                if($p_domain != "") {
                    $domain_insert = $p_domain;
                    $zone = $p_domain;
                } else {
                    $domain_insert = $p_local_domain . "." . $p_sel_domain;
                    $zone = $p_sel_domain;
                    $subdomain = $p_local_domain;
                }

                $quota_d = $quota - 100;

                $query = "insert into domains (ID, user_id, domain, subdomain, zone, num_emails, script, quota, traffic, day, month, year, expday, expmonth, expyear, free, category, enable, status, timestamp) values(NULL, '$user_id', '$domain_insert', '$subdomain', '$zone', '$p_num_emails', '$p_script', '$quota_d', '$p_traffic', '$day', '$month', '$year', '$expday', '$expmonth', '$expyear', 'y', '$p_category', 'y', '1', '$timestamp')";

                mysql_query($query) or die($error_insert);

                $query = "unlock tables;";
                mysql_query($query) or die($error_query);

                $quota_soft = $quota;
                $quota_hard = $quota_soft + 20;

                $passencrypt = crypt($p_password, $p_password);
                $exec_cmd = "$addusercmd -m -d $userhomedir/$p_user -p $passencrypt $p_user -s /bin/bash";
                $result_exec = execute_cmd("$exec_cmd");

                $exec_cmd = "$chgrpcmd $httpd_group ~$p_user";
                $result_exec = execute_cmd("$exec_cmd");

                $exec_cmd = "$chmod 755 ~$p_user";
                $result_exec = execute_cmd("$exec_cmd");

                if($email_home === "vpopmail") {
                    $exec_cmd = "$vadddomain $domain_insert $p_password";
                } else {
                    $exec_cmd = "$vadddomain -u $p_user $domain_insert $p_password";
                }
                
                execute_cmd("$exec_cmd");

                $exec_cmd = "$setquotacmd -u $p_user $quota_soft $quota_hard 0 0 -a $partition_used";
                execute_cmd("$exec_cmd");

                $ftp_server_ip = "127.0.0.1";

                $conn_id = ftp_connect($ftp_server_ip, 21, 5); 

                // login with username and password
                $login_result = ftp_login($conn_id, $p_user, $p_password); 

                // check connection
                if ((!$conn_id) || (!$login_result)) { 
                    echo "FTP connection has failed!";
                    echo "Attempted to connect to $ftp_server_ip for user $row_user[user]"; 
                    die; 
                } else {
                    ftp_mkdir ($conn_id, "$domain_insert");
                    ftp_put($conn_id, "$domain_insert/index.html", "$DocumentRoot/$version/templates/index.html", FTP_ASCII);

                    if($script === 1)
                        ftp_mkdir($conn_id, $domain_insert . "_cgi-bin");
                }

                ftp_close($conn_id); 	

                require_once '../unsetcookies.php';

                echo("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=ISO-8859-1" />
<link rel="stylesheet" type="text/css" href="../css/<?php echo($stylesheet); ?>/style.css" />
</head>
<body>
<div>
<?php
echo _("Free account with the following features is created:");
?>
<br />
<br />
<table cellpadding="2" cellspacing="2" margin-left="auto"
style="width: 100%;" margin-right="0px">
<tbody>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("User"); ?>:
</td>
<td valign="bottom" width="40%" style="text-align: left;"><?php echo($p_user); ?>
</td>
</tr>
<tr>
<td valign="bottom" align="right"><?php echo _("Contact email"); ?>:
</td>
<td valign="bottom"><?php echo($p_email); ?>
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("MySQL"); ?>:
</td>
<td valign="bottom" width="40%"><?php if($db===1) echo("yes"); else echo("no"); ?>
</td>
</tr>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Domain name"); ?>:
</td>
<td valign="bottom" width="40%"><?php if($p_local_domain!="") echo($p_local_domain.".".$p_sel_domain); else echo($p_domain); ?>
</td>
</tr>
<?php
                if($enable_qmail==="on") {
?>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Email accounts"); ?>:
</td>
<td valign="bottom" width="40%"><?php echo($p_num_emails); ?>
</td>
</tr>
<?php
                }
?>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("PHP and CGI"); ?>:
</td>
<td valign="bottom" width="40%"><?php if($script===1) echo("yes"); else echo("no"); ?>
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Months to host the site"); ?>:
</td>
<td valign="top" width="40%">                            
<?php echo($p_months); ?>
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Traffic"); ?>:
</td>
<td valign="bottom" style="width: 40%;"><?php echo($p_traffic); ?>  <?php echo _("Mbytes per month."); ?>
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Hard disk usage"); ?>:
</td>
<td valign="bottom" style="width: 40%;"><?php echo($p_quota); ?>  <?php echo _("Mbytes"); ?>
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Price"); ?>:
</td>
<td valign="bottom" style="width: 40%;"> $<?php echo($debit); ?>
</td>
</tr>             
</tbody>                          
</table>
</div>
</body>
</html>
<?php
            } else {
                $query = "unlock tables;";
                mysql_query($query) or die($error_query);

                require_once '../setcookies.php';

                header("Location:free_newuser.php?error=error_same_domain");
                exit;
            }
        } else {
            $query = "unlock tables;";
            mysql_query($query) or die($error_query);

            require_once '../setcookies.php';

            header("Location:free_newuser.php?error=error_same_user");
            exit;
        }
    } else {
        require_once '../setcookies.php';

        header("Location:free_newuser.php?error=error_fill");
        exit;
    }
}
