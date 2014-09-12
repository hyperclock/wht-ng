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
require_once './i18n.php';
require_once './errors_inc.php';

session_cache_limiter('nocache');

import_request_variables('p', 'p_');

error_reporting($error_reporting);

if(IsSet($p_user)) {
    @mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
    @mysql_select_db($database) or die($error_selectdb);


    $query = "select email,password from users where user='$p_user'";
    $result = mysql_query($query) or die($error_select);

    while($row = mysql_fetch_array($result)) {
        $res[] = $row;
        }

    if($res[0]['email'] !== NULL) {
        require_once './templates/mail/lost_password.php';
        
        mail($res[0]['email'], "$subject", "$body", "$mail_headers");

        include_once './templates/sent_lostpassword.tpl';
    }
    else {
        $error = "No such user.<br />";
        include_once './templates/lostpassword.tpl';
    }
} else {
    include_once './templates/lostpassword.tpl';
}
?>
