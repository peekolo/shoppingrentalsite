<?php
/**
* PHP class - customerAccess
*
* For Customer Operations by Admin Accounts
*
*
* Copyright: 30th July 2009, Pieceofcake.com.sg
*
*
**/

class customerAccess{
  
  var $dbName = DB_DATABASE;   // name of database
  
  var $userTable = "customer";  // name of table containing personal info
  var $tbFields1 = array(           // fields of $userTable
    'id' => 'ID',
    'first_name' => 'FIRST_NAME',
    'last_name' => 'LAST_NAME',
    'gender' => 'GENDER',
    'dob'=>'DOB',
    'telephone'=>'TELEPHONE',
    'fax'=>'FAX',
    'email' => 'EMAIL',
    'newsletter_flag'=>'NEWSLETTER_FLAG',
  	'password'  => 'PASSWORD',
  	'date_created' => 'DATE_CREATED',
  	'last_pw_change' => 'LAST_PW_CHANGE',    
  	'discount_type' => 'DISCOUNT_TYPE',
  	'rental_points'=>'RENTAL_POINTS'
  );
  
  var $memberTable = "customer_member";  // name of table containing member infor
  var $tbFields11 = array(
    'id' => 'ID',
    'type' => 'TYPE',
    'discount' => 'DISCOUNT',
    'exclusive_discount'=> 'EXCLUSIVE_DISCOUNT'
  );
    
  var $userAddressTable = "customer_address"; // name of table containing addresses
  var $tbFields2 = array(                     // fields of $userAddressTable
    'id' => 'ID',
    'customer_id' => 'CUSTOMER_ID',
  	'first_name' => 'FIRST_NAME',
    'last_name' => 'LAST_NAME',   
    'company' => 'COMPANY',     
    'address' => 'ADDRESS',
    'postal' => 'POSTAL_CODE',
    'city' => 'CITY',
    'country' => 'COUNTRY_ID',
    'primary_flag' => 'PRIMARY_FLAG'
  );
  
  var $countriesTable = "countries"; // name of table containing zones
  var $tbFields3 = array(                     // fields of $zoneTable
    'id' => 'ID',
    'name' => 'NAME',
    'iso_code_2' => 'ISO_CODE_2',
    'iso_code_3' => 'ISO_CODE_3',
    'address_format_id' => 'ADDRESS_FORMAT_ID'
  );  
  
  var $admincontentTable = "admin_content";
  var $admincontentFields = array(
    "id"=>"ID",
    "title"=>"TITLE",
    "content"=>"CONTENT",
    "type"=>"TYPE",
    "date_added"=>"DATE_ADDED"
  );
 
  var $ipnTable = "ipn_notifications";
  var $ipnFields = array(
    "id"=>"ID",
    "transaction_id"=>"TRANSACTION_ID",
    "transaction_type"=>"TRANSACTION_TYPE",
    "customer_id"=>"CUSTOMER_ID",
    "date_created"=>"DATE_CREATED",
    "first_name"=>"FIRST_NAME",
    "last_name"=>"LAST_NAME",
    "email"=>"EMAIL",
    "payer_first_name"=>"PAYER_FIRST_NAME",
    "payer_last_name"=>"PAYER_LAST_NAME",
    "payer_email"=>"PAYER_EMAIL",
    "payment_amount"=>"PAYMENT_AMOUNT",
    "product"=>"PRODUCT",
    "status"=>"STATUS",
    "notification"=>"NOTIFICATION"
  );
  
  var $rentalplanTable = "customer_rentalplan";
  var $rplanFields = array(
    "id"=>"ID",
    "customer_id"=>"CUSTOMER_ID",
    "date_paid"=>"DATE_PAID",
    "plan_type"=>"PLAN_TYPE",
    "rental_points"=>"RENTAL_POINTS"
  );

  var $olddiscountTable = "old_discount_type";
  var $olddiscountFields = array(
    "id"=>"ID",
    "customer_id"=>"CUSTOMER_ID",
    "discount_type"=>"DISCOUNT_TYPE",
    "discount_name"=>"DISCOUNT_NAME",
    "discount"=>"DISCOUNT",
    "exclusive_discount"=>"EXCLUSIVE_DISCOUNT"
  );

  var $rentedTable = "rented";
  var $rentedFields = array(
    'id'=>'ID',
    'customer_id'=>'CUSTOMER_ID',
    'invoice_number'=>'RENTAL_ID',
    'date_created'=>'DATE_CREATED',    
    'first_name'=>'FIRST_NAME',
    'last_name'=>'LAST_NAME',
    'telephone'=>'TELEPHONE',
    'company'=>'COMPANY',
    'address'=>'ADDRESS',
    'postal'=>'POSTAL_CODE',
    'city'=>'CITY',
    'country'=>'COUNTRY_ID',
    'product_id' => 'PRODUCT_ID',
    'product_model' => 'PRODUCT_MODEL',
    'product_title' => 'PRODUCT_TITLE',
    'rental_points'=>'RENTAL_POINTS',
    'shipping_method'=>'SHIPPING_METHOD',  //SHIPPING METHOD!  
    'delivery_status'=>'DELIVERY_STATUS',
    'comments'=>'COMMENTS'  
  );
  
  var $dbConn;                 // database connection handler
 
  /**
  * To be called as a constructor - setup and initialization
  **/
  function customerAccess() 
  {
    //setup database connector
    $this->dbConn = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
    if(!$this->dbConn) die(mysql_error($this->dbConn));
    mysql_select_db($this->dbName, $this->dbConn) or die(mysql_error($this->dbConn));    
          
  }  
  
  /**
  * SQL query function. Return Result Set.
  **/
  function query($sql)    // REMEMBER TO REMOVE ERRROR DIE!!!
  {
    $res = mysql_query($sql, $this->dbConn) or die(mysql_error($this->dbConn));
    //echo $sql.'<p>';
    return $res;
  }
  
  function count_rows($str = "", $col = "", $alpha = "") // count total number of records returned by a search query
  {
      if($col == "") $col = "CONCAT_WS(' ',`{$this->tbFields1["first_name"]}`,`{$this->tbFields1["last_name"]}`)";      
      
      $query = "SELECT COUNT(`{$this->tbFields1["id"]}`) AS `COUNT` from `{$this->userTable}` WHERE (".$col." LIKE '%".$str."%'";
    
      if(strrpos($str, " ")){
        $str_parts = explode(" ", $str);
        $first_tmp = 1;
        foreach ($str_parts AS $str_part){
          if($first_tmp == 1){
            $query = $query." OR (".$col." LIKE '%".$str_part."%'";
            $first_tmp = 0;
          }
          $query = $query." AND ".$col." LIKE '%".$str_part."%'";            
        }
        $query = $query.")) ";
      }else{
        $query = $query.") ";
      }
    
      if($alpha != ""){
        $query = $query."AND `{$this->tbFields["title"]}` LIKE '".$alpha."%' ";
      }
            
      //echo $query;
      $res = $this->query($query);
      if($res && mysql_num_rows($res) > 0){
        $res = mysql_fetch_array($res);
        return $res["COUNT"];  
      }else{
        return false;
      }                   
      
  }

  function getCustomerBasicInfo($cid){
      if($cid == "") return false;
  
      $res = $this->query("SELECT `{$this->tbFields1["first_name"]}`,`{$this->tbFields1["last_name"]}`,`{$this->tbFields1["email"]}` FROM `{$this->userTable}` WHERE `{$this->tbFields1["id"]}`=".$cid." LIMIT 1");
      if($res && mysql_num_rows($res) > 0){
        return mysql_fetch_array($res);
      }else{
        return false;
      }          
  }
  
    
  function giveRP($cid,$rp){
    $res = $this->query("UPDATE `{$this->userTable}` SET `{$this->tbFields1['rental_points']}`=".$rp." WHERE `{$this->tbFields1['id']}` = ".$cid." LIMIT 1");    
    if(mysql_affected_rows($this->dbConn)){
      return true;
    }
    return false;
  }
  
  function getRPvalue($plan_type){
    //find RP
    $plan_type = "  ".$plan_type;
    if(stripos($plan_type,"Basic Subscription")){
      return 3;
    }else if(stripos($plan_type,"Silver Subscription")){
      return 5;
    }else if(stripos($plan_type,"Gold Subscription")){
      return 7;
    }else if(stripos($plan_type,"Titanium Subscription")){
      return 10;
    }
    return 0;
  }
  
  function checkMemberships($days="",$cid){
    //TODOO!!!
    if($this->spentEnough(2000,$days)){  //check amount spent for discount membership
      $this->setCustomerMember($cid, $this->findCMember(10,0,"Discount_10"));
    }else if($this->spentEnough(1500,$days)){
      $this->setCustomerMember($cid, $this->findCMember(7,0,"Discount_7"));
    }else if($this->spentEnough(800,$days)){
      $this->setCustomerMember($cid, $this->findCMember(5,0,"Discount_5"));
    }else{
      $this->unsetCustomerMember($cid);
    }
    return true;
  }

  function spentEnough($amount=0,$days="",$cid){    
    if($days == ""){
      $res = $this->query("SELECT SUM(`TOTAL_PRICE`) AS `SUM` FROM `order` WHERE `CUSTOMER_ID`=".$cid);
    }else{
      $res = $this->query("SELECT SUM(`TOTAL_PRICE`) AS `SUM` FROM `order` WHERE (`DATE_CREATED` > DATE_SUB(CURDATE(),INTERVAL ".$days." DAY)) AND `CUSTOMER_ID`=".$cid);
    }
    if($res && mysql_num_rows($res) > 0){
      $res = mysql_fetch_array($res);
      if($res['SUM'] >= $amount){
        return true;
      }    
    }
    return false;
  }
 
  
  function storeOldDiscountType($cid,$discount_type,$discount_name,$discount,$exclu_discount){
    $res = $this->query("INSERT INTO `{$this->olddiscountTable}` (`{$this->olddiscountFields["customer_id"]}`,`{$this->olddiscountFields["discount_type"]}`,`{$this->olddiscountFields["discount_name"]}`,`{$this->olddiscountFields["discount"]}`,`{$this->olddiscountFields["exclusive_discount"]}`) VALUES(".$cid.",".$discount_type.",'".$discount_name."',".$discount.",".$exclu_discount.")");
  }
  
  function deleteOldDiscountType($id){
    $res = $this->query("DELETE FROM `{$this->olddiscountTable}` WHERE `{$this->olddiscountFields["id"]}`=".$id." LIMIT 1");
    return true;
  }
  
  function restoreOldDiscountType($cid){
    $res = $this->query("SELECT * FROM `{$this->olddiscountTable}` WHERE `{$this->olddiscountFields["customer_id"]}`=".$cid." LIMIT 1");
    if($res && mysql_num_rows($res) > 0){
      $res = mysql_fetch_array($res);
      $data = $this->getMemberTypeInfo($res[$this->olddiscountFields["discount_type"]]);
      if($data != ""){
         if($res[$this->olddiscountFields["discount"]] == $data[$this->tbFields11["discount"]] && $res[$this->olddiscountFields["exclusive_discount"]] == $data[$this->tbFields11["exclusive_discount"]]){
            $this->setCustomerMember($cid,$res[$this->olddiscountFields["discount_type"]]);
            return $this->deleteOldDiscountType($res[$this->olddiscountFields["id"]]);            
         }
      }
      
      $this->setCustomerMember($cid, $this->findCMember($res[$this->olddiscountFields["discount"]],$res[$this->olddiscountFields["exclusive_discount"]],$res[$this->olddiscountFields["discount_name"]]));
      return $this->deleteOldDiscountType($res[$this->olddiscountFields["id"]]);      
    }
    return false;
  }
  
  
  function addRentalMember($cid,$plan_type,$discountbundle){
  // get RP
    $rp = $this->getRPvalue($plan_type);
    if($rp){
      $res = $this->query("INSERT INTO `{$this->rentalplanTable}` (`{$this->rplanFields["customer_id"]}`,`{$this->rplanFields["date_paid"]}`,`{$this->rplanFields["plan_type"]}`,`{$this->rplanFields["rental_points"]}`) VALUES(".$cid.",CURDATE(),'".$plan_type."',".$rp.")");
      if(mysql_affected_rows($this->dbConn)){
        //give RP
        $this->giveRP($cid,$rp);
        //give discount, create new discount type if necessary
       if($mem_tmp = $this->getMember($cid)){          
          $dis_data = $this->getMemberTypeInfo($mem_tmp);
          if($dis_data[$this->tbFields11["discount"]] >= $discountbundle){
              return true;
          }else{
            if($dis_data[$this->tbFields11["exclusive_discount"]] != 0)
              $this->storeOldDiscountType($cid,$mem_tmp,$dis_data[$this->tbFields11["type"]],$dis_data[$this->tbFields11["discount"]],$dis_data[$this->tbFields11["exclusive_discount"]]);
            
            $this->setCustomerMember($cid, $this->findCMember($discountbundle,$dis_data[$this->tbFields11["exclusive_discount"]],$dis_data[$this->tbFields11["type"]]."_R"));
            return true;
          }          
       }else{
          $this->setCustomerMember($cid, $this->findCMember($discountbundle,0,"Rental Member"));       
          return true;
       }        
      }
    }
    return false;
  }
  
  function removeRentalMember($cid){
   $res = $this->query("DELETE FROM `{$this->rentalplanTable}` WHERE `{$this->rplanFields["customer_id"]}`=".$cid);
   $this->giveRP($cid,0);
   
   return true;
  }
  
  function getRentalPlan($cid){
    if($cid != ""){
      $res = $this->query("SELECT `{$this->rplanFields["plan_type"]}` AS `PT` FROM `{$this->rentalplanTable}` WHERE `{$this->rplanFields["customer_id"]}`=".$cid." LIMIT 1");
      if($res && mysql_num_rows($res) > 0){
        $res = mysql_fetch_array($res);
        return $res["PT"];
      }   
    }
    return "";      
  }
  
  function manualRentPay($cid, $rplan){
    $dt = date("dmYHi");
    $dt2 = date("d/m/Y/H:i");
    $tmp = $this->getCustomerBasicInfo($cid);
    $qu = "INSERT INTO `ipn_notifications` (`TRANSACTION_ID` ,`TRANSACTION_TYPE` ,`CUSTOMER_ID` ,`DATE_CREATED` ,`FIRST_NAME` ,`LAST_NAME`,`EMAIL` ,`PAYER_FIRST_NAME` ,`PAYER_LAST_NAME` ,`PAYER_EMAIL` ,`PAYMENT_AMOUNT` ,`PRODUCT` ,`STATUS` ,`NOTIFICATION`) VALUES ";
    $qu = $qu."('manual".$dt."', 'subscr_payment', ".$cid.", NOW(), '".$tmp[$this->tbFields1['first_name']]."','".$tmp[$this->tbFields1['last_name']]."','". $tmp[$this->tbFields1['email']]."', '-', '-', '-', 0,'".$rplan."', 'SUCCESS', 'manual_ipn_notification_".$dt2."')";
    $res = $this->query($qu);
    return true;
  }
  
  function isRentalMember($cid){
    if($cid != ""){
      $res = $this->query("SELECT `{$this->rplanFields["id"]}` FROM `{$this->rentalplanTable}` WHERE `{$this->rplanFields["customer_id"]}`=".$cid." LIMIT 1");
      if($res && mysql_num_rows($res) > 0){
        return true;
      }   
    }
    return false;
  }
  
  function findCMember($discount=0,$exclu_discount=0,$type=""){
    if(!$id = $this->findMember($discount,$exclu_discount)){
        while($this->findMember(0,0,$type)){
          $type = $type."v";
        }
        $id = $this->createMemberType($type, $discount, $exclu_discount); 
    }
    return $id;
  }
  
  
  function findMember($discount=0, $exclu_discount=0, $type=""){
    if($type != ""){
      $res = $this->query("SELECT `{$this->tbFields11["id"]}` AS `ID` FROM `{$this->memberTable}` WHERE `{$this->tbFields11["type"]}`='".$type."' LIMIT 1");
    }else{
      $res = $this->query("SELECT `{$this->tbFields11["id"]}` AS `ID` FROM `{$this->memberTable}` WHERE `{$this->tbFields11["discount"]}`=".$discount." AND `{$this->tbFields11["exclusive_discount"]}`=".$exclu_discount." LIMIT 1");
    }
    if($res && mysql_num_rows($res)>0){
      $res = mysql_fetch_array($res);
      return $res['ID'];
    }
    
    return false;
  
  }
  
  function getMember($cid){
    if($cid != ""){
      $res = $this->query("SELECT `{$this->tbFields1["discount_type"]}` AS `DT` FROM `{$this->userTable}` WHERE `{$this->tbFields1["id"]}`=".$cid." LIMIT 1");
      if($res && mysql_num_rows($res) > 0){
        $res = mysql_fetch_array($res);
        return $res['DT'];
      }
    }
    return false;
  }
  
  
  function storeIPN($txn_id,$txn_type,$cid,$payer_first_name,$payer_last_name,$payer_email,$amt=0,$product,$str,$status)
  {
    if(!($data=$this->getCustomerBasicInfo($cid))){          
      $data[$this->tbFields1["first_name"]] = "";
      $data[$this->tbFields1["last_name"]] = "";
      $data[$this->tbFields1["email"]] = "";
      $cid = "NULL";
    }

    $res = $this->query("INSERT INTO `{$this->ipnTable}` (`{$this->ipnFields["transaction_id"]}`,`{$this->ipnFields["transaction_type"]}`,`{$this->ipnFields["customer_id"]}`,`{$this->ipnFields["date_created"]}`,`{$this->ipnFields["first_name"]}`,`{$this->ipnFields["last_name"]}`,`{$this->ipnFields["email"]}`,`{$this->ipnFields["payer_first_name"]}`,`{$this->ipnFields["payer_last_name"]}`,`{$this->ipnFields["payer_email"]}`,`{$this->ipnFields["payment_amount"]}`,`{$this->ipnFields["product"]}`,`{$this->ipnFields["status"]}`,`{$this->ipnFields["notification"]}`) VALUES ('".$txn_id."','".$txn_type."',".$cid.",NOW(),'".$data[$this->tbFields1["first_name"]]."','".$data[$this->tbFields1["last_name"]]."','".$data[$this->tbFields1["email"]]."','".$payer_first_name."','".$payer_last_name."','".$payer_email."',".$amt.",'".$product."','".$status."','".mysql_real_escape_string($str)."')");      
    if(mysql_affected_rows($this->dbConn)){
        
        //handle RP
        if($txn_type == "subscr_payment"){
            if(!$this->isRentalMember($cid)){
              //add to table (and give RP and 10%)
              if($this->addRentalMember($cid,$product,10)){
                  //send email
                  return $this->sendSubscriptionEmail($data[$this->tbFields1["first_name"]],$data[$this->tbFields1["last_name"]],$data[$this->tbFields1["email"]],$product);
              }
            }else{
              //update table
              $res = $this->query("UPDATE `{$this->rentalplanTable}` SET `{$this->rplanFields["date_paid"]}`=CURDATE() WHERE `{$this->rplanFields["customer_id"]}`=".$cid." LIMIT 1");
              //check RP
              return $this->checkRP($cid,$this->getRPvalue($product));
            }                       
        }else if($txn_type == "subscr_cancel"){          
          return  $this->sendCancelEmail($data[$this->tbFields1["first_name"]],$data[$this->tbFields1["last_name"]],$data[$this->tbFields1["email"]],$product);
          //cancel
        }        
    }
     
    return false;
  }

  function checkRP($cid,$rp){
    $res = $this->query("SELECT `{$this->tbFields1["rental_points"]}` AS `RP` FROM `{$this->userTable}` WHERE `{$this->tbFields1["id"]}`=".$cid." LIMIT 1");
    if($res && mysql_num_rows($res)>0){
      $res = mysql_fetch_array($res);
      $total = $res['RP'];
      $used = 0;
      $res = $this->query("SELECT SUM(`{$this->rentedFields["rental_points"]}`) AS `SUM` FROM `{$this->rentedTable}` WHERE (`{$this->rentedFields["delivery_status"]}`=0 OR `{$this->rentedFields["delivery_status"]}`=3) AND `{$this->rentedFields["customer_id"]}`=".$cid);      
      if($res && mysql_num_rows($res)>0){
        $res = mysql_fetch_array($res);
        $usedRP = $res['SUM'];
        $total += $usedRP;      
      }
      if($total == $rp){
        return true;
      }else{
        $total = $rp - $usedRP;
        if($total < 0) $total = 0;
        $this->giveRP($cid,$total);        
      }    
    }
    return false;
  }

  //SEARCH FUNCTION, RETURNS ARRAY OF MATCHES
  function searchCustomer($str = "", $col = "", $id = "", $orderby = "", $ordertype = "", $from = 0, $num = 30, $alpha = "") 
  {
    $str = trim($str);
  
    if($id != ""){
      $res = $this->query("SELECT * FROM `{$this->userTable}` WHERE `{$this->tbFields1["id"]}` = ".$id." LIMIT 1");
      
      if($res && mysql_num_rows($res) > 0){
        return mysql_fetch_array($res);
      }else{
        return false;
      }                  
    }else{
      if($col == "") $col = "CONCAT(`{$this->tbFields1["first_name"]}`,' ',`{$this->tbFields1["last_name"]}`)";
      
      if($orderby == "") $orderby = $this->tbFields1["first_name"];
      if($ordertype == "") $ordertype = "ASC";
      
      $query = "SELECT * from `{$this->userTable}` WHERE (".$col." LIKE '%".$str."%'";
    
      if(strrpos($str, " ")){
        $str_parts = explode(" ", $str);
        $first_tmp = 1;
        foreach ($str_parts AS $str_part){
          if($first_tmp == 1){
            $query = $query." OR (".$col." LIKE '%".$str_part."%'";
            $first_tmp = 0;
          }
          $query = $query." AND ".$col." LIKE '%".$str_part."%'";            
        }
        $query = $query.")) ";
      }else{
        $query = $query.") ";
      }
    
      if($alpha != ""){
        $query = $query."AND `{$this->tbFields1["first_name"]}` LIKE '".$alpha."%' ";
      }
      
      $query = $query." ORDER BY `{$orderby}` ".$ordertype;
      
      if($num != -1) $query = $query." LIMIT ".$from.", ".$num;
      //echo $query;
      $res = $this->query($query);
      if($res && mysql_num_rows($res) > 0){
        return $this->SqlToArray($res);  
      }else{
        return false;
      }        
    }      
  }
    
  function resetDefaultPassword($userid,$pwd){
      $enc_new = $this->encrypt_password($pwd);
      $res = $this->query("UPDATE `{$this->userTable}` SET `{$this->tbFields1['password']}`='".$enc_new."', `{$this->tbFields1['last_pw_change']}`= CURRENT_TIMESTAMP WHERE `{$this->tbFields1['id']}`='".$userid."' LIMIT 1");            
      if(mysql_affected_rows($this->dbConn)){
          return true;
      }else
        return false;
  }
  
  function sendCancelEmail($first_name,$last_name,$email,$product){
   
      $our_email = WELCOME_EMAIL;
      
       //define the subject of the email
      $subject = "Cancellation of Subscription to Rental Service at Board Game Lifestyle";
      //create a boundary string, unique    
      $random_hash = md5(date('r', time()));
      //define the headers we want passed. Note that they are separated with \r\n
      $headers = "From: ".$our_email."\nReply-To: ".$our_email."\n";
      $headers .= "cc: ".$our_email."\n";
       //add boundary string and mime type specification
      $headers .= "MIME-Version:1.0\n";
      $headers .= "Content-Type: multipart/alternative; boundary=\"PHP-alt-".$random_hash."\"\n";
      //define the body of the message.
      ob_start(); //Turn on output buffering
      
      echo "\nThis is a multi-part message in MIME format.\n";      
      echo "\n--PHP-alt-".$random_hash."\n"; 
      echo "Content-Type: text/plain; charset=\"iso-8859-1\"\n";
      echo "Content-Transfer-Encoding: 7bit\n";
      
      echo "\n";
      //PLAIN     
      echo "Dear ".$first_name." ".$last_name." (".$email."),\n\n";                
      echo "You have cancelled the subscription to our Rental Services (".$product.").\n\n";
      echo "You will still have your Rental Points (RP) and the benefits of being a Rental Member until your current billing cycle is over. (1 month from the last payment)\n\n";
      echo "For help with any of our online services, please email the store-owner: grace@boardgamelifestyle.com.\n";
      echo "Thank you.\n\n\n";         
      echo "Note: If you did not unsubscribe from being a rental member, please notify us at grace@boardgamelifestyle.com."; 
      echo "\n";
            
      echo "\n--PHP-alt-".$random_hash."\n"; 
      echo "Content-Type: text/html; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: 7bit\n";
      //HTML
      echo "\n<html><body>";
      echo "Dear ".$first_name." ".$last_name." (".$email."),<p>";                
      echo "You have cancelled the subscription to our Rental Services <i>(".$product.")</i>.<p>";
      echo "You will still have your <b>Rental Points (RP)</b> and the benefits of being a Rental Member until your current billing cycle is over. (1 month from the last payment)<p>";
      echo "For help with any of our online services, please email the store-owner: grace@boardgamelifestyle.com.<br>";
      echo "Thank you.<p>&nbsp;<p>";         
      echo "<i>Note: If you did not signup to be a rental member, please notify us at grace@boardgamelifestyle.com.</i>"; 
      echo "<p>";                                  
      echo "</body></html>\n";
      echo "\n--PHP-alt-".$random_hash."--\n";

      //copy current buffer contents into $message variable and delete current output buffer
      $message = ob_get_clean();      
      //send the email
      $mail_sent = @mail( $email, $subject, $message, $headers );      
    
  }
  
  function sendSubscriptionEmail($first_name,$last_name,$email,$product){
   
      $our_email = WELCOME_EMAIL;
      
       //define the subject of the email
      $subject = $first_name." ".$last_name."- Subscription to Rental Service at Board Game Lifestyle";
      //create a boundary string, unique    
      $random_hash = md5(date('r', time()));
      //define the headers we want passed. Note that they are separated with \r\n
      $headers = "From: ".$our_email."\nReply-To: ".$our_email."\n";
      $headers .= "cc: ".$our_email."\n";
       //add boundary string and mime type specification
      $headers .= "MIME-Version:1.0\n";
      $headers .= "Content-Type: multipart/alternative; boundary=\"PHP-alt-".$random_hash."\"\n";
      //define the body of the message.
      ob_start(); //Turn on output buffering
      
      echo "\nThis is a multi-part message in MIME format.\n";      
      echo "\n--PHP-alt-".$random_hash."\n"; 
      echo "Content-Type: text/plain; charset=\"iso-8859-1\"\n";
      echo "Content-Transfer-Encoding: 7bit\n";
      
      echo "\n";
      //PLAIN     
      echo "Dear ".$first_name." ".$last_name." (".$email."),\n\n";                
      echo "Congratulations and thank you for becoming one of our Rental Members (".$product.")!\n\n";
      echo "You can log in at www.boardgamelifestyle.com right now to rent fantastic games with your Rental Points (RP) and at the same time enjoy 10% off purchases of non-promotional items!\n\n";
      echo "For help with any of our online services, please email the store-owner: grace@boardgamelifestyle.com.\n";
      echo "Thank you.\n\n\n";         
      echo "Note: If you did not signup to be a rental member, please notify us at grace@boardgamelifestyle.com."; 
      echo "\n";
            
      echo "\n--PHP-alt-".$random_hash."\n"; 
      echo "Content-Type: text/html; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: 7bit\n";
      //HTML
      echo "\n<html><body>";
      echo "Dear ".$first_name." ".$last_name." (".$email."),<p>";               
      echo "Congratulations and thank you for becoming one of our <b>Rental Members</b> <i>(".$product.")</i>!<p>";
      echo "You can log in at <a href='www.boardgamelifestyle.com '>www.boardgamelifestyle.com</a> right now to rent fantastic games with your <b>Rental Points (RP)</b> and at the same time enjoy <b>10% off</b> purchases of non-promotional items!<p>";
      echo "For help with any of our online services, please email the store-owner: grace@boardgamelifestyle.com.<br>";
      echo "Thank you.<p>&nbsp;<p>";         
      echo "<i>Note: If you did not signup to be a rental member, please notify us at grace@boardgamelifestyle.com.</i>"; 
      echo "<p>";                                  
      echo "</body></html>\n";
      echo "\n--PHP-alt-".$random_hash."--\n";

      //copy current buffer contents into $message variable and delete current output buffer
      $message = ob_get_clean();      
      //send the email
      $mail_sent = @mail( $email, $subject, $message, $headers );      
    
  }
  
    
  function emailNewsletter($id)
  {
    if($id == "") return false;
    
    $res = $this->query("SELECT * FROM `{$this->admincontentTable}` WHERE `{$this->admincontentFields["id"]}`=".$id." LIMIT 1");
    
    if($res && mysql_num_rows($res) > 0){
      $res = mysql_fetch_array($res);

      $our_email = NEWSLETTER_EMAIL;
      
      //define the subject of the email
      $subject = $res[$this->admincontentFields["title"]];
      //create a boundary string, unique    
      $random_hash = md5(date('r', time()));
      //define the headers we want passed. Note that they are separated with \r\n
      $headers = "From: ".$our_email."\nReply-To: ".$our_email."\n";
      
       //add boundary string and mime type specification
      $headers .= "MIME-Version:1.0\n";
      $headers .= "Content-Type: multipart/alternative; boundary=\"PHP-alt-".$random_hash."\"\n";
      //define the body of the message.
      ob_start(); //Turn on output buffering
      
      echo "\nThis is a multi-part message in MIME format.\n";      
      echo "\n--PHP-alt-".$random_hash."\n"; 
      echo "Content-Type: text/plain; charset=\"iso-8859-1\"\n";
      echo "Content-Transfer-Encoding: 7bit\n";
      
      echo "\n";
      //PLAIN                     
      $plain_tmp = str_replace("<p>","\n\n",$res[$this->admincontentFields["content"]]);
      $plain_tmp = str_replace("<br>","\n",$plain_tmp);         
      $plain_tmp = str_replace("<br />","\n",$plain_tmp);
      echo strip_tags($plain_tmp);
         
      echo "\n";
            
      echo "\n--PHP-alt-".$random_hash."\n"; 
      echo "Content-Type: text/html; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: 7bit\n";
      //HTML
      echo "\n<html><body>";
      echo $res[$this->admincontentFields["content"]];              
      echo "</body></html>\n";
      echo "\n--PHP-alt-".$random_hash."--\n";

      //copy current buffer contents into $message variable and delete current output buffer
      $message = ob_get_clean();      
      //send the email
      
      
      $emails = $this->getNewsletterEmails();
      $i = 0;
      $j = 0;
      if($emails && count($emails)>0){
        foreach($emails AS $email){        
          $i++;
          if($mail_sent = @mail( $email['EMAIL'], $subject, $message, $headers )) $j++;
        }
      }
      //if the message is sent successfully print "Mail sent". Otherwise print "Mail failed" 
      echo "<b>".$j." mails sent successfully out of ".$i." intended recipients.</b><p>";
      return $mail_sent;                                  
    
    }else return false;          
  }
    
  function getNewsletterEmails()
  {
    $res = $this->query("SELECT `{$this->tbFields1['email']}` AS `EMAIL` FROM `{$this->userTable}` WHERE `{$this->tbFields1['newsletter_flag']}`='1'");   
    
    if($res && mysql_num_rows($res)>0){
      return $this->SqlToArray($res);
    }else
      return false;
  
  }       
    
  function deleteMemberType($id)
  {
  //check for membertype being used first
    if($id != ""){
      $res = $this->query("DELETE FROM `{$this->memberTable}` WHERE `{$this->tbFields11['id']}` =".$id." LIMIT 1");
    }else{
      return false;
    }
    if(mysql_affected_rows($this->dbConn)==1){
      return true;
    }else
      return false;  
  }
  
  function createMemberType($type, $discount, $exclusive_d)
  {
    if($type != "" && is_numeric($discount) && $discount < 100 && is_numeric($exclusive_d) && $exclusive_d < 100)
      $res = $this->query("INSERT INTO `{$this->memberTable}` (`{$this->tbFields11['type']}`, `{$this->tbFields11['discount']}`,`{$this->tbFields11['exclusive_discount']}`) VALUES('".$type."',".$discount.",".$exclusive_d.")");
    else{
      return false;
    }
    return mysql_insert_id($this->dbConn);
  }
  
  function setCustomerMember($customer_id, $member_id)
  {
    if($customer_id != "" && $member_id != "")
      $res = $this->query("UPDATE `{$this->userTable}` SET `{$this->tbFields1['discount_type']}`='".$member_id."' WHERE `{$this->tbFields1['id']}`='".$customer_id."' LIMIT 1");        
  }
  
  function unsetCustomerMember($customer_id)
  {
    if($customer_id != "")
      $res = $this->query("UPDATE `{$this->userTable}` SET `{$this->tbFields1['discount_type']}`=0 WHERE `{$this->tbFields1['id']}`='".$customer_id."' LIMIT 1");    
  }

  function getMemberTypeInfo($id, $arr="")
  {
    if($id == 0){
      return "";
    }
    if($arr == ""){
      $arr = $this->getMemberTypes();    
    }
    foreach($arr AS $arr_ele){
      if($arr_ele[$this->tbFields11['id']] == $id){
        return $arr_ele;
      }    
    }
    return "";  
  }

  function getMemberTypes()
  {
    $res = $this->query("SELECT * FROM `{$this->memberTable}`");
    
    if($res && mysql_num_rows($res) > 0){
      return $this->SqlToArray($res);
    }else{
      return false;
    }
  
  }
  
  /**
  * Get Addresses
  **/
  function getAddress($customer_id){

      $res = $this->query("SELECT * FROM `{$this->userAddressTable}` WHERE `{$this->tbFields2['customer_id']}` = '".$customer_id."'");            
      
      if($res && mysql_num_rows($res) > 0){
         return $this->SqlToArray($res);
      }else{
        return false;        
      }
  }
  
  
  function getCountryName($id)
  {  
    $res = $this->query("SELECT `{$this->tbFields3["name"]}` AS `NAME` FROM `{$this->countriesTable}` WHERE `{$this->tbFields3['id']}`=".$id." LIMIT 1");
    if(!$res && mysql_num_rows($res) == 0){
      return "";
    }else{
      $res = mysql_fetch_array($res);
      return $res['NAME'];
    }
  }
  
  function getCountryCode($id, $code = '2')
  {  
    $country_code = "iso_code_".$code;    
    $res = $this->query("SELECT `{$this->tbFields3[$country_code]}` AS `CODE` FROM `{$this->countriesTable}` WHERE `{$this->tbFields3['id']}`=".$id." LIMIT 1");
    if(!$res && mysql_num_rows($res) == 0){
      return "";
    }else{
      $res = mysql_fetch_array($res);
      return $res['CODE'];
    }
  }
  
  function transformDate($date_str)  //transfrom SQL date to DD/MM/YY
  {
    if($date_str != "" && $date_str != "NULL" && $date_str != NULL){
      $tmp_date = explode("-",$date_str);
      if(sizeof($tmp_date) == 3){
        $date_str = $tmp_date[2]."/".$tmp_date[1]."/".$tmp_date[0];
      }
    }
    return $date_str;
  }
  
  function SqlToArray($res)
  {
    $result = array();
    if(!$res && mysql_num_rows($res) == 0){
      $result = "";
    }else{
      while($row = mysql_fetch_assoc($res)){
        $result[] = $row;
      }
    }      
    return $result;
  }
  // This function makes a new password from a plaintext password. 
  function encrypt_password($plain) {
    $password = '';

    for ($i=0; $i<10; $i++) {
      $password .= $this->random2();
    }

    $salt = substr(md5($password), 0, 2);

    $password = md5($salt . $plain) . ':' . $salt;

    return $password;
  }
  // Return a random value
  function random2($min = null, $max = null) {
    static $seeded;

    if (!isset($seeded)) {
      mt_srand((double)microtime()*1000000);
      $seeded = true;
    }

    if (isset($min) && isset($max)) {
      if ($min >= $max) {
        return $min;
      } else {
        return mt_rand($min, $max);
      }
    } else {
      return mt_rand();
    }
  }
  
}

?>