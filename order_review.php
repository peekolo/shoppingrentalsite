<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
/*==================================================================
 PayPal Express Checkout Call
 ===================================================================
*/
// Check to see if the Request object contains a variable named 'token'	
$token = "";
if (isset($_REQUEST['token']))
{
	$token = $_REQUEST['token'];
}

// If the Request object contains the variable 'token' then it means that the user is coming from PayPal site.	
if ( $token != "" )
{

	require_once ("php_shop/paypalfunctions.php");
  
	/*
	'------------------------------------
	' Calls the GetExpressCheckoutDetails API call
	'
	' The GetShippingDetails function is defined in PayPalFunctions.jsp
	' included at the top of this file.
	'-------------------------------------------------
	*/
	
  $_SESSION['token'] = $token;
	$resArray = GetShippingDetails( $token );
	$ack = strtoupper($resArray["ACK"]);
	if( $ack == "SUCCESS" )
	{
		/*
		' The information that is returned by the GetExpressCheckoutDetails call should be integrated by the partner into his Order Review 
		' page		
		*/
		$PaymentOption = "PayPal";
		$email 				= $resArray["EMAIL"]; // ' Email address of payer.
		$payerId 			= $resArray["PAYERID"]; // ' Unique PayPal customer account identification number.
		$payerStatus		= $resArray["PAYERSTATUS"]; // ' Status of payer. Character length and limitations: 10 single-byte alphabetic characters.
		$salutation			= $resArray["SALUTATION"]; // ' Payer's salutation.
		$firstName			= $resArray["FIRSTNAME"]; // ' Payer's first name.
		$middleName			= $resArray["MIDDLENAME"]; // ' Payer's middle name.
		$lastName			= $resArray["LASTNAME"]; // ' Payer's last name.
		$suffix				= $resArray["SUFFIX"]; // ' Payer's suffix.
		$cntryCode			= $resArray["COUNTRYCODE"]; // ' Payer's country of residence in the form of ISO standard 3166 two-character country codes.
		$business			= $resArray["BUSINESS"]; // ' Payer's business name.
		$shipToName			= $resArray["SHIPTONAME"]; // ' Person's name associated with this address.
		$shipToStreet		= $resArray["SHIPTOSTREET"]; // ' First street address.
		$shipToStreet2		= $resArray["SHIPTOSTREET2"]; // ' Second street address.
		$shipToCity			= $resArray["SHIPTOCITY"]; // ' Name of city.
		$shipToState		= $resArray["SHIPTOSTATE"]; // ' State or province
		$shipToCntryCode	= $resArray["SHIPTOCOUNTRYCODE"]; // ' Country code. 
		$shipToZip			= $resArray["SHIPTOZIP"]; // ' U.S. Zip code or other country-specific postal code.
		$addressStatus 		= $resArray["ADDRESSSTATUS"]; // ' Status of street address on file with PayPal   
		$invoiceNumber		= $resArray["INVNUM"]; // ' Your own invoice or tracking number, as set by you in the element of the same name in SetExpressCheckout request .
		$phonNumber			= $resArray["PHONENUM"]; // ' Payer's contact telephone number. Note:  PayPal returns a contact telephone number only if your Merchant account profile settings require that the buyer enter one. 
	} 
	else  
	{
		//Display a user friendly Error on the page using any of the following error information returned by PayPal
		$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
		$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
		$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
		$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
		
		echo "GetExpressCheckoutDetails API call failed. ";
		echo "Detailed Error Message: " . $ErrorLongMsg;
		echo "Short Error Message: " . $ErrorShortMsg;
		echo "Error Code: " . $ErrorCode;
		echo "Error Severity Code: " . $ErrorSeverityCode;
	}
}else if(isset($_GET["t"])){
  $PaymentOption = $_GET["t"];
  $_SESSION["PaymentOption"] = $PaymentOption;
  $invoiceNumber =$cart_obj->getInvoiceNum();
}else{
  echo "Unauthorized Access to the page.";
  exit;
}
?>
<div class="box_big" style="position:relative;">
&nbsp;<p>
<?php
$cart_data = $cart_obj->getCart();
?>
<p><h1>ORDER REVIEW</h1><p><b>INVOICE NUMBER: <?php echo $invoiceNumber;?></b><p>
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
  $_SESSION["INVNUM"]=$invoiceNumber;
  
  if(isset($_SESSION["deliveryType"])){    
    $ship_details = $cart_obj->getShippingDetails($_SESSION["deliveryType"]);
  }else{
    echo "Illegal Access. Fatal Error. Please try again or contact administrator.";
    exit;
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
<table class="plain">
<tr><td>
<?php
  $_SESSION['Member_Discount']=$member_discount;
  $_SESSION['Payment_Amount']=$grandtotal_price;  
  $_SESSION['Shipping_Amount']=$shipping_price;  
  $_SESSION['Item_Amount']=$subtotal_price;  

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
<b>Payment Type : <?php
  switch($PaymentOption){
    case "PayPal" :
      echo "PayPal";
      break;
    case "COD" : 
      default;
      echo "Cash on Delivery";
      break;
  }
?></b>
<p>&nbsp;<p><form action="confirmation.php" method="POST"><input type="hidden" name="confirm_purchases" value=1>
<input type="image" src="img/confirm.gif" alt="Confirm" name="confirm_purchase" value="confirm_purchase"></form>
<p>&nbsp;<p>
<table class="plain_nospace">
<tr><td><img src="img/timeline_head.gif"></td><td><img src="img/timeline.gif"></td><td><img src="img/timeline_active.gif"></td><td><img src="img/timeline_tail.gif"></td></tr>
<tr height="10px"><td colspan=4>&nbsp;</td></tr>
<tr><td id="inactive"><a href='?p=checkout1'>Delivery<br>Information</a></td><td id="inactive"><a href='?p=billing'>Payment<br>Information</a></td><td id="active">Confirmation</td><td id="inactive">Complete!</td></tr>
</table>
<p>&nbsp;<p>
</div>
