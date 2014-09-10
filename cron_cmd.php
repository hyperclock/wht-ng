#!/usr/bin/php -q
<?php
require_once './conf_inc.php';

error_reporting($error_reporting);

$arg = $HTTP_SERVER_VARS['argv'];

switch ($arg[1]) {
case "read":

    if(file_exists("$crond_spool/$arg[2]")) {
    
        $fp = fopen("$crond_spool/$arg[2]", "r");

        $content=fread($fp, filesize("$crond_spool/$arg[2]"));

        fclose($fp);

        echo($content);
    }

    break;

case "write":

    if(!file_exists("$crond_spool/$arg[2]")) {
    
        require_once './conf_inc.php';
        require_once './errors_inc.php';

        @mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
        @mysql_select_db($database) or die($error_selectdb);

        $query = "select email from users where user='$arg[2]'";
        $result = mysql_query($query) or die($error_select);

        $row = mysql_fetch_array($result);

        $fp = fopen("$crond_spool/$arg[2]", "a");

        $content = "MAILTO=$row[email]\n$arg[3] \"$userhomedir/$arg[2]$arg[4]\"\n";

        fwrite($fp, $content);

        fclose($fp);

        chmod("$crond_spool/$arg[2]", 0600);

        touch("$crond_spool");
    } else {
    
        $fp = fopen("$crond_spool/$arg[2]", "a");

        $content = "$arg[3] \"$userhomedir/$arg[2]$arg[4]\"\n";

        fwrite($fp, $content);

        fclose($fp);

        touch("$crond_spool");
    }

    break;

case "delete":

    if($arg[3] === 0) {
        break;
    }

    if(file_exists("$crond_spool/$arg[2]")) {
    
        $fp = fopen("$crond_spool/$arg[2]", "r");

        $i = 0;

        while(!feof($fp)) {
        
            $buffer = fgets($fp);

            if($buffer === "\n") {
                $buffer = "";
            }
            if($arg[3] == $i) {
                $buffer = "";
            } else {
                $content.=$buffer;
            }
            $i++;
        }

        fclose($fp);

        $fp = fopen("$crond_spool/$arg[2]", "w");

        fwrite($fp, $content . "\n");

        fclose($fp);

        touch("$crond_spool");
    }
    break;

case "change_email":

    if(file_exists("$crond_spool/$arg[2]")) {
    
        $fp = fopen("$crond_spool/$arg[2]", "r");

        $i = 0;

        while(!feof ($fp)) {
        
            $buffer = fgets($fp);

            if($buffer === "\n") {
                $buffer = "";
            }

            if($i == 0) {
                $content = "MAILTO=$arg[3]\n";
            } else {
                $content .=$buffer;
            }
            $i++;
        }

        fclose($fp);

        $fp = fopen("$crond_spool/$arg[2]", "w");

        fwrite($fp, $content . "\n");

        fclose($fp);

        touch("$crond_spool");
    }
    break;

}
?>
