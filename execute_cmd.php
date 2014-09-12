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
