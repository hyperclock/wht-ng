<?php
require_once '../conf_inc.php';
require_once '../i18n.php';
require_once '../errors_inc.php';

error_reporting($error_reporting);

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("New Domain") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="../css/<?php echo($stylesheet); ?>/style.css" />
</head>
<body>
<div>
<?php
echo _("Fill out the form.");
?>
<form name="form1" action="register_newdomain_nr.php" method="post" accept-charset="ISO-8859-1">             
<?php

if(IsSet($HTTP_GET_VARS['error_spell'])) {
    echo($$HTTP_GET_VARS['error_spell']);
}
elseif(IsSet($HTTP_GET_VARS['error'])) {
    echo($$HTTP_GET_VARS['error']);
}
?>
<br />
<table cellpadding="2" cellspacing="2" margin-left="auto"
width="100%" margin-right="0px">
<tbody>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Domain name"); ?>:
</td>
<td valign="bottom" width="40%"><input name="domain" size="30">
</td>
</tr>
<tr>
<td valign="top"><br />
</td>
<td valign="top"><br />
<input type="submit" name="Submit" value="<?php echo _("Submit"); ?>" />
<input type="reset" name="Reset" value="<?php echo _("Reset"); ?>" />
</td>
</tr>
</tbody>                          
</table>
<input type="hidden" name="hidden" value="newdomain">
<input type="hidden" name="user" value="admin">
</form>
</div>
</body>
</html>
