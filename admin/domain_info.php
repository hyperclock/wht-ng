<?php
require_once '../conf_inc.php';
require_once '../i18n.php';
require_once '../errors_inc.php';

session_start();
session_cache_limiter('nocache');

import_request_variables('g', 'g_');

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
<body >
<div>
<?php

if($_SESSION['login'] === "yes") {
    error_reporting($error_reporting);

    @mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
    @mysql_select_db($database) or die($error_selectdb);

    $query = "select domain, num_emails, script, quota, traffic, day, month, year, expday, expmonth, expyear, rotate_traffic, free from domains where domain='$g_domain'";
    $result = mysql_query($query) or die($error_select);

    $row = mysql_fetch_array($result);

    if($row['free'] === 'y') {
        echo _("This is a free account.") . "<br />";
    }
}
?>

<table cellpadding="2" cellspacing="2" margin-left="auto"
width="100%" margin-right="0px">
<tbody>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Domain"); ?>:
</td>
<td valign="bottom" width="40%"><?php echo($row['domain']); ?>
</td>
</tr>
 <?php
if($enable_qmail === "on") {
?>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Email accounts"); ?>:
</td>
<td valign="bottom" width="40%"><?php echo($row['num_emails']); ?>
</td>
</tr>
<?php
}
?>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("PHP and CGI"); ?>:
</td>
<td valign="bottom" width="40%"><?php echo($row['script']); ?>
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Traffic"); ?>:
</td>
<td valign="bottom" width="40%"><?php echo($row['traffic']); ?> <?php echo _("Mbytes"); ?> -
<a href="<?php echo($awstats."?config=".$row['domain']); ?>" > AWStats </a>
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Hard disk usage"); ?>:
</td>
<td valign="bottom" style="width: 40%;"><?php echo($row['quota'] / 1024); ?>
<?php echo _("Mbytes"); ?>
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Registration date"); ?>:
</td>
<td valign="bottom" width="40%">
<?php echo($row['day'] . " " . $row['month'] . " " . $row['year']); ?>
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Expiry date"); ?>:
</td>
<td valign="bottom" width="40%">
<?php echo($row['expday'] . " " . $row['expmonth'] . " " . $row['expyear']); ?>
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Traffic till the last log rotation"); ?>:
</td>
<td valign="bottom" width="40%"><?php echo($row['rotate_traffic']); ?>  <?php echo _("Mbytes"); ?>
</td>
</tr>
</tbody>
</table>
</div>
</boby>
</html>
