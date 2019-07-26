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
$wish_data = $cart_obj->getWish();
?>
<h1>Shopping Cart</h1>
<table class="wishtable">
    <tr class="wishhead"><td colspan=2>Save for Later</td><td>Price</td><td>Qty</td></tr>  
<?php
  if($cart_obj->is_logged_in()){
    if($wish_data != "" && sizeof($wish_data)>0){    
      foreach($wish_data AS $wish_single){
        $cart_obj->wishView($wish_single[$cart_obj->tbFields["product_id"]], $wish_single[$cart_obj->tbFields["id"]], $wish_single[$cart_obj->tbFields["quantity"]]);
      }
    }else{
      echo "<tr><td colspan=4>You have no items in your wish</td></tr>";     
    }
  }

?>
</table>
<p>
<table class="carttable">
<tr><td colspan=4 style="vertical-align:bottom;"><small><b>*Please update cart entry with QTY 0 to <u>delete</u> an item.</b></small></td></tr>
    <tr class="carthead"><td colspan=2>Shopping Cart Items</td><td>Price</td><td>Qty</td></tr>  
<?php  
  $total_price = 0.00;
  $member_tmp = false;
   
  if($cart_obj->is_logged_in()){    
    if($cart_data != "" && sizeof($cart_data)>0){    
      $member_tmp = $user_obj->getMemberType(); 
      foreach($cart_data AS $cart_single){
        $tmp_cart_id = $cart_single[$cart_obj->tbFields["id"]];
        $total_price = $total_price + $cart_obj->cartView($cart_single[$cart_obj->tbFields["product_id"]], $cart_single[$cart_obj->tbFields["id"]], $cart_single[$cart_obj->tbFields["quantity"]],$member_tmp[$user_obj->tbFields11['discount']]);
      }
    }else{
      echo "<tr><td colspan=4>You have no items in your cart</td></tr>";     
    }
  }else{        
    if($cart_data != "" && sizeof($cart_data)>0){
      $cart_data_size = sizeof($cart_data);
      for($i=0; $i<$cart_data_size;$i=$i+3){
        $total_price = $total_price + $cart_obj->cartView($cart_data[$i],$i,$cart_data[$i+1]);
      }    
    }else{
      echo "<tr><td colspan=4>You have no items in your cart</td></tr>";     
    }      
  }
    

//check for member discount  
  if($member_tmp){    
?>
<tr><td colspan=2 style="text-align:right;line-height:20px;">
<font color="#5555FF"><i><small>(Only Applicable to Non-Special/Discounted Items)</small></i></font>&nbsp;<b><?php echo $member_tmp[$user_obj->tbFields11['type']] ?> Member Discount:</b><br>
<b>Subtotal:</b>
</td><td style="text-align:left;line-height:20px;">
<font color="#5555FF"><?php echo number_format($member_tmp[$user_obj->tbFields11['discount']],2);?>%</font><br>
$<?php echo number_format($total_price,2);?>
</td><td>&nbsp;</td></tr>
<?php
  }else{
?>  
<tr><td colspan=2 style="text-align:right;"><b>Subtotal:</b>&nbsp;</td><td>$<?php echo number_format($total_price,2);?></td><td>&nbsp;</td></tr>
<?php  
  }
          
  $_SESSION['Payment_Amount']=$total_price;    
  $_SESSION["paymentType"]="Sale";

?>
</table>
<p>
<a href="?p=checkout1"><img src="img/buybutton_checkout.gif"></a><br>
<p>&nbsp;<p>
</div>