<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
require_once("../php_shop/includes/configure.php");
require_once("../php_shop/zone_class.php");

$zone_obj = new zoneAccess();
$zoneadd = 0;
if(isset($_POST["action"])){
  switch($_POST["action"]){
    case "deletemethod":
      if(isset($_POST["method_id"]) && $_POST["method_id"]!= "" ){
        $zone_obj->deleteMethod($_POST["method_id"]);
      }
      break;
    case "addmethod":
      if(isset($_POST["method_name"]) && isset($_POST["zone_id"]) &&  isset($_POST["method_cost"]) && $_POST["method_cost"] != "" && $_POST["method_name"] != "" && $_POST["zone_id"] !=""){
        $zone_obj->addMethod($_POST["zone_id"],$_POST["method_name"],$_POST["method_cost"],$_POST["method_min"],$_POST["method_max"]);
      }                  
      break;
    case "deletecountry":
      if(isset($_POST["country_id"]) && isset($_POST["zone_id"]) && $_POST["country_id"] != "" && $_POST["zone_id"] !=""){
        $zone_obj->deleteCountryfromZone($_POST["country_id"],$_POST["zone_id"]);
      }
      break;
    case "addzone":
      if(isset($_POST["zone"]) && $_POST["zone"]!="" ){
        $zoneadd = $_POST["zone"];
      }
      break;
    case "addcountry":
      if(isset($_POST["country_id"]) && isset($_POST["zone_id"]) && $_POST["country_id"] != "" && $_POST["zone_id"] !=""){
        $zone_obj->addCountrytoZone($_POST["country_id"],$_POST["zone_id"]);
      }                  
      default;
      break;     
  }
}

$zones = $zone_obj->getZone();
if($zoneadd != 0){
  array_push($zones, array('ZONES'=>$zoneadd));
}

?>
1. <b><i>Cost</i></b> Example: <i>5:22.55,7:28,12.5:35,20:0</i> means <i>$22.55 from 0-5kg (excl.), $28.00 from (incl.) 5-7kg (excl.), $35 from 7-12.5kg, $0 from 12.5-20kg AND above</i><br>
2. Leave <b><i>Min</i></b> blank if there is no lower price limit; Leave <b><i>Max</i></b> blank if there is no upper limit.<p>
<div style="overflow:auto;height:460px;padding:3px 3px 3px 3px;">
<table class="invis"><tr><td>
<table class="simplelist">
<tr><td style='font-size:18px;'><b>Zone</b></td><td style='font-size:18px;'><b>Countries</b></td><td style='font-size:18px;'><b>Shipping Details</b></td><tr>
<tr><td>&nbsp;</td><td><table class="simplelist"><tr><td>ALL COUNTRIES</td></tr></table>
<td><p><table class="simplelist">
<tr><td rowspan=2><b>Shipping Option</b></td><td rowspan=2><b>Cost</b></td><td colspan=2><b>For Price Range of</b></td><td rowspan=2>&nbsp;</td></tr>
<tr><td><b>Min (Inclusive)</b></td><td><b>Max (Exclusive)</b></td></tr>
<?php
    $methods = $zone_obj->getShippingMethodsZone(-1);
    if($methods){
      foreach($methods AS $method){
        echo "<form action='' method='POST'><input type='hidden' name='action' value='deletemethod'><input type='hidden' name='zone_id' value=-1><input type='hidden' name='method_id' value=".$method['ID'].">";
        echo "<tr><td>".$method['SHIPPING_METHOD']."</td><td>".$method['COST']."</td><td>".$method['MIN']."</td><td>".$method['MAX']."</td><td><input type='submit' value='Delete'></td></tr>";
        echo "</form>";
      }
    }else{
      echo "<tr><td colspan=5>No records found.</td></tr>";    
    }  
?>
<form action='' method='POST'><input type='hidden' name='action' value='addmethod'><input type='hidden' name='zone_id' value=-1>
<tr><td><input type='text' name='method_name' size=20 value=''></td><td><input type='text' name='method_cost' size=15 value=''></td><td><input type='text' name='method_min' size=4 value=''></td><td><input type='text' name='method_max' size=4 value=''></td><td><input type='submit' value='Add'></td></tr>
</form>
</table><p></td></tr>
<?php
  $last_zone = 1;
  if($zones){
  foreach($zones AS $zone){
?>
<tr>
<td><?php echo $zone['ZONES'];?></td>
<td><p><table class="simplelist">
<?php
    $countries = $zone_obj->getCountries($zone['ZONES']);
    if($countries){
      foreach($countries AS $country){
        echo "<form action='' method='POST'><input type='hidden' name='action' value='deletecountry'><input type='hidden' name='zone_id' value=".$zone['ZONES']."><input type='hidden' name='country_id' value=".$country['COUNTRY_ID'].">";
        echo "<tr><td>".$zone_obj->getCountryName($country['COUNTRY_ID'])."</td><td><input type='submit' value='Delete'></td></tr>";
        echo "</form>";
      }
    }else{
      echo "<tr><td colspan=2>No records found.</td></tr>";    
    }
?>
<form action='' method='POST'><input type='hidden' name='action' value='addcountry'><input type='hidden' name='zone_id' value=<?php echo $zone['ZONES'];?>>
<tr><td><select name="country_id">
<option value="">Please Select</option>
<?php
  $countries = $zone_obj->getAllCountries();
  
  foreach($countries as $country_single){
    echo "<option value=".$country_single["ID"];
    echo ">".$country_single["NAME"]."</option>";
  }
?>
</select></td><td><input type="submit" value="Add"></td></tr>
</form>
</table><p></td>
<td><p><table class="simplelist">
<tr><td rowspan=2><b>Shipping Option</b></td><td rowspan=2><b>Cost</b></td><td colspan=2><b>For Price Range of</b></td><td rowspan=2>&nbsp;</td></tr>
<tr><td><b>Min (Inclusive)</b></td><td><b>Max (Exclusive)</b></td></tr>
<?php
    $methods = $zone_obj->getShippingMethodsZone($zone['ZONES']);
    if($methods){
      foreach($methods AS $method){
        echo "<form action='' method='POST'><input type='hidden' name='action' value='deletemethod'><input type='hidden' name='zone_id' value=".$zone['ZONES']."><input type='hidden' name='method_id' value=".$method['ID'].">";
        echo "<tr><td>".$method['SHIPPING_METHOD']."</td><td>".$method['COST']."</td><td>".$method['MIN']."</td><td>".$method['MAX']."</td><td><input type='submit' value='Delete'></td></tr>";
        echo "</form>";
      }
    }else{
      echo "<tr><td colspan=5>No records found.</td></tr>";    
    }
?>
<form action='' method='POST'><input type='hidden' name='action' value='addmethod'><input type='hidden' name='zone_id' value=<?php echo $zone['ZONES'];?>>
<tr><td><input type='text' name='method_name' size=20 value=''></td><td><input type='text' name='method_cost' size=15 value=''></td><td><input type='text' name='method_min' size=4 value=''></td><td><input type='text' name='method_max' size=4 value=''></td><td><input type='submit' value='Add'></td></tr>
</form>
</table><p></td>
</tr>
<?php
    $last_zone=$zone['ZONES'];
  }
  }
?>
<tr><td colspan=3><form action='' method='POST'><input type='hidden' name='action' value='addzone'><input type='submit' value='Create Zone'>&nbsp;Zone Number<input type='text' name='zone' size=3 value=<?php echo ($last_zone+1);?>></form></td></tr>
</table></td></tr></table></div>