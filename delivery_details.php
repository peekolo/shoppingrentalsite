<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
?>
<div class="box_big" style="position:relative;">
&nbsp;<p>
<?php
if(!isset($_SESSION["paymentType"])){
  echo "Error - Illegal Access!";
  exit;
}

?>
<p><h1>Choose Shipping Address</h1><p>

<form action="?p=<?php if($_SESSION["paymentType"] == "Rent") echo 'rentcheckout'; else echo 'checkout1';?>" method="POST">
<table class="plain1">
<tr><td style="vertical-align:top;">
<?php
    if($user_obj->getAddress()){
      $reg_dats = $user_obj->userAddress;
      $i=0;
      foreach($reg_dats AS $reg_dat){            
?><table class="form_table" style="width:300px;">
<tr><td colspan=2 style="background-color:#888888;height:30px;text-align:left;color:#FFFFFF;"><b><input type="radio" name="address_id" value=<?php 
        echo $reg_dat[$user_obj->tbFields2['id']]." ";
        if($i==0) echo "CHECKED>&nbsp;&nbsp;&nbsp;&nbsp;Primary Address";
        else echo ">&nbsp;&nbsp;&nbsp;&nbsp;Address ".$i;
?></b></td></tr>
<tr><td>
Name</td><td><b><?php echo $reg_dat[$user_obj->tbFields2['first_name']]." ".$reg_dat[$user_obj->tbFields2['last_name']];?></b>
</td></tr>
<?php if($reg_dat[$user_obj->tbFields2['company']] != ""){ ?>
<tr><td>
Company</td><td><b><?php echo $reg_dat[$user_obj->tbFields2['company']];?></b>
</td></tr>
<?php } ?>
<tr><td>
Address</td><td><b><?php echo $reg_dat[$user_obj->tbFields2['address']];?></b>
</td></tr>
<tr><td>
Postal Code</td><td><b><?php echo $reg_dat[$user_obj->tbFields2['postal']];?></b>
</td></tr>
<tr><td>
City</td><td><b><?php echo $reg_dat[$user_obj->tbFields2['city']];?></b>
</td></tr>
<tr><td>
Country</td><td><b><?php echo $user_obj->getCountryName($reg_dat[$user_obj->tbFields2['country']]);?></b>
</td></tr>
<tr><td colspan=2 style="text-align:right;">&nbsp;</td></tr>
</table><p>
<?php
      $i++;
      }
  }else{
?><table class="form_table">
<tr><td colspan=2 align="center"><b>NO ADDRESSES SPECIFIED</b></td></tr>
</table>
<?php
  }
?>
</td><td style="vertical-align:top;">
<table class="form_table">
<tr><td colspan=2 style="background-color:#888888;height:30px;text-align:left;color:#FFFFFF;"><input type="radio" name="address_id" value=-1>&nbsp;&nbsp;&nbsp;&nbsp;<b>Use New Address</b></td></tr>
<input type="hidden" name="customer_id" value="<?php echo $user_obj->userID;?>">
<input type="hidden" name="processed" value=3><input type="hidden" name="primary_flag" value=0>
<tr><td>
First Name</td><td><input type="text" name="first_name" value="<?php echo $user_obj->getName();?>">
</td></tr>
<tr><td>
Last Name</td><td><input type="text" name="last_name" value="<?php echo $user_obj->getLastName();?>">
</td></tr>
<tr><td>
Company</td><td><input type="text" name="company">
</td></tr>
<tr><td>
Address</td><td><input type="text" name="address">
</td></tr>
<tr><td>
Postal Code</td><td><input type="text" name="postal">
</td></tr>
<tr><td>
City</td><td><input type="text" name="city">
</td></tr>
<tr><td>
Country</td><td><select name="country">
<option value="">Please Select</option>
<?php
  $countries = $user_obj->getCountries();
  
  foreach($countries as $country_single){
    echo "<option value=".$country_single["ID"].">".$country_single["NAME"]."</option>";
  }
?>
</select>
</td></tr>
</table>
</td></tr></table>
<p>
<input type="submit" name="Submit" value="Proceed">
</form>
<p>&nbsp;<p>
<?php if($_SESSION["paymentType"] == "Rent"){
?>
<table class="plain_nospace">
<tr><td><img src="img/timeline_head_active.gif"></td><td><img src="img/timeline.gif"></td><td><img src="img/timeline_tail.gif"></td></tr>
<tr height="10px"><td colspan=3>&nbsp;</td></tr>
<tr><td id="active">Delivery<br>Information</td><td id="inactive">Confirmation</td><td id="inactive">Complete!</td></tr>
</table>
<?php }else{
?>
<table class="plain_nospace">
<tr><td><img src="img/timeline_head_active.gif"></td><td><img src="img/timeline.gif"></td><td><img src="img/timeline.gif"></td><td><img src="img/timeline_tail.gif"></td></tr>
<tr height="10px"><td colspan=4>&nbsp;</td></tr>
<tr><td id="active">Delivery<br>Information</td><td id="inactive">Payment<br>Information</td><td id="inactive">Confirmation</td><td id="inactive">Complete!</td></tr>
</table>
<?php } ?>
<p>&nbsp;<p>
</div>