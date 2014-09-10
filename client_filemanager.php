<?php
require_once './conf_inc.php';
require_once './i18n.php';

session_start();

error_reporting($error_reporting);

import_request_variables('p', 'p_');

$n = sizeof($_FILES['userfile']['name']);

$dir = substr($p_dir, 1, strlen($p_dir)-2);

if($dir == "") {
    $dir = ".";
}

if($n === NULL) {
    $n = 1;
}

$user          = $_SESSION['user'];
$password = $_SESSION['pass'];


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
    if(ftp_chdir ($conn_id, $dir)) {

        $fl = 0;

        for($i = 0; $i < $n; $i++) {
        
            if (is_uploaded_file($_FILES['userfile']['tmp_name'][$i])) {
            
                ftp_put($conn_id, $_FILES['userfile']['name'][$i], $_FILES['userfile']['tmp_name'][$i], FTP_BINARY);

                $fl++;
            } elseif($_FILES['userfile']['name'][$i] != "") {
                $fl_cant="notuploaded";
            }
        }
    }
}

ftp_close($conn_id);

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("Client File Manager") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style.css" />
</head>
<body>
<div>
<b><i><?php echo _("C L I E N T"); ?></i></b><br /><br />
<form name="form1" enctype="multipart/form-data" action="client_filemanager.php" method="post" accept-charset="ISO-8859-1">


<select name="num_files" onChange="submit()">


<?php
if($p_num_files == 0) {
    $p_num_files = 1;
}

for($i = 1; $i < 10; ++$i) {

    if($i!=$p_num_files) {
        echo("<option value=\"" . $i . "\">" . $i . "</option>");
    } else {
        echo("<option value=\"".$i."\" selected >".$i."</option>");
    }
}

echo("</select> " . _("files to upload") . " <br /><br />");


for($i = 0; $i < $p_num_files; $i++) {

    echo(" " . _("Send this file") . ": <input name=\"userfile[]\" type=\"file\"><br />");

}
?>
<script type="text/javascript">
<!--
document.write("<input type=\"submit\" value=\"Send File\">");

if(top.main.serverfilemanager)
	var opt=top.main.serverfilemanager.document.getElementsByTagName("option");

if(opt[0])
	document.write("<input type=\"hidden\" name=\"dir\" value=\""+opt[0].value+"/\">");
else
	document.write("<input type=\"hidden\" name=\"dir\" value=\"/\">");
// -->
</script>

</form>

<?php
if($fl > 0) {
    echo("<script type=\"text/javascript\">if(parent.serverfilemanager.document.form1.length!=0) { parent.serverfilemanager.location.replace(\"server_filemanager.php?dir=\"+parent.serverfilemanager.document.form1.directory.options[0].value.substr(1)); } </script>");
}

if($fl_cant === "notuploaded") {
    echo _("Can't upload all the files");
}
?>
</div>
</body>
</html>
