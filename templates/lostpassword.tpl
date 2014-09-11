<?php
require_once './i18n.php';

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("Web Hosting Toolkit - Next Generation") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style.css" />
</head>
<body>
<div>
<?php
include_once './templates/header.php';
?>
<br />
<?php
echo($error);
echo _("The password will be sent to the email you provide when registering.");
?>
<p>
<form name="allocate" action="lostpassword.php" method="post" accept-charset="ISO-8859-1">
<?php echo _("User"); ?>:
<input name="user" size="15" tabindex="1">
</p>
<input value="<?php echo _("Send password"); ?>" type="submit" name="submit" tabindex="3">
<input type="reset" value="<?php echo _("Reset"); ?>" tabindex="4">
</form>
<?php
include_once './templates/footer.php';
?>
</div>
</body>
</html>
