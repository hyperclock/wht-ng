<?php
require_once './check_posted.php';
require_once './conf_inc.php';
require_once './i18n.php';
require_once './errors_inc.php';

session_start();
session_cache_limiter('nocache');


if(!IsSet($_SESSION['user'])) {
    header("Location:login.php");
    exit();
}

import_request_variables('g', 'g_');
import_request_variables('p', 'p_');

error_reporting($error_reporting);

@mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
@mysql_select_db($database) or die($error_selectdb);


if($g_del_email!="") {

    $query = "select domain_id from emails where email='$g_del_email'";
    $result = mysql_query($query) or die($error_select);

    $row = mysql_fetch_array($result);


    $query = "select user_id from domains where ID='$row[domain_id]'";
    $result = mysql_query($query) or die($error_select);

    $row = mysql_fetch_array($result);


    $query = "select user from users where ID='$row[user_id]'";
    $result = mysql_query($query) or die($error_select);

    $row = mysql_fetch_array($result);


    if($row['user'] != $_SESSION['user']) {
        echo("Nice try!");
        exit();
    }

    require_once './execute_cmd.php';

    $exec_cmd = "$vdeluser $g_del_email";
    $result = execute_cmd("$exec_cmd");


    $query = "delete from email_aliases where email='$g_del_email'";
    mysql_query($query) or die($error_delete);


    $query = "delete from emails where email='$g_del_email'";
    $result = mysql_query($query) or die($error_select);

    echo($result[0]);

}



if($g_del_alias != "" && $g_email != "") {

    require_once './execute_cmd.php';

    $query = "select domain_id from emails where email='$g_email'";
    $result = mysql_query($query) or die($error_select);


    $row_emails = mysql_fetch_array($result);


    $query = "select user_id from domains where ID='$row_emails[domain_id]'";
    $result = mysql_query($query) or die($error_select);


    $row_domains = mysql_fetch_array($result);


    $query = "select user from users where ID='$row_domains[user_id]'";
    $result = mysql_query($query) or die($error_select);


    $row_users = mysql_fetch_array($result);


    if($row_users['user']===$_SESSION['user']) {

        $query = "delete from email_aliases where alias='$g_del_alias' and email='$g_email'";
        mysql_query($query) or die($error_delete);


        $query = "select alias, email from email_aliases where email='$g_email'";
        $result = mysql_query($query) or die($error_select);

        $exec_cmd = "$valias -d $g_email";
        execute_cmd("$exec_cmd");

        while($row=mysql_fetch_array($result)) {
        
            $exec_cmd = "$valias $row[email] -i $row[alias]";
            execute_cmd("$exec_cmd");

        }

    } else {
            echo("Nice Try");
    }
}



if($p_Submit === "Change" && $p_password != "") {

    $query = "select domain_id from emails where email='$p_email'";
    $result = mysql_query($query) or die($error_select);

    $row = mysql_fetch_array($result);


    $query = "select user_id from domains where ID='$row[domain_id]'";
    $result = mysql_query($query) or die($error_select);

    $row = mysql_fetch_array($result);


    $query = "select user from users where ID='$row[user_id]'";
    $result = mysql_query($query) or die($error_select);

    $row = mysql_fetch_array($result);


    if($row['user'] != $_SESSION['user']) {
        echo("Nice try!");
        exit();
    }

    require_once './execute_cmd.php';

    $exec_cmd = "$vpasswd $p_email $p_password";
    $result = execute_cmd("$exec_cmd");



    $query = "update emails set password='$p_password' where email='$p_email'";
    mysql_query($query) or die($error_update);

    echo($result[0]);

}

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("Email Accounts") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style.css" />
</head>
<body>
<div>
<?php
include_once './templates/header.php';
?>
<br />

<?php

$query = "select ID from users where user='$_SESSION[user]' and status!='NULL'";
$result = mysql_query($query) or die($error_select);

$row = mysql_fetch_array($result);

$query = "select ID, domain, num_emails from domains where user_id='$row[ID]' and status!='NULL' and sub=''";
$result = mysql_query($query) or die($error_select);


while($row = mysql_fetch_array($result)) {

    echo("<div><font class=domain>" . _("domain") . ": &nbsp; </font>" . $row['domain']
    . "  &nbsp; - &nbsp;  <a href=\"newemail.php?domain=$row[domain]\">"
    . _("Create new email") . "</a> &nbsp; &nbsp; " . _("You can use") . " "
    . $row['num_emails'] ." " . _("email accounts with this domain") . ".</div> \n");


    $query = "select email from emails where domain_id='$row[ID]'";
    $result_emails = mysql_query($query) or die($error_select);


    while($row_emails = mysql_fetch_array($result_emails)) {
    
        echo("<div style=\"margin: 2px 10px;\"><font class=email>" . _("email") . ": &nbsp; </font>"
        . $row_emails['email'] . "  &nbsp; <a href=\"newemail_alias.php?email=$row_emails[email]\">"
        . _("Forward to") . "</a> &nbsp; <a href=\"change_email_password.php?email=$row_emails[email]\">"
        . _("Change password") . " </a> &nbsp;  <a href=\"emails.php?del_email=$row_emails[email]\"
        onclick=\"if(confirm('" . _("Are you sure you want to delete the email?") . "')) return true;
        else return false;\"> " . _("Delete") . " </a></div> \n");


        $query = "select alias from email_aliases where email='$row_emails[email]'";
        $result_email_aliases = mysql_query($query) or die($error_select);


        while($row_email_aliases = mysql_fetch_array($result_email_aliases)) {
        
            echo("<div style=\"margin: 2px 20px;\"><font class=alias>" . _("forwarded to") . ":
            &nbsp; </font>" . $row_email_aliases['alias'] . " &nbsp;
            <a href=\"emails.php?del_alias=$row_email_aliases[alias]&email=$row_emails[email]\"
            onclick=\"if(confirm('" . _("Are you sure you want to stop forwarding?") . "')) return true;
            else return false;\"> ". _("Delete") . " </a></div> \n");
        }
    }
}


include_once './templates/footer.php';
?>
</div>
</body>
</html>
