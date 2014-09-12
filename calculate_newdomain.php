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
    price=<?php echo($price); ?>;
    inittraffic=<?php echo($inittraffic); ?>;
    initquota=<?php echo($initquota); ?>;
    priceextratraffic=<?php echo($priceextratraffic); ?>;
    priceextraquota=<?php echo($priceextraquota); ?>;
    initemails=<?php echo($initemails); ?>;
    priceemail=<?php echo($priceemail); ?>;
    pricescript=<?php echo($pricescript); ?>;
    months_index=document.form1.months.selectedIndex;
    months=document.form1.months.options[months_index].text;
    num_emails=0;
<?php
if($enable_qmail==="on") {
?>
    num_emails=document.form1.num_emails.value;
<?php
}
?>
    traffic=document.form1.traffic.value;
    quota=document.form1.quota.value;

    if(document.form1.script.checked) {
        script=1;
    } else {
        script=0;
    }


    debit=price*months+priceemail*num_emails*months+pricescript*months*script+months*(priceextratraffic*(traffic-inittraffic))+months*(priceextraquota*(quota-initquota));

    document.form1.calc_value.value=debit;

}
