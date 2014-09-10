<?php
require_once './conf_inc.php'
?>
function calculate()
{
    months_index=document.form1.months.selectedIndex;
    months=document.form1.months.options[months_index].text;
    debit=<?php echo($pricedb); ?>*months;
    document.form1.calc_value.value=debit;
}