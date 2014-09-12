#!/usr/bin/php -q
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
require_once './errors_inc.php';

error_reporting($error_reporting);

$timestamp = time();

mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
mysql_select_db($database) or die($error_selectdb);


$timestamp_check = $timestamp - 7200;

$query = "delete from users where status IS NULL and timestamp<'$timestamp_check';";
$result = mysql_query($query) or die($error_delete);


$query = "delete from temporary_domains where timestamp<'$timestamp_check';";
$result = mysql_query($query) or die($error_delete);


$query = "delete from temporary_users where timestamp<'$timestamp_check';";
$result = mysql_query($query) or die($error_delete);

$query = "select domain, subdomain, zone from domains where domaincheck='1' and status IS NULL and timestamp<'$timestamp_check';";
$result = mysql_query($query) or die($error_select);

if(mysql_num_rows($result) != 0) {
    while($row = mysql_fetch_array($result)) {
        $res_not_verified[] = $row;
    }
}


for($i = 0; $i < sizeof($res_not_verified); $i++) {
    $query = "insert into deleted (domain, subdomain, zone) values ('$res_not_verified[domain]', '$res_not_verified[subdomain]', '$res_not_verified[zone]')";
    $result = mysql_query($query) or die($error_insert);
}


$query = "delete from domains where domaincheck='1' and status IS NULL and timestamp<'$timestamp_check';";
$result = mysql_query($query) or die($error_select);


$query = "lock tables domains write, deleted write, users write;";
mysql_query($query) or die($error_query);


$query = "select domain, subdomain, zone, user_id ,script, free from domains where domaincheck IS NULL;";
$result = mysql_query($query) or die($error_select);

if(mysql_num_rows($result) != 0) {
    while($row = mysql_fetch_array($result)) {
        $res[] = $row;
    }
}

$query = "select domain, subdomain, zone, modified from deleted;";
$result = mysql_query($query) or die($error_select);

if(mysql_num_rows($result) != 0) {
    while($row = mysql_fetch_array($result)) {
        $res_del[] = $row;
    }
}

if(sizeof($res_del) > 0  || sizeof($res) > 0) {
    $file = $httpd_confdir . "/include/include";
    $fp = fopen($file, "r") or die("Can't open ".$file);

    $read_array = file($file);

    fclose($fp);

    $size = sizeof($read_array);

    for($i = 0; $i < ($size); $i++) {
        for($i_del = 0;$i_del < sizeof($res_del) + 1; $i_del++) {
            if($res_del[$i_del]['domain'] != "" && $res_del[$i_del]['modified'] != "y"
            && strpos($read_array[$i], "include " . $httpd_confdir . "/include/" . $res_del[$i_del]['domain']) !== false) {

                unlink($httpd_confdir . "/include/" . $res_del[$i_del]['domain']);
                unlink($httpd_logdir . "/access_" . $res_del[$i_del]['domain']);
                unlink($httpd_logdir . "/bytes/" . $res_del[$i_del]['domain']);
                unlink($httpd_logdir . "/error_" . $res_del[$i_del]['domain']);
                unlink($httpd_logdir . "/ssl_request_" . $res_del[$i_del]['domain']);

                $read_array[$i] = NULL;

                if($enable_awstats === "on") {
                    if($directory_awstats = opendir($awstats_DataDir)) {
                        while(($file_awstats = readdir($directory_awstats)) !== false) {
                            if($file_awstats === "." || $file_awstats === "..") {
                                continue;
                            }
                            
                            if($res_del[$i_del]['domain'] . ".txt" === substr($file_awstats, 14)) {
                                unlink($awstats_DataDir . "/" . $file_awstats);
                            }

                        }
                        closedir($directory);
                    }
                }
            } elseif($res_del[$i_del]['domain']!=""
            && strpos($read_array[$i], "include " . $httpd_confdir . "/include/" . $res_del[$i_del]['domain']) !== false) {
                
                unlink($httpd_confdir . "/include/" . $res_del[$i_del]['domain']);

                $read_array[$i] = NULL;

            }
        }
    }

    for($i = 0; $i < sizeof($res); ++$i) {
        $user_id = $res[$i]['user_id'];

        $query = "select user from users where ID=$user_id";
        $result = mysql_query($query) or die($error_select);

        $usr = mysql_fetch_array($result);

        $fp = fopen($httpd_confdir . "/include/" . $res[$i]['domain'], "w+");

        if($ftp_server === "proftpd") {
            $www = "www/";
        } else {
            $www = "";
        }

        if($res[$i]['free'] === "y") {
            if($enable_cgi_free != "on") {
                $include_content = "<VirtualHost $virtual_host_ip>

    ServerName " . $res[$i]['domain'] . "
    ServerAlias www." . $res[$i]['domain'] . "
    RLimitCPU\t30
    RLimitMem\t20000000
    RLimitNProc\t20

    DocumentRoot " . $userhomedir . "/" . $usr['user'] . "/" . $www . $res[$i]['domain'] . "
    DirectoryIndex index.php index.php3 index.html index.htm

    ScriptAlias /cgi-bin/ \"$cgi_directory\"

    Action text/html /cgi-bin/wht-ng_handler.cgi

    LogFormat \" %b\" wht-ng_log
    CustomLog " . $httpd_logdir . "/bytes/" . $res[$i]['domain'] . " wht-ng_log
    CustomLog " . $httpd_logdir . "/access_" . $res[$i]['domain'] . " $httpd_logformat
    ErrorLog " . $httpd_logdir . "/error_" . $res[$i]['domain'] . "

</VirtualHost>

";
            } else {
                if($suexec === "on") {
                    $suexec_row = "SuexecUserGroup " . $usr['user'] . " " . $usr['user'];
                }
                
                $include_content = "<VirtualHost $virtual_host_ip>

    ServerName " . $res[$i]['domain'] . "
    ServerAlias www." . $res[$i]['domain'] . "
    RLimitCPU\t30
    RLimitMem\t20000000
    RLimitNProc\t20

    DocumentRoot " . $userhomedir . "/" . $usr['user'] . "/" . $www . $res[$i]['domain'] . "
    DirectoryIndex index.php index.php3 index.html index.htm

    ExtFilterDefine wht-ng_ext_filter mode=output cmd=\"/bin/wht-ng_ext_filter $userhomedir/".$usr[user]."/".$www.$res[$i][domain]."/some_file.html\"

    SetOutputFilter wht-ng_ext_filter

    ScriptAlias /cgi-bin/ \"" . $userhomedir . "/" . $usr['user'] . "/" . $www.$res[$i]['domain'] . "_cgi-bin/\"

    $suexec_row

    LogFormat \" %b\" wht-ng_log
    CustomLog " . $httpd_logdir . "/bytes/" . $res[$i]['domain'] . " wht-ng_log
    CustomLog " . $httpd_logdir . "/access_" . $res[$i]['domain'] . " $httpd_logformat
    ErrorLog " . $httpd_logdir . "/error_" . $res[$i]['domain'] . "

</VirtualHost>

";

            }
        } elseif($res[$i]['script'] === "on") {
            if($suexec === "on")
                $suexec_row = "SuexecUserGroup " . $usr['user'] . " " . $usr['user'];

        $include_content = "<VirtualHost $virtual_host_ip>

    ServerName " . $res[$i]['domain']."
    ServerAlias www." . $res[$i]['domain'] . "
    RLimitCPU\t30
    RLimitMem\t20000000
    RLimitNProc\t20

    DocumentRoot " . $userhomedir . "/" . $usr['user'] . "/" . $www.$res[$i]['domain'] . "
    DirectoryIndex index.php index.php3 index.html index.htm

    AddType application/x-httpd-php .php

    ScriptAlias /cgi-bin/ \"" . $userhomedir . "/" . $usr['user'] . "/" . $www . $res[$i]['domain'] . "_cgi-bin/\"

    php_admin_value open_basedir " . $userhomedir . "/" . $usr['user'] . "/" . $www . $res[$i]['domain'] . "

    $suexec_row

    LogFormat \" %b\" wht-ng_log
    CustomLog " . $httpd_logdir . "/bytes/" . $res[$i]['domain'] . " wht-ng_log
    CustomLog " . $httpd_logdir . "/access_" . $res[$i]['domain'] . " $httpd_logformat
    ErrorLog " . $httpd_logdir . "/error_" . $res[$i]['domain'] . "

</VirtualHost>

";
        } else {
            $include_content = "<VirtualHost $virtual_host_ip>

    ServerName " . $res[$i]['domain'] . "
    ServerAlias www." . $res[$i]['domain'] . "
    RLimitCPU\t30
    RLimitMem\t20000000
    RLimitNProc\t20

    DocumentRoot " . $userhomedir . "/" . $usr['user'] . "/" . $www . $res[$i]['domain'] . "
    DirectoryIndex index.php index.php3 index.html index.htm

    LogFormat \" %b\" wht-ng_log
    CustomLog " . $httpd_logdir . "/bytes/" . $res[$i]['domain'] . " wht-ng_log
    CustomLog " . $httpd_logdir . "/access_" . $res[$i]['domain'] . " $httpd_logformat
    ErrorLog " . $httpd_logdir . "/error_" . $res[$i]['domain'] . "

</VirtualHost>

";
        }



        fwrite($fp, $include_content);

        fclose($fp);

        $read_array[$size + $i] = "\ninclude " . $httpd_confdir . "/include/" . $res[$i]['domain'];

        if($enable_awstats === "on") {
            $file = $DocumentRoot . "/" . $version . "/awstats/awstats.model.conf";
            $fp = fopen($file, "r");

            $read_array_awstats = file($file);

            fclose($fp);

            $read_array_awstats[] = "\nLogFile=\"$httpd_logdir/access_" . $res[$i]['domain'] . "\"\n";
            $read_array_awstats[] = "SiteDomain=\"" . $res[$i]['domain'] . "\"\n";
            $read_array_awstats[] = "HostAliases=\"" . $res[$i]['domain'] . "\"\n";

            $output_awstats = "";

            foreach ($read_array_awstats as $key => $value) {
                $output_awstats .= $value;
            }

            $file = $awstats_confdir . "/awstats." . $res[$i]['domain'] . ".conf";
            $fp = fopen($file, "w");

            fwrite($fp, $output_awstats);

            fclose($fp);

        }
    }




    for($i = 0; $i <= ($size+sizeof($res)); $i++) {
        if($read_array[$i] == "\n" || $read_array[$i] == NULL) {
            continue;
        }
        
        $output = $output . $read_array[$i];
    }



    $query = "update domains set domaincheck=1 where domaincheck IS NULL;";
    $result = mysql_query($query) or die($error_query);

    $query = "delete from deleted;";
    $result = mysql_query($query) or die($error_query);

    $query = "unlock tables;";
    mysql_query($query) or die($error_query);

    $file = $httpd_confdir . "/include/include";

    $fp = fopen($file, "w+") or die("Can't open " . $file);

    fwrite($fp, $output . "\n");

    fclose($fp);



    for($i = 0; $i < sizeof($res_del); ++$i) {
        if($res_del[$i]['subdomain'] != "") {
            $file = $named_db . "/" . $res_del[$i]['zone'] . ".db";
            $fp = fopen($file, "r");

            $read_array = file($file);

            fclose($fp);

            $size_subdomain = sizeof($read_array);

            for($j = 0; $j < ($size_subdomain); $j++) {
                if($res_del[$i]['subdomain'] != "" &&
                (strpos($read_array[$j], $res_del[$i]['subdomain']."	IN	A	")===0 || strpos($read_array[$j], $res_del[$i]['subdomain']."    IN    A    ")===0)) {
                    $read_array[$j] = NULL;
                }
            }
            $output = "";

            for($j = 0; $j < ($size_subdomain + sizeof($res_del)); $j++) {
                $output = $output . $read_array[$j];
            }

            $output = $output . "\n";

            $fp = fopen($file, "w+");

            fwrite($fp, $output);

            fclose($fp);

        }
    }



    $file = $named_confdir . "/wht-ng_named/include";
    $fp = fopen($file, "r");

    $read_array = file($file);

    fclose($fp);

    $size_named_conf_del = sizeof($read_array);

    for($i = 0;$i < sizeof($res_del); ++$i) {
        if($res_del[$i]['subdomain'] == "") {
            for($j = 0; $j < ($size_named_conf_del); $j++) {
                if($res_del[$i]['zone'] != "" && $res_del[$i]['modified'] != "y"
                && strpos($read_array[$j], "include \"" . $named_confdir . "/wht-ng_named/" . $res_del[$i]['zone'] . "\";") !== false) {
                    $read_array[$j] = NULL;

                    unlink($named_confdir . "/wht-ng_named/" . $res_del[$i]['zone']);
                    unlink($named_db . "/" . $res_del[$i]['zone'] . ".db");
                }
            }
            $output = "";

            for($j = 0; $j < ($size_named_conf_del + sizeof($res_del)); $j++) {
                if($read_array[$j] == "\n" || $read_array[$j] == NULL) {
                    continue;
                }

                $output = $output.$read_array[$j];
            }

            $fp = fopen($file, "w+");

            fwrite($fp, $output . "\n");

            fclose($fp);
        }
    }



    $file = $named_confdir . "/wht-ng_named/include";
    $fp = fopen($file, "r");

    $read_array = file($file);

    fclose($fp);

    $size = sizeof($read_array);

    for($i = 0;$i < sizeof($res); ++$i) {
        if($res[$i]['subdomain'] == "") {
            if(!file_exists($named_confdir . "/wht-ng_named/" . $res[$i]['domain'])) {
                $read_array[$size + $i] = "\ninclude \"" . $named_confdir . "/wht-ng_named/" . $res[$i]['domain'] . "\";";

                $fp = fopen($named_confdir . "/wht-ng_named/" . $res[$i]['domain'], "w+") or die("Can't open ".$file);

                $include_content = "zone \"" . $res[$i]['domain'] . "\" {
    type master;
    file \"" . $res[$i]['domain'] . ".db\";
    notify yes;
};
";

                fwrite($fp, $include_content);

                fclose($fp);

            }

            
            if(!file_exists($named_db . "/" . $res[$i]['domain'] . ".db")) {
                $fp=fopen($named_db . "/" . $res[$i]['domain'] . ".db", "w+") or die("Can't open ".$file);

                $include_content = "\$TTL 86400

" . $res[$i]['domain'] . ".    IN    SOA    " . $NS1 . "    $hostmaster (
    199602151 ; serial
    $refresh ; refresh
    $retry ; retry
    $expire ; expire
    $ttl ; ttl
)


@    IN    A    $IP_address
@    IN    NS    $NS1
@    IN    NS    $NS2
@    IN    NS    $NS3
@    IN    MX    10    $MX
www    IN    CNAME    " . $res[$i]['domain'] . ".

";

                fwrite($fp, $include_content);

                fclose($fp);


                $new_domain_notify = $res[$i]['domain'];
                $new_domain_notify_info = "New domain. The primary name server is configured. You have to configure the secondary servers. Read doc/user_inform.txt";

                $query = "insert into admin_notify (ID, domain, notify, timestamp) values ('NULL', '$new_domain_notify', '$new_domain_notify_info', '$timestamp');";
                mysql_query($query) or die ($error_update);

            }
        }
    }
    
    $output = "";

    for($i = 0; $i < ($size+sizeof($res)); $i++) {
        if($read_array[$i] == "\n" || $read_array[$i] == NULL) {
            continue;
        }
        $output = $output.$read_array[$i];
    }

    $fp = fopen($file, "w+");

    fwrite($fp, $output."\n");

    fclose($fp);


    for($i = 0;$i < sizeof($res);++$i) {
        if($res[$i]['subdomain'] != "") {
            $file = $named_db . "/" . $res[$i]['zone'] . ".db";

            $fp = fopen($file, "r");

            $read_array = file($file);

            fclose($fp);

            $size_subdomain = sizeof($read_array);

            $read_array[$size_subdomain + $i] = "\n" . $res[$i]['subdomain'] . "    IN    A    $IP_address";


            $output = "";

            for($j = 0; $j < ($size_subdomain + sizeof($res)); $j++) {
                if($read_array[$j] == "\n" || $read_array[$j] == NULL) {
                    continue;
                }
                if(strpos($read_array[$j], "serial") !== false) {
                    /*
                    $today=getdate(time());
                    $year=$today['year'];
                    $month=$today['mon'];
                    $day=$today['mday'];
                    $hours=$today['hours'];
                    $minutes=$today['minutes'];

                    $last_two=($hours*60+$minutes)/15;

                    $serial=$year.$month.$day.(int)$last_two;

                    */
                    $serial = $timestamp;

                    $read_array[$j] = "    " . $serial . " ; serial\n";

                }

                $output = $output.$read_array[$j];
            }


            $fp = fopen($file, "w+");

            fwrite($fp, $output . "\n");

            fclose($fp);
        }
    }




    $query = "unlock tables;";
    mysql_query($query);

    if(IsSet($HTTP_SERVER_VARS['argv'][1])) {
        $sleep = (int)$HTTP_SERVER_VARS['argv'][1];
    } else {
        $sleep = 15;
    }
    
    sleep($sleep);
    system("apachectl graceful");
    system("killall -HUP named");
}


$query = "unlock tables;";
mysql_query($query);



$today = getdate($timestamp);
$year = $today['year'];
$month = $today['mon'];
$day = $today['mday'];
$hours = $today['hours'];
$minutes = $today['minutes'];

$query = "select domain from domains where expday<'$day' and expmonth<='$month' and expyear<='$year' or expmonth<'$month' and expyear<='$year' or expyear<'$year'";
$result = mysql_query($query) or die ($error_select);


while($row = mysql_fetch_array($result)) {
    $query = "insert into admin_notify (ID, domain, notify, timestamp) values ('NULL', '$row[domain]', 'expired', '$timestamp');";
    mysql_query($query) or die ($error_update);
}


$query = "select user from users where db_expday<'$day' and db_expmonth<='$month' and db_expyear<='$year' and db='on' and user!='admin' or db_expmonth<'$month' and db_expyear<='$year' and db='on' and user!='admin' or db_expyear<'$year' and db='on' and user!='admin'";
$result = mysql_query($query) or die ($error_select);


while($row = mysql_fetch_array($result)) {
    $query = "insert into admin_notify (ID, user, notify, timestamp) values ('NULL', '$row[user]', 'expired', '$timestamp');";
    mysql_query($query) or die ($error_update);
}



$query = "select user, quota from users where db='on'";
$result = mysql_query($query) or die ($error_select);


while($row = mysql_fetch_array($result)) {
    mysql_select_db("mysql") or die($error_selectdb);

    $query = "select Db from db where User='$row[user]'";
    $result_db = mysql_query($query) or die($error_select);

    while($row_db = mysql_fetch_array($result_db)) {
        $real_size += shell_exec("$exec_path/size_forbidden.php $mysql_datadir/$row_db[Db]");
    }


    $real_size = $real_size / 1024;


    if(!IsSet($quotacmd)) {
        $quotacmd = "quota";
    }
    
    $quota_array = NULL;

    exec("$quotacmd $row[user]", $quota_array);

    $quota_array[2] = substr($quota_array[2], 15);

    $quota_array[2] = trim($quota_array[2]);

    $quota_exploaded = explode(" ",  $quota_array[2]);

    $quota_system = trim($quota_exploaded[0], "*");

    $real_size += $quota_system;

    $real_size = (int)$real_size;

    if($real_size >= $row['quota']) {
        $notify_string = "The user $row[user] has exceeded the disk quota $real_size>$row[quota]";

        mysql_select_db($database) or die($error_selectdb);

        $query = "insert into admin_notify (ID, user, notify, timestamp) values ('NULL', '$row[user]', '$notify_string', '$timestamp');";
        mysql_query($query) or die ($error_update);
    }
}


mysql_select_db($database) or die($error_selectdb);

$query = "select ID, user from users where db=''";
$result = mysql_query($query) or die ($error_select);

while($row = mysql_fetch_array($result)) {
    $query = "select ID from domains where user_id='$row[ID]'";
    $result_domain = mysql_query($query) or die ($error_select);

    if(mysql_num_rows($result_domain) == 0) {
        $query = "insert into admin_notify (ID, user, notify, timestamp) values ('NULL', '$row[user]', 'The user has no domains and database usage is off', '$timestamp');";
        mysql_query($query) or die ($error_update);
    }
}

?>
