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

require_once '../conf_inc.php';
require_once '../i18n.php';
require_once '../errors_inc.php';

import_request_variables('g', 'g_');

error_reporting($error_reporting);

if($dir = opendir("$DocumentRoot/$version/advertise")) {
    while(($file = readdir($dir)) !== false) {
        if($file !== "." && $file !== ".." && $file !== "default" && $file !== "CVS") {
            $dir_content[] = $file;
        }
    }
    closedir($dir);
}

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("Modify Domain") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="../css/<?php echo($stylesheet); ?>/style.css" />
<script type="text/javascript" src="calculate_db.php"></script>
</head>
<body>
<div>
<?php
include_once '../templates/header.php';
?>
<form name="form1" action="register_modify_domain.php" method="post" accept-charset="ISO-8859-1">
<?php echo _("Change category."); ?>
<br />
<br />
<table cellpadding="2" cellspacing="2" margin-left="auto"
width="100%" margin-right="0px">
<tbody>
<tr>
<td valign="bottom" width="40%" align="right"><?php echo _("Category"); ?>:
</td>
<td valign="bottom" width="40%">
<select name="category">
<?php
for($i = 0; $i < sizeof($dir_content); $i++) {
    if($_COOKIE['category_c'] === $dir_content[$i]) {
        echo("<option selected>" . $dir_content[$i] . "  </option>");
    } else {
        echo("<option> " . $dir_content[$i] . " </option>");
    }
}
?>
</select><br />
</td>
</tr>
<tr>
<td valign="top"><br />
</td>
<td valign="top"><br />
<input type="submit" name="Submit" value="<?php echo _("Change"); ?>">
<input type="reset" value="<?php echo _("Reset"); ?>">
</td>
</tr>
</tbody>
</table>
<input type="hidden" name="domain" value="<?php echo($g_domain); ?>">
</form>
<?php
include_once '../templates/footer.php';
?>
</body>
</html>
