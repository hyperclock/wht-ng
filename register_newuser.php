<?php
require_once './conf_inc.php';
require_once './errors_inc.php';
require_once './execute_cmd.php';
require_once './check_posted.php';

session_start();
session_cache_limiter('nocache');

import_request_variables('p', 'p_');
import_request_variables('g', 'g_');

error_reporting($error_reporting);

require_once './check_correct.php';

if($p_hidden === "newuser") {
    if($p_user != "" && ($p_local_domain != "" || $p_domain != "")
    && $p_password != "" && $p_confpass != "" && $p_email != ""
    && $p_traffic >= $inittraffic && $p_quota >= $initquota) {
        if(strlen($p_password) < 8) {
            header("Location:newuser.php?error=error_short_password");
            exit;
        }

        $p_emails = (int)$p_emails;
        $p_months = (int)$p_months;
        $p_traffic = (int)$p_traffic;
        $p_quota = (int)$p_quota;


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


            if(mysql_num_rows($result) == 0) {

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

                $quota = $p_quota * 1024 + 100;


                if($p_db === "on") {
                    $query = "insert into users (ID, user, password, quota, email, db, db_expday, db_expmonth, db_expyear, timestamp) values(NULL, '$p_user', '$p_password', '$quota', '$p_email', '$p_db', '$expday', '$expmonth', '$expyear', '$timestamp')";
                    mysql_query($query) or die($error_insert);
                } else {
                    $query = "insert into users (ID, user, password, quota, email, db, timestamp) values(NULL, '$p_user', '$p_password', '$quota', '$p_email', '$p_db', '$timestamp')";
                    mysql_query($query) or die($error_insert);
                }

                $query = "select ID from users where user='$p_user'";
                $result = mysql_query($query) or die($error_select);

                while($row = mysql_fetch_array($result)) {
                    $res_insert[] = $row;
                }


                $user_id = $res_insert[0]['ID'];

                if($p_script == "on") {
                    $script = 1;
                } else {
                    $script = 0;
                }
                
                if($p_db == "on") {
                    $db = 1;
                } else {
                    $db = 0;
                }


                $debit = $price * $p_months + $priceemail * $p_num_emails * $p_months
                + $pricescript * $p_months * $script + $pricedb * $p_months * $db
                + $p_months * ($priceextratraffic * ($p_traffic - $inittraffic))
                + $p_months * ($priceextraquota * ($p_quota - $initquota));
                
                $debit=round($debit, 2);

                if(!ereg("[.]{1}", $debit)) {
                    $debit = $debit . ".";
                }
                if(!ereg("[.]{1}[0-9]{2}", $debit)) {
                    $debit = $debit . "0";
                }
                if(!ereg("[.]{1}[0-9]{2}", $debit)) {
                    $debit = $debit . "0";
                }


                if($p_domain != "") {
                    $domain_insert = $p_domain;
                    $zone = $p_domain;
                } else {
                    $domain_insert = $p_local_domain . "." . $p_sel_domain;
                    $zone = $p_sel_domain;
                    $subdomain = $p_local_domain;
                }

                $quota_d = $quota - 100;

                $query = "insert into domains (ID, user_id, domain, subdomain, zone, num_emails, script, ssl, months, quota, traffic, debit, day, month, year, expday, expmonth, expyear, enable, domaincheck, timestamp) values(NULL, '$user_id', '$domain_insert', '$subdomain', '$zone', '$p_num_emails', '$p_script', '$p_ssl', '$p_months', '$quota_d', '$p_traffic', '$debit', '$day', '$month', '$year', '$expday', '$expmonth', '$expyear', 'y', '1', '$timestamp')";

                mysql_query($query) or die($error_insert);

                $query = "unlock tables;";
                mysql_query($query) or die($error_query);

                require_once './unsetcookies.php';

                echo("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>");
                
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=ISO-8859-1" />
</head>
<body>
<div>
<?php
    include_once './templates/header.php';
?>

To pay for the following features:
<br />
<br />
<table cellpadding="2" cellspacing="2" margin-left="auto"
style="width: 100%;" margin-right="0px">
<tbody>
<tr>
<td valign="bottom" width="40%" align="right">user<br />
</td>
<td valign="bottom" width="40%" style="text-align: left;"><?php echo($p_user); ?><br />
</td>
</tr>
<tr>
<td valign="bottom" align="right">contact email<br />
</td>
<td valign="bottom"><?php echo($p_email); ?><br />
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right">use mysql<br />
</td>
<td valign="bottom" width="40%"><?php if($db===1) echo("yes"); else echo("no"); ?><br />
</td>
</tr>
</tr>
<tr>
 </tr>
<?php
                if($enable_qmail === "on") {
?>
<tr>
<td valign="bottom" width="40%" align="right">email accounts<br />
</td>
<td valign="bottom" width="40%"><?php echo($p_num_emails); ?><br />
</td>
</tr>
<?php
                }
?> 

<tr>
<td valign="bottom" width="40%" align="right">use php and CGI<br />
</td>
<td valign="bottom" width="40%"><?php if($script===1) echo("yes"); else echo("no"); ?><br />
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right">months to host my site<br />
</td>
<td valign="top" width="40%">                            
<?php echo($p_months); ?>
<br />
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right">traffic<br />
</td>
<td valign="bottom" style="width: 40%;"><?php echo($p_traffic); ?>  Mbytes per month
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right">disk usage<br />
</td>
<td valign="bottom" style="width: 40%;"><?php echo($p_quota); ?>  Mbytes
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right">price<br />
</td>
<td valign="bottom" style="width: 40%;"> $<?php echo($debit); ?>
</td>
</tr>             
</tbody>                          
</table>
<?php
                if($testmode === "on") {
?>
click the PayPal button. You will be directed to paypal.com and after paying returned back.
<form action="http://<?php echo($host_name."/".$version); ?>/ipntest.php" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="<?php echo($business); ?>">
<input type="hidden" name="notify_url" value="http://<?php echo($host_name."/".$version); ?>/notify.php">
<input type="hidden" name="return" value="http://<?php echo($host_name."/".$version); ?>/return_newuser.php">
<input type="hidden" name="cancel_return" value="http://<?php echo($host_name."/".$version); ?>/cancel_return.php">
<input type="hidden" name="item_name" value="NewUser">
<input type="hidden" name="amount" value="<?php echo($debit); ?>">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="custom" value="<?php echo("nu".$domain_insert); ?>">
<input type="image" src="noimage" name="submit" alt="PayPal - Test">
</form>
<?php
                } elseif($testmode === "eliteweaver") {
?>
click the PayPal button. You will be directed to paypal.com and after paying returned back.
<form action="http://www.eliteweaver.co.uk/testing/ipntest.php" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="<?php echo($business); ?>">
<input type="hidden" name="notify_url" value="http://<?php echo($host_name."/".$version); ?>/notify.php">
<input type="hidden" name="return" value="http://<?php echo($host_name."/".$version); ?>/return_newuser.php">
<input type="hidden" name="cancel_return" value="http://<?php echo($host_name."/".$version); ?>/cancel_return.php">
<input type="hidden" name="item_name" value="NewUser">
<input type="hidden" name="amount" value="<?php echo($debit); ?>">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="custom" value="<?php echo("nu".$domain_insert); ?>">
<input type="image" src="http://images.paypal.com/images/x-click-but01.gif" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
</form>
<?php
                } else {
?>
click the PayPal button. You will be directed to paypal.com and after paying returned back.
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="<?php echo($business); ?>">
<input type="hidden" name="notify_url" value="http://<?php echo($host_name."/".$version); ?>/notify.php">
<input type="hidden" name="return" value="http://<?php echo($host_name."/".$version); ?>/return_newuser.php">
<input type="hidden" name="cancel_return" value="http://<?php echo($host_name."/".$version); ?>/cancel_return.php">
<input type="hidden" name="item_name" value="NewUser">
<input type="hidden" name="amount" value="<?php echo($debit); ?>">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="custom" value="<?php echo("nu".$domain_insert); ?>">
<input type="image" src="http://images.paypal.com/images/x-click-but01.gif" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
</form>
<?php
                }
                include_once './templates/footer.php';
?>
</div>
</body>
</html>
<?php

            } else {
                $query="unlock tables;";
                mysql_query($query) or die($error_query);

                require_once './setcookies.php';

                header("Location:newuser.php?error=error_same_domain");
                exit;
            }
        } else {
            $query = "unlock tables;";
            mysql_query($query) or die($error_query);

            require_once './setcookies.php';

            header("Location:newuser.php?error=error_same_user");
            exit;
        }
    } else {
        require_once './setcookies.php';

        header("Location:newuser.php?error=error_fill");
        exit;
    }
}
