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
require_once './execute_cmd.php';
require_once './check_posted.php';
require_once './i18n.php';
require_once './errors_inc.php';

session_start();
session_cache_limiter('nocache');

import_request_variables('p', 'p_');
import_request_variables('g', 'g_');

error_reporting($error_reporting);

if(IsSet($_SESSION['user'])) {
    @mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
    @mysql_select_db($database) or die($error_selectdb);

    if(IsSet($p_password) && $p_password != "" && $p_confpass != "" && $p_password === $p_confpass && $p_email != "") {
        $user = $_SESSION['user'];

        $query = "update users set password='$p_password', email='$p_email' where user='$user';";
        $result = mysql_query($query) or die($error_query);

        $_SESSION['pass'] = $p_password;
        $_SESSION['email'] = $p_email;
        
        $passencrypt = crypt($p_password, $p_password);
        $exec_cmd = "$modusercmd  -p $passencrypt $user";
        $result_exec = execute_cmd("$exec_cmd");


        $query = "select ID from users where user='$_SESSION[user]'";
        $result = mysql_query($query) or die($error_select);

        $row = mysql_fetch_array($result);


        $query = "select domain from domains where user_id='$row[ID]' and sub=''";
        $result = mysql_query($query) or die($error_select);


        while($row = mysql_fetch_array($result)) {
            $exec_cmd = "$vpasswd postmaster@$row[domain] $p_password";
            $res_exec = execute_cmd("$exec_cmd");
        
        }

        @mysql_select_db("mysql") or die($error_selectdb);

        $query = "select Db from db where User='$user'";
        $result = mysql_query($query) or die($error_select);

        if(mysql_num_rows($result) != 0) {
            $query = "delete from db where User='$user'";
            mysql_query($query) or die($error_delete);

            $query = "delete from user where User='$user'";
            mysql_query($query) or die($error_delete);

            $database_u = $user;

            while($row = mysql_fetch_array($result)) {
                $database_u = $row['Db'];
                $query = "GRANT ALL PRIVILEGES ON $database_u.* TO $user@localhost IDENTIFIED BY '$p_password';";

                mysql_query($query) or die("Cant create new user");

            }

            $query = "FLUSH PRIVILEGES;";
            mysql_query($query) or die("Cant FLUSH PRIVILEGES");

        }

        @mysql_select_db($database) or die($error_selectdb);


        $updated = _("Properties updated.");
    }

    $user = $_SESSION['user'];

    $query = "select ID, user, email, db, db_expday, db_expmonth, db_expyear from users where user='$user'";

    $result = mysql_query($query) or die($error_select);

    if(mysql_num_rows($result) != 0) {
        $res = mysql_fetch_array($result);

    }


    $query = "select free from domains where user_id='$res[ID]'";
    $result = mysql_query($query) or die($error_select);

    $free = true;

    if(mysql_num_rows($result) != 0) {
        while($row = mysql_fetch_array($result)) {
            if($row['free'] != 'y') {
                $free = false;
            }
        }
    }

    echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");
    
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _(Domains); ?>Domains</title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style.css" />
<script type="text/javascript">
<!--
function check()
{
    if(document.form1.password.value == "") {
        alert("<?php echo _("Fill the Password field!"); ?>");
        return false;
    }
    if(document.form1.confpass.value == "") {
        alert("<?php echo _("Fill the Confirm password field!"); ?>");
        return false;
    }
    if(document.form1.email.value == "") {
        alert("<?php echo _("Fill the Email field!") ?>");
        return false;
    }
    if(document.form1.password.value != document.form1.confpass.value) {
        alert("<?php echo _("Password and Confirm password fields must contain the same password!"); ?>");
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
<div>
<?php
include_once './templates/header.php';
?>
<?php echo _("User") . ": " . $_SESSION['user'] . "<br />" . $updated; ?>
<form name="form1" action="user_properties.php" method="post" accept-charset="ISO-8859-1">  

<table cellpadding="2" cellspacing="2" margin-left="auto"
style="width: 100%;" margin-right="0px">
<tbody>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Password"); ?>: *
</td>
<td valign="bottom" width="40%"><input type="password"
name="password" size="15" maxlength="15">
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Confirm password"); ?>: *
</td>
<td valign="bottom" width="40%"><input type="password"
name="confpass" size="15" maxlength="15">
</td>
<tr>
<td valign="bottom" align="right"><?php echo _("Contact email"); ?>*
</td>
<td valign="bottom"><input name="email" size="30" value="<?php echo($res['email']); ?>">
</td>
</tr>
<tr>
<td valign="top"><br />
</td>
<td valign="top"><br />
<input type="submit"
name="Change" value="<?php echo _("Change"); ?>" onclick="if(check()) return true; else return false"><br />
</td>
</tr>

</tbody>                          
</table>
<?php
    if($only_free !== "yes") {
        if($free == false) {
            if($res['db'] !== "on") {
                echo "<a href=\"activate_mysql.php\">" . _("Activate MySQL.") . "</a>";
            } else {
                echo _("MySQL expiry date") . " " . $res['db_expday'] . " " . $res['db_expmonth'] . " "
                . $res['db_expyear'] . " <a href=\"extend_mysql.php\">" . _("extend") . "</a>";
            }
        }
    }
    

?>
</form>
<?php
include_once './templates/footer.php';
?>
</div>
</body>
</html>
<?php
} else {
    header("Location:login.php");
}
?>
