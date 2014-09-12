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
    @mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
    @mysql_select_db($database) or die($error_selectdb);

    if($p_local_domain != "" || $p_domain != "") {
        $query = "select ID, password, quota from users where user='$p_user'";
        $result_users = mysql_query($query) or die($error_select);

        $row_users = mysql_fetch_array($result_users);

        $p_password = $row_users['password'];
        $user_id = $row_users['ID'];

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

            $debit = $price_subdomain * $p_months + $pricescript_subdomain * $p_months * $script + $p_months * ($priceextratraffic_subdomain * ($p_traffic - $inittraffic_subdomain)) + $p_months * ($priceextraquota_subdomain * ($p_quota - $initquota_subdomain));
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

            if($p_domain != "") {
                $domain_insert = $p_domain;
                $zone = $p_domain;
            } else {
                $domain_insert = $p_local_domain . "." . $p_sel_domain;
                $zone = $p_sel_domain;
                $subdomain = $p_local_domain;
            }

            $timestamp = time();

            $quota = $p_quota * 1024;

            $query = "insert into domains (ID, user_id, domain, subdomain, zone, sub, script, ssl, months, quota, traffic, debit, day, month, year, expday, expmonth, expyear, enable, status, timestamp) values(NULL, '$user_id', '$domain_insert', '$subdomain', '$zone', 'y', '$p_script', '$p_ssl', '$p_months', '$quota', '$p_traffic', '$debit', '$day', '$month', '$year', '$expday', '$expmonth', '$expyear', 'y', '1', '$timestamp')";

            mysql_query($query) or die($error_insert);

            $query = "unlock tables;";
            mysql_query($query) or die($error_query);

            $quota_soft = $row_users['quota'] + $quota;
            $quota_hard = $quota_soft + 20;

            $exec_cmd = "$setquotacmd -u $p_user $quota_soft $quota_hard 0 0 -a $partition_used";
            execute_cmd("$exec_cmd");

            $query = "update users set quota='$quota_soft' where user='$p_user'";
            $result = mysql_query($query) or die($error_update);

            require_once '../include/ftp_newdomain.php';
                
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
echo _("Subdomain with the following features is created:");
?>
<br />
<br />
<table cellpadding="2" cellspacing="2" margin-left="auto"
style="width: 100%;" margin-right="0px">
<tbody>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Domain name"); ?>:
</td>
<td valign="bottom" width="40%"><?php echo($domain_insert); ?>
</td>
</tr>
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

            header("Location:newsubdomain.php?error=error_same_domain&zone=$p_sel_domain&user=$p_user");
            exit;
        }
    } else {
        require_once '../setcookies.php';

        header("Location:newsubdomain.php?error=error_fill_domain&zone=$p_sel_domain&user=$p_user");
        exit;
    }
}
