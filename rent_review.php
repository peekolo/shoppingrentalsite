<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 

  
  if(isset($_SESSION['Rental_Amount'])){
    $subtotal_price = $_SESSION['Rental_Amount'];
  }else{
    echo "Error - Illegal Access.";
    exit;
  }
?>
<div class="box_big" style="position:relative;">
&nbsp;<p>
<?php
$cart_data = $rentcart_obj->getCart();
  if(isset($_POST['delivery_type'])){
    $_SESSION["deliveryType"] = $_POST['delivery_type'];
    $ship_details = $rentcart_obj->getShippingDetails($_POST['delivery_type']);
  }else if(isset($_SESSION["deliveryType"])){
    $ship_details = $rentcart_obj->getShippingDetails($_SESSION["deliveryType"]);
  }else{
    echo "Illegal Access. Fatal Error. Please try again or contact administrator.";
    exit;
  }
  
  if(isset($_POST['comments'])){
    $_SESSION['Comments'] = $_POST['comments'];
  }

$user_RP = $user_obj->getRP();
?>
<p><h1>RENTAL ORDER REVIEW</h1>
<b>You have <font color=#FF0000><?php echo $user_RP;?></font> Rental Points (RP).</b><p>
<table class="carttable">
    <tr class="carthead"><td>Items to be bought</td><td>Price</td></tr>  
<?php  
  $subtotal_price = 0.00;
  $member_tmp = false;
  
  if($rentcart_obj->is_logged_in()){    
    if($cart_data != "" && sizeof($cart_data)>0){    
      //$member_tmp = $user_obj->getMemberType(); 
      foreach($cart_data AS $cart_single){
        $subtotal_price = $subtotal_price + $rentcart_obj->confirmView($cart_single[$rentcart_obj->tbFields["product_id"]], $cart_single[$rentcart_obj->tbFields["id"]]);
      }
    }else{
      echo "<tr><td colspan=2>You have no items to check out!</td></tr>";     
    }
  }else{        
    echo "Error. You need to be logged in.";
  }
  
      
  $_SESSION["paymentType"]="Rent";
  //$_SESSION["INVNUM"]=$invoiceNumber;
  
  if(isset($_SESSION["deliveryType"])){    
    $ship_details = $rentcart_obj->getShippingDetails($_SESSION["deliveryType"]);
  }else{
    echo "Illegal Access. Fatal Error. Please try again or contact administrator.";
    exit;
  }
  
  $grandtotal_price = $subtotal_price;

    
?>
<tr><td style="text-align:right;line-height:20px;padding:5px 25px 5px 25px;">
<b>Shipping by <?php echo $ship_details[$rentcart_obj->tbFields5['shipping_method']];?></b><br>
<b>Grand Total:</b>
</td><td style="text-align:left;line-height:20px;padding:5px 15px 5px 15px;">
&nbsp;<br>
<b><?php echo $grandtotal_price;?> RP</b>
</td></tr>
</table>
<p>&nbsp;<p>
<table class="plain">
<tr><td>
<?php

  $_SESSION['Rental_Amount']=$grandtotal_price;  

  if(isset($_SESSION["Address_ID"])){
    $tmp_add_id = $_SESSION["Address_ID"];
    $reg_dat = $user_obj->getAddress($tmp_add_id);
      
?>
<table class="form_table">
<tr><td colspan=2 style="background-color:#888888;height:30px;text-align:left;color:#FFFFFF;text-align:center;"><b>Shipping Address</b></td></tr>
<tr><td>
Name</td><td><b><?php echo $reg_dat[$user_obj->tbFields2['first_name']]." ".$reg_dat[$user_obj->tbFields2['last_name']];?></b>
</td></tr>
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
</table>
<?php
      
  }else{
?><table class="form_table">
<tr><td colspan=2 align="center"><b>ERROR! NO ADDRESSES SPECIFIED</b></td></tr>
</table>
<?php
  }
?>
</td>
<?php
  if(isset($_SESSION['Comments']) && trim($_SESSION['Comments']) != ""){
?>
<td width="20px">&nbsp;</td><td>
<b>Comments:</b><p>
<TEXTAREA name="comments" cols=40 rows=4 READONLY>
<?php
echo $_SESSION['Comments'];
?>
</TEXTAREA>
</td>
<?php
  }else{
    $_SESSION['Comments'] = "";
  }
?>
</tr></table>
<p>
<p>&nbsp;<p><form action="rent_confirmation.php" method="POST"><input type="hidden" name="confirm_purchases" value=1>
<input type="image" src="img/confirm.gif" alt="Confirm" name="confirm_purchase" value="confirm_purchase"></form>
<p>&nbsp;<p>
<table class="plain_nospace">
<tr><td><img src="img/timeline_head.gif"></td><td><img src="img/timeline_active.gif"></td><td><img src="img/timeline_tail.gif"></td></tr>
<tr height="10px"><td colspan=3>&nbsp;</td></tr>
<tr><td id="inactive"><a href='?p=rentcheckout'>Delivery<br>Information</a></td><td id="active">Confirmation</td><td id="inactive">Complete!</td></tr>
</table>
<p>&nbsp;<p>
</div>
