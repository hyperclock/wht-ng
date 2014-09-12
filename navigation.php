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
require_once './i18n.php';

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("Navigation") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style_navigation.css" />
<script type="text/javascript">
<!--
counter = 1;
InitHeight = 30;

function estimate_height()
{
    if(self.innerHeight) {
        CurrentHeight = self.innerHeight;
    } else if(document.documentElement && document.documentElement.clientHeight) {
        CurrentHeight = document.documentElement.clientHeight;
    } else if(document.body) {
        CurrentHeight = document.body.clientHeight;
    }
    
    if(document.layers || navigator.userAgent.toLowerCase().indexOf("gecko") >= 0) {
        CurrentHeight -= 16;
        nav_height = document.height;
    } else {
        nav_height = document.body.scrollHeight;
    } 
 
    if(CurrentHeight < nav_height  && counter < 65) {
        counter++;
        if(!parent.set_size(InitHeight + counter)) {
            clearInterval(timerId);
        }

    } else {
        clearInterval(timerId);
    }

}
// -->
</script>
</head>
<body onload="timerId = setInterval('estimate_height()', 100);">
<div name="nav" align="center" id="nav">
<a href="login.php" target="main"><?php echo _("Home"); ?></a> |
<a href="user_properties.php" target="main"><?php echo _("User Properties"); ?></a> |
<a href="allocate.php" target="main"><?php echo _("Domains"); ?></a> |
<?php
if($enable_qmail==="on") {
?>
 <a href="emails.php" target="main"><?php echo _("Email Accounts"); ?></a> |
 <?php
 }
 ?>
<a href="filemanager.php" target="main"><?php echo _("File Manager"); ?></a> |
 <?php
 if($only_free!=="yes") {
 ?>
<a href="mysql.php" target="main"><?php echo _("MySQL"); ?></a> |
<a href="cron.php" target="main"><?php echo _("Cron"); ?></a> |
<?php
}
?>
<a href="logout.php" target="_top"><?php echo _("Log out"); ?></a>
</div>
</body>
</html>
