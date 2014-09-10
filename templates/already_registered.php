<?php
require_once '../i18n.php';

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
<?php
include_once '../templates/header.php';
?>
<br />
<?php echo _("You are either already registered and may log in from the"); ?>
 <a href="<?php echo("http://$host_name/$version") ?>"><?php echo _("Home page"); ?></a>
<?php echo _("or your account is not free!"); ?>
<?php
include_once '../templates/footer.php';
?>
</div>
</body>
</html>
