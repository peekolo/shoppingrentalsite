<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
?>
<div class="box_big" style="position:relative;">
&nbsp;<p>
<?php
$_SESSION["paymentType"]="Sale";
$cart_data = $cart_obj->getCart();
?>
<p><h1>Choose Delivery Method</h1><p>
<?php
  $tmp_add_id = "";
  $add_try = false;
  if(isset($_POST["address_id"])){
    if($_POST["address_id"] != -1){
      $tmp_add_id = $_POST["address_id"];
      $reg_dat = $user_obj->getAddress($tmp_add_id);
    }else if($_POST["address_id"] == -1){
      if(isset($_POST['processed'])){
        if($_POST['processed'] == 3){
          $reg_dat = array(
            "customer_id" => $_POST['customer_id'],
            "first_name" => $_POST['first_name'],
            "last_name" => $_POST['last_name'],
            "company" => $_POST['company'],
            "address" => $_POST['address'],
            "postal" => $_POST['postal'],
            "city" => $_POST['city'],
            "country" => $_POST['country'],
            "primary_flag" => $_POST['primary_flag']
          );
          
//ADD CHECK ADDY
          if(trim($_POST['postal'])=="" || trim($_POST['first_name'])=="" || trim($_POST['last_name'])=="" || trim($_POST['country'])==""|| trim($_POST['address'])==""){
            echo "Please fill in all the compulsory fields marked with astericks.";
            $add_try = true;
            $tmp_add_id = false;
          }else{          
            if($tmp_add_id = $user_obj->addAddress($reg_dat)){
              $reg_dat = $user_obj->getAddress($tmp_add_id);
            }else{
              echo "Address adding failed. Please try again or contact the administrator.";
              $add_try=true;
              $tmp_add_id = false;                       
            }
          }
        }
      }      
    }
    
  }else{
    $reg_dat = $user_obj->getAddress("",1);
    $tmp_add_id = $reg_dat[$user_obj->tbFields2['id']];  
  }
  
  if($tmp_add_id && $tmp_add_id != ""){
  
    $_SESSION['Address_ID'] = $tmp_add_id;    
      
?><table class="plain1" width="95%">
<tr><td style="vertical-align:top;text-align:left;width:40%;">
<a href='?p=delivery'>[Deliver to another Address]</a><br>
<table class="form_table">
<tr><td colspan=2 style="background-color:#888888;height:30px;text-align:left;color:#FFFFFF;text-align:center;"><b>Shipping Address</b></td></tr>
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
</table></td></tr></table><p>
<b>Choose Delivery Method:</b><br>
<form action="?p=billing" method="POST">
<input type="hidden" name="address_id" value=<?php echo $tmp_add_id;?>>
<table class="options_table">
<tr><th>&nbsp;</th><th>Delivery Method</th><th>Cost</th></tr>
<?php
  
  $subtotal_price = 0.00;
  $member_tmp = false;  
  if($cart_obj->is_logged_in()){    
    if($cart_data != "" && sizeof($cart_data)>0){    
      $member_tmp = $user_obj->getMemberType(); 
      ob_start(); //Turn on output buffering
      $cart_obj->initCartWeight();
      foreach($cart_data AS $cart_single){
        $subtotal_price = $subtotal_price + $cart_obj->confirmView($cart_single[$cart_obj->tbFields["product_id"]], $cart_single[$cart_obj->tbFields["id"]], $cart_single[$cart_obj->tbFields["quantity"]],$member_tmp[$user_obj->tbFields11['discount']]);
      }
      $cart_weight = $cart_obj->getCartWeight();
      $trash = ob_get_clean();
      $trash = "";
    }else{
      echo "<h1>You have no items to check out!</h1>";     
    }
  }else{        
    echo "<h1>Error. You need to be logged in.</h1>";
  }
  

$shipping_methods = $cart_obj->getShippingMethods($reg_dat[$user_obj->tbFields2['country']], $subtotal_price);
$first_time_tmp = 1;
foreach($shipping_methods AS $shipping_method){
  echo "<tr><td><input type='radio' name='delivery_type' value=".$shipping_method[$cart_obj->tbFields5['id']];
  if($first_time_tmp ==1){
    echo " checked";
    $first_time_tmp = 0;
  }
  echo "></td><td>".$shipping_method[$cart_obj->tbFields5['shipping_method']]."</td><td>$".number_format($cart_obj->getShippingCost($shipping_method[$cart_obj->tbFields5['cost']],$cart_weight), 2)."</td></tr>";
}
?>
</table>
<?php //echo "Weight: ".$cart_weight;?>
<p>
<b>Comments:</b><p>
<TEXTAREA name="comments" value="" cols=40 rows=4>
<?php
if(isset($_SESSION['Comments'])) echo $_SESSION['Comments'];
?>
</TEXTAREA>
<?php
      
  }else{ //change name join here if error
    if(!$add_try) echo "No Address Records Found.";
?><p>
<form action="?p=checkout1" method="POST">
<table class="form_table">
<tr><td colspan=2 style="background-color:#888888;height:30px;text-align:center;color:#FFFFFF;"><b>Specify Address</b></td></tr>
<input type="hidden" name="address_id" value=-1>
<input type="hidden" name="customer_id" value="<?php echo $user_obj->userID;?>">
<input type="hidden" name="processed" value=3><input type="hidden" name="primary_flag" value=0>
<tr><td>
First Name*</td><td><input type="text" name="first_name" value="<?php if($add_try) echo $_POST['first_name']; else echo $user_obj->getName();?>">
</td></tr>
<tr><td>
Last Name*</td><td><input type="text" name="last_name" value="<?php if($add_try) echo $_POST['last_name']; else echo $user_obj->getLastName();?>">
</td></tr>
<tr><td>
Company</td><td><input type="text" name="company" value="<?php if($add_try) echo $_POST['company'];?>">
</td></tr>
<tr><td>
Address*</td><td><input type="text" name="address" value="<?php if($add_try) echo $_POST['address'];?>">
</td></tr>
<tr><td>
Postal Code*</td><td><input type="text" name="postal" value="<?php if($add_try) echo $_POST['postal'];?>">
</td></tr>
<tr><td>
City</td><td><input type="text" name="city" value="<?php if($add_try) echo $_POST['city'];?>">
</td></tr>
<tr><td>
Country*</td><td><select name="country">
<option value="">Please Select</option>
<?php
  $countries = $user_obj->getCountries();
  
  foreach($countries as $country_single){
    echo "<option value=".$country_single["ID"]." ";
    if($add_try && $_POST['country'] == $country_single["ID"]){
      echo "SELECTED";
    }
    echo ">".$country_single["NAME"]."</option>";
  }
?>
</select>
</td></tr>
</table>
<?php
  }
?>
<p>&nbsp;<p>
<input type="submit" name="submit" value="Proceed">
</form><p>&nbsp;<p>
<table class="plain_nospace">
<tr><td><img src="img/timeline_head_active.gif"></td><td><img src="img/timeline.gif"></td><td><img src="img/timeline.gif"></td><td><img src="img/timeline_tail.gif"></td></tr>
<tr height="10px"><td colspan=4>&nbsp;</td></tr>
<tr><td id="active">Delivery<br>Information</td><td id="inactive">Payment<br>Information</td><td id="inactive">Confirmation</td><td id="inactive">Complete!</td></tr>
</table>
<p>&nbsp;<p>
</div>