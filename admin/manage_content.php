<?php
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
        
        $result = execute_cmd("msgfmt -o $DocumentRoot/$version/locale/$p_code/LC_MESSAGES/wht.mo \
$DocumentRoot/$version/locale/$p_code/LC_MESSAGES/wht.po");

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
