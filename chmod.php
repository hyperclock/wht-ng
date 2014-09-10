<?php
require_once './conf_inc.php';
require_once './i18n.php';

session_start();
session_cache_limiter('nocache');

if(!IsSet($_SESSION['user'])) {
    die("NO USER HAD BEEN SET");
}

error_reporting($error_reporting);

import_request_variables('g', 'g_');
import_request_variables('p', 'p_');

if($p_file) {

    $file_old = $g_file;
    $g_file    = $p_file;
}

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("File Properties") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style.css" />
</head>
<body>
<div>

<form name="form1" action="chmod.php?dir=<?php echo($g_dir); ?>&file=<?php echo($g_file); ?>" method="post" accept-charset="ISO-8859-1">

<?php echo _("file"); ?>: <input type="text "name="file" value="<?php echo($g_file) ?>">
<br /><br />
<table cellpadding="2" cellspacing="2" margin-left="auto"
width="100%" margin-right="0px" border="3">
<tbody>
   
<?php


$user          = $_SESSION['user'];
$password = $_SESSION['pass'];
$real_dir     = $g_dir;

$ftp_server_ip = "127.0.0.1";

$conn_id = ftp_connect($ftp_server_ip, 21, 5); 

// login with username and password
$login_result = ftp_login($conn_id, $user, $password); 

// check connection
if ((!$conn_id) || (!$login_result)) {
 
    echo "FTP connection has failed!";
    echo "Attempted to connect to $ftp_server_ip for user $p_user"; 
    die; 
 }  else {

    if(sizeof($_POST) !== 0) {
        
        if($real_dir === ".") {
            $site_dir = "";
        } else {
            $site_dir = $real_dir;
        }

        ftp_rename ($conn_id, $site_dir.$file_old, $site_dir.$p_file);



        if($p_read_user === "on") {
            $ru = 4;
        }
        
        if($p_read_group === "on") {
            $rg = 4;
        }

        if($p_read_others === "on") {
            $ro = 4;
        }

        if($p_write_user === "on") {
            $wu = 2;
        }

        if($p_write_group === "on") {
            $wg = 2;
        }

        if($p_write_others === "on") {
            $wo = 2;
        }

        if($p_exec_user === "on") {
            $eu = 1;
        }

        if($p_exec_group === "on") {
            $eg = 1;
        }

        if($p_exec_others === "on") {
            $eo = 1;
        }



        $mod_u = $ru + $wu + $eu;
        $mod_g = $rg + $wg + $eg;
        $mod_o = $ro + $wo + $eo;

        $site_mod = "$mod_u$mod_g$mod_o";


        $site = "CHMOD $site_mod $site_dir$g_file";

        ftp_site($conn_id, $site);

        if($file_old != $p_file) {
        
            $refresh_dir = substr($g_dir, 0, strlen($g_dir) - 1);

            echo("<script type=\"text/javascript\"> opener.location.replace(\"server_filemanager.php?dir=$refresh_dir\"); window.close(); </script>");
        }

    }


    $result = ftp_rawlist($conn_id, $real_dir);

    require_once './split_rawlist.php';

    $directory_list = directory_list($result);

    for($i = 0; $i < sizeof($directory_list['file']['name']); $i++) {
        
        if($directory_list['file']['name'][$i] === $g_file) {

            if($directory_list[file][permitions][$i][1] !== "-") {
                $mod['r']['u'] = "checked";
            }
            if($directory_list['file']['permitions'][$i][2] !== "-") {
                $mod['w']['u'] = "checked";
            }
            if($directory_list['file']['permitions'][$i][3] !== "-") {
                $mod['e']['u'] = "checked";
            }
            if($directory_list['file']['permitions'][$i][4] !== "-") {
                $mod['r']['g'] = "checked";
            }
            if($directory_list['file']['permitions'][$i][5] !== "-") {
                $mod['w']['g'] = "checked";
            }
            if($directory_list['file']['permitions'][$i][6] !== "-") {
                $mod['e']['g'] ="checked";
            }
            if($directory_list['file']['permitions'][$i][7] !== "-") {
                $mod['r']['o'] = "checked";
            }
            if($directory_list['file']['permitions'][$i][8] !== "-") {
                $mod['w']['o'] = "checked";
            }
            if($directory_list['file']['permitions'][$i][9] !== "-") {
                $mod['e']['o'] = "checked";
            }
?>
<tr>
<td valign="bottom" width="25%" align="center"><?php echo _("Class"); ?><br />
</td>
<td valign="bottom" width="25%" align="center"><?php echo _("Read"); ?><br />
</td>
<td valign="bottom" width="25%" align="center"><?php echo _("Write"); ?><br />
</td>
<td valign="bottom" width="25%" align="center"><?php echo _("Exec"); ?><br />
</td>
</tr>

<?php
            echo("<tr><td width=\"30\" align=\"center\"> ". _("User") . " </td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"read_user\" ".$mod[r][u]."></td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"write_user\" ".$mod[w][u]."></td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"exec_user\" ".$mod[e][u]."></td></tr>");

            echo("<tr><td width=\"30\" align=\"center\"> ". _("Group") . "</td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"read_group\" ".$mod[r][g]."></td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"write_group\" ".$mod[w][g]."></td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"exec_group\" ".$mod[e][g]."></td></tr>");

            echo("<tr><td width=\"30\" align=\"center\"> ". _("Others") . "</td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"read_others\" ".$mod[r][o]."></td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"write_others\" ".$mod[w][o]."></td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"exec_others\" ".$mod[e][o]."></td></tr>");

            echo("<tr> ". _("Last Modified") . "- ".$directory_list['file']['last_modified'][$i]."<br /><br /></tr>");
            }
        }

    for($i = 0; $i < sizeof($directory_list['directory']['name']); $i++) {
    
        if($directory_list['directory']['name'][$i] === $g_file) {

            if($directory_list['directory']['permitions'][$i][1] !==  "-") {
                $mod[r][u] = "checked";
            }
            if($directory_list['directory']['permitions'][$i][2] !== "-") {
                $mod[w][u] = "checked";
            }
            if($directory_list['directory']['permitions'][$i][3] !== "-") {
                $mod[e][u] = "checked";
            }
            if($directory_list['directory']['permitions'][$i][4] !== "-") {
                $mod[r][g] = "checked";
            }
            if($directory_list['directory']['permitions'][$i][5] !== "-") {
                $mod[w][g] = "checked";
            }
            if($directory_list['directory']['permitions'][$i][6] !== "-") {
                $mod[e][g] = "checked";
            }
            if($directory_list['directory']['permitions'][$i][7] !== "-") {
                $mod[r][o] = "checked";
            }
            if($directory_list['directory']['permitions'][$i][8] !== "-") {
                $mod[w][o] = "checked";
            }
            if($directory_list['directory']['permitions'][$i][9] !== "-") {
                $mod[e][o] = "checked";
            }
?>
<tr>
<td valign="bottom" width="25%" align="center"><?php echo _("Class"); ?><br />
</td>
<td valign="bottom" width="25%" align="center"><?php echo _("Show"); ?><br />
</td>
<td valign="bottom" width="25%" align="center"><?php echo _("Write"); ?><br />
</td>
<td valign="bottom" width="25%" align="center"><?php echo _("Enter"); ?><br />
</td>
</tr>

<?php
            echo("<tr><td width=\"30\" align=\"center\"> ". _("User") . " </td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"read_user\" ".$mod[r][u]."></td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"write_user\" ".$mod[w][u]."></td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"exec_user\" ".$mod[e][u]."></td></tr>");

            echo("<tr><td width=\"30\" align=\"center\"> ". _("Group") . " </td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"read_group\" ".$mod[r][g]."></td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"write_group\" ".$mod[w][g]."></td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"exec_group\" ".$mod[e][g]."></td></tr>");

            echo("<tr><td width=\"30\" align=\"center\"> ". _("Others") . " </td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"read_others\" ".$mod[r][o]."></td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"write_others\" ".$mod[w][o]."></td>
            <td width=\"30\" align=\"center\"><input type=\"checkbox\" name=\"exec_others\" ".$mod[e][o]."></td></tr>");

            echo("<tr> ". _("Last Modified") . " - ".$directory_list['directory']['last_modified'][$i]."<br /><br /></tr>");
        }
    }
}

ftp_close($conn_id);

?>

</tbody>
</table>
<br />
<table cellpadding="2" cellspacing="2" border="0" align="left" width="100%">
<tr>
<td width="70%"><input type="submit"  value="<?php echo _("Close"); ?>" onclick="window.close()" ></td>
<td width="15%"><input type="submit"  value="<?php echo _("OK"); ?>" ></td>
<td width="15%"><input type="button" value="<?php echo _("Cancel"); ?>" onclick="window.close()"></td>

</tr>
</table>

</form>
</div>
</body>
</html>
