<?php
require_once './conf_inc.php';
require_once './i18n.php';
require_once './errors_inc.php';

session_start();
session_cache_limiter('nocache');

import_request_variables('g', 'g_');

error_reporting($error_reporting);

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("New Email") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style.css" />
<script type="text/javascript">
<!--
function check()
{
    if(document.form1.email.value == "") {
        alert("<?php echo _("Fill the Email field!"); ?>");
        return false;
    }
    if(document.form1.password.value == "") {
        alert("<?php echo _("Fill the Password field!"); ?>");
        return false;
    }
    if(document.form1.confpass.value == "") {
        alert("<?php echo _("Fill the Confirm password field!"); ?>");
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
    if(document.form1.email.value.length < 2) {
        alert("<?php echo _("Email must be at least 2 characters long!"); ?>");
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
<form name="form1" action="register_newemail.php" method="post" accept-charset="ISO-8859-1">
<br />
<br />

<?php

if(IsSet($HTTP_GET_VARS['error_spell'])) {
    echo($$HTTP_GET_VARS['error_spell']);
}
elseif(IsSet($HTTP_GET_VARS['error'])) {
    echo($$HTTP_GET_VARS['error']);
}

@mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
@mysql_select_db($database) or die($error_selectdb);

$query = "select ID, num_emails from domains where domain='$g_domain'";
$result = mysql_query($query) or die($error_select);


$row = mysql_fetch_array($result);


$query = "select ID from emails where domain_id='$row[ID]'";
$result = mysql_query($query) or die($error_select);


if(mysql_num_rows($result) >= $row['num_emails']) {
    echo($error_end_emails);
} else {
    if($HTTP_GET_VARS['error'] == "error_same_email") {
        echo($error_same_email . "<br />");
    }
    if($HTTP_GET_VARS['error'] == "error_fill_email") {
        echo($error_fill_email . "<br />");
    }
    if(IsSet($HTTP_GET_VARS['error']) && $HTTP_GET_VARS['error'] == "error_short_password") {
        echo($error_short_password);
    }
?>

<table cellpadding="5" cellspacing="2" margin-left="auto" margin-right="0px" width="80%">
<tbody>
<tr>
<td valign="bottom" width="40%" align="right">
<?php echo _("Email"); ?>: *
</td>
<td valign="bottom" width="40%" align="left">
<input name="email" size="20">@<?php echo($g_domain); ?>
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Password"); ?>: *
</td>
<td valign="bottom" width="40%"><input type="password"
name="password" size="15" maxlength="15">
</td>
</tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Confirm password"); ?>: *
</td>
<td valign="bottom" width="40%"><input type="password"
name="confpass" size="15" maxlength="15">
</td>
</tr>
<tr><td> <br /><br /></td></tr>
<tr>
<td valign="top">
</td>
<td valign="top">
<input type="submit" name="Submit" value="<?php echo _("Create"); ?>"
onclick="if(check()) return true; else return false">
<input type="reset" name="Reset" value="<?php echo _("Reset"); ?>">
</td>
</tr>
</tbody>
</table>
<input type="hidden" name="domain" value="<?php echo($g_domain); ?>">
<input type="hidden" name="domain_id" value="<?php echo($row['ID']); ?>">
</form>
<?php
include_once './templates/footer.php';
?>
</div>
</body>
</html>

<?php
}
?>
