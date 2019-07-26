<?php
require_once ("php_shop/paypalfunctions.php");
require_once("php_shop/includes/configure.php");
require_once("php_shop/user_class.php");
require_once("php_shop/cart_class.php");

if(isset($_GET["t"])){
  $PaymentOption = $_GET["t"];
  $_SESSION["PaymentOption"] = $PaymentOption;
}
$user_obj = new userAccess();	

if ( $user_obj->is_logged_in() && $PaymentOption == "PayPal")
{
	// ==================================
	// PayPal Express Checkout Module
	// ==================================

	//'------------------------------------
	//' The paymentAmount is the total value of 
	//' the shopping cart, that was set 
	//' earlier in a session variable 
	//' by the shopping cart page
	//'------------------------------------
	
  $cart_obj = new cartAccess($user_obj->userID);
	$paymentAmount = $_SESSION["Payment_Amount"];	
	$shippingAmount = $_SESSION["Shipping_Amount"];	
	$itemAmount = $_SESSION["Item_Amount"];	
	$invoiceNumber =$cart_obj->getInvoiceNum(); // INVOICE NUMBER!!!!		
	$reg_dat = $user_obj->getAddress($_SESSION["Address_ID"]);


	//'------------------------------------
	//' When you integrate this code 
	//' set the variables below with 
	//' shipping address details 
	//' entered by the user on the 
	//' Shipping page.
	//'------------------------------------
	$shipToName = $reg_dat[$user_obj->tbFields2['first_name']]." ".$reg_dat[$user_obj->tbFields2['last_name']];
	$shipToStreet = $reg_dat[$user_obj->tbFields2['address']];
	$shipToStreet2 = ""; //Leave it blank if there is no value
	$shipToCity = $reg_dat[$user_obj->tbFields2['city']];
	$shipToState = $reg_dat[$user_obj->tbFields2['city']];
	$shipToCountryCode = $user_obj->getCountryCode($reg_dat[$user_obj->tbFields2['country']]); // Please refer to the PayPal country codes in the API documentation
	$shipToZip = $reg_dat[$user_obj->tbFields2['postal']];
	$phoneNum = "";

	//'------------------------------------
	//' The currencyCodeType and paymentType 
	//' are set to the selections made on the Integration Assistant 
	//'------------------------------------
	$currencyCodeType = "SGD";
	$paymentType = "Sale";

	//'------------------------------------
	//' The returnURL is the location where buyers return to when a
	//' payment has been succesfully authorized.
	//'
	//' This is set to the value entered on the Integration Assistant 
	//'------------------------------------
	$returnURL = PAYPAL_RETURN_URL;

	//'------------------------------------
	//' The cancelURL is the location buyers are sent to when they hit the
	//' cancel button during authorization of payment during the PayPal flow
	//'
	//' This is set to the value entered on the Integration Assistant 
	//'------------------------------------
	$cancelURL = PAYPAL_CANCEL_URL;

	//'------------------------------------
	//' Calls the SetExpressCheckout API call
	//'
	//' The CallMarkExpressCheckout function is defined in the file PayPalFunctions.php,
	//' it is included at the top of this file.
	//'-------------------------------------------------
		

		
	$resArray = CallMarkExpressCheckout ($paymentAmount, $itemAmount, $shippingAmount, $currencyCodeType, $paymentType, $returnURL,
										  $cancelURL, $shipToName, $shipToStreet, $shipToCity, $shipToState,
										  $shipToCountryCode, $shipToZip, $shipToStreet2, $phoneNum, $invoiceNumber
	);

	$ack = strtoupper($resArray["ACK"]);
	if($ack=="SUCCESS")
	{
		$token = urldecode($resArray["TOKEN"]);
		$_SESSION['reshash']=$token;
		RedirectToPayPal ( $token );
	} 
	else  
	{
		//Display a user friendly Error on the page using any of the following error information returned by PayPal
		$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
		$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
		$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
		$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
		
		echo "SetExpressCheckout API call failed. ";
		echo "Detailed Error Message: " . $ErrorLongMsg;
		echo "Short Error Message: " . $ErrorShortMsg;
		echo "Error Code: " . $ErrorCode;
		echo "Error Severity Code: " . $ErrorSeverityCode;
	}
}
else
{
	if ((( $PaymentOption == "Visa") || ( $PaymentOption == "MasterCard") || ($PaymentOption == "Amex") || ($PaymentOption == "Discover"))
			&& ( $PaymentProcessorSelected == "PayPal Direct Payment"))

	//'------------------------------------
	//' The paymentAmount is the total value of 
	//' the shopping cart, that was set 
	//' earlier in a session variable 
	//' by the shopping cart page
	//'------------------------------------
	$paymentAmount = $_SESSION["Payment_Amount"];

	//'------------------------------------
	//' The currencyCodeType and paymentType 
	//' are set to the selections made on the Integration Assistant 
	//'------------------------------------
	$currencyCodeType = "SGD";
	$paymentType = "Sale";
	
	//' Set these values based on what was selected by the user on the Billing page Html form
	
	$creditCardType 		= "<<Visa/MasterCard/Amex/Discover>>"; //' Set this to one of the acceptable values (Visa/MasterCard/Amex/Discover) match it to what was selected on your Billing page
	$creditCardNumber 		= "<<CC number>>"; //' Set this to the string entered as the credit card number on the Billing page
	$expDate 				= "<<Expiry Date>>"; //' Set this to the credit card expiry date entered on the Billing page
	$cvv2 					= "<<cvv2>>"; //' Set this to the CVV2 string entered on the Billing page 
	$firstName 				= "<<firstName>>"; //' Set this to the customer's first name that was entered on the Billing page 
	$lastName 				= "<<lastName>>"; //' Set this to the customer's last name that was entered on the Billing page 
	$street 				= "<<street>>"; //' Set this to the customer's street address that was entered on the Billing page 
	$city 					= "<<city>>"; //' Set this to the customer's city that was entered on the Billing page 
	$state 					= "<<state>>"; //' Set this to the customer's state that was entered on the Billing page 
	$zip 					= "<<zip>>"; //' Set this to the zip code of the customer's address that was entered on the Billing page 
	$countryCode 			= "<<PayPal Country Code>>"; //' Set this to the PayPal code for the Country of the customer's address that was entered on the Billing page 
	$currencyCode 			= "<<PayPal Currency Code>>"; //' Set this to the PayPal code for the Currency used by the customer 
	
	/*	
	'------------------------------------------------
	' Calls the DoDirectPayment API call
	'
	' The DirectPayment function is defined in PayPalFunctions.php included at the top of this file.
	'-------------------------------------------------
	*/
	
	$resArray = DirectPayment ( $paymentType, $paymentAmount, $creditCardType, $creditCardNumber,
							$expDate, $cvv2, $firstName, $lastName, $street, $city, $state, $zip, 
							$countryCode, $currencyCode ); 

	$ack = strtoupper($resArray["ACK"]);
	if($ack != "SUCCESS")
	{
		//Display a user friendly Error on the page using any of the following error information returned by PayPal
		$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
		$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
		$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
		$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
		
		echo "SetExpressCheckout API call failed. ";
		echo "Detailed Error Message: " . $ErrorLongMsg;
		echo "Short Error Message: " . $ErrorShortMsg;
		echo "Error Code: " . $ErrorCode;
		echo "Error Severity Code: " . $ErrorSeverityCode;
	}
}
?>