<?php

$ftp_server_ip = "127.0.0.1";

$conn_id = ftp_connect($ftp_server_ip, 21, 5); 

$login_result = ftp_login($conn_id, $p_user, $p_password); 

if ((!$conn_id) || (!$login_result)) { 
    echo "FTP connection has failed!";
    echo "Attempted to connect to $ftp_server_ip for user $p_user"; 
    die; 
} else {
    ftp_mkdir ($conn_id, "$domain_insert");
    ftp_put($conn_id, "$domain_insert/index.html", "$DocumentRoot/$version/templates/index.html", FTP_ASCII);

    if($script === 1)
        ftp_mkdir($conn_id, $domain_insert . "_cgi-bin");
}

ftp_close($conn_id); 	

?>