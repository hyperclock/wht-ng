<?php
require_once '../conf_inc.php';
require_once '../i18n.php';
require_once '../errors_inc.php';

import_request_variables('g', 'g_');

error_reporting($error_reporting);

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("New Subdomain") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="../css/<?php echo($stylesheet); ?>/style.css" />
<script type="text/javascript">
<!--
function check()
{
    if(document.form1.local_domain.value == "") {
        alert("<?php echo _("Fill the Subdomain field!"); ?>");
        return false;
    }
    if(document.form1.traffic.value < <?php echo($inittraffic_subdomain); ?>) {
        alert("<?php echo _("The traffic have to be more than") . " " . $inittraffic_subdomain . " " . _("Mbytes per month!"); ?>");
        return false;
    }
    if(document.form1.quota.value < <?php echo($initquota_subdomain); ?>) {
        alert("<?php echo _("The hard disk usage have to be more than") . " " . $initquota_subdomain . " " . _("Mbytes!"); ?>");
        return false;
    }

    return true;
}

// -->
</script>
<script type="text/javascript" src="../calculate_newsubdomain.php"></script>
</head>
<body>
<?php
echo _("Fill out the form.")
?>
<form name="form1" action="register_newsubdomain.php" method="post" accept-charset="ISO-8859-1">             
<br />
<br />
<?php

if(IsSet($HTTP_GET_VARS['error_spell'])) {
    echo($$HTTP_GET_VARS['error_spell']);
}
elseif(IsSet($HTTP_GET_VARS['error'])) {
    echo($$HTTP_GET_VARS['error']);
}
?>
<table cellpadding="2" cellspacing="2" margin-left="auto"
width="100%" margin-right="0px">
<tbody>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Subdomain"); ?>:
</td>
<td valign="bottom" width="40%"><input name="local_domain" size="20" value="<?php echo($_COOKIE['local_domain_c']) ?>">.<?php echo($g_zone); ?>
</td>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Use PHP and CGI"); ?>:
</td>
<td valign="bottom" width="40%"><input type="checkbox" name="script"  <?php if($_COOKIE['script_c'] === "on") echo("checked=\'true\'"); ?>>
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Months to host your site"); ?>:
</td>
<td valign="bottom" width="40%">
<select name="months">
<?php
for($i = 0; $i < (sizeof($hosting_months) - 1); $i++) {
    if ($_COOKIE['months_c'] == $hosting_months[$i]) {
        echo("<option selected=\"true\">" . $hosting_months[$i] . " </option>");
    } elseif(!IsSet($_COOKIE['months_c']) && $hosting_months['initial_selected'] === $hosting_months[$i]) {
        echo("<option selected=\"true\"> " . $hosting_months[$i] . " </option>");
    } else {
        echo("<option> " . $hosting_months[$i] . " </option>");
    }
}
?>
</select>
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Traffic"); ?>:
</td>
<td valign="bottom" style="width: 40%;">
<input value="<?php if(IsSet($_COOKIE['traffic_c']))  echo($_COOKIE['traffic_c']); else echo($inittraffic_subdomain); ?>"
name="traffic" size="5">  <?php echo _("Mbytes per month."); ?>
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Hard disk usage"); ?>:
</td>
<td valign="bottom" style="width: 40%;"><input value="<?php if(IsSet($_COOKIE['quota_c']))  echo($_COOKIE['quota_c']); else echo($initquota_subdomain); ?>"
name="quota" size="5">  <?php echo _("Mbytes"); ?>
</td>
</tr>
<tr><td> <br /><br /></td></tr>
<tr>
<td valign="top">
<input type="button" value="<?php echo _("Calculate"); ?>" onclick="if(check()) calculate();" >
<input type="text" name="calc_value" size="6" readonly=true >
</td>
<td valign="top">
<input type="submit" value="<?php echo _("Submit"); ?>"
onclick="if(check()) return true; else return false">
<input type="reset" name="Reset" value="<?php echo _("Reset"); ?>">
</td>
</tr>
</tbody>
</table>
<input type="hidden" name="hidden" value="newsubdomain">
<input type="hidden" name="sel_domain" value="<?php echo($g_zone); ?>">
<input type="hidden" name="user" value="<?php echo($g_user); ?>">
</form>
</div>
</body>
</html>
