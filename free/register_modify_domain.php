<?php
require_once '../conf_inc.php';
require_once '../errors_inc.php';

session_start();
session_cache_limiter('nocache');

if(IsSet($_SESSION['user'])) {
    import_request_variables('p', 'p_');

    error_reporting($error_reporting);

    @mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
    @mysql_select_db($database) or die($error_selectdb);


    $query = "update domains set category='$p_category' where domain='$p_domain';";
    mysql_query($query) or die($error_update);


    header("Location:../allocate.php");
    exit;

}
