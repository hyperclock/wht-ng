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

require_once './conf_inc.php';
require_once './execute_cmd.php';
require_once './i18n.php';
require_once './errors_inc.php';

session_start();
session_cache_limiter('nocache');

if(IsSet($_SESSION['user'])) {

    import_request_variables('p', 'p_');
    import_request_variables('g', 'g_');


    error_reporting($error_reporting);

    if(IsSet($g_delete)) {
        $res = execute_cmd("$exec_path/cron_cmd.php delete \"$_SESSION[user]\" \"$g_delete\"");
    }


    if(IsSet($p_change_email)) {
        $res = execute_cmd("$exec_path/cron_cmd.php change_email \"$_SESSION[user]\" \"$p_change_email\"");
    }


    if(IsSet($p_minute)) {
        if((sizeof($p_minute) > 1 && $p_minute[0] === "*") || (sizeof($p_hour) > 1 && $p_hour[0] === "*")
        || (sizeof($p_day_month) > 1 && $p_day_month[0] === "*")
        || (sizeof($p_month) > 1 && $p_month[0] === "*")
        || (sizeof($p_day_week) > 1 && $p_day_week[0] === "*")) {
            
            die("You can't select the \"every\" and another option!");
        }
        
        for($i = 0; $i < sizeof($p_minute); $i++) {
            if($i === 0) {
                $minute .= $p_minute[$i];
            } else {
                $minute .= "," . $p_minute[$i];
            }
        }

        for($i = 0; $i < sizeof($p_hour); $i++) {
            if($i === 0) {
                $hour .= $p_hour[$i];
            } else {
                $hour .= "," . $p_hour[$i];
            }
        }

        for($i = 0; $i < sizeof($p_day_month); $i++) {
            if($i === 0) {
                $day_month .= $p_day_month[$i];
            } else {
                $day_month .= "," . $p_day_month[$i];
            }
        }

        for($i = 0; $i < sizeof($p_month); $i++) {
            if($i === 0) {
                $month .= $p_month[$i];
            } else {
                $month .= "," . $p_month[$i];
            }
        }

        for($i = 0; $i < sizeof($p_day_week); $i++) {
            if($i === 0) {
                $day_week .= $p_day_week[$i];
            } else {
                $day_week .= "," . $p_day_week[$i];
            }
        }

        execute_cmd("$exec_path/cron_cmd.php write \"$_SESSION[user]\" \"$minute $hour $day_month $month $day_week\" \"$p_executable\"");
    }
    
    echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("User Properties") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style.css" />
<script type="text/javascript">
<!--
 function cron_browse()
 {
    resWin=window.open("cron_browse.php","", "dependent=1, height=430, width=300, screenX=0, screenY=10");

 }
 // --> 
 </script>
</head>
<body>
<div>
<?php
include_once './templates/header.php';
?>
<form name="form1" action="cron.php" method="post" accept-charset="ISO-8859-1">
<table border="1">
<tbody>
<tr>
<td> <?php echo _("minute"); ?> </td>
<td> <?php echo _("hour"); ?> </td>
<td> <?php echo _("day of month"); ?> </td>
<td> <?php echo _("month"); ?> </td>
<td> <?php echo _("day of week"); ?> </td>
<td> <?php echo _("path to the executable file"); ?> </td>
</tr>
<tr>
<td> <select name="minute[]" multiple="true" size="5">
<option value="*"><?php echo _("every"); ?></option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4" selected="true">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
<option value="21">21</option>
<option value="22">22</option>
<option value="23">23</option>
<option value="24">24</option>
<option value="25">25</option>
<option value="26">26</option>
<option value="27">27</option>
<option value="28">28</option>
<option value="29">29</option>
<option value="30">30</option>
<option value="31">31</option>
<option value="32">32</option>
<option value="33">33</option>
<option value="34">34</option>
<option value="35">35</option>
<option value="36">36</option>
<option value="37">37</option>
<option value="38">38</option>
<option value="39">39</option>
<option value="40">40</option>
<option value="41">41</option>
<option value="42">42</option>
<option value="43">43</option>
<option value="44">44</option>
<option value="45">45</option>
<option value="46">46</option>
<option value="47">47</option>
<option value="48">48</option>
<option value="49">49</option>
<option value="50">50</option>
<option value="51">51</option>
<option value="52">52</option>
<option value="53">53</option>
<option value="54">54</option>
<option value="55">55</option>
<option value="56">56</option>
<option value="57">57</option>
<option value="58">58</option>
<option value="59">59</option>
<option value="0">0</option>
</select> </td>
<td>  <select name="hour[]" multiple="true" size="5">
<option value="*"><?php echo _("every"); ?></option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3" selected="true">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
<option value="21">21</option>
<option value="22">22</option>
<option value="23">23</option>
<option value="0">0</option>
</select> </td>
<td>  <select name="day_month[]" multiple="true" size="5">
<option value="*" selected="true"><?php echo _("every") ?></option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
<option value="21">21</option>
<option value="22">22</option>
<option value="23">23</option>
<option value="24">24</option>
<option value="25">25</option>
<option value="26">26</option>
<option value="27">27</option>
<option value="28">28</option>
<option value="29">29</option>
<option value="30">30</option>
<option value="31">31</option>
</select> </td>
<td>  <select name="month[]" multiple="true" size="5">
<option value="*" selected="true"><?php echo _("every"); ?></option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
</select> </td>
<td>  <select name="day_week[]" multiple="true" size="5">
<option value="*" selected="true"><?php echo _("every"); ?></option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
</select> </td>
<td>
<input type="text" name="executable" value="/" size=30>
<input type="button" value="<?php echo _("Browse"); ?>..." onclick="cron_browse()">
</td>
</tr>
</tbody>
</table>
<br />
<table>
<tbody>
<tr>
<td> <?php echo _("To select more than one option use the Ctrl key and the Left mouse button"); ?>. </td>
<td width="190" align="center">
<input type="submit" value="<?php echo _("Submit"); ?>">
<input type="reset" value="<?php echo _("Reset"); ?>">
</td>
</tr>
</tbody>
</table>
</form>
<br />
<hr>
<br />
<?php
    $content = execute_cmd("$exec_path/cron_cmd.php read $_SESSION[user] \"$minute $hour $day_month $month $day_week\" $p_executable");

    $content_modified = str_replace("$userhomedir/$_SESSION[user]", "", $content);

    for($i = 0; $i < sizeof($content_modified); $i++) {
        if($i === 0 && ereg("MAILTO=", $content_modified[0])) {
            $email_output = substr($content_modified[0], 7);
        } elseif($content_modified[$i] != "") {
            echo("&nbsp;&nbsp;&nbsp;" . $content_modified[$i] . " &nbsp;&nbsp; <a href=cron.php?delete=$i> Delete </a> <br />");
        }
    }

    if(IsSet($email_output)) {
        echo("<br /><br /> " . _("If some of the programmes output a text it will be sent to email") . ": <i> $email_output </i> -
        <a href=\"cron_email_change.php\">" . _("Change") . "</a>");
    }
    include_once './templates/footer.php';
?>
</div>
</body>
</html>
<?php
} else {
    header("Location:login.php");
}
?>
