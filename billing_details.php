<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
?>
<div class="box_big" style="position:relative;">
&nbsp;<p>
<?php

$cart_data = $cart_obj->getCart();
?>
<p><h1>Choose Payment Method</h1><p>
<table class="carttable">
<tr class="carthead"><td>Items to be bought</td><td>Price</td><td>Qty</td></tr>  
<?php  
  $subtotal_price = 0.00;
  $member_tmp = false;
  
  if($cart_obj->is_logged_in()){    
    if($cart_data != "" && sizeof($cart_data)>0){    
      $member_tmp = $user_obj->getMemberType(); 
      foreach($cart_data AS $cart_single){
        $subtotal_price = $subtotal_price + $cart_obj->confirmView($cart_single[$cart_obj->tbFields["product_id"]], $cart_single[$cart_obj->tbFields["id"]], $cart_single[$cart_obj->tbFields["quantity"]],$member_tmp[$user_obj->tbFields11['discount']]);
      }
    }else{
      echo "<tr><td colspan=3>You have no items to check out!</td></tr>";     
    }
  }else{        
    echo "Error. You need to be logged in.";
  }
  
    
  $_SESSION["paymentType"]="Sale";
  
  if(isset($_POST['delivery_type'])){
    $_SESSION["deliveryType"] = $_POST['delivery_type'];
    $ship_details = $cart_obj->getShippingDetails($_POST['delivery_type']);
  }else if(isset($_SESSION["deliveryType"])){
    $ship_details = $cart_obj->getShippingDetails($_SESSION["deliveryType"]);
  }else{
    echo "Illegal Access. Fatal Error. Please try again or contact administrator.";
    exit;
  }
  
  if(isset($_POST['comments'])){
    $_SESSION['Comments'] = $_POST['comments'];
  }
  
  $shipping_price = $ship_details[$cart_obj->tbFields5['cost']];  
  $total_price = $shipping_price + $subtotal_price;
  $grandtotal_price = $total_price;
  $member_discount=0;

?>
<tr><td style="text-align:right;line-height:20px;padding:5px 25px 5px 25px;">
<?php
  if($member_tmp){
    $member_discount = $member_tmp[$user_obj->tbFields11['discount']];
?>
<font color="#5555FF"><i><small>(Only Applicable to Non-Special/Discounted Items)</small></i></font>&nbsp;<b><?php echo $member_tmp[$user_obj->tbFields11['type']] ?> Member Discount:</b><br>
<?php      
  }
?>
<b>Subtotal:</b><br>
<b>Shipping by <?php echo $ship_details[$cart_obj->tbFields5['shipping_method']];?></b><br>
<b>Grand Total:</b>
</td><td style="text-align:left;line-height:20px;padding:5px 15px 5px 15px;">
<?php
  if($member_flag){
    echo "<font color='#5555FF'>".$member_discount."%</font><br>";
  }
?>
$<?php echo number_format($subtotal_price,2);?><br>
$<?php echo number_format($shipping_price,2);?><br>
<b>$<?php echo number_format($grandtotal_price,2);?></b>
</td>
<td>&nbsp;</td></tr>
</table>
<p>&nbsp;<p>
<?php
  $_SESSION['Member_Discount']=$member_discount;
  $_SESSION['Payment_Amount']=$grandtotal_price;  
  $_SESSION['Shipping_Amount']=$shipping_price;  
  $_SESSION['Item_Amount']=$subtotal_price;  
  
  
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
            if($tmp_add_id = $user_obj->addAddress($reg_dat)){
              $reg_dat = $user_obj->getAddress($tmp_add_id);
            }else{
              echo "Address adding failed. Please try again or contact the administrator.";
              exit;
            }
          }
        }  
      }    
      $_SESSION['Address_ID'] = $tmp_add_id; 
   }else if(isset($_SESSION['Address_ID'])){
      $tmp_add_id = $_SESSION["Address_ID"];
      $reg_dat = $user_obj->getAddress($tmp_add_id);
   }else{
      echo "Illegal Access. Fatal Error. Please try again or contact administrator.";
      exit;   
   }
      
      
?><table class="plain1" width="95%">
<tr><td style="vertical-align:top;text-align:left;width:40%;"><table class="form_table">
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
</table><p>

</td><td style="vertical-align:top;text-align:left;">

<p>
<table class="options_table">
<tr><th colspan=2>Choose a Payment Method:</th></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>Cash on Delivery</td><td><a href="?p=order_review&t=COD"><img src="img/select.gif"></a></td></tr>
<tr><td>Paypal</td><td><a href="billing.php?t=PayPal"><img src="img/select.gif"></a></td></tr>
</table>
</td></tr></table>
<p>&nbsp;<p>
<table class="plain_nospace">
<tr><td><img src="img/timeline_head.gif"></td><td><img src="img/timeline_active.gif"></td><td><img src="img/timeline.gif"></td><td><img src="img/timeline_tail.gif"></td></tr>
<tr height="10px"><td colspan=4>&nbsp;</td></tr>
<tr><td id="inactive"><a href='?p=checkout1'>Delivery<br>Information</a></td><td id="active">Payment<br>Information</td><td id="inactive">Confirmation</td><td id="inactive">Complete!</td></tr>
</table>
<p>&nbsp;<p>
</div>