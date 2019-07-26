<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
?>
<div class="box_big" style="position:relative;">
&nbsp;<p>
<?php
if(isset($_POST["submit_retreq"])){
  for($i=0; $i<$_POST["maxreq"]; $i++){
    if(isset($_POST["reqret_".$i])){
      if($_POST["reqret_".$i] != ""){
        if($tmpRP = $rentcart_obj->requestReturn($_POST["reqret_".$i])){
          $user_obj->addRP($tmpRP);
        }else{
          echo "Error! Request Return Fail!";
        }
      }
    }  
  }
}

$cart_data = $rentcart_obj->getCart();
$wish_data = $rentcart_obj->getWish();
$rent_data = $user_obj->getRental();
$user_RP = $user_obj->getRP();

?>
<h1>Rental Cart</h1>
<b>You have <font color=#FF0000><?php echo $user_RP;?></font> Rental Points (RP).</b><p>
<form onSubmit="return confirmreturn();" action="" method="POST">
<table class="renttable">
    <tr class="renthead"><td>Rental ID</td><td>Pending Rentals</td><td>RP</td><td>Loan Date</td><td>Loan Status</td></tr>  
<?php
  if($rentcart_obj->is_logged_in()){
    if($rent_data != "" && sizeof($rent_data)>0){    
      $possibility_flag = 0;
      foreach($rent_data AS $rent_single){
?>
     <tr><td><?php echo $rent_single[$rentcart_obj->tbFields3["invoice_number"]];?></td><td><?php echo "<a href='?p=search&t=detailed&p_id=".$rent_single[$rentcart_obj->tbFields3["product_id"]]."'>".$rent_single[$rentcart_obj->tbFields3["product_title"]]."</a>";?></td><td><?php echo $rent_single[$rentcart_obj->tbFields3["rental_points"]];?> RP</td><td><?php echo $rent_single[$rentcart_obj->tbFields3["date_created"]];?></td>
     <td><?php 
     if($rent_single[$rentcart_obj->tbFields3["delivery_status"]] == 3){
?>
      <select id="idreq_<?php echo $possibility_flag;?>" name="reqret_<?php echo $possibility_flag;?>">
      <option value="" selected>Rented</option>
      <option value="<?php echo $rent_single[$rentcart_obj->tbFields3["invoice_number"]];?>">Request Return</option>
      </select>
<?php        
        $possibility_flag++;
     }else{
        echo $rentcart_obj->getStatusString($rent_single[$rentcart_obj->tbFields3["delivery_status"]],"rental");
     }
     ?></td></tr>
        
<?php        //$rentcart_obj->rentView($rent_single[$rentcart_obj->tbFields["product_id"]], $rent_single[$rentcart_obj->tbFields["id"]]);
      }
    }else{
      echo "<tr><td colspan=3>You have no pending rentals</td></tr>";     
    }
  }

?>
</table>
<?php
if($possibility_flag != 0){
  echo "<input type='hidden' name='maxreq' value=".$possibility_flag.">";
  echo "<input type='submit' name='submit_retreq' value='Submit Return Requests'>";
}?>
</form>

<script>
function confirmreturn(){
  var max = <?php echo $possibility_flag;?>;
  var num = 0;
  var obj;
  var str = "idreq_";
  for(var i=0;i<max;i++){
    obj = getE(str+""+i);
    if(obj.selectedIndex == 1){
      num++;
    }
  }
  if(num > 0){
    return confirm("Confirm to submit return request for "+num+" items?");
  }else{
    alert("No items are selected to be returned.");
    return false;
  }
}
</script>
<p>&nbsp;<p>
<table class="wishtable">
    <tr class="wishhead"><td colspan=2>Save for Later</td><td>RP</td></tr>  
<?php
  if($rentcart_obj->is_logged_in()){
    if($wish_data != "" && sizeof($wish_data)>0){    
      foreach($wish_data AS $wish_single){
        $rentcart_obj->wishView($wish_single[$rentcart_obj->tbFields["product_id"]], $wish_single[$rentcart_obj->tbFields["id"]]);
      }
    }else{
      echo "<tr><td colspan=3>You have no items in your wish</td></tr>";     
    }
  }

?>
</table>
<p>&nbsp;<p>
<table class="carttable">
    <tr class="carthead"><td colspan=2>Rental Cart Items</td><td>RP</td></tr>  
<?php  
  $total_price = 0;
  //$member_tmp = false;
   
  if($rentcart_obj->is_logged_in()){    
    if($cart_data != "" && sizeof($cart_data)>0){
      $rentcart_obj->startcheckAvail();    
      //$member_tmp = $user_obj->getMemberType(); 
      foreach($cart_data AS $cart_single){
        $tmp_cart_id = $cart_single[$rentcart_obj->tbFields["id"]];
        $total_price = $total_price + $rentcart_obj->cartView($cart_single[$rentcart_obj->tbFields["product_id"]], $cart_single[$rentcart_obj->tbFields["id"]]);
      }
    }else{
      echo "<tr><td colspan=3>You have no items in your cart</td></tr>";     
    }
  }else{        
    if($cart_data != "" && sizeof($cart_data)>0){
      $rentcart_obj->startcheckAvail();
      $cart_data_size = sizeof($cart_data);
      for($i=0; $i<$cart_data_size;$i++){
        $total_price = $total_price + $rentcart_obj->cartView($cart_data[$i],$i);
      }    
    }else{
      echo "<tr><td colspan=3>You have no items in your cart</td></tr>";     
    }      
  }
?>    


<tr><td colspan=2 style="text-align:right;"><b>Total:</b>&nbsp;</td><td><?php echo $total_price;?> RP</td></tr>
<?php  

          
  $_SESSION['Rental_Amount']=$total_price;    
  $_SESSION["paymentType"]="Rent";

?>
</table>
<p>
<?php if($rentcart_obj->isAvail()){ 
  if($user_RP >= $total_price){
?><a href="?p=rentcheckout"><?php
  }else{
?><a href="javascript:alert('You cannot check out these items.\n\nYou do not have enough Rental Points (RP).');"><?php
  }
}else{ 
?><a href="javascript:alert('You cannot check out these items.\n\nSome of the items are out on loan.\nPlease choose other games or save for later.');"><?php 
}?><img src="img/buybutton_checkout.gif"></a><br>
<p>&nbsp;<p>
</div>