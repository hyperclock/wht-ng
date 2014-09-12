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

require_once './conf_inc.php'
?>
function calculate()
{
    price=<?php echo($price_subdomain); ?>;
    inittraffic=<?php echo($inittraffic_subdomain); ?>;
    initquota=<?php echo($initquota_subdomain); ?>;
    priceextratraffic=<?php echo($priceextratraffic_subdomain); ?>;
    priceextraquota=<?php echo($priceextraquota_subdomain); ?>;
    pricescript=<?php echo($pricescript_subdomain); ?>;
    months_index=document.form1.months.selectedIndex;
    months=document.form1.months.options[months_index].text;
    traffic=document.form1.traffic.value;
    quota=document.form1.quota.value;

    if(document.form1.script.checked) {
        script=1;
    } else {
        script=0;
    }


    debit=price*months+pricescript*months*script+months*(priceextratraffic*(traffic-inittraffic))+months*(priceextraquota*(quota-initquota));

    document.form1.calc_value.value=debit;

}
