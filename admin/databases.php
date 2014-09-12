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

session_start();
session_cache_limiter('nocache');

if($_SESSION['login'] === "yes") {

    import_request_variables('g', 'g_');
    
    echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("Web Hosting Toolkit") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="../css/<?php echo($stylesheet); ?>/style.css" />
</head>
<body>
<div>
<?php
    echo _("User") . ": $g_user <br /> <br />";

    mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
    mysql_select_db($database) or die($error_selectdb);

    mysql_select_db("mysql") or die($error_selectdb);

    if(IsSet($g_drop_db)) {
        mysql_select_db("mysql") or die($error_selectdb);

        $query = " DROP DATABASE $g_drop_db";
        mysql_query($query) or die("Cant drop database");

        $query = "delete from db where Db='$g_drop_db' and User='$g_user'";
        mysql_query($query) or die($error_delete);

        $query = "FLUSH PRIVILEGES;";
        mysql_query($query) or die("Cant FLUSH PRIVILEGES");

    }


    $query = "select Db from db where User='$g_user'";
    $result_db = mysql_query($query) or die($error_select);

    while($row_db = mysql_fetch_array($result_db)) {
        echo("&nbsp;&nbsp; <font class=domain>" . _("database")
        . ":  &nbsp; </font> $row_db[Db] &nbsp;&nbsp;&nbsp;
        <a href=\"databases.php?user=$g_user&drop_db=$row_db[Db]\"
        onclick=\"if(confirm('" . _("Are you sure you want to drop database") . " $row_db[Db]?')) return true; else return false;\">"
        . _("Drop") . "</a><br />");
    }

}
?>
</div>
</body>
</html>
