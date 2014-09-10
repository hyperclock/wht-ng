<?php
require_once './i18n.php';

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("Change Email") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style.css" />
</head>
<body>
<div>
<?php
include_once './templates/header.php';
?>
<br /><br />
<form name="form1" action="cron.php" method="post" accept-charset="ISO-8859-1">
<table cellpadding="4" cellspacing="4" border="0" width="100%"
 align="left">
<tr>
<td valign="bottom" width="40%" align="right">
<?php echo _("New email"); ?>:
</td>
<td valign="bottom" width="40%" align="left">
<input name="change_email">
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right">
</td>
<td valign="bottom" width="40%" align="left">
<input value="<?php echo _("Change"); ?>" type="submit">
</td>
</tr>
</table>
</form>
<br />
<?php
include_once './templates/footer.php';
?>
</div>
</body>
</html>
