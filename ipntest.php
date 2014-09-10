<?php
if($_POST['cmd'] == "_notify-validate") {
    echo($_POST['ipnstatus']);
} elseif($_POST['verify_sign']!="") {
    
    $req="no=no";

    foreach ($HTTP_POST_VARS as $key => $value) {
        $value = urlencode(stripslashes($value));
        $req .= "&$key=$value";
        }

    require_once './conf_inc.php';


    $header .= "POST /".$version."/notify.php HTTP/1.0\r\n";
    $header .= "Host: www.nfusion.net\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= 'Content-Length: ' . strlen($req) . "\r\n\r\n";
    $fp = fsockopen("$host_name", 80, $errno, $errstr, 30);

    if(!$fp) {
        // ERROR
        echo "error $errstr ($errno)";
    } else {
        fputs ($fp, $header . $req);

        fclose ($fp);
    }



    header("Location:$_POST[return]");
    exit;
} else {
require_once './conf_inc.php';
require_once './i18n.php';

import_request_variables('p', 'p_');

session_cache_limiter('nocache');

echo("<?xml version=\"1.0\" encoding=\"$charset\"?>");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo($lang); ?>" xml:lang="<?php echo($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo _("Instant Payment Notification - Test") ?></title>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo($charset); ?>" />
<link rel="stylesheet" type="text/css" href="css/<?php echo($stylesheet); ?>/style.css" />
</head>
<body>
<div>
<?php
include_once './templates/header.php';
?>
 <div align="center">
If you see this page WHT is in test mode! <br /><br />
<table width="90%" cellspacing="0" cellpadding="3" border="0">
<tbody>
<form name="ipntest" action="ipntest.php" method="post" accept-charset="ISO-8859-1">
<tr>
    <td align="right">IPN Result:</td>
    <td><input type="radio" name="ipnstatus" value="VERIFIED" checked="checked"> VERIFIED <input type="radio" name="ipnstatus" value="INVALID"> INVALID</td>
</tr>

<tr>
    <td colspan="2" >  IPN Standard Variables</td>
</tr>

<tr>
    <td class="form_std" align="right">receiver_email:</td>
    <td><input type="text" maxlength="150" size="30" name="receiver_email" value="paypal@yourdomain.com"></td>
</tr>

<tr>
    <td align="right">business:</td>
    <td><input type="text" maxlength="150" size="30" name="business" value="<?php echo($p_business); ?>"></td>
</tr>

<tr>
    <td align="right">item_name:</td>
    <td><input type="text" maxlength="150" size="30" name="item_name" value="<?php echo($p_item_name); ?>"></td>
</tr>

<tr>
    <td align="right">quantity:</td>
    <td><input type="text" maxlength="25" size="15" name="quantity" value="1"></td>
</tr>


<tr>
    <td align="right">custom:</td>
    <td><input type="text" maxlength="150" size="30" name="custom" value="<?php echo($p_custom); ?>"></td>
</tr>

<tr>
    <td align="right">payment_status:</td>
    <td><input type="radio" name="payment_status" value="Completed" checked="checked"> Completed <input type="radio" name="payment_status" value="Pending"> Pending <input type="radio" name="payment_status" value="Failed"> Failed <input type="radio" name="payment_status" value="Denied"> Denied <input type="radio" name="payment_status" value="Reversed"> Reversed</td>
</tr>

<tr>
    <td align="right">pending_reason:</td>
    <td><input type="radio" name="pending_reason" value="echeck"> echeck <input type="radio" name="pending_reason" value="multi_currency"> multi_currency <input type="radio" name="pending_reason" value="intl"> intl <input type="radio" name="pending_reason" value="verify"> verify</td>
</tr>

<tr>
    <td align="right"></td>
    <td><input type="radio" name="pending_reason" value="address"> address <input type="radio" name="pending_reason" value="upgrade"> upgrade <input type="radio" name="pending_reason" value="unilateral"> unilateral <input type="radio" name="pending_reason" value="other"> other</td>
</tr>

<tr>
    <td align="right">payment_gross:</td>
    <td><input type="text" maxlength="25" size="15" name="payment_gross" value="<?php echo($p_amount); ?>"></td>
</tr>

<tr>
    <td align="right">payment_fee:</td>
    <td><input type="text" maxlength="25" size="15" name="payment_fee" value="0.00"></td>
</tr>

<tr>
    <td align="right">tax:</td>
    <td><input type="text" maxlength="25" size="15" name="tax" value="0.00"></td>
</tr>

<tr>
    <td align="right">txn_id:</td>
    <td><input type="text" maxlength="25" size="25" name="txn_id" value="<?php echo(time()); ?>"> &lt;- Dynamic</td>
</tr>

<tr>
    <td align="right">txn_type:</td>
    <td><input type="radio" name="txn_type" checked="checked" value="web_accept"> web_accept <input type="radio" name="txn_type" value="cart"> cart <input type="radio" name="txn_type" value="send_money"> send_money</td>
</tr>

<tr>
    <td align="right">for_auction:</td>
    <td><input type="radio" name="for_auction" value="true"> true</td>
</tr>

<tr>
    <td align="right">first_name:</td>
    <td><input type="text" maxlength="150" size="30" name="first_name" value="Thomas"></td>
</tr>

<tr>
    <td align="right">last_name:</td>
    <td><input type="text" maxlength="150" size="30" name="last_name" value="Tester"></td>
</tr>

<tr>
    <td align="right">address_street:</td>
    <td><input type="text" maxlength="150" size="30" name="address_street" value="21 Test Street"></td>
</tr>

<tr>
    <td align="right">address_city:</td>
    <td><input type="text" maxlength="150" size="30" name="address_city" value="Testopia"></td>
</tr>

<tr>
    <td align="right">address_state:</td>
    <td><input type="text" maxlength="150" size="30" name="address_state" value="Testville"></td>
</tr>

<tr>
    <td align="right">address_zip:</td>
    <td><input type="text" maxlength="25" size="15" name="address_zip" value="123456"></td>
</tr>

<tr>
    <td align="right">address_country:</td>
    <td><select name="address_country" value=""><option value="Afghanistan">Afghanistan
</option><option value="Albania">Albania
</option><option value="Algeria">Algeria
</option><option value="American-Samoa">American Samoa
</option><option value="Andorra">Andorra
</option><option value="Angola">Angola
</option><option value="Anguilla">Anguilla
</option><option value="Antarctica">Antarctica
</option><option value="Antigua-&amp;-Barbuda">Antigua &amp; Barbuda
</option><option value="Argentina">Argentina
</option><option value="Armenia">Armenia
</option><option value="Aruba">Aruba
</option><option value="Australia">Australia
</option><option value="Austria">Austria
</option><option value="Azerbaijan">Azerbaijan
</option><option value="Bahamas">Bahamas
</option><option value="Bahrain">Bahrain
</option><option value="Bangladesh">Bangladesh
</option><option value="Barbados">Barbados
</option><option value="Belarus">Belarus
</option><option value="Belgium">Belgium
</option><option value="Belize">Belize
</option><option value="Benin">Benin
</option><option value="Bermuda">Bermuda
</option><option value="Bhutan">Bhutan
</option><option value="Bolivia">Bolivia
</option><option value="Bosnia-Herzegovina">Bosnia Herzegovina
</option><option value="Botswana">Botswana
</option><option value="Bouvet-Island">Bouvet Island
</option><option value="Brazil">Brazil
</option><option value="British-Indian-Ocean-Territory">British Indian Ocean Territory
</option><option value="British-Virgin-Islands">British Virgin Islands
</option><option value="Brunei-Darussalam">Brunei Darussalam
</option><option value="Bulgaria">Bulgaria
</option><option value="Burkina-Faso">Burkina Faso
</option><option value="Burma">Burma
</option><option value="Burundi">Burundi
</option><option value="Cambodia">Cambodia
</option><option value="Cameroon">Cameroon
</option><option value="Canada">Canada
</option><option value="Canary-Islands">Canary Islands
</option><option value="Cape-Verde">Cape Verde
</option><option value="Cayman-Islands">Cayman Islands
</option><option value="Central-African-Republic">Central African Republic
</option><option value="Chad">Chad
</option><option value="Chile">Chile
</option><option value="China">China
</option><option value="Christmas-Island">Christmas Island
</option><option value="Cocos-Keeling-Islands">Cocos Keeling Islands
</option><option value="Colombia">Colombia
</option><option value="Comoros">Comoros
</option><option value="Congo-Democratic-Republic">Congo Democratic Republic
</option><option value="Congo-Republic">Congo Republic
</option><option value="Cook-Islands">Cook Islands
</option><option value="Costa-Rica">Costa Rica
</option><option value="Cote-dIvoire-Ivory-Coast">Cote dIvoire Ivory Coast
</option><option value="Croatia">Croatia
</option><option value="Cyprus">Cyprus
</option><option value="Czech-Republic">Czech Republic
</option><option value="Denmark">Denmark
</option><option value="Djibouti">Djibouti
</option><option value="Dominica">Dominica
</option><option value="Dominican-Republic">Dominican Republic
</option><option value="East-Timor">East Timor
</option><option value="Ecuador">Ecuador
</option><option value="Egypt">Egypt
</option><option value="El-Salvador">El Salvador
</option><option value="England">England
</option><option value="Equatorial-Guinea">Equatorial Guinea
</option><option value="Eritrea">Eritrea
</option><option value="Espana">Espana
</option><option value="Estonia">Estonia
</option><option value="Ethiopia">Ethiopia
</option><option value="Falkland-Islands">Falkland-Islands
</option><option value="Faroe-Islands">Faroe Islands
</option><option value="Fiji">Fiji
</option><option value="Finland">Finland
</option><option value="France">France
</option><option value="French-Guiana">French Guiana
</option><option value="French-Polynesia">French Polynesia
</option><option value="French-Southern-Territories">French Southern Territories
</option><option value="Gabon">Gabon
</option><option value="Gambia">Gambia
</option><option value="Georgia-Republic">Georgia Republic
</option><option value="Germany">Germany
</option><option value="Ghana">Ghana
</option><option value="Gibraltar">Gibraltar
</option><option value="Great-Britain">Great Britain
</option><option value="Greece">Greece
</option><option value="Greenland">Greenland
</option><option value="Grenada">Grenada
</option><option value="Guadeloupe">Guadeloupe
</option><option value="Guam">Guam
</option><option value="Guatemala">Guatemala
</option><option value="Guinea">Guinea
</option><option value="Guinea-Bissau">Guinea Bissau
</option><option value="Guyana">Guyana
</option><option value="Haiti">Haiti
</option><option value="Heard-&amp;-Mc-Donald-Islands">Heard &amp; Mc Donald Islands
</option><option value="Honduras">Honduras
</option><option value="Hong-Kong">Hong Kong
</option><option value="Hungary">Hungary
</option><option value="Iceland">Iceland
</option><option value="India">India
</option><option value="Indonesia">Indonesia
</option><option value="Iran">Iran
</option><option value="Ireland-Eire">Ireland Eire
</option><option value="Israel">Israel
</option><option value="Italy">Italy
</option><option value="Jamaica">Jamaica
</option><option value="Japan">Japan
</option><option value="Jordan">Jordan
</option><option value="Kazakhstan">Kazakhstan
</option><option value="Kenya">Kenya
</option><option value="Kiribati">Kiribati
</option><option value="Korea-South">Korea South
</option><option value="Korea-Republic">Korea Republic
</option><option value="Kuwait">Kuwait
</option><option value="Kyrgyzstan">Kyrgyzstan
</option><option value="Lao-Democratic-Republic">Lao Democratic Republic
</option><option value="Latvia">Latvia
</option><option value="Lebanon">Lebanon
</option><option value="Lesotho">Lesotho
</option><option value="Liberia">Liberia
</option><option value="Libya">Libya
</option><option value="Liechtenstein">Liechtenstein
</option><option value="Lithuania">Lithuania
</option><option value="Luxembourg">Luxembourg
</option><option value="Macao">Macao
</option><option value="Macedonia-Republic">Macedonia Republic
</option><option value="Madagascar">Madagascar
</option><option value="Malawi">Malawi
</option><option value="Malaysia">Malaysia
</option><option value="Maldives">Maldives
</option><option value="Mali">Mali
</option><option value="Malta">Malta
</option><option value="Marshall-Islands">Marshall Islands
</option><option value="Martinique">Martinique
</option><option value="Mauritania">Mauritania
</option><option value="Mauritius">Mauritius
</option><option value="Mayotte">Mayotte
</option><option value="Mexico">Mexico
</option><option value="Micronesia-Federated-States">Micronesia Federated States
</option><option value="Moldova-Republic">Moldova Republic
</option><option value="Monaco">Monaco
</option><option value="Mongolia">Mongolia
</option><option value="Montserrat">Montserrat
</option><option value="Morocco">Morocco
</option><option value="Mozambique">Mozambique
</option><option value="Myanmar">Myanmar
</option><option value="Namibia">Namibia
</option><option value="Nauru">Nauru
</option><option value="Nepal">Nepal
</option><option value="Netherlands">Netherlands
</option><option value="Netherlands-Antilles">Netherlands Antilles
</option><option value="New-Caledonia">New Caledonia
</option><option value="New-Zealand">New Zealand
</option><option value="Nicaragua">Nicaragua
</option><option value="Niger">Niger
</option><option value="Nigeria">Nigeria
</option><option value="Niue">Niue
</option><option value="Norfolk" island="">Norfolk Island
</option><option value="Northern-Ireland">Northern Ireland
</option><option value="Northern-Mariana-Islands">Northern Mariana Islands
</option><option value="Norway">Norway
</option><option value="Oman">Oman
</option><option value="Pakistan">Pakistan
</option><option value="Palua">Palua
</option><option value="Panama">Panama
</option><option value="Papua-New-Guinea">Papua New Guinea
</option><option value="Paraguay">Paraguay
</option><option value="Peru">Peru
</option><option value="Philippines">Philippines
</option><option value="Pitcairn-Island">Pitcairn Island
</option><option value="Poland">Poland
</option><option value="Portugal">Portugal
</option><option value="Puerto-Rico">Puerto Rico
</option><option value="Qatar">Qatar
</option><option value="Reunion">Reunion
</option><option value="Romania">Romania
</option><option value="Russia">Russia
</option><option value="Russian-Federation">Russian Federation
</option><option value="Rwanda">Rwanda
</option><option value="Saint-Helena">Saint Helena
</option><option value="Saint-Kitts-&amp;-Nevis">Saint Kitts &amp; Nevis
</option><option value="Saint-Lucia">Saint Lucia
</option><option value="Saint-Pierre-&amp;-Miquelon">Saint Pierre &amp; Miquelon
</option><option value="Saint-Vincent-&amp;-Grenadines">Saint Vincent &amp; Grenadines
</option><option value="Samoa-Independent">Samoa Independent
</option><option value="San-Marino">San Marino
</option><option value="Sao-Tome-&amp;-Principe">Sao Tome &amp; Principe
</option><option value="Saudi-Arabia">Saudi Arabia
</option><option value="Scotland">Scotland
</option><option value="Senegal">Senegal
</option><option value="Serbia-Montenegro-Yugoslavia">Serbia Montenegro Yugoslavia
</option><option value="Seychelles">Seychelles
</option><option value="Sierra-Leone">Sierra Leone
</option><option value="Singapore">Singapore
</option><option value="Slovak-Republic-Slovakia">Slovak Republic Slovakia
</option><option value="Slovenia">Slovenia
</option><option value="Solomon-Islands">Solomon Islands
</option><option value="Somalia">Somalia
</option><option value="South-Africa">South Africa
</option><option value="South-Georgia-Sandwich-Islands">South Georgia Sandwich Islands
</option><option value="South-Korea">South Korea
</option><option value="Spain">Spain
</option><option value="Sri-Lanka">Sri Lanka
</option><option value="Sudan">Sudan
</option><option value="Suriname">Suriname
</option><option value="Svalbard-&amp;-Jan-Mayen-Islands">Svalbard &amp; Jan Mayen Islands
</option><option value="Swaziland">Swaziland
</option><option value="Sweden">Sweden
</option><option value="Switzerland">Switzerland
</option><option value="Syrian-Arab-Republic-Syria">Syrian Arab Republic Syria
</option><option value="Taiwan">Taiwan
</option><option value="Tajikistan">Tajikistan
</option><option value="Tanzania">Tanzania
</option><option value="Thailand">Thailand
</option><option value="Togo">Togo
</option><option value="Tokelau">Tokelau
</option><option value="Tonga">Tonga
</option><option value="Trinidad">Trinidad
</option><option value="Trinidad-&amp;-Tobago">Trinidad &amp; Tobago
</option><option value="Tristan-da-Cunha">Tristan da Cunha
</option><option value="Tunisia">Tunisia
</option><option value="Turkey">Turkey
</option><option value="Turkmenistan">Turkmenistan
</option><option value="Turks-&amp;-Caicos-Islands">Turks &amp; Caicos Islands
</option><option value="Tuvalu">Tuvalu
</option><option value="Uganda">Uganda
</option><option value="Ukraine">Ukraine
</option><option value="United-Arab-Emirates">United Arab Emirates
</option><option value="United-Kingdom">United Kingdom
</option><option selected="" value="United-States">United States
</option><option value="Uruguay">Uruguay
</option><option value="US-Virgin-Islands">US Virgin Islands
</option><option value="Uzbekistan">Uzbekistan
</option><option value="Vanuatu">Vanuatu
</option><option value="Vatican-City">Vatican City
</option><option value="Venezuela">Venezuela
</option><option value="Vietnam">Vietnam
</option><option value="Wales">Wales
</option><option value="Wallis-&amp;-Futuna-Islands">Wallis &amp; Futuna Islands
</option><option value="Western-Samoa">Western Samoa
</option><option value="Yemen">Yemen
</option><option value="Zambia">Zambia
</option><option value="Zimbabwe">Zimbabwe</option></select></td>
  
      </tr>

      <tr>
          <td align="right">address_status:</td>
          <td><input type="radio" name="address_status" value="confirmed" checked="checked"> confirmed <input type="radio" name="address_status" value="unconfirmed"> unconfirmed</td>
      </tr>

      <tr>
          <td align="right">payer_email:</td>
          <td><input type="text" maxlength="150" size="30" name="payer_email" value="paypal@theirdomain.com"> </td>
      </tr>

      <tr>
          <td align="right">payer_status:</td>
          <td><input type="radio" name="payer_status" value="verified" checked="checked"> verified <input type="radio" name="payer_status" value="unverified"> unverified <input type="radio" name="payer_status" value="intl_verified"> intl_verified <input type="radio" name="payer_status" value="intl_unverified"> intl_unverified </td>
      </tr>

      <tr>
          <td align="right">payment_type:</td>
          <td><input type="radio" name="payment_type" value="echeck"> echeck <input type="radio" name="payment_type" value="instant" checked="checked"> instant</td>
      </tr>

      <tr>
          <td>  Form Actions</td>
      </tr>

      <tr>
          <td> <input type="button" name="Cancel" value="Cancel" onClick="location.href='cancel_return.php'"></td>
          <td> <input type="reset" name="reset" value="Reset Form">
          <input type="submit" name="action" value="Submit IPN"></td>
      </tr>
<input type="hidden" name="verify_sign" value="KDFJOAEAOSIDJFOPAJJGIIUJKFUIHF838RHOL">
<input type="hidden" name="return" value="<?php echo($p_return); ?>">
<input type="hidden" name="cancel_return" value="<?php echo($p_cancel_return); ?>">
</form>
  </tbody></table>
<?php
include_once './templates/footer.php';
?>
</div>
</body>
</html>

<?php
}
?>
