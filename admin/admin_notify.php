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

session_start();
session_cache_limiter('nocache');

set_time_limit (60);

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?> 

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("Notify") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="../css/<?php echo($stylesheet); ?>/style.css" />
</head>
<body>
<div>
<script type="text/javascript">
<!--
function domain_properties(domain, edit)
{
    window.opener.document.form1.search.value = domain;
    window.opener.document.form1.action = "domains.php";
    window.opener.document.form1.submit();

    edit.value = "edit";
}

function user_properties(user, edit)
{
    window.opener.document.form1.search.value = user;
    window.opener.document.form1.action = "users.php";
    window.opener.document.form1.submit();

    edit.value = "edit";
}

function check_all()
{
    if(document.form1.checkall.checked) {
        for(i = 0; i < document.form1.elements.length; i++) {
            document.form1.elements[i].checked = true;
        }
    } else {
        for(i = 0; i < document.form1.elements.length; i++) {
            document.form1.elements[i].checked = false;
        }
    }
}
// -->
</script>
</head>
<body>
<div>
<form name="form1" action="admin_notify.php" method="post" accept-charset="ISO-8859-1">
<input type="submit" name="Submit" value="<?php echo _("Delete"); ?>">
<table cellpadding="2" cellspacing="2" margin-left="0px"
style="width: 100%;" margin-right="0px" border="3">
<tbody>
<tr>
<td valign="bottom" width="30pt" align="center" > 
<input type="checkbox" name="checkall" onclick="check_all()">
</td>
<td valign="bottom" width="*" align="center" > 
<?php echo _("domain"); ?>
</td>
<td valign="bottom" width="*" align="center" > 
<?php echo _("properties"); ?>
</td>
<td valign="bottom" width="*" align="center" > 
<?php echo _("comment"); ?>
</td>
<td valign="bottom" width="*" align="center" > 
<?php echo _("date"); ?>
</td>
</tr>

<?php

if($_SESSION['login'] === "yes") {

    error_reporting($error_reporting);

    import_request_variables('p', 'p_');
    import_request_variables('g', 'g_');


    @mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
    @mysql_select_db($database) or die($error_selectdb);


    foreach ($p_check_ as $key => $value) {
        if($value === "on") {
            $query = "delete from admin_notify where ID='$key'";
            mysql_query($query) or die($error_select);
        }
    }

    $query = "select ID, domain, notify, timestamp from admin_notify where domain!=''";
    $result = mysql_query($query) or die($error_select);

    if(mysql_num_rows($result)!=0) {
        while($row = mysql_fetch_array($result)) {
            $date = getdate($row['timestamp']);

            echo("<tr><td valign=\"bottom\" width=\"30pt\" align=\"right\" >
            <input type=\"checkbox\" name=\"check_[$row[ID]]\" ></td>
            <td valign=\"bottom\" width=\"*\" align=\"left\" >
            <a href=\"http://$row[domain]\" target=\"_blank\"> $row[domain] </a> </td>
            <td valign=\"bottom\" width=\"*\" align=\"center\" >
            <input type=\"button\" value=\"Edit\" onclick=\"domain_properties('$row[domain]', this)\"></td>
            <td valign=\"bottom\" width=\"*\" align=\"left\" > $row[notify] </td>
            <td valign=\"bottom\" width=\"90pt\" align=\"left\" > $date[mday] $date[mon] $date[year] </td></tr>");

            $res_domain[] = $row;

        }
    }

?>
<tr>
<td>
 &nbsp; 
</td>
</tr>
<tr>
<td valign="bottom" width="30pt" align="center" > 
</td>
<td valign="bottom" width="*" align="center" > 
<?php echo _("user"); ?>
</td>
<td valign="bottom" width="*" align="center" > 
<?php echo _("properties"); ?>
</td>
<td valign="bottom" width="*" align="center" > 
<?php echo _("comment"); ?>
</td>
<td valign="bottom" width="*" align="center" > 
<?php echo _("date"); ?>
</td>
</tr>
<?php

    $query = "select ID, user, notify, timestamp from admin_notify where user!=''";
    $result = mysql_query($query) or die($error_select);


    if(mysql_num_rows($result)!=0) {
        while($row = mysql_fetch_array($result)) {
            $date = getdate($row['timestamp']);

            echo("<tr><td valign=\"bottom\" width=\"30pt\" align=\"right\" >
            <input type=\"checkbox\" name=\"check_[$row[ID]]\" ></td>
            <td valign=\"bottom\" width=\"*\" align=\"left\" > $row[user] </td>
            <td valign=\"bottom\" width=\"*\" align=\"center\" >
            <input type=\"button\" value=\"Edit\" onclick=\"user_properties('$row[user]', this)\"></td>
            <td valign=\"bottom\" width=\"*\" align=\"left\" > $row[notify] </td>
            <td valign=\"bottom\" width=\"90pt\" align=\"left\" > $date[mday] $date[mon] $date[year] </td></tr>");

            $res_user[] = $row;

        }
    }
}

?>

</tbody>
</table>
<input type="submit" name="Submit" value="<?php echo _("Delete"); ?>">
</form>
</div>
</body>
</html>
