<?php
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
