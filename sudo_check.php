<?php
require_once './conf_inc.php';
require_once './execute_cmd.php';


$exec_cmd = "ls";
$result = execute_cmd("$exec_cmd");

foreach($result as $key => $value)
{
  echo($value . "<br />");
}

?>
