<?php
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
