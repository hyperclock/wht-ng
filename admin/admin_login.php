<?php
require_once '../conf_inc.php';
include_once '../set_language_cookie.php';
require_once '../i18n.php';
require_once '../errors_inc.php';

session_start();

import_request_variables('p', 'p_');

error_reporting($error_reporting);

mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
mysql_select_db($database) or die($error_selectdb);

$query = "select user, password from users where user='admin'";
$result = mysql_query($query) or die($error_select);

while($row = mysql_fetch_array($result)) {
    $res[] = $row;
}

if($res[0]['password'] == "") {
    header("Location:set_admin_pass.php");
    exit;
}

if($p_admin_password != "") {
    if(crypt($p_admin_password,$res[0]['password']) == $res[0]['password']) {
        $_SESSION['login'] = "yes";

        header("Location:admin.html");
        exit;
    } else {
        $error = TRUE;
    }
}

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("WHT - Administration") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="../css/<?php echo($stylesheet); ?>/style.css" />
</head>
<body>
<div>
<?php
if($error==TRUE)
echo _("Wrong password!") . "<br />";

echo _("Log in page for the administrator.") . "<br />";
?>
<br />
<table>
<tbody>
<tr>
<td valign="top" align="right">
Language:
</td>
<td align="left">
<form name="form1" action="admin_login.php" method="post" accept-charset="ISO-8859-1">
<select name="language" onchange="document.form1.submit()">
<?php
$options = array_keys($languages);

foreach($options as $value) {
    if(IsSet($languageSel) && $languageSel === $value) {
        echo("<option value=\"$value\" selected=\"true\">$value</option>\n");
    } else {
        echo("<option value=\"$value\">$value</option>\n");
    }
}
?>
</select>
</form>
</td>
</tr>
<tr>
<td align="right">
<form name="form2" action="admin_login.php" method="post" accept-charset="ISO-8859-1">
<?php echo _("Password"); ?>:
</td>
<td align="left">
<input type="password" name="admin_password" size="15" maxlength="15">
</td>
</tr>
<tr>
<td align="right" colspan="2">
<input type="submit" name="Submit" value="<?php echo _("Log in"); ?>">
<input type="reset" value="<?php echo _("Reset"); ?>">
</form>
<br />
<h5>
<a href="set_admin_pass.php"><?php echo _("Change administrator's password."); ?> </a>
</h5>
</td>
</tr>
</tbody>
</table>
</div>
</body>
</html>

