<?php
require_once './conf_inc.php';
require_once './i18n.php';

error_reporting($error_reporting);

$return_message = _("You are registered and may log in from the home page.");

include_once './templates/return.php';
?>
