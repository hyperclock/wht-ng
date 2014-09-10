<?php
require_once '../conf_inc.php';
require_once '../i18n.php';
require_once '../errors_inc.php';

error_reporting($error_reporting);

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="../css/<?php echo($stylesheet); ?>/style_navigation.css" />
<script type="text/javascript">
<!--
function change()
{
    switch(document.form1.select.selectedIndex) {
    case 0:
        document.form1.action="domains.php";
        break;

    case 1:
        document.form1.action="users.php";
        break;
    }
}

function admin_notify()
{
    resWin=window.open("admin_notify.php","", "dependent, height=420, width=450, screenX=90, screenY=10, scrollbars=1 ");
}
counter = 1;
InitHeight = 55;

function estimate_height()
{
    if(self.innerHeight) {
        CurrentHeight = self.innerHeight;
    } else if(document.documentElement && document.documentElement.clientHeight) {
        CurrentHeight = document.documentElement.clientHeight;
    } else if(document.body) {
        CurrentHeight = document.body.clientHeight;
    }
    
    if(document.layers || navigator.userAgent.toLowerCase().indexOf("gecko") >= 0) {
        CurrentHeight -= 23;
        nav_height = document.height;
    } else {
        nav_height = document.body.scrollHeight;
    } 
 
    if(CurrentHeight < nav_height  && counter < 65) {
        counter++;
        if(!parent.set_size(InitHeight + counter)) {
            clearInterval(timerId);
        }

    } else {
        clearInterval(timerId);
    }

}
// -->
</script>
</head>
<body onload="timerId = setInterval('estimate_height()', 100);">
<form name="form1" action="domains.php" method="post" target="domains" accept-charset="ISO-8859-1">
<table cellpadding="2" cellspacing="2" margin-left="auto"
style="width: 100%;" margin-right="0px">
<tbody>
<tr>
<td valign="bottom" width="370" align="right">
<input name="search" size="25"> 

<select name="select" onchange="change()">
<option value="domain"><?php echo _("domain"); ?></option>
<option value="user"><?php echo _("user"); ?></option>
</select>

<input value="" type="hidden" name="num">
<input type="submit" value="<?php echo _("search"); ?>">
</td>
<td valign="bottom" align="right" >
<a href="newuser.php" target="domains"><?php echo _("New user"); ?></a> |
<a href="free_newuser.php" target="domains"><?php echo _("Free hosting"); ?></a> |
<a href="domains.php" target="domains" OnClick="admin_notify()"><?php echo _("Notify"); ?></a> |
</td>
<td valign="top" width="45px" align="right" >
<a href="../logout.php" target="_top"><?php echo _("Log out"); ?></a>
</td>
</tbody>
</table>
</form>
</body>
</html>
