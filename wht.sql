# phpMyAdmin MySQL-Dump
# version 2.5.0
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Generation Time: Jul 04, 2003 at 10:59 PM
# Server version: 3.23.54
# PHP Version: 4.2.2
# Database : `wht`
# --------------------------------------------------------

#
# Table structure for table `admin_notify`
#
# Creation: Jun 28, 2003 at 08:03 PM
# Last update: Jun 28, 2003 at 08:03 PM
#

CREATE TABLE `admin_notify` (
  `ID` int(11) NOT NULL auto_increment,
  `user` varchar(20) NOT NULL default '',
  `domain` varchar(40) NOT NULL default '',
  `notify` varchar(120) NOT NULL default '',
  `timestamp` int(14) NOT NULL default '0',
  KEY `ID` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;
# --------------------------------------------------------

#
# Table structure for table `deleted`
#
# Creation: Jul 04, 2003 at 06:19 PM
# Last update: Jul 04, 2003 at 06:19 PM
#

CREATE TABLE `deleted` (
  `ID` int(11) NOT NULL auto_increment,
  `domain` varchar(50) default NULL,
  `subdomain` varchar(30) NOT NULL default '',
  `zone` varchar(40) NOT NULL default '',
  `modified` char(1) NOT NULL default '',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;
# --------------------------------------------------------

#
# Table structure for table `domains`
#
# Creation: Jul 04, 2003 at 06:04 PM
# Last update: Jul 04, 2003 at 10:50 PM
#

CREATE TABLE `domains` (
  `ID` int(11) NOT NULL auto_increment,
  `domain` varchar(50) NOT NULL default '',
  `subdomain` varchar(30) NOT NULL default '',
  `zone` varchar(40) NOT NULL default '',
  `sub` char(1) NOT NULL default '',
  `ns1` varchar(50) NOT NULL default '',
  `ns2` varchar(50) NOT NULL default '',
  `ns3` varchar(50) NOT NULL default '',
  `ns4` varchar(50) NOT NULL default '',
  `num_emails` int(3) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `script` char(2) NOT NULL default '',
  `ssl` char(2) NOT NULL default '',
  `months` int(2) NOT NULL default '0',
  `quota` int(11) NOT NULL default '0',
  `traffic` int(11) NOT NULL default '0',
  `rotate_traffic` float NOT NULL default '0',
  `debit` float NOT NULL default '0',
  `day` int(2) NOT NULL default '0',
  `month` int(2) NOT NULL default '0',
  `year` int(4) NOT NULL default '0',
  `expday` int(2) NOT NULL default '0',
  `expmonth` int(2) NOT NULL default '0',
  `expyear` int(4) NOT NULL default '0',
  `free` char(1) NOT NULL default '',
  `category` varchar(30) NOT NULL default '',
  `enable` char(1) NOT NULL default '',
  `domaincheck` char(1) default NULL,
  `status` char(1) default NULL,
  `timestamp` int(14) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=132 ;
# --------------------------------------------------------

#
# Table structure for table `email_aliases`
#
# Creation: Jun 28, 2003 at 08:03 PM
# Last update: Jun 28, 2003 at 08:03 PM
#

CREATE TABLE `email_aliases` (
  `ID` int(11) NOT NULL auto_increment,
  `email` varchar(50) NOT NULL default '',
  `alias` varchar(50) NOT NULL default '',
  KEY `ID` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=4 ;
# --------------------------------------------------------

#
# Table structure for table `emails`
#
# Creation: Jun 28, 2003 at 08:03 PM
# Last update: Jun 28, 2003 at 08:03 PM
#

CREATE TABLE `emails` (
  `ID` int(11) NOT NULL auto_increment,
  `domain_id` int(11) NOT NULL default '0',
  `email` varchar(50) NOT NULL default '',
  `password` varchar(15) NOT NULL default '',
  KEY `ID` (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=11 ;
# --------------------------------------------------------

#
# Table structure for table `ipn`
#
# Creation: Jun 28, 2003 at 08:03 PM
# Last update: Jun 28, 2003 at 08:03 PM
#

CREATE TABLE `ipn` (
  `ipn_id` int(10) NOT NULL auto_increment,
  `receiver_email` varchar(75) NOT NULL default '',
  `business` varchar(75) NOT NULL default '',
  `item_name` varchar(127) default NULL,
  `item_number` varchar(127) default NULL,
  `quantity` int(10) default NULL,
  `invoice` varchar(64) default NULL,
  `custom` varchar(64) default NULL,
  `option_name1` varchar(127) default NULL,
  `option_selection1` varchar(127) default NULL,
  `option_name2` varchar(127) default NULL,
  `option_selection2` varchar(127) default NULL,
  `num_cart_items` int(10) default NULL,
  `payment_status` enum('Completed','Pending','Failed','Denied','Reversed') default NULL,
  `pending_reason` enum('echeck','intl','verify','address','upgrade','unilateral','other') default NULL,
  `payment_date` varchar(55) default NULL,
  `settle_amount` decimal(9,2) default NULL,
  `settle_currency` enum('USD','CAD','GBP','EUR','JPY') default NULL,
  `exchange_rate` varchar(15) default NULL,
  `payment_gross` decimal(9,2) default NULL,
  `payment_fee` decimal(9,2) default NULL,
  `mc_gross` decimal(9,2) default NULL,
  `mc_fee` decimal(9,2) default NULL,
  `mc_currency` enum('USD','CAD','GBP','EUR','JPY') default NULL,
  `tax` decimal(9,2) default NULL,
  `txn_id` varchar(50) default NULL,
  `txn_type` enum('web_accept','cart','send_money') default NULL,
  `memo` tinytext,
  `first_name` varchar(32) default NULL,
  `last_name` varchar(32) default NULL,
  `address_street` varchar(64) default NULL,
  `address_city` varchar(32) default NULL,
  `address_state` varchar(32) default NULL,
  `address_zip` varchar(25) default NULL,
  `address_country` varchar(32) default NULL,
  `address_status` enum('confirmed','unconfirmed') default NULL,
  `payer_email` varchar(75) default NULL,
  `payer_id` varchar(60) default NULL,
  `payer_status` enum('verified','unverified','intl_verified','intl_unverified') default NULL,
  `payment_type` enum('echeck','instant') default NULL,
  `notify_version` varchar(10) default NULL,
  `verify_sign` varchar(128) default NULL,
  `STATUS` enum('VERIFIED','INVALID') default NULL,
  `recorded` int(15) default NULL,
  PRIMARY KEY  (`ipn_id`),
  UNIQUE KEY `txn_id` (`txn_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;
# --------------------------------------------------------

#
# Table structure for table `ipn_error`
#
# Creation: Jun 28, 2003 at 08:03 PM
# Last update: Jun 28, 2003 at 08:03 PM
#

CREATE TABLE `ipn_error` (
  `error` varchar(50) NOT NULL default ''
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `ipn_test`
#
# Creation: Jun 28, 2003 at 08:03 PM
# Last update: Jun 28, 2003 at 08:03 PM
#

CREATE TABLE `ipn_test` (
  `ipn_id` int(10) NOT NULL auto_increment,
  `receiver_email` varchar(75) NOT NULL default '',
  `business` varchar(75) NOT NULL default '',
  `item_name` varchar(127) default NULL,
  `item_number` varchar(127) default NULL,
  `quantity` int(10) default NULL,
  `invoice` varchar(64) default NULL,
  `custom` varchar(64) default NULL,
  `option_name1` varchar(127) default NULL,
  `option_selection1` varchar(127) default NULL,
  `option_name2` varchar(127) default NULL,
  `option_selection2` varchar(127) default NULL,
  `num_cart_items` int(10) default NULL,
  `payment_status` enum('Completed','Pending','Failed','Denied','Reversed') default NULL,
  `pending_reason` enum('echeck','intl','verify','address','upgrade','unilateral','other') default NULL,
  `payment_date` varchar(55) default NULL,
  `settle_amount` decimal(9,2) default NULL,
  `settle_currency` enum('USD','CAD','GBP','EUR','JPY') default NULL,
  `exchange_rate` varchar(15) default NULL,
  `payment_gross` decimal(9,2) default NULL,
  `payment_fee` decimal(9,2) default NULL,
  `mc_gross` decimal(9,2) default NULL,
  `mc_fee` decimal(9,2) default NULL,
  `mc_currency` enum('USD','CAD','GBP','EUR','JPY') default NULL,
  `tax` decimal(9,2) default NULL,
  `txn_id` varchar(50) default NULL,
  `txn_type` enum('web_accept','cart','send_money') default NULL,
  `memo` tinytext,
  `first_name` varchar(32) default NULL,
  `last_name` varchar(32) default NULL,
  `address_street` varchar(64) default NULL,
  `address_city` varchar(32) default NULL,
  `address_state` varchar(32) default NULL,
  `address_zip` varchar(25) default NULL,
  `address_country` varchar(32) default NULL,
  `address_status` enum('confirmed','unconfirmed') default NULL,
  `payer_email` varchar(75) default NULL,
  `payer_id` varchar(60) default NULL,
  `payer_status` enum('verified','unverified','intl_verified','intl_unverified') default NULL,
  `payment_type` enum('echeck','instant') default NULL,
  `notify_version` varchar(10) default NULL,
  `verify_sign` varchar(128) default NULL,
  `STATUS` enum('VERIFIED','INVALID') default NULL,
  `recorded` int(15) default NULL,
  PRIMARY KEY  (`ipn_id`),
  UNIQUE KEY `txn_id` (`txn_id`)
) TYPE=MyISAM AUTO_INCREMENT=63 ;
# --------------------------------------------------------

#
# Table structure for table `temporary_domains`
#
# Creation: Jun 28, 2003 at 08:03 PM
# Last update: Jun 28, 2003 at 08:03 PM
#

CREATE TABLE `temporary_domains` (
  `ID` int(11) NOT NULL auto_increment,
  `domain` varchar(50) NOT NULL default '0',
  `num_emails` int(3) NOT NULL default '0',
  `script` char(2) NOT NULL default '',
  `ssl` char(2) NOT NULL default '',
  `months` int(2) NOT NULL default '0',
  `quota` int(11) NOT NULL default '0',
  `traffic` int(11) NOT NULL default '0',
  `debit` float NOT NULL default '0',
  `expday` int(2) NOT NULL default '0',
  `expmonth` int(2) NOT NULL default '0',
  `expyear` int(4) NOT NULL default '0',
  `timestamp` int(14) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=27 ;
# --------------------------------------------------------

#
# Table structure for table `temporary_users`
#
# Creation: Jun 28, 2003 at 08:03 PM
# Last update: Jun 28, 2003 at 08:03 PM
#

CREATE TABLE `temporary_users` (
  `ID` int(11) NOT NULL auto_increment,
  `user` varchar(15) default NULL,
  `db_expday` int(2) NOT NULL default '0',
  `db_expmonth` int(2) NOT NULL default '0',
  `db_expyear` int(4) NOT NULL default '0',
  `timestamp` int(14) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;
# --------------------------------------------------------

#
# Table structure for table `users`
#
# Creation: Jun 28, 2003 at 08:03 PM
# Last update: Jul 04, 2003 at 06:17 PM
#

CREATE TABLE `users` (
  `ID` int(11) NOT NULL auto_increment,
  `user` varchar(15) default NULL,
  `password` varchar(50) default NULL,
  `quota` int(11) NOT NULL default '0',
  `email` varchar(50) default NULL,
  `db` char(2) NOT NULL default '',
  `db_expday` int(2) NOT NULL default '0',
  `db_expmonth` int(2) NOT NULL default '0',
  `db_expyear` int(4) NOT NULL default '0',
  `debit` float NOT NULL default '0',
  `status` char(1) default NULL,
  `timestamp` int(14) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=74 ;

