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
require_once '../execute_cmd.php';
require_once '../check_posted.php';
require_once '../i18n.php';
require_once '../errors_inc.php';

session_start();
session_cache_limiter('nocache');

import_request_variables('p', 'p_');
import_request_variables('g', 'g_');

error_reporting($error_reporting);

if($_SESSION['login'] === "yes") {
    @mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
    @mysql_select_db($database) or die($error_selectdb);

    if(IsSet($p_password) && $p_password != "" && $p_email != "") {
        $user = $p_user;
        $num = $p_num;

        $query = "update users set password='$p_password', email='$p_email' where user='$user';";
        $result = mysql_query($query) or die($error_update);

        $passencrypt = crypt($p_password, $p_password);
        $exec_cmd = "$modusercmd  -p $passencrypt $user";
        $result_exec = execute_cmd("$exec_cmd");

        $query = "select ID from users where user='$user'";
        $result = mysql_query($query) or die($error_select);

        $row = mysql_fetch_array($result);

        $query = "select domain from domains where user_id='$row[ID]' and sub=''";
        $result = mysql_query($query) or die($error_select);


        while($row = mysql_fetch_array($result)) {
            $exec_cmd = "$vpasswd postmaster@$row[domain] $p_password";
            $res_exec = execute_cmd("$exec_cmd");

        }


        $updated = _("Properties updated.");
    } else {
        $user = $g_user;
        $num = $g_num;
    }


    $query = "select user, password, email, db, db_expday, db_expmonth, db_expyear from users where user='$user'";

    $result = mysql_query($query) or die($error_select);

    if(mysql_num_rows($result) != 0) {
        while($row = mysql_fetch_array($result)) {
            $res[] = $row;
        }
    }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _(Domains); ?>Domains</title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="../css/<?php echo($stylesheet); ?>/style.css" />
<script type="text/javascript">
<!--
function check()
{
    if(document.form1.password.value == "") {
        alert("<?php echo _("Fill the Password field!"); ?>");
        return false;
    }
    if(document.form1.email.value == "") {
        alert("<?php echo _("Fill the Email field!") ?>");
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
<?php echo("user: $user <br /> $updated"); ?>
<form name="form1" action="change_properties.php" method="post" accept-charset="ISO-8859-1">  

  <table cellpadding="2" cellspacing="2" margin-left="auto"
 style="width: 100%;" margin-right="0px">
<tbody>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Password"); ?>: *
</td>
<td valign="bottom" width="40%"><input
name="password" size="15" maxlength="15" value="<?php echo($res[0]['password']); ?>"><br />
</td>
</tr>
<tr>
<td valign="bottom" align="right"><?php echo _("Contact email"); ?>*
</td>
<td valign="bottom"><input name="email" size="30" value="<?php echo($res[0]['email']); ?>"><br />
</td>
</tr>
<tr>
<td valign="top"><br />
</td>
<td valign="top"><br />
<input type="submit"
name="Change" value="Change" onclick="if(check()) return true; else return false"><br />
        </td>
      </tr>
                                            
    </tbody>                          
  </table>

 <input type="hidden" name="user" value="<?php echo($user); ?>">
 <input type="hidden" name="num" value="<?php echo($num); ?>">
</form>

<br />

<form name="form2" action="register_change_mysql.php" method="post" accept-charset="ISO-8859-1"> 

<?php
    if($res[0]['db'] !== "on" && $res[0]['db_expyear'] == 0) {
        echo "<a href=\"activate_mysql.php?user=$user\">" . _("Activate MySQL.") . "</a>";
    } elseif($res[0]['db'] === "on") {
        echo _("If you want to deactivate MySQL uncheck")
        . " - <input type=\"checkbox\" name=\"db\" checked ><br />"
        . _("This will not delete the databases created from the user.")
        . "<br /> <br />" . _("MySQL expiry date") . ": <input name=\"day\" size=\"2\" maxlength=\"2\" value=\""
        . $res[0]['db_expday'] . "\"><input name=\"month\" size=\"2\" maxlength=\"2\" value=\""
        . $res[0]['db_expmonth'] . "\"><input name=\"year\" size=\"4\" maxlength=\"4\" value=\""
        . $res[0]['db_expyear'] . "\"> d:m:y <br /><br /><input type=\"submit\" name=\"Submit\"
        value=\""  . _("Change MySQL expiry date") . "\">";
    } else {
        echo _("Activate MySQL again") . "<input type=\"checkbox\" name=\"db\" > <br /><br />"
        . _("MySQL expiry date") . ": <input name=\"day\" size=\"2\" maxlength=\"2\" value=\""
        . $res[0]['db_expday'] . "\"><input name=\"month\" size=\"2\" maxlength=\"2\" value=\""
        . $res[0]['db_expmonth'] . "\"><input name=\"year\" size=\"4\" maxlength=\"4\" value=\""
        . $res[0]['db_expyear'] . "\"> d:m:y <br /><br /><input type=\"submit\" name=\"Submit\"
        value=\""  . _("Change MySQL expiry date") . "\">";
    }
?>

<input type="hidden" name="user" value="<?php echo($user); ?>">
</form>

<?php
    if($p_num != "")
    {
?>
<script type="text/javascript">
window.opener.num(<?php echo($p_num); ?>);
</script>

<?php
    }
?>
</div>
</body>
</html>
<?php
}
?>
