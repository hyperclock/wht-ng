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
require_once './execute_cmd.php';
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

if($_COOKIE['clipboard'] != "" && $_COOKIE['clipboard'] !== "<>") {
    $clipboard = $_COOKIE['clipboard'];

}

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("Server File Manager") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style.css" />
<script type="text/javascript">
<!--
function edit_file(dir, file)
{
    if(dir == ".") {
        dir = "/";
    }
    
    resWin = window.open("edit_file.php?file=" + dir + file, "", "menubar=1, dependent=1, status=1, height=430, width=630, screenX=10, screenY=10");

}

function new_folder()
{
    resWin = window.open("new_folder.php", "", "dependent=1, height=170, width=220, screenX=80, screenY=200");
}

function gotoDirectory(num)
{
    num = num.substr(1);
    self.location = "server_filemanager.php?dir=" + num;
}

function ups()
{
    document.form1.up.value = "down"
}

function chmod(dir, file)
{
    chmod_location = "chmod.php?dir=" + dir + "&file=" + file;
    resWin = window.open(chmod_location, "", "dependent, height=270, width=350, screenX=80, screenY=250");

}

function copy()
{
    strCookie = "<>";
    for(i = 0; i < document.form1.elements.length; i++) {
        if(document.form1.elements[i].type == "checkbox" && document.form1.elements[i].checked == true) {
            if(document.form1.directory.value == "/") {
                strCookie += document.form1.elements[i].name + "<.>" + document.form1.directory.value + document.form1.elements[i].value + "<>";
            } else {
                strCookie += document.form1.elements[i].name + "<.>" + document.form1.directory.value + "/" + document.form1.elements[i].value + "<>";
            }
        }
    }

    document.cookie = "clipboard=" + strCookie;


}
// -->
</script>
</head>
<body>
<div>
<b><i><?php echo _("S E R V E R"); ?></i></b>
<br />
<form name="form1" action="server_filemanager.php" method="post">

<?php

$user          = $_SESSION['user'];
$password = $_SESSION['pass'];

if($_SESSION['dir'] === "/") {
    $_SESSION['dir'] = "";
}


if($p_SB === _("Delete")) {
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

$up=substr($up, 1);

if($pos === 0) {
    echo("<option value=\"/\">/</option>");
}

echo("</select>");

echo("</td><td>");

echo(" <a href=\"server_filemanager.php?dir=" . $up . "\" title=\"" . _("up") . "\"><IMG SRC=\"images/up.png\" hight=\"25\" width=\"25\" ALT=\"up\"></a>");

echo("</td></tr></table><br />");

$real_dir = $dirend;

$real_dir = substr($dirend, 1);

if($real_dir == "" || $real_dir == "/") {
    $real_dir = ".";
}

echo("<table border=\"1\"><tr>
<td width=\"30\"></td><td width=\"30\"></td>
<td align=\"center\">" . _("name") . " </td>
<td align=\"center\">" . _("size Kbytes") . " </td>
<td align=\"center\">" . _("edit") . " </td>
<td align=\"center\">" . _("properties") . "</td> <tr>");


$ftp_server_ip = "127.0.0.1";

$conn_id = ftp_connect($ftp_server_ip, 21, 5);

// login with username and password
$login_result = ftp_login($conn_id,$user,$password); 

// check connection
if((!$conn_id) || (!$login_result)) { 
    echo "FTP connection has failed!";
    echo "Attempted to connect to $ftp_server_ip for user $user"; 
    die; 
}  else {

    if($p_SB === _("Delete")) {

        require_once './del_dir.php';

        for($i = 0; $i <= sizeof($p_checkfile); $i++) {
            if(IsSet($p_checkfile[$i])) {
                ftp_delete($conn_id, $real_dir . "/" . $p_checkfile[$i]);
            }
        }

        for($i = 0; $i <= sizeof($p_checkdir); $i++) {
            if(IsSet($p_checkdir[$i])) {
                del_dir($real_dir . "/" . $p_checkdir[$i]);
            }
        }

    }


    if(IsSet($p_NewFolder) && $p_NewFolder !==  "") {
        ftp_mkdir($conn_id, $real_dir . "/" . $p_NewFolder) or die($error_makedir);
    }

    if($p_Paste === _("Paste")) {
        $paste_array = explode("<>", $_COOKIE['clipboard']);

        require_once './copy_directory.php';

        for($i = 1; $i < sizeof($paste_array) - 1; $i++) {
            $paste_specify = explode("<.>", $paste_array[$i]);

            if($paste_specify[0] === "checkfile[]") {
                $paste_filename = explode("/", $paste_specify[1]);

                ftp_put($conn_id, $real_dir . "/" . $paste_filename[sizeof($paste_filename) - 1],
                $userhomedir . "/" . $_SESSION['user'] . $www . $paste_specify[1], FTP_BINARY);
            } elseif($paste_specify[0] === "checkdir[]") {

                copy_directory($conn_id, $paste_specify[1], substr($real_dir, 0, sizeof($real_dir) - 2));

            }
        }
    }


    $result = ftp_rawlist($conn_id, $real_dir);

    require_once './split_rawlist.php';

    $directory_list = directory_list($result);

    for($i = 0; $i < sizeof($directory_list['directory']['name']); $i++) {
        $dirend_h = substr($dirend, 0, strlen($dirend) - 1);

        $dirend_size = $dirend_h;

        if($dirend_h === "/")
            $dirend_size = "";

        $forbidden_folder = $userhomedir . "/" . $_SESSION['user'] . $www
        . $dirend_size . "/" . $directory_list['directory']['name'][$i];

        $size_dir = execute_cmd("$exec_path/size_forbidden.php \"$forbidden_folder\"");

        $size_dir = $size_dir[0] / 1024;

        $size_dir = ceil($size_dir);

        echo("<tr><td width=\"30\"><input type=\"checkbox\" name=\"checkdir[]\"value=\"" . $directory_list[directory][name][$i] . "\"></td>
        <td width=\"30\"><a href=\"server_filemanager.php?dir=" . $directory_list['directory']['name'][$i] . "&down=down\" onclick=\"ups()\">
        <IMG SRC=\"images/folder.png\" ALT=\"dir\"align=\"left\"></a></td>
        <td>&nbsp;" . $directory_list['directory']['name'][$i] . "&nbsp;</td>
        <td>&nbsp;" . $size_dir . "&nbsp;</td>
        <td></td>
        <td><a href=\"javascript:chmod('$real_dir', '" . $directory_list['directory']['name'][$i] . "')\">" . _("properties") . "</a></td></tr>\n");

    }

    for($i = 0; $i < sizeof($directory_list['file']['name']); $i++) {
        $directory_list['file']['size'][$i] = ceil($directory_list['file']['size'][$i] / 1024);

        echo("<tr><td width=\"30\"><input type=\"checkbox\" name=\"checkfile[]\"value=\"" . $directory_list['file']['name'][$i] . "\"></td>
        <td width=\"30\"><IMG SRC=\"images/file.png\" ALT=\"file\"align=\"left\"></td>
        <td>&nbsp;" . $directory_list['file']['name'][$i] . "&nbsp;</td>
        <td>&nbsp;" . $directory_list['file']['size'][$i] . "&nbsp;</td>
        <td> <a href=\"javascript:edit_file('$real_dir', '" . $directory_list['file']['name'][$i] . "')\">" . _("edit") . "</a></td>
        <td><a href=\"javascript:chmod('$real_dir', '" . $directory_list['file']['name'][$i] . "')\">" . _("properties") . "</a></td></tr>\n");

    }
}

echo("</table>");

ftp_close($conn_id);
?>
<br />
<table cellpadding="2" cellspacing="2" border="0" align="left" width="100%">
<tr>
<td><input type="submit" name="SB" value="<?php echo _("Delete"); ?>"
onclick="if(confirm('<?php echo _("Delete these files and folders?"); ?>')) return true; else return false;">
<input type="button" name="NF" value="<?php echo _("New Folder"); ?>" onclick="new_folder()">
<input type="button" name="Copy" value="<?php echo _("Copy"); ?>" onclick="copy()">
<input type="submit" name="Paste" value="<?php echo _("Paste"); ?>">
</td>
</tr>
</table>

<input value="up" type="hidden" name="up">
<input type="hidden" name="NewFolder">

</form>

<script type="text/javascript">
if(parent.clientfilemanager.document.forms.length != 0) {
    parent.clientfilemanager.document.form1.dir.value = "<?php echo($dirend); ?>";
}
</script>
</div>
</body>
</html>
