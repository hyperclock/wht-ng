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
