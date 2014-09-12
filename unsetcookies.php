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
