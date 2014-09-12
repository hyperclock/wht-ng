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

import_request_variables('g', 'g_');
import_request_variables('p', 'p_');

error_reporting($error_reporting);

if($_SESSION['login'] === "yes") {

    echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("Web Hosting Toolkit") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="../css/<?php echo($stylesheet); ?>/style.css" />
</head>
<body>
<div>
<script type="text/javascript">
<!--
function check()
{
    if(document.form1.password.value == "") {
        alert("<?php echo _("Fill the Password field!"); ?>");
        return false;
    }
    if(document.form1.password.value.length < 8) {
        alert("<?php echo _("Password must be at least 8 characters long!"); ?>");
        return false;
    }


return true;
}
// -->
</script>
</head>
<body>
<form name="form1" action="change_email_password.php" method="post" accept-charset="ISO-8859-1">             
 
<br /><br />

<?php

    if(IsSet($HTTP_GET_VARS['error']) && $HTTP_GET_VARS['error'] == "error_short_password") {
        echo($error_short_password);
    }

    @mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
    @mysql_select_db($database) or die($error_selectdb);


    if($p_Submit === "Change" && $p_password != "") {
        require_once '../execute_cmd.php';

        $exec_cmd = "$vpasswd $p_email $p_password";
        $result = execute_cmd("$exec_cmd");


        if($result[0] != "") {
            die("Error");
        } else {
            $query = "update emails set password='$p_password' where email='$p_email'";
            mysql_query($query) or die($error_select);

            $g_email = $p_email;

            echo("Password changed.");
        }
    }


    $query = "select password from emails where email='$g_email'";
    $result = mysql_query($query) or die($error_query);

    $row = mysql_fetch_array($result);

?>
<table cellpadding="5" cellspacing="2" margin-left="auto" margin-right="0px" width="80%">
<tbody>
<tr>
<td valign="bottom" width="40%" align="right">
<?php echo _("Email"); ?>:
</td>
<td valign="bottom" width="40%" align="left">
<?php echo($g_email); ?>
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Password"); ?>*<br />
</td>
<td valign="bottom" width="40%"><input
 name="password" size="15" maxlength="15" value="<?php echo($row['password']); ?>"><br />
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right">
<input type="button" value="<?php echo _("Cancel"); ?>" onclick="window.close()">
</td>
<td valign="bottom" width="40%" align="right">
<br />
<input type="submit" name="Submit" value="<?php echo _("Change"); ?>" onclick="if(check()) return true; else return false">
<input type="reset" value="<?php echo _("Reset"); ?>">
</td> 
</tr>
</tbody>
</table>
<input type="hidden" name="email" value="<?php echo($g_email); ?>">
</form>
</div>
</body>
</html>
<?php
}
?>
