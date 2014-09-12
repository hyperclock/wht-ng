<?php
/**
*    Web Hosting Toolkit - Next Generation (WHT-NG)
*    Copyright (C) 2014  Jimmy M. Coleman <hyperclock@ok.de>
*    Copyright (C) 2003  Nikolay Ivanov <nivanov@email.com> (GPLv2)
*
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once './conf_inc.php';
include_once './set_language_cookie.php';
require_once './i18n.php';

error_reporting($error_reporting);

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("Log in") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style.css" />
</head>
<body>
<div>
<?php
include_once './templates/header.php';

if (IsSet( $HTTP_GET_VARS['error']) && $HTTP_GET_VARS['error'] == "error_fill") {

    require_once './errors_inc.php';

    echo($error_login_fill);
}
?>

<table cellpadding="2" cellspacing="2" border="0" style="text-align: left; width: 100%;">
<tbody>
<tr>
<td valign="top" rowspan="5" colspan="1"  style="width: 50%;">
<?php echo _("Log in page"); ?>.
<br />
<br />
&nbsp; <a href="http://wht-ng.tk/" target="_top">
<img src="images/wht-ng_logo.png"> </a>
</td >
<td valign="top" align="right">
Language:
</td>
<td align="left">
<form name="form1" action="login.php" method="post" accept-charset="ISO-8859-1">
<select name="language" onchange="document.form1.submit()">
<?php
$options = array_keys($languages);

foreach($options as $value) {
    if(IsSet($languageSel) && $languageSel === $value) {
        echo("<option value=\"$value\" selected=\"true\">$value</option>\n");
    } else {
        echo("<option value=\"$value\">$value</option>\n");
    }
}
?>
</select>
</form>
</td>
</tr>
<tr>
<form name="allocate" action="allocate.php" method="post" accept-charset="ISO-8859-1">

<td valign="top" style="text-align: right; width: 25%;"><?php echo _("User"); ?>:<br />
</td>
<td valign="top">
<input name="user" size="15" tabindex="1"><br />
</td>
</tr>
<tr align="center">
<td valign="top" style="text-align: right;"><?php echo _("Password"); ?>:<br />
</td>
<td valign="top" style="text-align: left;">
<input type="password" name="password" size="15" tabindex="2"><br />
</td>
</tr>
<tr>
<td valign="top" style="text-align: right;">

</td>
<td valign="top" style="text-align: left;"><input value="<?php echo _("Log in"); ?>"
type="submit" name="login" tabindex="3"><br />
</td>
</tr>
<tr>
<td valign="top" colspan="2" style="text-align: center;">
<br />
<font size="-2">
<a href="lostpassword.php"><?php echo _("Lost password"); ?></a>
<?php

if($only_free !== "yes") {
    echo(" | <a href=\"newuser.php\">" . _("New user") . "</a>");
}
if($free_enable === "yes") {
    echo("<br />&nbsp;<a href=\"free/newuser.php\">" . _("Free hosting") . "</a>");
}
?>
<br />
</font>
</td>
</tr>
</tbody>
</table>
<input value="login" type="hidden" name="hidden">
</form>
<?php
include_once './templates/footer.php';

if(IsSet($HTTP_POST_VARS['language'])) {
    echo("<script type=\"text/javascript\">
<!--
parent.navigation.location.reload();
// -->
</script>
");
}
?>
</div>
</body>
</html>
