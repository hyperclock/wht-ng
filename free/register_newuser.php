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

require_once '../conf_inc.php';
require_once '../i18n.php';
require_once '../errors_inc.php';
require_once '../check_posted.php';
require_once '../execute_cmd.php';

session_start();
session_cache_limiter('nocache');

import_request_variables('p', 'p_');
import_request_variables('g', 'g_');

error_reporting($error_reporting);


if($p_hidden === "newuser") {
    if($p_user != "" && ($p_local_domain != "" || $p_domain != "")
    && $p_password != "" && $p_confpass != "" && $p_email != "") {
        if(strlen($p_password) < 8) {
            header("Location:newuser.php?error=error_short_password");
            exit;
        }

        $cookies_free = "/free";
        $p_months = 12;
        $p_quota = $free_quota;

        if($free_db == "on") {
            $p_db = "on";
        }

        mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);

        if($enable_cgi_free == "on") {
            mysql_select_db("mysql") or die($error_selectdb);

            $query = "select user from user where user='$p_user'";
            $result = mysql_query($query) or die($error_select);

            $db_user = mysql_num_rows($result);
        }

        mysql_select_db($database) or die($error_selectdb);

        $query = "lock tables users write, domains write;";
        $result = mysql_query($query) or die($error_query);

        $query = "select user from users where user='$p_user'";
        $result = mysql_query($query) or die($error_select);

        $check_user = execute_cmd("finger \"$p_user\"");

        if(mysql_num_rows($result) == 0 && sizeof($check_user) < 2 && $db_user == 0) {
            if($p_domain != "") {
                $domain_insert = $p_domain;
                $zone = $p_domain;
            } else {
                $domain_insert = $p_local_domain.".".$p_sel_domain;
                $zone = $p_sel_domain;
                $subdomain = $p_local_domain;
            }

            $query = "select domain from domains where domain='$domain_insert';";
            $result = mysql_query($query) or die($error_select);

            if(mysql_num_rows($result)==0) {
                $timestamp = time();
                $today = getdate();
                $year = $today['year'];
                $month = $today['mon'];
                $day = $today['mday'];


                if($month === 2 && $day > 28) {
                    $expday = 28;
                } else {
                    $expday = $day;
                }
                
                $expmonth = $month + $p_months;

                if($expmonth > 12) {
                    $expyear = $year + (int)(($expmonth - 1) / 12);
                    $expmonth = $expmonth - 12 * (int)(($expmonth - 1) / 12);
                } else {
                    $expyear = $year;
                }

                $expyear += 100;
                
                $quota = $p_quota * 1024 + 100;

                if($p_db === "on") {
                    $query = "insert into users (ID, user, password, quota, email, db, db_expday, db_expmonth, db_expyear,  timestamp) values(NULL, '$p_user', '$p_password', '$quota', '$p_email', '$p_db', '$expday', '$expmonth', '$expyear', '$timestamp')";
                    mysql_query($query) or die($error_insert);
                } else {
                    $query = "insert into users (ID, user, password, quota, email, db, timestamp) values(NULL, '$p_user', '$p_password', '$quota', '$p_email', '$p_db', '$timestamp')";
                    mysql_query($query) or die($error_insert);
                }

                $query = "select ID from users where user='$p_user'";
                $result = mysql_query($query) or die($error_select);

                while($row = mysql_fetch_array($result)) {
                    $res_insert[] = $row;
                }

                $user_id = $res_insert[0]['ID'];

                if($p_domain != "") {
                    $domain_insert = $p_domain;
                    $zone = $p_domain;
                } else {
                    $domain_insert = $p_local_domain . "." . $p_sel_domain;
                    $zone = $p_sel_domain;
                    $subdomain = $p_local_domain;
                }

                $quota_d = $quota - 100;

                $query = "insert into domains (ID, user_id, domain, subdomain, zone, num_emails, quota, traffic, day, month, year, expday, expmonth, expyear, free, category, enable, domaincheck, timestamp) values(NULL, '$user_id', '$domain_insert', '$subdomain', '$zone', '$free_email_accounts', '$quota_d', '$free_traffic', '$day', '$month', '$year', '$expday', '$expmonth', '$expyear', 'y', '$p_category', 'y', '1', '$timestamp')";
                mysql_query($query) or die($error_insert);

                $query = "unlock tables;";
                mysql_query($query) or die($error_query);

                $query = "select ID from domains where user_id='$user_id'";
                $result = mysql_query($query) or die($error_select);

                $row_domain = mysql_fetch_array($result);

                $conf = $row_domain['ID'];

                require_once '../templates/mail/confirm_free.php';
                
                mail("$p_email", "$subject", "$body", "$mail_headers");

                require_once '../unsetcookies.php';

                require_once '../templates/register_newuser.tpl';

            } else {
                $query = "unlock tables;";
                mysql_query($query) or die($error_query);

                require_once '../setcookies.php';

                header("Location:newuser.php?error=error_same_domain");
                exit;
            }
        } else {
            $query = "unlock tables;";
            mysql_query($query) or die($error_query);

            require_once '../setcookies.php';

            header("Location:newuser.php?error=error_same_user");
            exit;
        }
    } else {
        require_once '../setcookies.php';

        header("Location:newuser.php?error=error_fill");
        exit;
    }
}
