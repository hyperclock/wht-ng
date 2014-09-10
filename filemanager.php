<?php
require_once './conf_inc.php';
require_once './i18n.php';

session_start();

if(!IsSet($_SESSION['user'])) {
    header("Location:login.php");
    exit();
}

import_request_variables('g', 'g_');

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">

<html> 
<head>
<title>File Manager</title>
</head>

<frameset cols="*,240pt" border="5"> 
<frame src="server_filemanager.php?domain=<?php echo($g_domain); ?>"
name="serverfilemanager" />
<frame src="client_filemanager.php" name="clientfilemanager" /> 

<noframes>You must use a browser that can display frames 
to see this page. </noframes>
</frameset> 
</html>
