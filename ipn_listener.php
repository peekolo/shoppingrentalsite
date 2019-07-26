<?php

require_once("php_shop/includes/configure.php");
require_once("php_shop/customer_class.php");
    
    $customer_obj = new customerAccess();

    // read the post from PayPal system and add 'cmd'
    $req = 'cmd=_notify-validate';

    if(function_exists('get_magic_quotes_gpc')) 
    { $get_magic_quotes_exists = true;} 
  
    foreach ($_POST as $key => $value) {
    if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
      $value = urlencode(stripslashes($value)); 
    } else { 
      $value = urlencode($value); 
    }      
    $req .= "&".$key."=".$value;
    }

    // post back to PayPal system to validate
    $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
    $fp = fsockopen ('ssl://www.paypal.com',443, $errno, $errstr, 30);

    // assign posted variables to local variables

    $item_name = $_POST['item_name'];
    $payment_status = $_POST['payment_status'];
    if(isset($_POST['mc_gross'])){
      $payment_amount = $_POST['mc_gross'];
    }else{
      $payment_amount = 0;
    }
    $txn_type = $_POST['txn_type'];
    $txn_id = $_POST['txn_id'];
    $receiver_email = $_POST['receiver_email'];
    $payer_email = $_POST['payer_email'];
    $payer_first_name = $_POST['first_name'];
    $payer_last_name = $_POST['last_name'];
    $cid = $_POST['custom'];

    if (!$fp) {
      echo "<h1>HTTP Error! Please contact administrator.</h1>";
      exit;
    } else {
      fputs ($fp, $header . $req);
      while (!feof($fp)) {
        $res = fgets ($fp, 1024);
        //echo $res;

        if (strcmp ($res, "VERIFIED") == 0) {
          // check the payment_status is Completed
          // check that txn_id has not been previously processed
          // check that receiver_email is your Primary PayPal email
          // check that payment_amount/payment_currency are correct
          // process payment
          $customer_obj->storeIPN($txn_id,$txn_type,$cid,$payer_first_name,$payer_last_name,$payer_email,$payment_amount,$item_name,$req,"VERIFIED");
        }
        else if (strcmp ($res, "INVALID") == 0) {
         // log for manual investigation
          $customer_obj->storeIPN($txn_id,$txn_type,$cid,$payer_first_name,$payer_last_name,$payer_email,$payment_amount,$item_name,$req,"FAILED");
     
        }
      }
      fclose ($fp);
    }
?>
