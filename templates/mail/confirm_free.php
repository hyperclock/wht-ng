<?php
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
