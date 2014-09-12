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
require_once './execute_cmd.php';


@mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
@mysql_select_db($database) or die($error_selectdb);

if($testmode==="on") {

    // read the post from PayPal system and add 'cmd'
    $req = 'cmd=_notify-validate';

    foreach($HTTP_POST_VARS as $key => $value) {
        $value = urlencode(stripslashes($value));
        $req .= "&$key=$value";
    }


    // post back to PayPal system to validate
    $header .= "POST /".$version."/ipntest.php HTTP/1.0\r\n";
    $header.= "Host: $host_name\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= 'Content-Length: ' . strlen($req) . "\r\n\r\n";
    $fp = fsockopen ("$host_name", 80, $errno, $errstr, 30);

    // assign posted variables to local variables
    // note: additional IPN variables also available -- see IPN documentation
    $item_name = $HTTP_POST_VARS['item_name'];
    $receiver_email = $HTTP_POST_VARS['receiver_email'];
    $item_number = $HTTP_POST_VARS['item_number'];
    $invoice = $HTTP_POST_VARS['invoice'];
    $payment_status = $HTTP_POST_VARS['payment_status'];
    $payment_gross = $HTTP_POST_VARS['payment_gross'];
    $txn_id = $HTTP_POST_VARS['txn_id'];
    $payer_email = $HTTP_POST_VARS['payer_email'];

    if (!$fp) {
        // ERROR
        echo "$errstr ($errno)";
        $query="insert into ipn_error values('$erstr($erno)')";
        mysql_query($query) or die($error_insert);

    } else {

        fputs($fp, $header . $req);
        while(!feof($fp)) {
            $res = fgets ($fp, 1024);
            if (strcmp ($res, "VERIFIED") == 0) {
                // check the payment_status is Completed
                // check that txn_id has not been previously processed
                // check that receiver_email is an email address in your PayPal account
                // process payment


                $duptxn = mysql_query("select txn_id from ipn_test where txn_id = '$txn_id' and status = '$res'");
                $duplicate = mysql_num_rows($duptxn);




                foreach ($paypal_receiver_email as $key => $value) {
                    if($value === $receiver_email) {
                        $email_check = true;
                    }
                }


                $custom_select = substr($HTTP_POST_VARS['custom'], 2);

                if(substr($HTTP_POST_VARS['custom'], 0, 2) === "ed"  || substr($HTTP_POST_VARS['custom'], 0, 2) === "db") {
                    $query = "select debit from users where user='$custom_select'";
                } else {
                    $query = "select debit from domains where domain='$custom_select'";
                }

                $result_payment_gross = mysql_query($query) or die();

                $row_payment_gross = mysql_fetch_array($result_payment_gross);

                if($payment_gross >= $row_payment_gross['debit']) {
                    $payment_gross_check = true;
                }


                if($payment_status === "Completed" && !$duplicate && $email_check && $payment_gross_check) {
                    $procede=true;
                }

            } elseif (strcmp ($res, "INVALID") == 0) {
            // log for manual investigation
            }
        }
    fclose($fp);
    }

    if($procede) {
        import_request_variables('p', 'p_');
        $timestamp = time();

        $query = "insert into ipn_test values('NULL','$p_receiver_email','$p_business','$p_item_name','$p_item_number','$p_quantity','$p_invoice','$p_custom','$p_option_name1','$p_option_selection1','$p_option_name2','$p_option_selection2','$p_num_cart_items','$p_payment_status','$p_pending_reason','$p_payment_date','$p_settle_amount','$p_settle_currency','$p_exchange_rate','$p_payment_gross','$p_payment_fee','$p_mc_gross','$p_mc_fee','$p_mc_currency','$p_tax','$p_txn_id','$p_txn_type','$p_memo','$p_first_name','$p_last_name','$p_address_street','$p_address_city','$p_address_state','$p_address_zip','$p_address_country','$p_address_status','$p_payer_email','$p_payer_id','$p_payer_status','$p_payment_type','$p_notify_version','$p_verify_sign','$res','$timestamp')";
        mysql_query($query) or die($error_insert);

        if(substr($p_custom, 0, 2) === "nu") {
            $custom = substr($p_custom, 2);

            $query = "update domains set domaincheck=NULL, status='1' where domain='$custom';";
            $result = mysql_query($query) or die($error_query);


            $query = "select user_id, script from domains where domain='$custom'";
            $result = mysql_query($query) or die($error_query);

            $row_dom = mysql_fetch_array($result);


            $query = "update users set status='1' where ID='$row_dom[user_id]';";
            $result = mysql_query($query) or die($error_query);


            $query = "select user, password, quota from users where ID='$row_dom[user_id]'";
            $result = mysql_query($query) or die($error_query);

            $row_user = mysql_fetch_array($result);

            $quota_soft = $row_user['quota'];
            $quota_hard = $quota_soft + 20;


            $passencrypt = crypt($row_user['password'], $row_user['password']);
            $exec_cmd = "$addusercmd -m -d $userhomedir/$row_user[user] -p $passencrypt $row_user[user] -s /bin/bash";
            $result_exec = execute_cmd("$exec_cmd");

            $exec_cmd = "$chgrpcmd $httpd_group ~$row_user[user]";
            $result_exec = execute_cmd("$exec_cmd");

            $exec_cmd = "$chmod 750 ~$row_user[user]";
            $result_exec = execute_cmd("$exec_cmd");


            if($email_home === "vpopmail") {
                $exec_cmd = "$vadddomain $custom $row_user[password]";
            } else {
                $exec_cmd="$vadddomain -u $row_user[user] $custom $row_user[password]";
            }
            execute_cmd("$exec_cmd");


            $exec_cmd = "$setquotacmd -u $row_user[user] $quota_soft $quota_hard 0 0 -a $partition_used";
            execute_cmd("$exec_cmd");

    //		$exec_cmd="$modusercmd  -d $userhomedir/$row_user[user]/./www $row_user[user]";
    //		$result_exec=execute_cmd("$exec_cmd");

            $ftp_server_ip = "127.0.0.1";

            $conn_id = ftp_connect($ftp_server_ip, 21, 5);

            // login with username and password
            $login_result = ftp_login($conn_id,$row_user[user],$row_user[password]); 

            // check connection
            if ((!$conn_id) || (!$login_result)) { 
                echo "FTP connection has failed!";
                echo "Attempted to connect to $ftp_server_ip for user $row_user[user]"; 
                die; 
            } else {

                ftp_mkdir ($conn_id, "$custom");
                ftp_put($conn_id, "$custom/index.html", "$DocumentRoot/$version/templates/index.html", FTP_ASCII);

                if($row_dom['script'] === 'on') {
                    ftp_mkdir ($conn_id, $custom."_cgi-bin");
                }
            }
            ftp_close($conn_id); 	



        } elseif(substr($p_custom, 0, 2) === "nd") {
            $custom = substr($p_custom, 2);

            $query = "select user_id, script, quota from domains where domain='$custom'";
            $result = mysql_query($query) or die($error_query);

            $row_dom = mysql_fetch_array($result);


            $query = "select user, password, quota from users where ID='$row_dom[user_id]'";
            $result = mysql_query($query) or die($error_query);

            $row_user = mysql_fetch_array($result);

            if($email_home === "vpopmail") {
                $exec_cmd = "$vadddomain $custom $row_user[password]";
            } else {
                $exec_cmd = "$vadddomain -u $row_user[user] $custom $row_user[password]";
            }
            
            execute_cmd("$exec_cmd");


            $quota_soft = $row_user['quota'] + $row_dom['quota'];
            $quota_hard = $quota_soft + 20;

            $exec_cmd = "$setquotacmd -u $row_user[user] $quota_soft $quota_hard 0 0 -a $partition_used";
            execute_cmd("$exec_cmd");


            $query = "update domains set domaincheck=NULL, status='1' where domain='$custom';";
            $result = mysql_query($query) or die($error_update);

            $query = "update users set quota='$quota_soft' where ID='$row_dom[user_id]'";
            $result = mysql_query($query) or die($error_update);


        } elseif(substr($p_custom, 0, 2) === "ns") {
            $custom = substr($p_custom, 2);

            $query = "select user_id, quota from domains where domain='$custom'";
            $result = mysql_query($query) or die($error_query);

            $row_dom = mysql_fetch_array($result);


            $query = "select user, quota from users where ID='$row_dom[user_id]'";
            $result = mysql_query($query) or die($error_query);

            $row_user = mysql_fetch_array($result);

            $quota_soft = $row_user['quota'] + $row_dom['quota'];
            $quota_hard = $quota_soft + 20;

            $exec_cmd = "$setquotacmd -u $row_user[user] $quota_soft $quota_hard 0 0 -a $partition_used";
            execute_cmd("$exec_cmd");


            $query = "update domains set domaincheck=NULL, status='1' where domain='$custom';";
            $result = mysql_query($query) or die($error_update);

            $query = "update users set quota='$quota_soft' where ID='$row_dom[user_id]'";
            $result = mysql_query($query) or die($error_update);


        } elseif(substr($p_custom, 0, 2) === "db") {
            $custom = substr($p_custom, 2);

            $query = "update users set db='on' where user='$custom';";
            $result = mysql_query($query) or die($error_update);

        } elseif(substr($p_custom, 0, 2) === "md") {
            $custom = substr($p_custom, 2);


            $query = "select domain, user_id, subdomain, zone, quota, script, ssl, free from domains where domain='$custom'";
            $result = mysql_query($query) or die($error_select);

            $row_real = mysql_fetch_array($result);

            $query = "select user, quota from users where ID='$row_real[user_id]'";
            $result = mysql_query($query) or die($error_query);

            $row_user = mysql_fetch_array($result);

            $query = "select domain, num_emails, script, ssl, months, quota, traffic, debit, expday, expmonth, expyear from temporary_domains where domain='$custom'";
            $result = mysql_query($query) or die($error_select);

            $row = mysql_fetch_array($result);

            if($row['quota'] != $row_real['quota']) {
                $quota_soft  = $row['quota'] - $row_real['quota'];
                $quota_soft  = $row_user['quota'] + $quota_soft;
                $quota_hard = $quota_soft + 20;

                $exec_cmd = "$setquotacmd -u $row_user[user] $quota_soft $quota_hard 0 0 -a $partition_used";
                execute_cmd("$exec_cmd");

                $query = "update users set quota='$quota_soft' where ID='$row_real[user_id]'";
                $result = mysql_query($query) or die($error_update);


            }

            if($row_real['script']!=$row['script'] || $row_real[free] === "y") {
                $query = "insert into deleted (ID, domain, subdomain, zone, modified) values(NULL, '$row_real[domain]', '$row_real[subdomain]', '$row_real[zone]', 'y');";
                mysql_query($query) or die($error_insert);

                $query = "update domains set num_emails='$row[num_emails]', script='$row[script]', ssl='$row[ssl]', quota='$row[quota]', months='$row[months]', traffic='$row[traffic]', debit='$row[debit]', expday='$row[expday]', expmonth='$row[expmonth]', expyear='$row[expyear]', free='', domaincheck=NULL where domain='$custom';";

            } else {
                $query = "update domains set num_emails='$row[num_emails]', script='$row[script]', ssl='$row[ssl]', quota='$row[quota]', months='$row[months]', traffic='$row[traffic]', debit='$row[debit]', expday='$row[expday]', expmonth='$row[expmonth]', expyear='$row[expyear]' where domain='$custom';";
            }

            $result = mysql_query($query) or die($error_update);

            if($row_real['free'] === "y") {
                $query = "update users set db='';";

                $result = mysql_query($query) or die($error_update);
            }

            $query = "delete from temporary_domains where domain='$custom';";
            mysql_query($query) or die($error_delete);

        } elseif(substr($p_custom, 0, 2) === "ed") {
            $custom = substr($p_custom, 2);

            $query = "select user, db_expday, db_expmonth, db_expyear from temporary_users where user='$custom'";
            $result = mysql_query($query) or die($error_select);


            $row = mysql_fetch_array($result);

            $query = "update users set db_expday='$row[db_expday]', db_expmonth='$row[db_expmonth]', db_expyear='$row[db_expyear]' where user='$custom';";
            $result = mysql_query($query) or die($error_update);

            $query = "delete from temporary_users where user='$custom';";
            mysql_query($query) or die($error_delete);

        }


    } else {
        import_request_variables('p', 'p_');
        $timestamp = time();

        $query = "insert into ipn_test values('NULL','$p_receiver_email','$p_business','$p_item_name','$p_item_number','$p_quantity','$p_invoice','$p_custom','$p_option_name1','$p_option_selection1','$p_option_name2','$p_option_selection2','$p_num_cart_items','$p_payment_status','$p_pending_reason','$p_payment_date','$p_settle_amount','$p_settle_currency','$p_exchange_rate','$p_payment_gross','$p_payment_fee','$p_mc_gross','$p_mc_fee','$p_mc_currency','$p_tax','$p_txn_id','$p_txn_type','$p_memo','$p_first_name','$p_last_name','$p_address_street','$p_address_city','$p_address_state','$p_address_zip','$p_address_country','$p_address_status','$p_payer_email','$p_payer_id','$p_payer_status','$p_payment_type','$p_notify_version','$p_verify_sign','$res','$timestamp')";
        mysql_query($query) or die($error_insert);
    }


} elseif($testmode === "eliteweaver") {

// read the post from Eliteweaver's system and add 'cmd'
    $req = 'cmd=_notify-validate';

    foreach ($HTTP_POST_VARS as $key => $value) {
        $value = urlencode(stripslashes($value));
        $req .= "&$key=$value";
    }

    $host_ew = "www.eliteweaver.co.uk";
    // post back to PayPal system to validate
    $header .= "POST /testing/ipntest.php HTTP/1.0\r\n";
    $header.= "Host: www.eliteweaver.co.uk\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= 'Content-Length: ' . strlen($req) . "\r\n\r\n";
    $fp = fsockopen ($host_ew, 80, $errno, $errstr, 30);

    // assign posted variables to local variables
    // note: additional IPN variables also available -- see IPN documentation
    $item_name = $HTTP_POST_VARS['item_name'];
    $receiver_email = $HTTP_POST_VARS['receiver_email'];
    $item_number = $HTTP_POST_VARS['item_number'];
    $invoice = $HTTP_POST_VARS['invoice'];
    $payment_status = $HTTP_POST_VARS['payment_status'];
    $payment_gross = $HTTP_POST_VARS['payment_gross'];
    $txn_id = $HTTP_POST_VARS['txn_id'];
    $payer_email = $HTTP_POST_VARS['payer_email'];




    if (!$fp) {
        // ERROR
        echo "$errstr ($errno)";
        $query = "insert into ipn_error values('$erstr($erno)')";
        mysql_query($query) or die($error_insert);

    } else {

        fputs($fp, $header . $req);
        while(!feof($fp)) {
            $res = fgets ($fp, 1024);
            if (strcmp ($res, "VERIFIED") == 0) {
                // check the payment_status is Completed
                // check that txn_id has not been previously processed
                // check that receiver_email is an email address in your PayPal account
                // process payment


                $duptxn = mysql_query("select txn_id from ipn_test where txn_id = '$txn_id' and status = '$res'");
                $duplicate = mysql_num_rows($duptxn);




                foreach ($paypal_receiver_email as $key => $value) {
                    if($value === $receiver_email) {
                        $email_check = true;
                    }
                }


                $custom_select = substr($HTTP_POST_VARS['custom'], 2);

                if(substr($HTTP_POST_VARS['custom'], 0, 2) === "ed"  || substr($HTTP_POST_VARS['custom'], 0, 2) === "db") {
                    $query = "select debit from users where user='$custom_select'";
                } else {
                    $query = "select debit from domains where domain='$custom_select'";
                }

                $result_payment_gross = mysql_query($query) or die();

                $row_payment_gross = mysql_fetch_array($result_payment_gross);

                if($payment_gross >= $row_payment_gross['debit']) {
                    $payment_gross_check = true;
                }


                if($payment_status === "Completed" && !$duplicate && $email_check && $payment_gross_check) {
                    $procede=true;
                }

            } elseif (strcmp ($res, "INVALID") == 0) {
            // log for manual investigation
            }
        }
    fclose($fp);
    }

    if($procede) {
        import_request_variables('p', 'p_');
        $timestamp = time();

        $query = "insert into ipn_test values('NULL','$p_receiver_email','$p_business','$p_item_name','$p_item_number','$p_quantity','$p_invoice','$p_custom','$p_option_name1','$p_option_selection1','$p_option_name2','$p_option_selection2','$p_num_cart_items','$p_payment_status','$p_pending_reason','$p_payment_date','$p_settle_amount','$p_settle_currency','$p_exchange_rate','$p_payment_gross','$p_payment_fee','$p_mc_gross','$p_mc_fee','$p_mc_currency','$p_tax','$p_txn_id','$p_txn_type','$p_memo','$p_first_name','$p_last_name','$p_address_street','$p_address_city','$p_address_state','$p_address_zip','$p_address_country','$p_address_status','$p_payer_email','$p_payer_id','$p_payer_status','$p_payment_type','$p_notify_version','$p_verify_sign','$res','$timestamp')";
        mysql_query($query) or die($error_insert);

        if(substr($p_custom, 0, 2) === "nu") {
            $custom = substr($p_custom, 2);

            $query = "update domains set domaincheck=NULL, status='1' where domain='$custom';";
            $result = mysql_query($query) or die($error_query);


            $query = "select user_id, script from domains where domain='$custom'";
            $result = mysql_query($query) or die($error_query);

            $row_dom = mysql_fetch_array($result);


            $query = "update users set status='1' where ID='$row_dom[user_id]';";
            $result = mysql_query($query) or die($error_query);


            $query = "select user, password, quota from users where ID='$row_dom[user_id]'";
            $result = mysql_query($query) or die($error_query);

            $row_user = mysql_fetch_array($result);

            $quota_soft = $row_user['quota'];
            $quota_hard = $quota_soft + 20;


            $passencrypt = crypt($row_user['password'], $row_user['password']);
            $exec_cmd = "$addusercmd -m -d $userhomedir/$row_user[user] -p $passencrypt $row_user[user] -s /bin/bash";
            $result_exec = execute_cmd("$exec_cmd");

            $exec_cmd = "$chgrpcmd $httpd_group ~$row_user[user]";
            $result_exec = execute_cmd("$exec_cmd");

            $exec_cmd = "$chmod 750 ~$row_user[user]";
            $result_exec = execute_cmd("$exec_cmd");


            if($email_home === "vpopmail") {
                $exec_cmd = "$vadddomain $custom $row_user[password]";
            } else {
                $exec_cmd="$vadddomain -u $row_user[user] $custom $row_user[password]";
            }
            execute_cmd("$exec_cmd");


            $exec_cmd = "$setquotacmd -u $row_user[user] $quota_soft $quota_hard 0 0 -a $partition_used";
            execute_cmd("$exec_cmd");

    //		$exec_cmd="$modusercmd  -d $userhomedir/$row_user[user]/./www $row_user[user]";
    //		$result_exec=execute_cmd("$exec_cmd");

            $ftp_server_ip = "127.0.0.1";

            $conn_id = ftp_connect($ftp_server_ip, 21, 5);

            // login with username and password
            $login_result = ftp_login($conn_id,$row_user[user],$row_user[password]); 

            // check connection
            if ((!$conn_id) || (!$login_result)) { 
                echo "FTP connection has failed!";
                echo "Attempted to connect to $ftp_server_ip for user $row_user[user]"; 
                die; 
            } else {

                ftp_mkdir ($conn_id, "$custom");
                ftp_put($conn_id, "$custom/index.html", "$DocumentRoot/$version/templates/index.html", FTP_ASCII);

                if($row_dom['script'] === 'on') {
                    ftp_mkdir ($conn_id, $custom."_cgi-bin");
                }
            }
            ftp_close($conn_id); 	



        } elseif(substr($p_custom, 0, 2) === "nd") {
            $custom = substr($p_custom, 2);

            $query = "select user_id, script, quota from domains where domain='$custom'";
            $result = mysql_query($query) or die($error_query);

            $row_dom = mysql_fetch_array($result);


            $query = "select user, password, quota from users where ID='$row_dom[user_id]'";
            $result = mysql_query($query) or die($error_query);

            $row_user = mysql_fetch_array($result);

            if($email_home === "vpopmail") {
                $exec_cmd = "$vadddomain $custom $row_user[password]";
            } else {
                $exec_cmd = "$vadddomain -u $row_user[user] $custom $row_user[password]";
            }
            
            execute_cmd("$exec_cmd");


            $quota_soft = $row_user['quota'] + $row_dom['quota'];
            $quota_hard = $quota_soft + 20;

            $exec_cmd = "$setquotacmd -u $row_user[user] $quota_soft $quota_hard 0 0 -a $partition_used";
            execute_cmd("$exec_cmd");


            $query = "update domains set domaincheck=NULL, status='1' where domain='$custom';";
            $result = mysql_query($query) or die($error_update);

            $query = "update users set quota='$quota_soft' where ID='$row_dom[user_id]'";
            $result = mysql_query($query) or die($error_update);


        } elseif(substr($p_custom, 0, 2) === "ns") {
            $custom = substr($p_custom, 2);

            $query = "select user_id, quota from domains where domain='$custom'";
            $result = mysql_query($query) or die($error_query);

            $row_dom = mysql_fetch_array($result);


            $query = "select user, quota from users where ID='$row_dom[user_id]'";
            $result = mysql_query($query) or die($error_query);

            $row_user = mysql_fetch_array($result);

            $quota_soft = $row_user['quota'] + $row_dom['quota'];
            $quota_hard = $quota_soft + 20;

            $exec_cmd = "$setquotacmd -u $row_user[user] $quota_soft $quota_hard 0 0 -a $partition_used";
            execute_cmd("$exec_cmd");


            $query = "update domains set domaincheck=NULL, status='1' where domain='$custom';";
            $result = mysql_query($query) or die($error_update);

            $query = "update users set quota='$quota_soft' where ID='$row_dom[user_id]'";
            $result = mysql_query($query) or die($error_update);


        } elseif(substr($p_custom, 0, 2) === "db") {
            $custom = substr($p_custom, 2);

            $query = "update users set db='on' where user='$custom';";
            $result = mysql_query($query) or die($error_update);

        } elseif(substr($p_custom, 0, 2) === "md") {
            $custom = substr($p_custom, 2);


            $query = "select domain, user_id, subdomain, zone, quota, script, ssl, free from domains where domain='$custom'";
            $result = mysql_query($query) or die($error_select);

            $row_real = mysql_fetch_array($result);

            $query = "select user, quota from users where ID='$row_real[user_id]'";
            $result = mysql_query($query) or die($error_query);

            $row_user = mysql_fetch_array($result);

            $query = "select domain, num_emails, script, ssl, months, quota, traffic, debit, expday, expmonth, expyear from temporary_domains where domain='$custom'";
            $result = mysql_query($query) or die($error_select);

            $row = mysql_fetch_array($result);

            if($row['quota'] != $row_real['quota']) {
                $quota_soft  = $row['quota'] - $row_real['quota'];
                $quota_soft  = $row_user['quota'] + $quota_soft;
                $quota_hard = $quota_soft + 20;

                $exec_cmd = "$setquotacmd -u $row_user[user] $quota_soft $quota_hard 0 0 -a $partition_used";
                execute_cmd("$exec_cmd");

                $query = "update users set quota='$quota_soft' where ID='$row_real[user_id]'";
                $result = mysql_query($query) or die($error_update);


            }

            if($row_real['script']!=$row['script'] || $row_real[free] === "y") {
                $query = "insert into deleted (ID, domain, subdomain, zone, modified) values(NULL, '$row_real[domain]', '$row_real[subdomain]', '$row_real[zone]', 'y');";
                mysql_query($query) or die($error_insert);

                $query = "update domains set num_emails='$row[num_emails]', script='$row[script]', ssl='$row[ssl]', quota='$row[quota]', months='$row[months]', traffic='$row[traffic]', debit='$row[debit]', expday='$row[expday]', expmonth='$row[expmonth]', expyear='$row[expyear]', free='', domaincheck=NULL where domain='$custom';";

            } else {
                $query = "update domains set num_emails='$row[num_emails]', script='$row[script]', ssl='$row[ssl]', quota='$row[quota]', months='$row[months]', traffic='$row[traffic]', debit='$row[debit]', expday='$row[expday]', expmonth='$row[expmonth]', expyear='$row[expyear]' where domain='$custom';";
            }

            $result = mysql_query($query) or die($error_update);

            if($row_real['free'] === "y") {
                $query = "update users set db='';";

                $result = mysql_query($query) or die($error_update);
            }

            $query = "delete from temporary_domains where domain='$custom';";
            mysql_query($query) or die($error_delete);

        } elseif(substr($p_custom, 0, 2) === "ed") {
            $custom = substr($p_custom, 2);

            $query = "select user, db_expday, db_expmonth, db_expyear from temporary_users where user='$custom'";
            $result = mysql_query($query) or die($error_select);


            $row = mysql_fetch_array($result);

            $query = "update users set db_expday='$row[db_expday]', db_expmonth='$row[db_expmonth]', db_expyear='$row[db_expyear]' where user='$custom';";
            $result = mysql_query($query) or die($error_update);

            $query = "delete from temporary_users where user='$custom';";
            mysql_query($query) or die($error_delete);

        }


    } else {
        import_request_variables('p', 'p_');
        $timestamp = time();

        $query = "insert into ipn_test values('NULL','$p_receiver_email','$p_business','$p_item_name','$p_item_number','$p_quantity','$p_invoice','$p_custom','$p_option_name1','$p_option_selection1','$p_option_name2','$p_option_selection2','$p_num_cart_items','$p_payment_status','$p_pending_reason','$p_payment_date','$p_settle_amount','$p_settle_currency','$p_exchange_rate','$p_payment_gross','$p_payment_fee','$p_mc_gross','$p_mc_fee','$p_mc_currency','$p_tax','$p_txn_id','$p_txn_type','$p_memo','$p_first_name','$p_last_name','$p_address_street','$p_address_city','$p_address_state','$p_address_zip','$p_address_country','$p_address_status','$p_payer_email','$p_payer_id','$p_payer_status','$p_payment_type','$p_notify_version','$p_verify_sign','$res','$timestamp')";
        mysql_query($query) or die($error_insert);
    }



}
else {

    // read the post from PayPal system and add 'cmd'
    $req = 'cmd=_notify-validate';

    foreach ($HTTP_POST_VARS as $key => $value) {
        $value = urlencode(stripslashes($value));
        $req .= "&$key=$value";
    }

    $host_pp = "www.paypal.com";

    // post back to PayPal system to validate
    $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $header.= "Host: www.paypal.com\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= 'Content-Length: ' . strlen($req) . "\r\n\r\n";
    $fp = fsockopen ($host_pp, 80, $errno, $errstr, 30);

    // assign posted variables to local variables
    // note: additional IPN variables also available -- see IPN documentation
    $item_name = $HTTP_POST_VARS['item_name'];
    $receiver_email = $HTTP_POST_VARS['receiver_email'];
    $item_number = $HTTP_POST_VARS['item_number'];
    $invoice = $HTTP_POST_VARS['invoice'];
    $payment_status = $HTTP_POST_VARS['payment_status'];
    $payment_gross = $HTTP_POST_VARS['payment_gross'];
    $txn_id = $HTTP_POST_VARS['txn_id'];
    $payer_email = $HTTP_POST_VARS['payer_email'];




    if(!$fp) {
        // ERROR
        echo "$errstr ($errno)";
        $query = "insert into ipn_error values('$erstr($erno)')";
        mysql_query($query) or die($error_insert);

    } else {

        fputs($fp, $header . $req);
        while(!feof($fp)) {
            $res = fgets ($fp, 1024);
            if (strcmp ($res, "VERIFIED") == 0) {
                // check the payment_status is Completed
                // check that txn_id has not been previously processed
                // check that receiver_email is an email address in your PayPal account
                // process payment


                $duptxn = mysql_query("select txn_id from ipn where txn_id = '$txn_id' and status = '$res'");
                $duplicate = mysql_num_rows($duptxn);




                foreach ($paypal_receiver_email as $key => $value) {
                    if($value === $receiver_email) {
                        $email_check = true;
                    }
                }


                $custom_select = substr($HTTP_POST_VARS['custom'], 2);

                if(substr($HTTP_POST_VARS['custom'], 0, 2) === "ed"  || substr($HTTP_POST_VARS['custom'], 0, 2) === "db") {
                    $query = "select debit from users where user='$custom_select'";
                } else {
                    $query = "select debit from domains where domain='$custom_select'";
                }

                $result_payment_gross = mysql_query($query) or die();

                $row_payment_gross = mysql_fetch_array($result_payment_gross);

                if($payment_gross >= $row_payment_gross['debit']) {
                    $payment_gross_check = true;
                }


                if($payment_status === "Completed" && !$duplicate && $email_check && $payment_gross_check) {
                    $procede=true;
                }

            } elseif (strcmp ($res, "INVALID") == 0) {
            // log for manual investigation
            }
        }
    fclose($fp);
    }

    if($procede) {
        import_request_variables('p', 'p_');
        $timestamp = time();

        $query = "insert into ipn values('NULL','$p_receiver_email','$p_business','$p_item_name','$p_item_number','$p_quantity','$p_invoice','$p_custom','$p_option_name1','$p_option_selection1','$p_option_name2','$p_option_selection2','$p_num_cart_items','$p_payment_status','$p_pending_reason','$p_payment_date','$p_settle_amount','$p_settle_currency','$p_exchange_rate','$p_payment_gross','$p_payment_fee','$p_mc_gross','$p_mc_fee','$p_mc_currency','$p_tax','$p_txn_id','$p_txn_type','$p_memo','$p_first_name','$p_last_name','$p_address_street','$p_address_city','$p_address_state','$p_address_zip','$p_address_country','$p_address_status','$p_payer_email','$p_payer_id','$p_payer_status','$p_payment_type','$p_notify_version','$p_verify_sign','$res','$timestamp')";
        mysql_query($query) or die($error_insert);

        if(substr($p_custom, 0, 2) === "nu") {
            $custom = substr($p_custom, 2);

            $query = "update domains set domaincheck=NULL, status='1' where domain='$custom';";
            $result = mysql_query($query) or die($error_query);


            $query = "select user_id, script from domains where domain='$custom'";
            $result = mysql_query($query) or die($error_query);

            $row_dom = mysql_fetch_array($result);


            $query = "update users set status='1' where ID='$row_dom[user_id]';";
            $result = mysql_query($query) or die($error_query);


            $query = "select user, password, quota from users where ID='$row_dom[user_id]'";
            $result = mysql_query($query) or die($error_query);

            $row_user = mysql_fetch_array($result);

            $quota_soft = $row_user['quota'];
            $quota_hard = $quota_soft + 20;


            $passencrypt = crypt($row_user['password'], $row_user['password']);
            $exec_cmd = "$addusercmd -m -d $userhomedir/$row_user[user] -p $passencrypt $row_user[user] -s /bin/bash";
            $result_exec = execute_cmd("$exec_cmd");

            $exec_cmd = "$chgrpcmd $httpd_group ~$row_user[user]";
            $result_exec = execute_cmd("$exec_cmd");

            $exec_cmd = "$chmod 750 ~$row_user[user]";
            $result_exec = execute_cmd("$exec_cmd");


            if($email_home === "vpopmail") {
                $exec_cmd = "$vadddomain $custom $row_user[password]";
            } else {
                $exec_cmd="$vadddomain -u $row_user[user] $custom $row_user[password]";
            }
            execute_cmd("$exec_cmd");


            $exec_cmd = "$setquotacmd -u $row_user[user] $quota_soft $quota_hard 0 0 -a $partition_used";
            execute_cmd("$exec_cmd");

    //		$exec_cmd="$modusercmd  -d $userhomedir/$row_user[user]/./www $row_user[user]";
    //		$result_exec=execute_cmd("$exec_cmd");

            $ftp_server_ip = "127.0.0.1";

            $conn_id = ftp_connect($ftp_server_ip, 21, 5);

            // login with username and password
            $login_result = ftp_login($conn_id,$row_user[user],$row_user[password]); 

            // check connection
            if ((!$conn_id) || (!$login_result)) { 
                echo "FTP connection has failed!";
                echo "Attempted to connect to $ftp_server_ip for user $row_user[user]"; 
                die; 
            } else {

                ftp_mkdir ($conn_id, "$custom");
                ftp_put($conn_id, "$custom/index.html", "$DocumentRoot/$version/templates/index.html", FTP_ASCII);

                if($row_dom['script'] === 'on') {
                    ftp_mkdir ($conn_id, $custom."_cgi-bin");
                }
            }
            ftp_close($conn_id); 	



        } elseif(substr($p_custom, 0, 2) === "nd") {
            $custom = substr($p_custom, 2);

            $query = "select user_id, script, quota from domains where domain='$custom'";
            $result = mysql_query($query) or die($error_query);

            $row_dom = mysql_fetch_array($result);


            $query = "select user, password, quota from users where ID='$row_dom[user_id]'";
            $result = mysql_query($query) or die($error_query);

            $row_user = mysql_fetch_array($result);

            if($email_home === "vpopmail") {
                $exec_cmd = "$vadddomain $custom $row_user[password]";
            } else {
                $exec_cmd = "$vadddomain -u $row_user[user] $custom $row_user[password]";
            }
            
            execute_cmd("$exec_cmd");


            $quota_soft = $row_user['quota'] + $row_dom['quota'];
            $quota_hard = $quota_soft + 20;

            $exec_cmd = "$setquotacmd -u $row_user[user] $quota_soft $quota_hard 0 0 -a $partition_used";
            execute_cmd("$exec_cmd");


            $query = "update domains set domaincheck=NULL, status='1' where domain='$custom';";
            $result = mysql_query($query) or die($error_update);

            $query = "update users set quota='$quota_soft' where ID='$row_dom[user_id]'";
            $result = mysql_query($query) or die($error_update);


        } elseif(substr($p_custom, 0, 2) === "ns") {
            $custom = substr($p_custom, 2);

            $query = "select user_id, quota from domains where domain='$custom'";
            $result = mysql_query($query) or die($error_query);

            $row_dom = mysql_fetch_array($result);


            $query = "select user, quota from users where ID='$row_dom[user_id]'";
            $result = mysql_query($query) or die($error_query);

            $row_user = mysql_fetch_array($result);

            $quota_soft = $row_user['quota'] + $row_dom['quota'];
            $quota_hard = $quota_soft + 20;

            $exec_cmd = "$setquotacmd -u $row_user[user] $quota_soft $quota_hard 0 0 -a $partition_used";
            execute_cmd("$exec_cmd");


            $query = "update domains set domaincheck=NULL, status='1' where domain='$custom';";
            $result = mysql_query($query) or die($error_update);

            $query = "update users set quota='$quota_soft' where ID='$row_dom[user_id]'";
            $result = mysql_query($query) or die($error_update);


        } elseif(substr($p_custom, 0, 2) === "db") {
            $custom = substr($p_custom, 2);

            $query = "update users set db='on' where user='$custom';";
            $result = mysql_query($query) or die($error_update);

        } elseif(substr($p_custom, 0, 2) === "md") {
            $custom = substr($p_custom, 2);


            $query = "select domain, user_id, subdomain, zone, quota, script, ssl, free from domains where domain='$custom'";
            $result = mysql_query($query) or die($error_select);

            $row_real = mysql_fetch_array($result);

            $query = "select user, quota from users where ID='$row_real[user_id]'";
            $result = mysql_query($query) or die($error_query);

            $row_user = mysql_fetch_array($result);

            $query = "select domain, num_emails, script, ssl, months, quota, traffic, debit, expday, expmonth, expyear from temporary_domains where domain='$custom'";
            $result = mysql_query($query) or die($error_select);

            $row = mysql_fetch_array($result);

            if($row['quota'] != $row_real['quota']) {
                $quota_soft  = $row['quota'] - $row_real['quota'];
                $quota_soft  = $row_user['quota'] + $quota_soft;
                $quota_hard = $quota_soft + 20;

                $exec_cmd = "$setquotacmd -u $row_user[user] $quota_soft $quota_hard 0 0 -a $partition_used";
                execute_cmd("$exec_cmd");

                $query = "update users set quota='$quota_soft' where ID='$row_real[user_id]'";
                $result = mysql_query($query) or die($error_update);


            }

            if($row_real['script']!=$row['script'] || $row_real[free] === "y") {
                $query = "insert into deleted (ID, domain, subdomain, zone, modified) values(NULL, '$row_real[domain]', '$row_real[subdomain]', '$row_real[zone]', 'y');";
                mysql_query($query) or die($error_insert);

                $query = "update domains set num_emails='$row[num_emails]', script='$row[script]', ssl='$row[ssl]', quota='$row[quota]', months='$row[months]', traffic='$row[traffic]', debit='$row[debit]', expday='$row[expday]', expmonth='$row[expmonth]', expyear='$row[expyear]', free='', domaincheck=NULL where domain='$custom';";

            } else {
                $query = "update domains set num_emails='$row[num_emails]', script='$row[script]', ssl='$row[ssl]', quota='$row[quota]', months='$row[months]', traffic='$row[traffic]', debit='$row[debit]', expday='$row[expday]', expmonth='$row[expmonth]', expyear='$row[expyear]' where domain='$custom';";
            }

            $result = mysql_query($query) or die($error_update);

            if($row_real['free'] === "y") {
                $query = "update users set db='';";

                $result = mysql_query($query) or die($error_update);
            }

            $query = "delete from temporary_domains where domain='$custom';";
            mysql_query($query) or die($error_delete);

        } elseif(substr($p_custom, 0, 2) === "ed") {
            $custom = substr($p_custom, 2);

            $query = "select user, db_expday, db_expmonth, db_expyear from temporary_users where user='$custom'";
            $result = mysql_query($query) or die($error_select);


            $row = mysql_fetch_array($result);

            $query = "update users set db_expday='$row[db_expday]', db_expmonth='$row[db_expmonth]', db_expyear='$row[db_expyear]' where user='$custom';";
            $result = mysql_query($query) or die($error_update);

            $query = "delete from temporary_users where user='$custom';";
            mysql_query($query) or die($error_delete);

        }


    } else {
        import_request_variables('p', 'p_');
        $timestamp = time();

        $query = "insert into ipn values('NULL','$p_receiver_email','$p_business','$p_item_name','$p_item_number','$p_quantity','$p_invoice','$p_custom','$p_option_name1','$p_option_selection1','$p_option_name2','$p_option_selection2','$p_num_cart_items','$p_payment_status','$p_pending_reason','$p_payment_date','$p_settle_amount','$p_settle_currency','$p_exchange_rate','$p_payment_gross','$p_payment_fee','$p_mc_gross','$p_mc_fee','$p_mc_currency','$p_tax','$p_txn_id','$p_txn_type','$p_memo','$p_first_name','$p_last_name','$p_address_street','$p_address_city','$p_address_state','$p_address_zip','$p_address_country','$p_address_status','$p_payer_email','$p_payer_id','$p_payer_status','$p_payment_type','$p_notify_version','$p_verify_sign','$res','$timestamp')";
        mysql_query($query) or die($error_insert);
    }


}

?>
