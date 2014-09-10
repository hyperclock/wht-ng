<?php
// The email that will be sent to a user that requests his lost password.

// The $from_replyto variable is defined in conf_inc.php
// You can change it here too.
// $from_replyto = "";

$mail_headers = "Return-Path: <$from_replyto>\r\n" . "From: $from_replyto\r\n" . "Reply-To: $from_replyto\r\n";

$subject = "Lost password";

$body = "

Lost password - "

. $res[0]['password'];

?>
