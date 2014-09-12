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
require_once './errors_inc.php';

import_request_variables('p', 'p_');
import_request_variables('g', 'g_');

session_start();
session_cache_limiter('nocache');

error_reporting($error_reporting);

if($ftp_server === "proftpd") {
    $www = "/www";
} else {
    $www = "";
}

if($g_file[0] === "/") {
    $g_file = substr($g_file, 1, strlen($g_file));
}

$user               = $_SESSION['user'];
$password      = $_SESSION['pass'];
$ftp_server_ip = "127.0.0.1";

if($p_content) {

    $p_content = stripslashes($p_content);
    $p_content = str_replace("\r", "", $p_content);

    $conn_id = ftp_connect($ftp_server_ip, 21, 5);

    // login with username and password
    $login_result = ftp_login($conn_id, $user, $password);

    // check connection
    if ((!$conn_id) || (!$login_result)) {
        echo "FTP connection has failed!";
        echo "Attempted to connect to $ftp_server_ip for user $p_user";
        die;
    } else {
        ftp_delete($conn_id, "/" . $g_file);
        $file = "ftp://$user:$password@$ftp_server_ip/$g_file";

        $fp = fopen($file, "wb") or die("Can't open file");
        fwrite($fp, $p_content);

        fclose($fp);

    }

    ftp_close($conn_id);
}

$file = "ftp://$user:$password@$ftp_server_ip/$g_file";

$fp = fopen($file, "r") or die("Can't open for reading $file");

while(!feof ($fp)) {
$content .= fgets($fp);
}

fclose($fp);

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("Edit File") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style.css" />
<script type="text/javascript">
<!--
function save_file()
{
    if(document.form1.modify.value=='m')
        document.form1.submit();
}

function close_file()
{
    if(document.form1.modify.value=='nm') {
        window.close();
    } else {
        if(confirm('<?php echo _("Close without saving the file?"); ?>'))
            window.close();
    }

}
// -->
</script>
</head>
<body>
<div>
<form name="form1" action="edit_file.php?file=/<?php echo($g_file); ?>" method="post" accept-charset="ISO-8859-1">
<input type="button" name="save" value="<?php echo _("Save"); ?>" onclick="save_file()">
<input type="button" name="close" value="<?php echo _("Close"); ?>" onclick="close_file()">
<br />
<textarea name="content" rows="25" cols="80" OnChange="document.form1.modify.value='m'">
<?php echo(htmlentities($content)); ?>
</textarea>
<input type="hidden" value="nm" name="modify">
</form>
</div>
</body>
</html>
