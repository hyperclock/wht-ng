<?php
require_once './conf_inc.php';

import_request_variables('g', 'g_');

$userhomedir_array = explode("/", $userhomedir);
$num_subdirs = sizeof($userhomedir_array);

$domain_array = explode("/", $g_domain);

if($ftp_server === "proftpd") {
    $domain=$domain_array[$num_subdirs+2];
} else {
    $domain=$domain_array[$num_subdirs+1];
}

if($ftp_server === "proftpd") {
    $index_file=$domain_array[$num_subdirs+3];
} else {
    $index_file=$domain_array[$num_subdirs+2];
}

if($popup_url != "" && false !== strstr($index_file, "index.")) {
    echo("open(\"$popup_url\")\n");
}

?>
function frame_size()
{
    var sfMax = 0;
    var sf, frameMax;
    var ad = 1;

    if(self.innerWidth) {
        AD_windowWidth = self.innerWidth;
        AD_windowHeight = self.innerHeight;
     } else if(document.documentElement && document.documentElement.clientWidth) {
        AD_windowWidth = document.documentElement.clientWidth;
        AD_windowHeight = document.documentElement.clientHeight;
    } else if(document.body) {
        AD_windowWidth = document.body.clientWidth;
        AD_windowHeight = document.body.clientHeight;
    }
    
    if(document.layers || navigator.userAgent.toLowerCase().indexOf("gecko") >= 0)
        AD_windowWidth -= 16;

    if ( top != self || top.frames.length != 0 ) {
        ad = 0;
        if(document.all) {
            sf = (top.document.body.clientWidth * top.document.body.clientHeight) / (self.document.body.clientWidth * self.document.body.clientHeight);
            if (sf<3) {
                ad = 1;
            }
        } else {
            function getSurface(w)
            {
                if(!w.sf) {
                    w.sf = w.innerWidth * w.innerHeight;
                }
                
                if(w.sf >= sfMax) {
                    sfMax = w.sf;
                    frameMax = w;
                }
            }
            
            function findFrameMax(w)
            {
                var i;
                if(w.frames.length == 0) {
                    getSurface(w);
                } else {
                    for(i = 0; i < w.frames.length; i++) {
                        if (w.frames[i].frames.length > 0) {
                            findFrameMax(w.frames[i]);
                        } else {
                            getSurface(w.frames[i]);
                        }
                    }
                }
            }
            
            if ((top != self) || (top.frames.length == 0)) {
                findFrameMax(top);
                
                if (frameMax == self) {
                    ad = 1;
                }
            }
        }
    }
    if(AD_windowWidth < 400) {
        ad = 0;
    }
    
    if(AD_windowHeight < 80) {
        ad = 0;
    }

    return ad;
}


if(frame_size() == 1) {

<?php

@mysql_connect($hostname, $admin, $password_sql) or die($error_connectdb);
@mysql_select_db($database) or die($error_selectdb);


$query = "select category from domains where domain='$domain'";
$result = mysql_query($query) or die($error_select);

$res = mysql_fetch_array($result);
$category = $res['category'];

if($category == "" || $category == NULL) {
    $category = "default";
}

$file = "$DocumentRoot/$version/advertise/$category/append.php";

require_once $file;

?>

}
