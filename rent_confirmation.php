<?php
session_start();
require_once("php_shop/includes/configure.php");
require_once("php_shop/user_class.php");
require_once("php_shop/rentcart_class.php");

$user_obj = new userAccess();	

if($user_obj->is_logged_in()  && isset($_SESSION["Rental_Amount"]) && isset($_POST["confirm_purchases"])){
  $cart_obj = new rentcartAccess($user_obj->userID);
  $finalPaymentAmount =  $_SESSION["Rental_Amount"];
  
  if($finalPaymentAmount == 0){
    echo "<h1>Unknown Error! Payment Amount is 0.</h1>";
    exit;  
  }
  
}else{
  echo "<h1>Unauthorized Access!<br>Please do not Refresh Page or use the Forward and Back buttons during a Transaction!</h1>";
  exit;
}

$reg_dat = $user_obj->getAddress($_SESSION["Address_ID"]);
//deduct RP
  if($user_obj->minusRP($_SESSION["Rental_Amount"]) == -1){
    echo "<h1>Fatal Error. Please contact administrator.</h1>";
    exit;
  }
//place order  
$invoiceNumber = $cart_obj->getInvoiceNum();
if($cart_obj->placeOrder($invoiceNumber, $reg_dat[$user_obj->tbFields2['first_name']], $reg_dat[$user_obj->tbFields2['last_name']],$user_obj->userData[$user_obj->tbFields1['telephone']] ,$reg_dat[$user_obj->tbFields2['company']], $reg_dat[$user_obj->tbFields2['address']], $reg_dat[$user_obj->tbFields2['postal']], $reg_dat[$user_obj->tbFields2['city']], $reg_dat[$user_obj->tbFields2['country']], $_SESSION["Rental_Amount"], $_SESSION["deliveryType"], 0, $_SESSION["Comments"])){  

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
  
  if($user_obj->mailRental($invoiceNumber)){
    echo "<b>A copy of the Rental Order has been sent to your email.</b>";
  }else{      
    echo "<b>Automatic sending of the Rental Order to your email has failed. <br>Please contact administrator to receive a copy of your Rental Order.</b>";
  }
  
  $user_obj->printRental($invoiceNumber);
?>
<p>&nbsp;<p>
<a href="index.php"><input type="submit" name="submit" value="Back to Board Game Lifestyle"></a>
<p>&nbsp;<p>
<table class="plain_nospace">
<tr><td><img src="img/timeline_head.gif"></td><td><img src="img/timeline.gif"></td><td><img src="img/timeline_tail_active.gif"></td></tr>
<tr height="10px"><td colspan=4>&nbsp;</td></tr>
<tr><td id="inactive">Delivery<br>Information</td><td id="inactive">Confirmation</td><td id="active">Complete!</td></tr>
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