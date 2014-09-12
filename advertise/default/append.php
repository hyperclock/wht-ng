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
?>

wht-ng_body = document.getElementsByTagName("body");

wht-ng_div_header = document.createElement("div");
wht-ng_body[0].insertBefore(wht-ng_div_header,wht-ng_body[0].childNodes[0]);

wht-ng_txt_header = document.createTextNode(" Header text for default category.");
wht-ng_div_header.appendChild(wht-ng_txt_header);

wht-ng_br = document.createElement("br");
wht-ng_div_header.appendChild(wht-ng_br);

wht-ng_a_header = document.createElement("a");
wht-ng_a_header_href = wht-ng_a_header.setAttribute("href", "http://wht.sourceforge.net");
wht-ng_div_header.appendChild(wht-ng_a_header);

wht-ng_img_header = document.createElement("img");
wht-ng_img_header_src = wht-ng_img_header.setAttribute("src", "<?php echo("http://$host_name/$version/advertise/$category/header.png"); ?>");
wht-ng_a_header.appendChild(wht-ng_img_header);




wht-ng_div_footer = document.createElement("div");
wht-ng_body[0].appendChild(wht-ng_div_footer);

wht-ng_a_footer = document.createElement("a");
wht-ng_a_footer_href = wht-ng_a_footer.setAttribute("href", "http://wht.sourceforge.net");
wht-ng_div_footer.appendChild(wht-ng_a_footer);

wht-ng_img_footer = document.createElement("img");
wht-ng_img_footer_src = wht-ng_img_footer.setAttribute("src", "<?php echo("http://$host_name/$version/advertise/$category/footer.png"); ?>");
wht-ng_a_footer.appendChild(wht-ng_img_footer);

wht-ng_br = document.createElement("br");
wht-ng_div_footer.appendChild(wht-ng_br);

wht-ng_txt_footer = document.createTextNode(" Footer text.");
wht-ng_div_footer.appendChild(wht-ng_txt_footer);
