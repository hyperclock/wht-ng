<?php
require_once './conf_inc.php';
require_once './execute_cmd.php';
require_once './i18n.php';
require_once './errors_inc.php';

session_start();
session_cache_limiter('nocache');

import_request_variables('p', 'p_');
import_request_variables('g', 'g_');


error_reporting($error_reporting);

if(!IsSet($p_hidden) || $p_hidden === "login") {
    @mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
    @mysql_select_db($database) or die($error_selectdb);
}

if(IsSet($g_del_domain)) {
    if($g_del_domain === "." || $g_del_domain === ".." || false !== strstr($g_del_domain, "/")) {
        echo("Nice Try!");
    } else {
        $p_user          = $_SESSION['user'];
        $p_password = $_SESSION['pass'];


        $query = "select ID, domain, subdomain, zone, user_id, quota from domains where domain='$g_del_domain'";
        $result  = mysql_query($query) or die($error_select);

        $res_del_dom = mysql_fetch_array($result);

        if($res_del_dom[subdomain] == "") {
            $query = "select subdomain from domains where zone='$res_del_dom[zone]'";
            $result = mysql_query($query) or die($error_select);

            while($row = mysql_fetch_array($result)) {
                $res_del[] = $row;
            }

            if(sizeof($res_del) > 1) {
                die($error_exist_subdomain);
            }

        }


        $query = "select email from emails where domain_id='$res_del_dom[ID]'";
        $result_emails = mysql_query($query) or die($error_select);

        while($row_emails = mysql_fetch_array($result_emails)) {
            $query = "delete from email_aliases where email='$row_emails[email]'";
            mysql_query($query);
        }


        $query = "delete from emails where domain_id='$res_del_dom[ID]'";
        mysql_query($query);



        $exec_cmd = "$vdeldomain $g_del_domain";
        execute_cmd("$exec_cmd");




        $ftp_server_ip = "127.0.0.1";

        $conn_id = ftp_connect($ftp_server_ip, 21, 5);

        // login with username and password
        $login_result = ftp_login($conn_id, $p_user, $p_password);

        // check connection
        if ((!$conn_id) || (!$login_result)) {
            echo "FTP connection has failed!";
            echo "Attempted to connect to $ftp_server_ip for user $p_user";
            die;
        }
        else {
            require_once './del_dir.php';

            del_dir($g_del_domain . "_cgi-bin");
            $del_res = del_dir($g_del_domain);
        }

        ftp_close($conn_id);



        $query = "insert into deleted (ID, domain, subdomain, zone) values(NULL, '$g_del_domain', '$res_del_dom[subdomain]', '$res_del_dom[zone]');";
        mysql_query($query) or die($error_insert);

        $query = "select user, quota from users where ID='$res_del_dom[user_id]'";
        $result = mysql_query($query) or die($error_select);

        $row_user = mysql_fetch_array($result);

        $quota_soft  = $row_user[quota] - $res_del_dom[quota];
        $quota_hard = $quota_soft + 20;

        $exec_cmd = "$setquotacmd -u $row_user[user] $quota_soft $quota_hard 0 0 -a $partition_used";
        execute_cmd("$exec_cmd");

        $query = "update users set quota='$quota_soft' where ID='$res_del_dom[user_id]'";
        $result = mysql_query($query) or die($error_update);



        $query = "delete from domains where domain='$g_del_domain';";
        mysql_query($query) or die($error_delete);



    }
}





if(IsSet($_SESSION['user']) && $p_hidden !== "newuser" && $p_hidden !== "login") {
    $p_user = $_SESSION['user'];
}

if(IsSet($_SESSION['pass']) && $p_hidden !== "newuser" && $p_hidden !== "login") {
    $p_password=$_SESSION['pass'];
}

$query = "select ID, user, password, quota, email, db from users where user='$p_user' and status!='NULL'";

$result = mysql_query($query) or die($error_select);

if(mysql_num_rows($result) != 0) {
    while($row=mysql_fetch_array($result)) {
        $res[]=$row;
    }

    if ($p_password !== $res[0]['password']) {
        header("Location:login.php?error=error_fill");
        exit;
    } else {
        $_SESSION['user']     = $res[0]['user'];
        $_SESSION['email']    = $res[0]['email'];
        $_SESSION['pass']     = $p_password;
        $_SESSION['user_id'] = $res[0]['ID'];
        $_SESSION['db']         = $res[0]['db'];
    }
} else {
header("Location:login.php?error=error_fill");
exit;
}

$user_id = $res[0]['ID'];


$query = "select domain, subdomain, zone, sub, free from domains where user_id='$user_id' and status!='NULL'";
$result  = mysql_query($query) or die($error_select);


while($row = mysql_fetch_array($result)) {
    $res_domains[]=$row;
}

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _(Domains); ?>Domains</title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style.css" />
</head>
<body>
<div>
<?php
include_once './templates/header.php';

echo _("User") . ":";
echo(" &nbsp; ");

$user_db = $res[0]['user'];


if(!IsSet($quotacmd)) {
    $quotacmd = "quota";
}

$quota_array = execute_cmd("$quotacmd $user_db");

$quota_array[2] = substr($quota_array[2], 15);

$quota_array[2] = trim($quota_array[2]);

$quota_exploaded = explode(" ",  $quota_array[2]);

$size_forbidden_result[0] = trim($quota_exploaded[0], "*");

$size_forbidden_result[0] = $size_forbidden_result[0] * 1024;


echo($res[0]['user']." -&nbsp;&nbsp;&nbsp;");


if($res[0]['db'] === "on") {
    
    mysql_select_db("mysql") or die($error_selectdb);

    $query = "select Db from db where User='$user_db'";
    $result_db = mysql_query($query) or die($error_select);

    while($row_db = mysql_fetch_array($result_db)) {
        $size_db_array = execute_cmd("$exec_path/size_forbidden.php \"$mysql_datadir/$row_db[Db]\"");

        $size_forbidden_result[0] += $size_db_array[0];
    }

}


$percents = $size_forbidden_result[0] * 100 / 1024 / $res[0]['quota'];
$percents = (int)$percents;

echo($percents . _("% of disk quota in use") .". <br /><br />");

for($i = 0; $i < sizeof($res_domains); $i++) {
    echo("<br /><font class=domain> " .  _("domain") .":  &nbsp; </font>");
    echo("<a href=\"http://".$res_domains[$i]['domain']."\" target=\"_blank\">".$res_domains[$i]['domain']."</a>\n");

    $domain = $res_domains[$i]['domain'];

    if ($res_domains[$i]['sub'] === "y") {
        $sub = "sub";
    } else {
        $sub = NULL;
    }


    $at_least_one_free = false;

    if($res_domains[$i]['free'] === "y") {
        $at_least_one_free = true;

        $modify = "&nbsp;&nbsp;&nbsp; <a href=\"free/modify_domain.php?domain=$domain\"> " . _("Change category") . " </a> ";

        $modify .= "&nbsp;&nbsp;&nbsp; <a href=\"modify_".$sub."domain.php?domain=$domain\"> " . _("Remove advertisement") . " </a>";

        $delete = " &nbsp;&nbsp;&nbsp; <a href=\"allocate.php?del_domain=$domain\" onclick=\"if(confirm('" . _("Are you sure you want to delete") . " $domain ?')) return true; else return false;\">" . _("Delete") . "</a>";
    } else {
        $modify = "&nbsp;&nbsp;&nbsp; <a href=\"modify_".$sub."domain.php?domain=$domain\"> " . _("Modify") . " </a> ";

        $delete = " &nbsp;&nbsp;&nbsp; <a href=\"allocate.php?del_domain=$domain\" onclick=\"if(confirm('" . _("Are you sure you want to delete") . " $domain ?')) return true; else return false;\">" . _("Delete") . "</a>";
    }



    if($enable_awstats === "on") {
        echo($modify . "&nbsp;&nbsp;&nbsp; <a href=\"$awstats?config=$domain\" target=\"_blank\"> AWStats </a>".$delete."<br />");
    } else {
        echo($modify . $delete . "<br />");
    }

}

if($only_free !== "yes") {

    echo("<br /><br />");

    for($i = 0; $i < sizeof($res_domains); $i++) {
        $fl_zone = false;
        for($j = 0; $j < sizeof($domain_name); $j++) {
            if($res_domains[$i]['subdomain'] == "" && $res_domains[$i]['zone'] !== $domain_name[$j]) {
                $fl_zone = true;
                break;
            } else {
                $fl_zone=false;
                break;
            }
        }
        if($fl_zone === true && ($at_least_one_free !== true && $enable_cgi_free == "on" || $at_least_one_free === true && $enable_cgi_free != "on" || $at_least_one_free !== true && $enable_cgi_free != "on")) {
            echo("<br /><font class=zone> " . _("zone") . ":  &nbsp; </font>");
            echo($res_domains[$i]['zone']);
            $zone = $res_domains[$i]['zone'];
            echo("  &nbsp; <a href=\"newsubdomain.php?zone=$zone\" >" . _("New subdomain") . "</a><br />");

        }


    }

 ?>
<br />
<br />
<?php
    if (!($enable_cgi_free == "on" && $at_least_one_free === true)) {
        echo("<a href=\"newdomain.php\">" . _("Register new domain") . "</a><br />");
    }
}

include_once './templates/footer.php';

?>

</div>
</body>
</html>
