wht_body = document.getElementsByTagName("body");

wht_div_header = document.createElement("div");
wht_body[0].insertBefore(wht_div_header,wht_body[0].childNodes[0]);

wht_txt_header = document.createTextNode(" Header text for entertainment category.");
wht_div_header.appendChild(wht_txt_header);

wht_br = document.createElement("br");
wht_div_header.appendChild(wht_br);

wht_a_header = document.createElement("a");
wht_a_header_href = wht_a_header.setAttribute("href", "http://wht.sourceforge.net");
wht_div_header.appendChild(wht_a_header);

wht_img_header = document.createElement("img");
wht_img_header_src = wht_img_header.setAttribute("src", "<?php echo("http://$host_name/$version/advertise/$category/header.png"); ?>");
wht_a_header.appendChild(wht_img_header);




wht_div_footer = document.createElement("div");
wht_body[0].appendChild(wht_div_footer);

wht_a_footer = document.createElement("a");
wht_a_footer_href = wht_a_footer.setAttribute("href", "http://wht.sourceforge.net");
wht_div_footer.appendChild(wht_a_footer);

wht_img_footer = document.createElement("img");
wht_img_footer_src = wht_img_footer.setAttribute("src", "<?php echo("http://$host_name/$version/advertise/$category/footer.png"); ?>");
wht_a_footer.appendChild(wht_img_footer);

wht_br = document.createElement("br");
wht_div_footer.appendChild(wht_br);

wht_txt_footer = document.createTextNode(" Footer text.");
wht_div_footer.appendChild(wht_txt_footer);
