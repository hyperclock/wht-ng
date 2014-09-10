<?php
if(IsSet($p_user)) {
    setcookie("user_c", "", "0", "/" . $version . $cookies_free, $host_name);
}
if(IsSet($p_email)) {
    setcookie("email_c", "", "0", "/" . $version . $cookies_free, $host_name);
}
if(IsSet($p_db)) {
    setcookie("db_c", "", "0", "/" . $version . $cookies_free, $host_name);
}
if(IsSet($p_local_domain)) {
    setcookie("local_domain_c", "", "0", "/" . $version . $cookies_free, $host_name);
}
if(IsSet($p_local_domain)) {
    setcookie("sel_domain_c", "", "0", "/" . $version . $cookies_free, $host_name);
}
if(IsSet($p_domain)) {
    setcookie("domain_c", "", "0", "/".$version . $cookies_free, $host_name);
}
if(IsSet($p_num_emails)) {
    setcookie("num_emails_c", "", "0", "/" . $version . $cookies_free, $host_name);
}
if(IsSet($p_script)) {
    setcookie("script_c", "", "0", "/" . $version . $cookies_free, $host_name);
}
if(IsSet($p_months)) {
    setcookie("months_c", "", "0", "/" . $version . $cookies_free, $host_name);
}
if(IsSet($p_traffic)) {
    setcookie("traffic_c", "", "0", "/" . $version . $cookies_free, $host_name);
}
if(IsSet($p_quota)) {
    setcookie("quota_c", "", "0", "/" . $version . $cookies_free, $host_name);
}
if(IsSet($p_category)) {
    setcookie("category_c", "", "0", "/" . $version . $cookies_free, $host_name);
}
?>
