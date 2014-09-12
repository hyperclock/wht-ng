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

require_once './i18n.php';

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("New Folder") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style.css" />
<script type="text/javascript">
<!--
function ok_func()
{
    opener.document.form1.NewFolder.value = this.document.form1.NewFolder.value;
    opener.document.form1.submit();
    close();
}

function cancel_func()
{
    close();
}
// -->
</script>
</head>
<body>
<div>
<form name="form1" action="no" method="post" accept-charset="ISO-8859-1">
<table cellpadding="4" cellspacing="4" border="0" width="100%" align="left">
<tr>
<td width="100%"><?php echo _("New Folder"); ?>:
</td>
</tr>
<tr>
<td width="100%"><input name="NewFolder">
<br />
</td>
</tr>
<tr>
<td width="100%">
<br />
</td>
</tr>
<tr>
<td align="right">
<input value="<?php echo _("OK"); ?>" type="button" name="OK" OnClick="ok_func()">
<input  value="<?php echo _("Cancel"); ?>" type="button" name="Cancel" onclick="cancel_func()">
</td>
</tr>

</table>
</form>
<br />
</div>
</body>
</html>
