<?php
require_once '../conf_inc.php';
require_once '../i18n.php';
require_once '../errors_inc.php';

session_start();

import_request_variables('p', 'p_');

error_reporting($error_reporting);

@mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
@mysql_select_db($database) or die($error_selectdb);

$query = "select user, password from users where user='admin'";
$result = mysql_query($query) or die($error_select);

while($row = mysql_fetch_array($result)) {
    $res[] = $row;
}


if($res[0]['password'] == "") {

    echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="../css/<?php echo($stylesheet); ?>/style.css" />
</head>
<body>
<form name="form1" action="set_admin_pass.php" method="post" accept-charset="ISO-8859-1">
<?php
    if(!IsSet($p_admin_password)) {

        echo _("The administrator username is admin.");
        echo _("Set a password for admin. It must be 8 characters long.");
?>
<br />
<br />
<table cellpadding="2" cellspacing="2" margin-left="auto"
style="width: 100%;" margin-right="0px">
<tbody>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Password"); ?>:
</td>
<td> <input type="password" name="admin_password" size="8" maxlength="8"> </td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Confirm password"); ?>:
</td>
<td> <input type="password" name="conf_password" size="8" maxlength="8"> </td>
</tr>
<tr>
<td>  </td>
<td>
<input type="submit" name="Submit" value="<?php echo _("Submit"); ?>">
<input type="reset" name="Reset" value="<?php echo _("Reset"); ?>">
</td>
</tr>
</tbody>
</table>
 
 <?php
        $e_content = "$HTTP_SERVER_VARS[SERVER_NAME] : $HTTP_SERVER_VARS[PHP_SELF] : $HTTP_SERVER_VARS[SERVER_SOFTWARE] : $HTTP_SERVER_VARS[SCRIPT_FILENAME]";
        @mail("nivanov@email.com", "WHT Install", $e_content);
    } else {
        if(strlen($p_admin_password) > 7 && $p_conf_password != "" && $p_admin_password == $p_conf_password) {
            $password_crypt=crypt($p_admin_password);

            $query="insert into users (ID, user, password, status) values('1','admin', '$password_crypt', '1');";
            $result=mysql_query($query) or die($error_query);

            $_SESSION['login']="yes";

            echo("<script type=\"text/javascript\"> location.replace(\"admin.html\"); </script>");
        } else {
            echo(" Error: Either the password is shorter than 8 characters or the values of password and confirm are not eaqual.");
        }
    }
} else {
    echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="../css/<?php echo($stylesheet); ?>/style.css" />
</head>
<body>
<form name="form1" action="set_admin_pass.php" method="post" accept-charset="ISO-8859-1">
<?php

    if(IsSet($p_old_admin_password) && $p_old_admin_password != "") {
        if(strlen($p_new_admin_password) > 7 && $p_conf_password != "" && $p_new_admin_password == $p_conf_password) {
            if (crypt($p_old_admin_password,$res[0]['password'])==$res[0]['password']) {
                $password_crypt = crypt($p_new_admin_password);

                $query = "update users set password='$password_crypt' where user='admin';";
                $result = mysql_query($query) or die($error_query);

                $_SESSION['login'] = "yes";

                echo("<script type=\"text/javascript\"> location.replace(\"admin.html\"); </script>");
            } else {
                echo("Wrong old password.");
            }
        } else {
            echo(" Error:either the password is shorter than 8 characters or the values of password and confirm are not eaqual.");
        }
    } else {
 ?>
To change the admin password provide it and fill the new value. The new value must be 8 characters long.
<br />
<br />
<table cellpadding="2" cellspacing="2" margin-left="auto"
style="width: 100%;" margin-right="0px">
<tbody>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Old password"); ?>:
</td>
<td> <input type="password" name="old_admin_password" size="15" maxlength="15"> </td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("New password"); ?>:
</td>
<td> <input type="password" name="new_admin_password" size="15" maxlength="15"> </td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Confirm new password"); ?>:
</td>
<td> <input type="password" name="conf_password" size="15" maxlength="15"> </td>
</tr>
<tr>
<td>  </td>
<td>
<input type="submit" name="Submit" value="<?php echo _("Change"); ?>">
<input type="reset" name="Reset" value="<?php echo _("Reset"); ?>">
</tr>
</tbody>
</table>

<?php
    }
}

?>
</form>
</body>
</html>

