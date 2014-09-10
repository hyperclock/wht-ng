<?php
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
