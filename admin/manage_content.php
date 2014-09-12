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
require_once "../i18n.php";
require_once '../errors_inc.php';

error_reporting($error_reporting);

session_start();
session_cache_limiter('nocache');

if($_SESSION['login']==="yes") {

    import_request_variables('p', 'p_');
    
    if(IsSet($p_code)) {
    
        require_once '../execute_cmd.php';
        
        $result = execute_cmd("msgfmt -o $DocumentRoot/$version/locale/$p_code/LC_MESSAGES/wht-ng.mo \
$DocumentRoot/$version/locale/$p_code/LC_MESSAGES/wht-ng.po");

    }

    if ($dir=opendir("$DocumentRoot/$version/locale")) {
        while(($file = readdir($dir)) !== false) {
            if($file !== "." && $file !== ".." && $file !== "CVS") {
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
<title><?php echo _("Content Management"); ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="../css/<?php echo($stylesheet) ?>/style.css" />
</head>
<body>
<div>
<form name="form1" action="manage_content.php" method="post" accept-charset="ISO-8859-1">             
<br />
<?php
    if(IsSet($result)) {
        foreach($result as $key => $value) {
        echo($value);
        }
    }
    echo _("To build the mo file from the po one choose the language which file you have
edited and click the rebuild button");
?>

    <select name="code">
<?php
    for($i=0; $i<sizeof($dir_content); $i++) {
        echo("<option> ".$dir_content[$i]." </option>");
    }
?>
</select>
<input type="submit" name="Submit" value="Rebuild">
<input type="reset" name="Reset">
</form>
</div>
</body>
</html>

<?php
}

?>
