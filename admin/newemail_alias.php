<?php
require_once '../conf_inc.php';
require_once '../i18n.php';

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("Forward Email") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="../css/<?php echo($stylesheet); ?>/style.css" />
<script type="text/javascript">
<!--
function check()
{
    if(document.form1.alias.value == "")
    {
        alert("<?php echo _("Fill the Forward to field!"); ?>");
        return false;
    }

    return true;
}
\\ -->
</script>
</head>
<body>
<div>
<form name="form1" action="register_newemail_alias.php" method="post" accept-charset="ISO-8859-1">             
 
<br />
<?php

if(IsSet($HTTP_GET_VARS['error_spell'])) {
    echo($$HTTP_GET_VARS['error_spell']);
}
elseif(IsSet($HTTP_GET_VARS['error'])) {
    echo($$HTTP_GET_VARS['error']);
}
?>
<br />
<table cellpadding="5" cellspacing="2" margin-left="auto" margin-right="0px" width="80%">
<tbody>
<tr>
<td valign="bottom" width="40%" align="right">
<?php echo _("Forward to"); ?>:
</td>
<td valign="bottom" width="40%" align="left">
<input name="alias" size="40">
</td>
</tr>
<tr><td> <br /><br /></td></tr>
<tr>
<td valign="top">
</td>
<td valign="top">
<input type="submit" name="Submit" value="<?php echo _("Submit"); ?>"
onclick="if(check()) return true; else return false">
<input type="reset" name="Reset" value="<?php echo _("Reset"); ?>">
</td>
</tr>
</tbody>
</table>
<input type="hidden" name="email" value="<?php echo($_GET['email']); ?>">
<input type="hidden" name="num" value="<?php echo($_GET['num']); ?>">
</form>
</div>
</body>
</html>
