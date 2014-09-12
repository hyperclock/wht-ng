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

session_start();
session_cache_limiter('nocache');

if(IsSet($_SESSION['user'])) {

    error_reporting($error_reporting);

    @mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
    @mysql_select_db($database) or die($error_selectdb);


    $query = "select ID, db from users where user='$_SESSION[user]'";
    $result = mysql_query($query) or die($error_select);

    $row = mysql_fetch_array($result);

    $user = $_SESSION['user'];

    if($row['db'] === "on") {

        import_request_variables('p', 'p_');
        import_request_variables('g', 'g_');

        $query = "select ID from domains where user_id='$row[ID]'";
        $result = mysql_query($query) or die($error_select);

        $num_db = $mysql_num_db*mysql_num_rows($result);

        if(IsSet($p_database)) {

            mysql_select_db("mysql") or die($error_selectdb);

            $query = "select Db from db where User='$user'";
            $result = mysql_query($query) or die($error_select);

            $num_created_db = mysql_num_rows($result);

            if($num_created_db < $num_db) {
            
                $password = $_SESSION['pass'];

                $p_database = $user . "_" . $p_database;

                mysql_create_db($p_database) or die(_("Database with that name already exist. Can't create database."));

                $query = "GRANT ALL PRIVILEGES ON $p_database.* TO $user@localhost IDENTIFIED BY '$password';";

                mysql_query($query) or die("Can't create new user");

                $query = "FLUSH PRIVILEGES;";
                mysql_query($query) or die("Cant FLUSH PRIVILEGES");

                $db_created = _("Database") . " $p_database ". _("created") . ". <br /> <br />";

            } else {
                $limit_reached = _("You can't create more than") . " $num_db " . _("databases") . ".";
            }
        }
        
        if(IsSet($g_drop_db)) {
        
            mysql_select_db("mysql") or die($error_selectdb);

            $query = " DROP DATABASE $g_drop_db";
            mysql_query($query) or die("Cant drop database");


            $query = "delete from db where Db='$g_drop_db' and User='$user'";
            mysql_query($query) or die($error_delete);

            $query = "FLUSH PRIVILEGES;";
            mysql_query($query) or die("Cant FLUSH PRIVILEGES");

        }
        
        echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("MySQL") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style.css" />
</head>
<body>
<div>
<?php
include_once './templates/header.php';
?>
<br />
<?php echo _("You can create"); ?> <?php echo("$num_db " . _("databases") . ". <br /><br />"); ?>
<?php echo("$db_created $limit_reached <br /><br />"); ?>
<?php echo _("Create database"); ?>:
<form name="form1" action="mysql.php" method="post" accept-charset="ISO-8859-1">             
<?php echo($user); ?>_<input name="database" size="20"> 
<input type="hidden" name="vari" value="vv">
<input type="submit" value="<?php echo _("Create"); ?>">
</form>
<br />
<p>
<?php


        mysql_select_db("mysql") or die($error_selectdb);

        $query = "select Db from db where User='$user'";
        $result_db = mysql_query($query) or die($error_select);

        while($row_db = mysql_fetch_array($result_db)) {
            
            echo("&nbsp;&nbsp; <font class=domain> " . _("database") . ":  &nbsp; </font>
            $row_db[Db] &nbsp;&nbsp;&nbsp; <a href=\"mysql.php?drop_db=$row_db[Db]\"
            onclick=\"if(confirm('" . _("Are you sure you want to drop database") . " $row_db[Db]?')) return true; else return false;\">" . _("Drop") . "</a><br />");
        }

    } else {
        require_once './templates/error_mysql.tpl';
    }


} else {
    header("Location:login.php");
    exit;
}
?>
</p>
<br />
<a href="<?php echo($phpmyadmin); ?>" target="_blank">
<?php echo _("Manage databases with phpMyAdmin"); ?> </a>
<?php
include_once './templates/footer.php';
?>
</div>
</body>
</html>
