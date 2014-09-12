<?php
/**
*    Web Hosting Toolkit - Next Generation (WHT-NG)
*    Copyright (C) 2014  Jimmy M. Coleman <hyperclock@ok.de>
*    Copyright (C) 2003  Nikolay Ivanov <nivanov@email.com> (GPLv2)
*
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

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

    $query = "select db_expday, db_expmonth, db_expyear from users where user='$_SESSION[user]';";
    $result = mysql_query($query) or die($error_select);

    $row = mysql_fetch_array($result);


    $expmonth = $row['db_expmonth'] + $p_months;

    if($expmonth > 12) {
        $expyear = $row['db_expyear'] + (int)($expmonth/12);
        $expmonth = $expmonth - 12 * (int)($expmonth/12);
    }else {
        $expyear = $row['db_expyear'];
    }
    
    $expday = $row['db_expday'];



    $debit = $pricedb * $p_months;
    $debit = round($debit, 2);

    if(!ereg("[.]{1}", $debit)) {
        $debit = $debit . ".";
    }
    if(!ereg("[.]{1}[0-9]{2}", $debit)) {
        $debit = $debit . "0";
    }
    if(!ereg("[.]{1}[0-9]{2}", $debit)) {
        $debit = $debit."0";
    }


    $query = "update users set debit='$debit' where user='$_SESSION[user]';";
    mysql_query($query) or die($error_select);


    $query = "delete from temporary_users where user='$_SESSION[user]'";
    mysql_query($query) or die($error_delete);

    $query = "insert into temporary_users  (user, db_expday, db_expmonth, db_expyear, timestamp) values('$_SESSION[user]', '$expday', '$expmonth', '$expyear', '$timestamp')";
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


To pay for the following features:<br /><br />

                
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
<td valign="bottom" width="40%" align="right">use MySQL for extra<br />
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
<input type="hidden" name="return" value="http://<?php echo($host_name."/".$version); ?>/return_extend_db.php">
<input type="hidden" name="cancel_return" value="http://<?php echo($host_name."/".$version); ?>/cancel_return.php">
<input type="hidden" name="item_name" value="ExtendMySQL">
<input type="hidden" name="amount" value="<?php echo($debit); ?>">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="custom" value="<?php echo("ed".$_SESSION[user]); ?>">
<input type="image" src="noimage" name="submit" alt="PayPal - Test">
</form>
<?php
    } elseif($testmode==="eliteweaver") {
?>
click the PayPal button. You will be directed to paypal.com and after paying returned back.
<form action="http://www.eliteweaver.co.uk/testing/ipntest.php" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="<?php echo($business); ?>">
<input type="hidden" name="notify_url" value="http://<?php echo($host_name."/".$version); ?>/notify.php">
<input type="hidden" name="return" value="http://<?php echo($host_name."/".$version); ?>/return_extend_db.php">
<input type="hidden" name="cancel_return" value="http://<?php echo($host_name."/".$version); ?>/cancel_return.php">
<input type="hidden" name="item_name" value="ExtendMySQL">
<input type="hidden" name="amount" value="<?php echo($debit); ?>">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="custom" value="<?php echo("ed".$_SESSION[user]); ?>">
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
<input type="hidden" name="return" value="http://<?php echo($host_name."/".$version); ?>/return_extend_db.php">
<input type="hidden" name="cancel_return" value="http://<?php echo($host_name."/".$version); ?>/cancel_return.php">
<input type="hidden" name="item_name" value="ExtendMySQL">
<input type="hidden" name="amount" value="<?php echo($debit); ?>">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="custom" value="<?php echo("ed".$_SESSION[user]); ?>">
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
