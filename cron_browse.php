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

session_start();

if(!IsSet($_SESSION['user'])) {
    die("NO USER HAD BEEN SET");
}

error_reporting($error_reporting);

import_request_variables('g', 'g_');
import_request_variables('p', 'p_');

if($ftp_server === "proftpd") {
    $www = "/www";
} else {
    $www = "";
}

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("Choose Executable") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style.css" />
<script type="text/javascript">
<!--
function gotoDirectory(num)
{
    num = num.substr(1);
    self.location = "cron_browse.php?dir=" + num;
}

function ups()
{
    document.form1.up.value = "down"
}

function choose(executable)
{
    opener.document.form1.executable.value = executable;
    window.close();
}
// -->
</script>
</head>
<body>
<div>
<?php echo _("To choose file click it's icon"); ?>.
<br />
<br />
<form name="form1" action="cron_browse.php" method="post" accept-charset="ISO-8859-1">

<?php

$user          = $_SESSION['user'];
$password = $_SESSION['pass'];

if($_SESSION['dir'] === "/") {
    $_SESSION['dir'] = "";
}


if($p_SB === "Delete") {
    $dirend = "/" . $_SESSION['dir'];
} elseif(IsSet($p_NewFolder) || IsSet($p_Paste)) { 
    if(!IsSet($p_Paste) && $p_NewFolder == "") {
        echo("Invalid folder name<br />");
    }
    $dirend = "/".$_SESSION['dir'];
} else {
    if($g_down === "down") {
        if(IsSet($g_dir)) {
            $dirend = "/" . $_SESSION['dir'] . $g_dir . "/";
            $_SESSION['dir'] = $_SESSION['dir'] . $g_dir . "/";
        } else {
            $dirend = "/" . $_SESSION['dir'];
        }
    } elseif($g_dir === "/") {
        $dirend = "/";
        $_SESSION['dir'] = "";
    } else {
        if(!IsSet($_SESSION['dir'])){
            $dirend = "/";
        } elseif($g_dir === "") {
            $dirend = "/";
            $_SESSION['dir'] = "";
        } else {
            $dirend = "/" . $g_dir . "/";
            $_SESSION['dir'] = $g_dir . "/";
        }
    }
}


$dirlist = $dirend;
$counter = 0;
$pos = strrpos($dirlist, '/');

?>

<table>
<tr>
<td width="90%">
<select name="directory" size="1" onChange="gotoDirectory(this.options[this.selectedIndex].value)">
 

<?php

while($pos !== 0) {

    $pos = strrpos($dirlist, '/');
    if($pos !== 0) {
        $counter++;

        $dirlist=substr($dirlist, 0, $pos);
        echo("<option value=\"$dirlist\">$dirlist</option>");
    }

    if($counter === 2) {
        $up = $dirlist;
    }
}

if($pos === 0) {
    echo("<option value=\"/\">/</option>");
}

echo("</select>");

echo("</td><td>");

$up = substr($up, 1);

echo(" <a href=\"cron_browse.php?dir=" . $up . "\" title=\"" . _("up") . "\">
<IMG SRC=\"images/up.gif\" hight=\"25\" width=\"25\" ALT=\"" . _("up") . "\"></a>");

echo("</td></tr></table><br />");

$real_dir = $dirend;

$real_dir = substr($dirend, 1);

if($real_dir == "" || $real_dir == "/") {
    $real_dir = ".";
}


echo("<table border=\"1\"><tr>
<td width=\"30\"></td>
<td align=\"center\"> " . _("name") . " </td> <tr>");


$ftp_server_ip = "127.0.0.1";

$conn_id = ftp_connect($ftp_server_ip, 21, 5);

// login with username and password
$login_result = ftp_login($conn_id, $user, $password); 

// check connection
if ((!$conn_id) || (!$login_result)) { 
    echo "FTP connection has failed!";
    echo "Attempted to connect to $ftp_server_ip for user $user"; 
    die; 
} else {

    $result = ftp_rawlist($conn_id, $real_dir);

    require_once './split_rawlist.php';                                                                                                                                                                                

    $directory_list = directory_list($result);

    for($i = 0; $i < sizeof($directory_list['directory']['name']); $i++) {
        echo("<tr><td width=\"30\"><a href=\"cron_browse.php?dir="
        . $directory_list['directory']['name'][$i] . "&down=down\" onClick=\"ups()\">
        <IMG SRC=\"images/folder.gif\" ALT=\"dir\"align=\"left\"></a></td><td>&nbsp;"
        . $directory_list['directory']['name'][$i] . "&nbsp;</td></tr>");
    }

    for($i = 0; $i < sizeof($directory_list['file']['name']); $i++) {
        if($real_dir === ".") {
            $choose_dir="/";
        } else {
            $choose_dir="/$real_dir";
        }
        echo("<tr><td width=\"30\"><a href=\"javascript:choose('$choose_dir".$directory_list[file][name][$i]."');\"><IMG SRC=\"images/file.gif\" ALT=\"file\"align=\"left\"></a></td><td>&nbsp;".$directory_list[file][name][$i]."&nbsp;</td></tr>");
    }
}
	
echo("</table>");

ftp_close($conn_id);
?>

<input value="up" type="hidden" name="up">

</form>
</div>
</body>
</html>
