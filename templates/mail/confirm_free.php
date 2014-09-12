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

// The email that will be sent for a free hosting confirmation.

// The $from_replyto variable is defined in conf_inc.php
// You can change it here too.
// $from_replyto = "";

$mail_headers = "Return-Path: <$from_replyto>\r\n" . "From: $from_replyto\r\n" . "Reply-To: $from_replyto\r\n";

$subject = "Free hosting confirmation";

$body = "


To confirm your free hosting registration click the link - http://$host_name/$version/free/confirm.php?conf=$conf


";

?>
