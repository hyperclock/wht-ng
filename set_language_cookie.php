<?php
$host_name = $HTTP_SERVER_VARS['SERVER_NAME'];
if(IsSet($HTTP_POST_VARS['language'])) {
    setcookie("lang", $HTTP_POST_VARS['language'], time() + 93312000, "/" . $version, $host_name);
    $language_cookie = $HTTP_POST_VARS['language'];
}
?>
