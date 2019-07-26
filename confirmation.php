<?php
	/*==================================================================
	 PayPal Express Checkout Call
	 ===================================================================
	*/
require_once ("php_shop/paypalfunctions.php");
require_once("php_shop/includes/configure.php");
require_once("php_shop/user_class.php");
require_once("php_shop/cart_class.php");

$user_obj = new userAccess();	

if($user_obj->is_logged_in()  && isset($_SESSION["PaymentOption"]) && isset($_POST["confirm_purchases"])){
  $cart_obj = new cartAccess($user_obj->userID);
  $PaymentOption = $_SESSION["PaymentOption"];
  $finalPaymentAmount =  $_SESSION["Payment_Amount"];
  
  if($finalPaymentAmount == 0){
    echo "<h1>Unknown Error! Payment Amount is 0.</h1>";
    exit;  
  }
  
}else{
  echo "<h1>Unauthorized Access!<br>Please do not Refresh Page or use the Forward and Back buttons during a Transaction!</h1>";
  exit;
}

if ( $PaymentOption == "PayPal" )
{
	
	/*
	'------------------------------------
	' Calls the DoExpressCheckoutPayment API call
	'
	' The ConfirmPayment function is defined in the file PayPalFunctions.jsp,
	' that is included at the top of this file.
	'-------------------------------------------------
	*/

	//'------------------------------------
	//' The currencyCodeType and paymentType 
	//' are set to the selections made on the Integration Assistant 
	//'------------------------------------
	$currencyCodeType = "SGD";
	$paymentType = "Sale";	

	$resArray = ConfirmPayment ( $finalPaymentAmount );
									  
	$ack = strtoupper($resArray["ACK"]);
	if( $ack == "SUCCESS" ){
    $token = urldecode($resArray["TOKEN"]);
    
    if ( $token != "" ){
        $resArray1 = GetShippingDetails( $token );

		$invoiceNumber		= $resArray1["INVNUM"]; // ' Your own invoice or tracking number, as set by you in the element of the same name in SetExpressCheckout request .
                  
    }    	
	
		/*
		'********************************************************************************************************************
		'
		' THE PARTNER SHOULD SAVE THE KEY TRANSACTION RELATED INFORMATION LIKE 
		'                    transactionId & orderTime 
		'  IN THEIR OWN  DATABASE
		' AND THE REST OF THE INFORMATION CAN BE USED TO UNDERSTAND THE STATUS OF THE PAYMENT 
		'
		'********************************************************************************************************************
		*/		
		

		$transactionId		= $resArray["TRANSACTIONID"]; // ' Unique transaction ID of the payment. Note:  If the PaymentAction of the request was Authorization or Order, this value is your AuthorizationID for use with the Authorization & Capture APIs. 
		$transactionType 	= $resArray["TRANSACTIONTYPE"]; //' The type of transaction Possible values: l  cart l  express-checkout 
		$paymentType		= $resArray["PAYMENTTYPE"];  //' Indicates whether the payment is instant or delayed. Possible values: l  none l  echeck l  instant 
		$orderTime 			= $resArray["ORDERTIME"];  //' Time/date stamp of payment
		$amt				= $resArray["AMT"];  //' The final amount charged, including any shipping and taxes from your Merchant Profile.
		$currencyCode		= $resArray["CURRENCYCODE"];  //' A three-character currency code for one of the currencies listed in PayPay-Supported Transactional Currencies. Default: USD. 
		$feeAmt				= $resArray["FEEAMT"];  //' PayPal fee amount charged for the transaction
		$settleAmt			= $resArray["SETTLEAMT"];  //' Amount deposited in your PayPal account after a currency conversion.
		$taxAmt				= $resArray["TAXAMT"];  //' Tax charged on the transaction.
		$exchangeRate		= $resArray["EXCHANGERATE"];  //' Exchange rate if a currency conversion occurred. Relevant only if your are billing in their non-primary currency. If the customer chooses to pay with a currency other than the non-primary currency, the conversion occurs in the customer’s account.
		
		/*
		' Status of the payment: 
				'Completed: The payment has been completed, and the funds have been added successfully to your account balance.
				'Pending: The payment is pending. See the PendingReason element for more information. 
		*/		
		$paymentStatus	= $resArray["PAYMENTSTATUS"]; 
		
		//echo $paymentStatus;
		
		if($paymentStatus == "Completed"){
      $payment_status = 1;
    }else if($paymentStatus == "Pending"){
      $payment_status = 2;
    }else{
      $payment_status = -1;
    }

		/*
		'The reason the payment is pending:
		'  none: No pending reason 
		'  address: The payment is pending because your customer did not include a confirmed shipping address and your Payment Receiving Preferences is set such that you want to manually accept or deny each of these payments. To change your preference, go to the Preferences section of your Profile. 
		'  echeck: The payment is pending because it was made by an eCheck that has not yet cleared. 
		'  intl: The payment is pending because you hold a non-U.S. account and do not have a withdrawal mechanism. You must manually accept or deny this payment from your Account Overview. 		
		'  multi-currency: You do not have a balance in the currency sent, and you do not have your Payment Receiving Preferences set to automatically convert and accept this payment. You must manually accept or deny this payment. 
		'  verify: The payment is pending because you are not yet verified. You must verify your account before you can accept this payment. 
		'  other: The payment is pending for a reason other than those listed above. For more information, contact PayPal customer service. 
		*/
		
		$pendingReason	= $resArray["PENDINGREASON"];  

		/*
		'The reason for a reversal if TransactionType is reversal:
		'  none: No reason code 
		'  chargeback: A reversal has occurred on this transaction due to a chargeback by your customer. 
		'  guarantee: A reversal has occurred on this transaction due to your customer triggering a money-back guarantee. 
		'  buyer-complaint: A reversal has occurred on this transaction due to a complaint about the transaction from your customer. 
		'  refund: A reversal has occurred on this transaction because you have given the customer a refund. 
		'  other: A reversal has occurred on this transaction due to a reason not listed above. 
		*/
		
		$reasonCode		= $resArray["REASONCODE"];   
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
		echo "<h1>TRANSACTION ERROR! PLEASE CONTACT THE ADMINISTRATOR!</h1>";
		exit;
	}
}else{
  
  $invoiceNumber = $_SESSION["INVNUM"];
  $payment_status = 0;

}		


$reg_dat = $user_obj->getAddress($_SESSION["Address_ID"]);

if($cart_obj->placeOrder($invoiceNumber, $reg_dat[$user_obj->tbFields2['first_name']], $reg_dat[$user_obj->tbFields2['last_name']],$user_obj->userData[$user_obj->tbFields1['telephone']] ,$reg_dat[$user_obj->tbFields2['company']], $reg_dat[$user_obj->tbFields2['address']], $reg_dat[$user_obj->tbFields2['postal']], $reg_dat[$user_obj->tbFields2['city']], $reg_dat[$user_obj->tbFields2['country']], $_SESSION["Payment_Amount"], $_SESSION["Shipping_Amount"], $_SESSION["Member_Discount"],$_SESSION["PaymentOption"], $payment_status, $_SESSION["deliveryType"], 2, $_SESSION["Comments"])){  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="keywords" content="Board Game, Card Game, Singapore">
<meta name="Author" content="peekolo">
<title>Board Game Lifestyle</title>
<style type="text/css">
<!--body {	margin-left: 0px;	margin-top: 0px;	margin-right: 0px;	margin-bottom: 0px;}--></style>
<link href="css.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="box_big" style="position:relative;">
<?php
  echo "<h1>Transaction completed.</h1><p>";
  
  if($user_obj->mailOrder($invoiceNumber)){
    echo "<b>A copy of the invoice has been sent to your email.</b>";
  }else{      
    echo "<b>Automatic sending of the invoice to your email has failed. <br>Please contact administrator to receive a copy of your invoice.</b>";
  }
  
  $user_obj->printOrder($invoiceNumber);  
?>
<p>&nbsp;<p>
<a href="index.php"><input type="submit" name="submit" value="Back to Board Game Lifestyle"></a>
<p>&nbsp;<p>
<table class="plain_nospace">
<tr><td><img src="img/timeline_head.gif"></td><td><img src="img/timeline.gif"></td><td><img src="img/timeline.gif"></td><td><img src="img/timeline_tail_active.gif"></td></tr>
<tr height="10px"><td colspan=4>&nbsp;</td></tr>
<tr><td id="inactive">Delivery<br>Information</td><td id="inactive">Payment<br>Information</td><td id="inactive">Confirmation</td><td id="active">Complete!</td></tr>
</table>
<p>&nbsp;<p>
</div>
<?php
}else{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN""http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="keywords" content="Board Game, Card Game, Singapore">
<meta name="Author" content="peekolo">
<title>Title</title>
<style type="text/css">
<!--body {	margin-left: 0px;	margin-top: 0px;	margin-right: 0px;	margin-bottom: 0px;}--></style>
<link href="css.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="box_big" style="position:relative;">
<?php
  echo "<h1>Error. Please contact administrator</h1>";
}
?>
</body></html>