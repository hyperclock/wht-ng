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

if(IsSet($p_hidden) && $p_hidden === "newdomain") {

    $p_emails = (int)$p_emails;
    $p_months = (int)$p_months;
    $p_traffic = (int)$p_traffic;
    $p_quota = (int)$p_quota;

    @mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
    @mysql_select_db($database) or die($error_selectdb);

    if($p_local_domain != "" || $p_domain != "" && $p_traffic >= $inittraffic && $p_quota >= $initquota) {
        $p_user = $_SESSION['user'];
        $p_password = $_SESSION['pass'];
        $user_id = $_SESSION['user_id'];

        mysql_select_db($database) or die($error_selectdb);

        $query = "lock tables domains write;";
        $result = mysql_query($query) or die($error_query);


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

        if(mysql_num_rows($result) == 0 && $p_local_domain !== "www") {

            $today = getdate();
            $year = $today['year'];
            $month = $today['mon'];
            $day = $today['mday'];

            if($p_script == "on") {
                $script = 1;
            } else {
                $script = 0;
            }

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

            $debit = $price * $p_months + $priceemail * $p_num_emails * $p_months
            + $pricescript * $p_months * $script
            + $p_months * ($priceextratraffic * ($p_traffic - $inittraffic))
            + $p_months * ($priceextraquota * ($p_quota - $initquota));

            $debit = round($debit, 2);

            if(!ereg("[.]{1}", $debit)) {
                $debit = $debit . ".";
            }
            if(!ereg("[.]{1}[0-9]{2}", $debit)) {
                $debit = $debit . "0";
            }
            if(!ereg("[.]{1}[0-9]{2}", $debit)) {
                $debit = $debit . "0";
            }


            $timestamp = time();
            $quota = $p_quota * 1024;

            $query = "insert into domains (ID, user_id, domain, subdomain, zone, num_emails, script, ssl, months, quota, traffic, debit, day, month, year, expday, expmonth, expyear, enable, domaincheck, timestamp) values(NULL, '$user_id', '$domain_insert', '$subdomain', '$zone', '$p_num_emails', '$p_script', '$p_ssl', '$p_months', '$quota', '$p_traffic', '$debit', '$day', '$month', '$year', '$expday', '$expmonth', '$expyear', 'y', '1', '$timestamp')";

            mysql_query($query) or die($error_insert);

            $query = "unlock tables;";
            mysql_query($query) or die($error_query);


            $ftp_server_ip = "127.0.0.1";

            $conn_id = ftp_connect($ftp_server_ip, 21, 5);

            // login with username and password
            $login_result = ftp_login($conn_id,$p_user,$p_password); 

            // check connection
            if ((!$conn_id) || (!$login_result)) { 
                echo "FTP connection has failed!";
                echo "Attempted to connect to $ftp_server_ip for user $p_user"; 
                die; 
            } else {
                if($p_domain != "") {
                    ftp_mkdir($conn_id, "$p_domain");
                    ftp_put($conn_id, "$p_domain/index.html", "$DocumentRoot/$version/templates/index.html", FTP_ASCII);

                    if($script === 1) {
                        ftp_mkdir ($conn_id, $p_domain."_cgi-bin");
                    }
                } else {
                    ftp_mkdir($conn_id, "$p_local_domain.$p_sel_domain");
                    ftp_put($conn_id, "$p_local_domain.$p_sel_domain/index.html", "$DocumentRoot/$version/templates/index.html", FTP_ASCII);

                    if($script === 1) {
                        ftp_mkdir($conn_id, "$p_local_domain.$p_sel_domain"."_cgi-bin");
                    }
                }
            }

            ftp_close($conn_id); 	


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
<td valign="bottom" width="40%" align="right">your domain name<br />
</td>
<td valign="bottom" width="40%"><?php echo($domain_insert); ?><br />
</td>
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
<td valign="bottom" width="40%"><?php if($script === 1) echo("yes"); else echo("no"); ?><br />
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
<input type="hidden" name="return" value="http://<?php echo($host_name."/".$version); ?>/return_newdomain.php">
<input type="hidden" name="cancel_return" value="http://<?php echo($host_name."/".$version); ?>/cancel_return.php">
<input type="hidden" name="item_name" value="NewDomain">
<input type="hidden" name="amount" value="<?php echo($debit); ?>">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="custom" value="<?php echo("nd".$domain_insert); ?>">
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
<input type="hidden" name="return" value="http://<?php echo($host_name."/".$version); ?>/return_newdomain.php">
<input type="hidden" name="cancel_return" value="http://<?php echo($host_name."/".$version); ?>/cancel_return.php">
<input type="hidden" name="item_name" value="NewDomain">
<input type="hidden" name="amount" value="<?php echo($debit); ?>">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="custom" value="<?php echo("nd".$domain_insert); ?>">
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
<input type="hidden" name="return" value="http://<?php echo($host_name."/".$version); ?>/return_newdomain.php">
<input type="hidden" name="cancel_return" value="http://<?php echo($host_name."/".$version); ?>/cancel_return.php">
<input type="hidden" name="item_name" value="NewDomain">
<input type="hidden" name="amount" value="<?php echo($debit); ?>">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="custom" value="<?php echo("nd".$domain_insert); ?>">
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
            $query = "unlock tables;";
            mysql_query($query) or die($error_query);

            require_once './setcookies.php';

            header("Location:newdomain.php?error=error_same_domain");
            exit;
        }
    } else {
        require_once './setcookies.php';

        header("Location:newdomain.php?error=error_fill");
        exit;
    }
}

?>
