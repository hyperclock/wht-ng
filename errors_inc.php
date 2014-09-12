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

$error_b = "<br /><blink>";
$error_a = "<br /></blink>";
$error_connectdb = _("error connect db");
$error_selectdb = _("error select db");
$error_query = _("error query db");
$error_insert = _("Can't insert in db");
$error_update = _("Can't update db");
$error_fill = $error_b . _("Fill in the areas marked with*") . $error_a;
$error_select = _("Can't select the requested element.");
$error_same_user = $error_b . _("User with the same name already exists.") . $error_a;
$error_login_fill = $error_b . _("Wrong user or password.") . $error_a;
$error_same_domain = $error_b . _("The domain you chose is busy. Make another choice.") . $error_a;
$error_short_password = $error_b . _("The password must be at least 8 characters long!") . $error_a;
$error_makedir = _("Can't make directory.");
$error_delete = _("Can't delete the element from the database.");
$error_exist_subdomain = _("You first have to delete all subdomains of this domain before deleting it!");
$error_end_emails = _("Limit reached. If you want to create more email accounts click Domains and the modify link for the proper domain.");
$error_not_allowed_char = $error_b . _("Some of the fields contain not allowed characters ( ; \" $ ...)!") . $error_a;
$error_not_allowed_char_ul =$error_b . _("The user and domain name must not contain \"@\"  \".\" or space!") . $error_a;
$error_not_allowed_char_ud = $error_b . _("The user and domain name must not contain \"@\" or space!") . $error_a;
$error_not_allowed_char_u = $error_b . _("The user name must not begin with a number!") . $error_a;
?>
