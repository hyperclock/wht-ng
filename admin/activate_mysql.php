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
<title><?php echo _("Web Hosting Toolkit") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="../css/<?php echo($stylesheet); ?>/style.css" />
<script type="text/javascript" src="../calculate_db.php"></script>
</head>
<body>
<div>
<form name="form1" action="register_mysql.php" method="post" accept-charset="ISO-8859-1">             
 <br />
<?php
echo _("Activate MySQL for");
?>

<br />
<br />
<select name="months">
<?php
for($i = 0; $i < (sizeof($hosting_months) - 1); $i++) {
    if ($hosting_months['initial_selected'] === $hosting_months[$i]) {
        echo("<option selected=\"true\"> ".$hosting_months[$i]." </option>");
    } else {
        echo("<option> ".$hosting_months[$i]." </option>");
    }
}
?>
</select>
<?php echo _("months"); ?>.

<br />
<br />
<input type="button" value="<?php echo _("Calculate"); ?>" onclick="calculate()" >
<input type="text" name="calc_value" size="6" readonly="true" >
<br />
<br />
<input type="submit" name="submit" value="<?php echo _("Submit"); ?>">
<input type="reset" value="<?php echo _("Reset"); ?>">
<input type="hidden" name="user" value="<?php echo($_GET['user']); ?>">
</form>
</div>
</body>
</html>
