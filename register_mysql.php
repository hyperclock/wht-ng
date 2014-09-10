<?php
require_once './conf_inc.php';
require_once './errors_inc.php';

session_start();
session_cache_limiter('nocache');

error_reporting($error_reporting);

import_request_variables('p', 'p_');


if(IsSet($_SESSION['user'])) {
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

    $debit = $pricedb * $p_months;

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


    $query = "update users set debit='$debit', db_expday='$expday', db_expmonth='$expmonth', db_expyear='$expyear' where user='$_SESSION[user]'";
    $result = mysql_query($query) or die($error_update);

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
<td valign="bottom" width="40%" align="right" ;="">user<br />
</td>
<td valign="bottom" width="40%" style="text-align: left;"><?php echo($_SESSION['user']); ?><br />
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right">use mysql<br />
</td>
<td valign="bottom" width="40%">on<br />
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right">use MySQL for<br />
</td>
<td valign="top" width="40%">                            
<?php echo($p_months); ?> months
<br />
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
<input type="hidden" name="return" value="http://<?php echo($host_name."/".$version); ?>/return_db.php">
<input type="hidden" name="cancel_return" value="http://<?php echo($host_name."/".$version); ?>/cancel_return.php">
<input type="hidden" name="item_name" value="UseMySQL">
<input type="hidden" name="amount" value="<?php echo($debit); ?>">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="custom" value="<?php echo("db".$_SESSION[user]); ?>">
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
<input type="hidden" name="return" value="http://<?php echo($host_name."/".$version); ?>/return_db.php">
<input type="hidden" name="cancel_return" value="http://<?php echo($host_name."/".$version); ?>/cancel_return.php">
<input type="hidden" name="item_name" value="UseMySQL">
<input type="hidden" name="amount" value="<?php echo($debit); ?>">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="custom" value="<?php echo("db".$_SESSIION[user]); ?>">
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
<input type="hidden" name="return" value="http://<?php echo($host_name."/".$version); ?>/return_db.php">
<input type="hidden" name="cancel_return" value="http://<?php echo($host_name."/".$version); ?>/cancel_return.php">
<input type="hidden" name="item_name" value="UseMySQL">
<input type="hidden" name="amount" value="<?php echo($debit); ?>">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="custom" value="<?php echo("db".$_SESSION[user]); ?>">
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
}
?>
