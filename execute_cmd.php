<?php

function execute_cmd($cmd)
{
    //$cmd = escapeshellcmd($cmd);

    require_once 'conf_inc.php';

    global $sudo_cmd, $httpd_passwd;

    $fhandle = popen("$sudo_cmd -v \n","w");
    $fsave = fputs($fhandle, "$httpd_passwd");
    pclose($fhandle);

    exec("$sudo_cmd -u root $cmd\n\n", $result_cmd);

    exec("$sudo_cmd -k\n\n", $result_cmd);

    return $result_cmd;




/*
    $fhandle = popen("$sudo_cmd -u root $cmd \n\n","w");

    $buffer = fgets($fhandle, 1024);

    $fsave = fputs($fhandle, "$httpd_passwd");
    $buffer = fgets($fhandle, 1024);

    pclose($fhandle);

    return $buffer;
*/

}
?>
