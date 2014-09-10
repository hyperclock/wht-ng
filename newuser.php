<?php
require_once './conf_inc.php';
require_once './i18n.php';
require_once './errors_inc.php';

error_reporting($error_reporting);

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("New User") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style.css" />
<script type="text/javascript">
<!--
function check()
{
    if(document.form1.user.value == "") {
        alert("<?php echo _("Fill the User field!"); ?>");
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
    if(document.form1.domain.value == "" && document.form1.local_domain.value == "") {
        alert("<?php echo _("Fill the Domain or Local domain field!"); ?>");
        return false;
    }
    if(document.form1.domain.value != "" && document.form1.local_domain.value != "") {
        alert("<?php echo _("Fill only one of the two fields - Local domain or Domain!"); ?>");
        return false;
    }
    if(document.form1.email.value == "") {
        alert("<?php echo _("Fill the Email field!") ?>");
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
    if(document.form1.traffic.value < <?php echo($inittraffic); ?>) {
        alert("<?php echo _("The traffic have to be more than") . " " . $inittraffic . " " . _("Mbytes per month!"); ?>");
        return false;
    }
    if(document.form1.quota.value < <?php echo($initquota); ?>) {
        alert("<?php echo _("The hard disk usage have to be more than") . " " . $initquota . " " . _("Mbytes!"); ?>");
        return false;
    }

    return true;
}

// -->
</script>
<script type="text/javascript" src="./calculate_newuser.php"></script>
</head>
<body>
<div>
<?php
include_once './templates/header.php';

echo _("Fill out the form.");
?>
<form name="form1" action="register_newuser.php" method="post" accept-charset="ISO-8859-1">          
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
style="width: 100%;" margin-right="0px">
<tbody>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("User"); ?>: *
</td>
<td valign="bottom" width="40%" style="text-align: left;"><input
name="user" size="8" value="<?php echo($_COOKIE['user_c']) ?>" maxlength="8">
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Password"); ?>: *
</td>
<td valign="bottom" width="40%"><input type="password"
name="password" size="15" maxlength="15">
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Confirm password"); ?>: *
</td>
<td valign="bottom" width="40%"><input type="password"
name="confpass" size="15" maxlength="15">
</td>
</tr>
<tr>
<td valign="bottom" align="right"><?php echo _("Contact email"); ?>: *
</td>
<td valign="bottom"><input name="email" size="30" value="<?php echo($_COOKIE['email_c']) ?>">
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Use MySQL"); ?>:
</td>
<td valign="bottom" width="40%"><input type="checkbox" name="db"
<?php
if($_COOKIE['db_c'] === "on") {
    echo("checked=\"true\"");
}
?>>
</td>
</tr>
</tbody>
</table>
<br />
<?php echo _("You must fill one of the next two fields. Fill the first one if you have not registered domain name.<br />")
. _("Fill the second one if you allready have a registered domain. Can be of any type ( your_domain.com, your_domain.net ...). We will not register your domain name."); ?>
<br /> <br />
<table cellpadding="2" cellspacing="2" margin-left="auto"
style="width: 100%;" margin-right="0px">
<tbody>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Local domain name"); ?>:
</td>
<td valign="bottom" width="40%">
<input name="local_domain" size="20" value="<?php echo($_COOKIE['local_domain_c']) ?>">
.<select name="sel_domain">
<?php
for($i = 0; $i < sizeof($domain_name); $i++) {
    if($_COOKIE['sel_domain_c'] === $domain_name[$i]) {
        echo("<option selected=\"true\">".$domain_name[$i]."  </option>");
    } else {
        echo("<option> ".$domain_name[$i]." </option>");
     }
 }
 ?>
 </select>
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Your domain name ( must be registered )"); ?>:
</td>
<td valign="bottom" width="40%"><input name="domain" size="30" value="<?php echo($_COOKIE['domain_c']); ?>">
</td>
</tr>

<?php
if($enable_qmail==="on")
{
?>
<tr>
<td valign="bottom" width="40%" align="right">
<?php echo _("Email accounts ( will be of type somename@your_domain.com )"); ?>:
</td>
<td valign="bottom" width="40%">
<input name="num_emails" size="2" value="<?php if(IsSet($_COOKIE['num_emails_c'])) echo($_COOKIE['num_emails_c']); else echo($initemails); ?>" maxlength="2">
</td>
</tr>

<?php
}
?> 

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
<input value="<?php if(IsSet($_COOKIE['traffic_c']))  echo($_COOKIE['traffic_c']); else echo($inittraffic); ?>"
name="traffic" size="5">  <?php echo _("Mbytes per month."); ?>
</td>
</tr>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Hard disk usage"); ?>:
</td>
<td valign="bottom" style="width: 40%;"><input value="<?php if(IsSet($_COOKIE['quota_c']))  echo($_COOKIE['quota_c']); else echo($initquota); ?>"
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
<input type="submit" name="Submit" value="<?php echo _("Submit"); ?>"
onclick="if(check()) return true; else return false">
<input type="reset" name="Reset" value="<?php echo _("Reset"); ?>">
</td>
</tr>
</tbody>
</table>
<input type="hidden" name="hidden" value="newuser">
</form>
<?php
include_once './templates/footer.php';
?>
</div>
</body>
</html>
