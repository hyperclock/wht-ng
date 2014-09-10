<?php
function check_url($error_to_get)
{
    global $url_back;

    if(strpos($_SERVER['HTTP_REFERER'], "?")) {
        $url_back = $_SERVER['HTTP_REFERER'] . "&error_spell=$error_to_get";
    } else {
        $url_back = $_SERVER['HTTP_REFERER'] . "?error_spell=$error_to_get";
    }
}

foreach ($HTTP_POST_VARS as $key => $value) {

    If($key === "submit" || $key === "Submit" || $key === "NF" || $key === "Paste") {
        continue;
    }

    $value_escaped = escapeshellcmd($value);

    if($value_escaped != $value) {
    
        check_url("error_not_allowed_char");

        import_request_variables('p', 'p_');
        import_request_variables('g', 'g_');

        if(file_exists('./setcookies.php')) {
            include_once './setcookies.php';
        } else {
            include_once '../setcookies.php';
        }

        header("Location:$url_back");
        echo(" ");

        }


    if($key === "user") {
    
        if(ereg("^[0-9]+", $value)) {
            
            check_url("error_not_allowed_char_u");

            import_request_variables('p', 'p_');
            import_request_variables('g', 'g_');

            if(file_exists('./setcookies.php')) {
                include_once './setcookies.php';
            } else {
                include_once '../setcookies.php';
            }

            header("Location:$url_back");
            echo(" ");

            }
        }


    if($key === "user" || $key === "local_domain") {
        
        if(strpos($value, "@") !== false || strpos($value, " ") !== false || strpos($value, ".") !== false) {
            
            check_url("error_not_allowed_char_ul");

            import_request_variables('p', 'p_');
            import_request_variables('g', 'g_');

            if(file_exists('./setcookies.php')) {
                include_once './setcookies.php';
            } else {
                include_once '../setcookies.php';
            }


            header("Location:$url_back");
            echo(" ");

            }
        }

    if($key === "domain") {
        
        if(strpos($value, "@") !== false || strpos($value, " ") !== false) {
            
            check_url("error_not_allowed_char_ud");

            import_request_variables('p', 'p_');
            import_request_variables('g', 'g_');

            if(file_exists('./setcookies.php')) {
                include_once './setcookies.php';
            } else {
                include_once '../setcookies.php';
            }

            header("Location:$url_back");
            echo(" ");

            }

        }

}

?>
