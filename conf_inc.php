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

//	<<>>	Configuration	<<>>


// the domain name of your machine
$host_name = "wht.org";

// IP address of your server
$IP_address = "192.168.1.2";

// Default language
$languageDefault = "English";


//	<<>>	MySQL section	<<>>

// To connect to MySQL - usually "localhost"
$hostname = "localhost";

// Do not edit $admin value
$admin = "root";

// The root's MySQL password
$password_sql = "parolata";

// Do not edit $database value
$database = "wht";

// MySQL's data directory
$mysql_datadir = "/var/lib/mysql";

// Number of database allowed per one domain
$mysql_num_db = 3;



//	<<>>	Apache section	<<>>

// password for the apache user
$httpd_passwd = "parolata";

//Apache DocumentRoot like it is set in http.conf file
$DocumentRoot = "/var/www/html";

// CGI directory
$cgi_directory = "/var/www/cgi-bin/";

// The directory where http.conf is
$httpd_confdir = "/etc/httpd/conf";

// Log format common or combined
$httpd_logformat = "combined";

// the value for the <VirtualHost > area
// if you set $virtual_host_ip = "213.07.78.97:80"; all the
// virtual areas in apache conf files will begin with
// <VirtualHost 213.07.78.97:80>
// the default is your IP as you set it in $IP_address
// at the beginning of this file

$virtual_host_ip = $IP_address;

// Using the apache module suexec will improve your server security.
// Set $suexec to off if apache is not compilled with the option --enable-suexec
$suexec = "on";

// Period for rotating the log files
$rotate_months = 1;   //months

// Must be the same as the Group directive in httpd.conf
//Do not edit
$httpd_group = "apache";



//	<<>>	BIND section	<<>>

// The directory where are BIND zone files
$named_db = "/var/named";

// The directory where named.conf is
$named_confdir = "/etc";

// BIND configuration file
$named_conffile = "/etc/named.conf";

// zone file configuration
$hostmaster = "root.wht.org.";
$refresh = "28800";
$retry = "7200";
$expire = "60400";
$ttl = "86400";
$NS1 = "wht.org.";
$NS2 = "ns.secondary.net.";
$NS3 = "ns2.secondary.net.";
$MX = "wht.org.";

// The domain names of your server. You can set as many domains as
// you want. There must be a zone file for every domain (see BIND
// documentation for more info). Example:
$domain_name[] = "wht.org";
//$domain_name[] = "domain1.com";
//$domain_name[] = "domain2.net";
// $domain_name[2] = "domain3.org";



//	<<>>	Cron	<<>>

// The crond spool directory
$crond_spool = "/var/spool/cron";



//	<<>>	Sell commands	<<>>

$addusercmd = "useradd";
//$addusercmd = "/usr/sbin/useradd";
$chgrpcmd = "chgrp"; 
//$chgrpcmd = "/bin/chgrp";
$quotacmd = "quota";
//$quotacmd = "/usr/bin/quota";
$chmod = "chmod";
//$chmod = "/bin/chmod";
$delusercmd = "userdel";
//$delusercmd = "/usr/sbin/userdel";
$modusercmd = "usermod";
//$modusercmd = "/usr/sbin/usermod";
$setquotacmd = "setquota";
//$setquotacmd = "/usr/sbin/setquota";
$rmdircmd = "rm";
//$rmdircmd = "/bin/rm";
$sudo_cmd = "sudo";
//$sudo_cmd = "/usr/bin/sudo";

// users home directory
// If you use suexec module (recommended) this directory must be in the docroot directory
// In RedHat 9 it is /var/www (--with-suexec-docroot = /var/www)
//You only have to creae the home subdirectory
$userhomedir = "/var/www/home";

// partition where the home directory is
$partition_used = "hda1";



//	<<>>	PayPal section	<<>>

// set to off when ready to use or eliteweaver for better testing
// $testmode = "eliteweaver";
$testmode = "on";

// your PayPal business email
$business = "you@youremail.com";

// PayPal receiver email. Could be more than one. Must be the same you have set in paypal.com
$paypal_receiver_email[0] = "paypal@yourdomain.com";
$paypal_receiver_email[1] = "you@youremail.com";

// domain prices (per month)

// price for the initial traffic without  email accounts, scripts and MySQL
$price = 5;

// initial traffic (must be integer)
$inittraffic = 1000;

// Initial disk usage quota (must be integer)
$initquota = 10;   //Mbytes

// price for 1Mbytes extra traffic
$priceextratraffic = 0.005;

// price for 1Mbytes extra disk usage
$priceextraquota = 0.1;

$initemails = 5;
$priceemail = 1;
$pricescript = 1;
$pricedb = 1;


$initquota_subdomain = 5;   //Mbytes
$priceextraquota_subdomain = 0.1;
$price_subdomain = 2;
$inittraffic_subdomain = 500;
$priceextratraffic_subdomain = 0.005;
$pricescript_subdomain = 1;



//  options for hosting period
$hosting_months[] = 6;
$hosting_months[] = 9;
$hosting_months[] = 12;
$hosting_months[] = 15;
$hosting_months[] = 18;
$hosting_months[] = 21;
$hosting_months[] = 24;
$hosting_months['initial_selected'] = $hosting_months[2];



//	<<>>	Qmail	<<>>

// on or off
$enable_qmail = "on";

//	<<>>	Vpopmail	<<>>

//  the mail directories can be placed in the users' home directories or where Vpopmail
// creates them by default (usually vpopmail's home directory)
// The value can be "user" or "vpopmail"
$email_home = "vpopmail";

// quota in bytes
$email_quota = 3000000;

// postmaster's password for domains which will be registered from the admin section
// more info at the admin section
$postmaster_password = "qwertyui";

$vadddomain = "/home/vpopmail/bin/vadddomain";
$vdeldomain = "/home/vpopmail/bin/vdeldomain";
$vadduser = "/home/vpopmail/bin/vadduser";
$vdeluser = "/home/vpopmail/bin/vdeluser";
$vpasswd = "/home/vpopmail/bin/vpasswd";
$valias = "/home/vpopmail/bin/valias";


//	<<>>	Awstats	<<>>

// on or off
$enable_awstats = "on";

// awstats's link
$awstats = "/cgi-bin/awstats.pl";

// awstats update file
$awstats_update = "/var/www/cgi-bin/awstats.pl";

// awstats's configuration directory
$awstats_confdir = "/etc/awstats";

// awstats DataDir like it is set in wht-x.x/awstats/awstats.model.conf
$awstats_DataDir = "/var/cache/awstats";


//	<<>>	phpMyAdmin	<<>>

// phpMyAdmin's link
$phpmyadmin = "/phpmyadmin/index.php";



//	<<>>	Free hosting	<<>>

// use free hosting yes or no
$free_enable = "yes";

// To get rid of the commercial and use only the free hosting set $only_free to "yes"
$only_free = "no";

// disk usage quota
$free_quota = 10;    // Mbytes

// email accounts
$free_email_accounts = 5;

// free traffic
$free_traffic = 10000;    // Mbytes

// URL to the page you want to load in the pop up window (http://wht.sourceforge.net/index.html)
//  If you don't want pop up window leave blank
// This will work if $enable_cgi_free = "off"
//$popup_url = "";
$popup_url = "http://wht.sourceforge.net";

// To change the content of the confirmation email
// edit /templates/mail/confirm_free.php

// From and Reply to address. This will appear in all the mail sent from WHT
// You can change it's value localy for every template in /templates/mail
$from_replyto = "nivanov@email.com";

// If you have installed the experimental cgi_free and want to offer the free hosting
// users a CGI support set this to "on". For more info read the documentation.
$enable_cgi_free = "off";

// free database on or off
$free_db = "off";




// Do not edit the next section
$version = "wht";

// The directory where log files are
$httpd_logdir = "/var/log/wht";

// even if you use proftpd leave this "vsftpd"
$ftp_server = "vsftpd";

// Where the PHP command line scripts are
//$exec_path = "/bin";
$exec_path = "$DocumentRoot/$version";

// Error reporting: E_ERROR for displaying fatal errors only;
// E_ALL for displaying everything (use only for debugging)
//$error_reporting = E_ALL & ~E_NOTICE;
$error_reporting = E_ERROR;
//$error_reporting = E_ALL;

?>
