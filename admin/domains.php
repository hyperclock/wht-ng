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


import_request_variables('p', 'p_');
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
<script type="text/javascript">
<!--
function num(num)
{
    document.form1.num.value = num;
    document.form1.submit();
}
// -->
</script>
</head>
<body >
<div>
<form name="form1" action="domains.php" method="post" accept-charset="ISO-8859-1">
<input value="" type="hidden" name="num">
<input value="" type="hidden" name="domdel">
<input value="" type="hidden" name="user">
</form>
<?php

if($_SESSION['login'] === "yes") {

    error_reporting($error_reporting);

    if($g_restart === "yes") {
        require_once '../execute_cmd.php';

        execute_cmd("../wht-ng_cron.php 1");
    }

    if($g_num != "") {
        $p_num = $g_num; 
    }
    
    @mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
    @mysql_select_db($database) or die($error_selectdb);

    if($g_del_email != "") {
        require_once '../execute_cmd.php';

        $exec_cmd = "$vdeluser $g_del_email";
        $result = execute_cmd("$exec_cmd");

        $query = "delete from email_aliases where email='$g_del_email'";
        mysql_query($query) or die($error_delete);

        $query = "delete from emails where email='$g_del_email'";
        mysql_query($query) or die($error_delete);

        echo($result[0]);

    }

    if($g_del_alias != "" && $g_email != "") {
        require_once '../execute_cmd.php';

        $query = "delete from email_aliases where alias='$g_del_alias' and email='$g_email'";
        mysql_query($query) or die($error_delete);


        $query = "select alias, email from email_aliases where email='$g_email'";
        $result = mysql_query($query) or die($error_select);

        $exec_cmd = "$valias -d $g_email";
        execute_cmd("$exec_cmd");

        while($row = mysql_fetch_array($result)) {
            $exec_cmd = "$valias $row[email] -i $row[alias]";
            execute_cmd("$exec_cmd");

        }
    }


    if(IsSet($g_domdel) && $g_domdel != "") {
        require_once './domdel.php';
    }


    If($g_deluser != "") {
        require_once './deluser.php';
    }

    if($p_search == "" && $p_num == "") {
        $query = "select COUNT(*) from domains where status='1';";

        $result = mysql_query($query) or die($error_select);
        $row = mysql_fetch_array($result);
        $res[] = $row;

        echo _("Total domains") . " - " . $res[0][0];
        echo("<br /><br />To register new domain without WHT to change Apache's and BIND's configurations click <a href=\"newdomain_nr.php\" > here </a>");
        echo("<br /><br /> A postmaster email account will be created with a password from the configuration file (the variable \$postmaster_password)");
        echo("<br /><br />If you have deleted a user and intend to restart apache it will fail. To fix the problem");
        echo(" start wht-ng_cron.php from the command line or from here <br /><br /><a href=\"domains.php?restart=yes\">wht-ng_cron.php</a><br /><br />");
        echo("<a href=\"manage_content.php\">Manage content</a>");
    } else {
        if($p_search != "") {
            $_SESSION['search'] = $p_search;
            $search = "%" . $p_search . "%";

            $query = "select ID from domains where status='1' and domain like '$search' ORDER BY ID";

            $result = mysql_query($query) or die($error_select);

            if(mysql_num_rows($result) != 0) {
                while($row = mysql_fetch_array($result)) {
                    $res_id[] = $row;
                }

                echo " " . sizeof($res_id) . " " . _("matches found. Pages") . " ";

                $_SESSION['size'] = sizeof($res_id);

                $pages = sizeof($res_id) / 5;

                if(sizeof($res_id)%5 > 0) {
                    $pages = (int)$pages + 1;
                }
                
                $_SESSION['pages'] = $pages;
                $_SESSION['total'] = $res_id[sizeof($res_id) - 1][0];

                for($i = 0; $i < sizeof($_SESSION['num']); $i++) {
                    $_SESSION['num'][$i][1] = NULL;
                    $_SESSION['num'][$i][2] = NULL;
                }



                for($i = 1; $i <= ($pages); $i++) {
                    if($i == 1) {
                        echo(" 1");
                    } else {
                        echo(" <a href=\"domains.php?num=$i\">" . $i . "</a>");
                    }
                    
                    if($pages > 1) {
                        $_SESSION['num'][$i][1] = $res_id[($i - 1) * 5][0];

                        if($res_id[($i - 1) * 5 + 5][0] > $_SESSION['total']) {
                            $_SESSION['num'][$i][2] = $_SESSION['total'];
                        } else {
                            $_SESSION['num'][$i][2] = $res_id[($i - 1) * 5 + 4][0];
                        }

                    }
                }
            }
        } else {
            echo $_SESSION['size'] . " " . _("matches found. Pages") . " ";
        
            for($i = 1; $i <= ($_SESSION['pages']); $i++) {
                if($i == $p_num) {
                    echo(" $i ");
                } else {
                    echo(" <a href=\"domains.php?num=$i\">" . $i . "</a>");
                }
            }
        }


        if($p_num > 0) {
            $search = '%' . $_SESSION['search'] . '%';
            $from = $_SESSION['num'][$p_num][1];
            if($_SESSION['num'][$p_num][2] == NULL) {
                $to=$_SESSION['total'];
            } else {
                $to = $_SESSION['num'][$p_num][2];
            }
            
            $query = "select ID, domain, subdomain, zone, sub, user_id, num_emails from domains where status='1' and ID>='$from' and ID<='$to' and domain like '$search'";
        } else {
            $from = $_SESSION['num'][1][1];
            if($_SESSION['size'] > 5) {
                $to = $_SESSION['num'][1][2];
            } else {
                $to = $_SESSION['total'];
            }

            $query = "select ID, domain, subdomain, zone, user_id, sub, num_emails from domains where status='1' and ID>='$from' and ID<='$to' and domain like '$search' ";
        }

        $result = mysql_query($query) or die($error_query);

        if(mysql_num_rows($result) != 0) {
            while($row = mysql_fetch_array($result)) {
                $res[] = $row;
            }

            if($p_num == "") {
                $p_num = 1;
            }
            
            for($i = 0; $i < sizeof($res); $i++) {
                $user_id = $res[$i]['user_id'];
                $query = "select user, email from users where ID=$user_id";
                $result = mysql_query($query) or die($error_query);

                $row_user = mysql_fetch_array($result);

                $domain_id = $res[$i]['ID'];
                $query = "select email from emails where domain_id='$domain_id'";
                $result_emails = mysql_query($query) or die($error_select);

                if($row_user['user'] === "admin") {
                    echo "<br /><hr align=\"left\" size=\"3\" width=\"100%\">
                    <font class=user>" . _("user") . ": </font>" . $row_user['user'] . " <br />"
                    . _("If you delete this domain WHT will not delete Apache's and BIND's conf entries!")
                    . "<br /><font class=domain>" . _("domain") . ": </font> &nbsp;  <a href=\"http://".$res[$i]['domain']
                    . "\" target=\"_blank\">" . $res[$i]['domain']."</a>  &nbsp; <a href=\"newemail.php?domain="
                    . $res[$i]['domain'] . "&num=" . $p_num . "\" target=\"_blank\"> " . _("Create new email")
                    . "</a> &nbsp; <a href=\"domains.php?domdel=" . $res[$i]['domain']."&num=" . $p_num
                    . "\" onclick=\"if(confirm('" . _("Are you sure you want to delete") . " " .  $res[$i]['domain'] . "?')) return true; else return false;\">"
                    . _("Delete") . "</a>";
                } else {
                    if($res[$i]['sub'] === 'y' && $res[$i]['subdomain'] != "") {
                        echo "<br /><hr align=\"left\" size=\"3\" width=\"100%\"><font class=user>" . _("user") . ": </font>"
                        . $row_user['user'] . " &nbsp;  <a href=\"change_properties.php?user="
                        . $row_user['user'] . "&num=" . $p_num . "\" target=\"_blank\">" . _("Change properties")
                        . "</a> &nbsp;  <a href=\"databases.php?user=" . $row_user['user'] . "\" target=\"_blank\">"
                        . _("Databases") . "</a>  &nbsp; <a href=\"newdomain.php?user=" . $row_user['user']
                        . "\">" . _("New domain") . "</a>  &nbsp; <a href=\"domains.php?deluser="
                        . $row_user['user'] . "&num=" . $p_num
                        . "\" onclick=\"if(confirm('" . _("If you delete user") . " ".$row_user['user'] . " "
                        . _("you will delete all domains, databases and email accounts created from the user !!!   Delete ?")
                        . "')) return true; else return false;\">" . _("Delete") . "</a><br /><font class=email>"
                        . _("contact email") . ": &nbsp; </font> <a href=\"mailto:" . $row_user['email'] . "\">"
                        . $row_user['email'] . "</a><br /><font class=domain>" . _("domain") . ": </font> &nbsp;  
                        <a href=\"http://" . $res[$i]['domain']."\" target=\"_blank\">" . $res[$i]['domain']
                        . "</a> &nbsp; <a href=\"modify_subdomain.php?domain=" . $res[$i]['domain'] . "\" target=\"_blank\">"
                        . _("Modify") . "</a> &nbsp; <a href=\"domains.php?domdel=" . $res[$i]['domain'] . "&num="
                        . $p_num."\" onclick=\"if(confirm('" . _("Are you sure you want to delete") . "  "
                        . $res[$i]['domain'] . " ?')) return true; else return false;\">" . _("Delete") . "</a> &nbsp;
                        <a href=\"domain_info.php?domain=" . $res[$i]['domain'] . "\" target=\"_blank\">"
                        . _("Full info") . "</a>";
                    } elseif($res[$i]['subdomain'] != "") {
                        echo("<br /><hr align=\"left\" size=\"3\" width=\"100%\"><font class=user>" . _("user") . ": </font>"
                        . $row_user['user'] . " &nbsp;  <a href=\"change_properties.php?user="
                        . $row_user['user'] . "&num=" . $p_num . "\" target=\"_blank\">" . _("Change properties")
                        . "</a> &nbsp;  <a href=\"databases.php?user=" . $row_user['user'] . "\" target=\"_blank\">"
                        . _("Databases") . "</a>  &nbsp; <a href=\"newdomain.php?user=" . $row_user['user']
                        . "\">" . _("New domain") . "</a>  &nbsp; <a href=\"domains.php?deluser="
                        . $row_user['user'] . "&num=" . $p_num
                        . "\" onclick=\"if(confirm('" . _("If you delete user") . " ".$row_user['user'] . " "
                        . _("you will delete all domains, databases and email accounts created from the user !!!   Delete ?")
                        . "')) return true; else return false;\">" . _("Delete") . "</a><br /><font class=email>"
                        . _("contact email") . ": &nbsp; </font> <a href=\"mailto:" . $row_user['email'] . "\">"
                        . $row_user['email'] . "</a><br /><font class=domain>" . _("domain") . ": </font> &nbsp;  
                        <a href=\"http://" . $res[$i]['domain']."\" target=\"_blank\">" . $res[$i]['domain']
                        . "</a> &nbsp; <a href=\"modify_domain.php?domain=" . $res[$i]['domain'] . "\" target=\"_blank\">"
                        . _("Modify") . "</a> &nbsp; <a href=\"newemail.php?domain=" . $res[$i]['domain']
                        . "&num=" . $p_num . "\" target=\"_blank\">" . _("Create new email") . "</a>
                        &nbsp; <a href=\"domains.php?domdel=" . $res[$i]['domain'] . "&num="
                        . $p_num."\" onclick=\"if(confirm('" . _("Are you sure you want to delete") . "  "
                        . $res[$i]['domain'] . " ?')) return true; else return false;\">" . _("Delete") . "</a> &nbsp;
                        <a href=\"domain_info.php?domain=" . $res[$i]['domain'] . "\" target=\"_blank\">"
                        . _("Full info") . "</a>");
                    } else {
                        echo("<br /><hr align=\"left\" size=\"3\" width=\"100%\"><font class=user>" . _("user") . ": </font>"
                        . $row_user['user'] . " &nbsp;  <a href=\"change_properties.php?user="
                        . $row_user['user'] . "&num=" . $p_num . "\" target=\"_blank\">" . _("Change properties")
                        . "</a> &nbsp;  <a href=\"databases.php?user=" . $row_user['user'] . "\" target=\"_blank\">"
                        . _("Databases") . "</a>  &nbsp; <a href=\"newdomain.php?user=" . $row_user['user']
                        . "\">" . _("New domain") . "</a>  &nbsp; <a href=\"domains.php?deluser="
                        . $row_user['user'] . "&num=" . $p_num
                        . "\" onclick=\"if(confirm('" . _("If you delete user") . " ".$row_user['user'] . " "
                        . _("you will delete all domains, databases and email accounts created from the user !!!   Delete ?")
                        . "')) return true; else return false;\">" . _("Delete") . "</a><br /><font class=email>"
                        . _("contact email") . ": &nbsp; </font> <a href=\"mailto:" . $row_user['email'] . "\">"
                        . $row_user['email'] . "</a><br /><font class=domain>" . _("domain") . ": </font> &nbsp;  
                        <a href=\"http://" . $res[$i]['domain']."\" target=\"_blank\">" . $res[$i]['domain']
                        . "</a> &nbsp; <a href=\"modify_domain.php?domain=" . $res[$i]['domain'] . "\" target=\"_blank\">"
                        . _("Modify") . "</a> &nbsp; <a href=\"newemail.php?domain=" . $res[$i]['domain']
                        . "&num=" . $p_num . "\" target=\"_blank\">" . _("Create new email") . "</a>
                        &nbsp; <a href=\"newsubdomain.php?zone=".$res[$i]['zone'] . "&user=" . $row_user['user']
                        . "\">" . _("New subdomain") . "</a>&nbsp; <a href=\"domains.php?domdel=" . $res[$i]['domain'] . "&num="
                        . $p_num."\" onclick=\"if(confirm('" . _("Are you sure you want to delete") . "  "
                        . $res[$i]['domain'] . " ?')) return true; else return false;\">" . _("Delete") . "</a> &nbsp;
                        <a href=\"domain_info.php?domain=" . $res[$i]['domain'] . "\" target=\"_blank\">"
                        . _("Full info") . "</a>");
                    }
                }


                while($row_emails = mysql_fetch_array($result_emails)) {
                    echo "<div style=\"margin: 2px 10px;\"><font class=email>" . _("email") . ": </font>
                    $row_emails[email]  &nbsp; <a href=\"newemail_alias.php?email=$row_emails[email]&num="
                    . $p_num . "\" target=\"_blank\">" . _("Forward to") . "</a>  &nbsp;
                    <a href=\"change_email_password.php?email=$row_emails[email]\" target=\"_blank\">"
                    . _("Password") . "</a> &nbsp; <a href=\"domains.php?del_email=$row_emails[email]&num="
                    . $p_num . "\"  onclick=\"if(confirm('" . _("Are you sure you want to delete") . "  "
                    . $row['emails'] . " ?')) return true; else return false;\">" . _("Delete") . "</a></div>";



                    $query = "select alias from email_aliases where email='$row_emails[email]'";
                    $result_email_aliases = mysql_query($query) or die($error_select);


                    while($row_email_aliases = mysql_fetch_array($result_email_aliases)) {
                        echo("<div style=\"margin: 2px 20px;\"><font class=alias>" . _("forward to")
                        . ": </font> $row_email_aliases[alias] &nbsp;
                        <a href=\"domains.php?del_alias=$row_email_aliases[alias]&email=$row_emails[email]&num=$p_num\"
                        onclick=\"if(confirm('" . _("Are you sure you want to delete") . " $row_email_aliases[alias]?'))
                        return true; else return false;\">" . _("Delete") . "</a></div>");

                    }
                }
            }
        } else {
            echo _("Can not find domain") . " - $p_search.";
        }
    } 
}
?>
</div>
</body>
</html>
