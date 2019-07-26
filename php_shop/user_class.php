<?php
/**
* PHP class - userAccess
*
* For Login, Logout, Register and other user related administrative functions.
*
*
* Copyright: 1st May 2009, Ho Wen Yang
*
* FUNCTIONS:
*
*     1.  userAccess()                    -- Constructor.
*     2.  is_logged_in()                  -- Returns Boolean.
*     3.  query($sql)                     -- Perform SQL query using $dbConn.
*     4.  login($uname, $pwd)             -- Login. Returns Boolean.
*     5.  logout()                        -- Logout. Invalidate Cookie.
*     6.  activate($uname, $activation)   -- Activate Account.
*     7.  addUserTemp($data)              -- Add User Before Activation.
*     8.  addUser($data)                  -- Create User Account. Upon Activation.
*     9.  addUserInfo($data)              -- Add User's First, Last Name, etc..
*     10. addAddress($data)               -- Add Address.
*     11. changePassword($oldpwd, $newpwd)-- Change Password.
*     12. randomPass()                    -- Generate random password for activation. Default length 10.
*     13. user_exist($uname)              -- Check if $uname is already taken.
*     14. getAddress()                    -- Get Address(es) of user. Returns Boolean. Intializes $userAddress with array if present.
*
**/

class userAccess{
  
  var $dbName = DB_DATABASE;   // name of database
  
  var $userTempTable = "customer_temporary";    // name of table containing user info BEFORE activation
  var $tbTmpFields = array(     // fields of $userTempTable
    'id'=> 'ID',   	
  	'password'  => 'PASSWORD',
  	'first_name' => 'FIRST_NAME',
    'last_name' => 'LAST_NAME',
    'gender' => 'GENDER',
    'dob'=>'DOB',
    'telephone'=>'TELEPHONE',
    'fax'=>'FAX',
    'email' => 'EMAIL',
    'newsletter_flag'=>'NEWSLETTER_FLAG',
    'company' => 'COMPANY',    
    'address' => 'ADDRESS',
    'postal' => 'POSTAL_CODE',
    'city' => 'CITY',
    'country' => 'COUNTRY_ID',
    'primary_flag' => 'PRIMARY_FLAG',
    'activation_code' => 'ACTIVATION_CODE',
  	'date_registered' => 'DATE_REGISTERED'
  );

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
  	'rental_points' => 'RENTAL_POINTS'
  );
  
  var $memberTable = "customer_member";  // name of table containing member infor
  var $tbFields11 = array(
    'id' => 'ID',
    'type' => 'TYPE',
    'discount' => 'DISCOUNT',
    'exclusive_discount' => 'EXCLUSIVE_DISCOUNT'  
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
  
  var $order_productTable = "order_products";
  var $tbFields4 = array(       
    'id'=>'ID',
    'order_id'=>'ORDER_ID',    	
  	'product_id'  => 'PRODUCT_ID',
  	'product_model'=>'PRODUCT_MODEL',
  	'product_title' => 'PRODUCT_TITLE',
  	'quantity' => 'QUANTITY',
  	'price_per_unit' => 'PRICE_PER_UNIT',
  	'sub_total_price'=>'SUB_TOTAL_PRICE'
  );
  
  var $order_products_attribTable = "order_products_attributes";
  var $tbFields41 = array(
    'id'=>'ID',
    'order_products_id'=>'ORDER_PRODUCTS_ID',
    'attributes_id'=>'ATTRIBUTES_ID',
    'options_name'=>'OPTIONS_NAME',
    'options_values_name'=>'OPTIONS_VALUES_NAME',
    'price'=>'PRICE'    
  );
  
  var $orderTable = "order";
  var $tbFields5 = array(
    'id'=>'ID',
    'customer_id'=>'CUSTOMER_ID',
    'invoice_number'=>'INVOICE_NUMBER',
    'date_created'=>'DATE_CREATED',    
    'first_name'=>'FIRST_NAME',
    'last_name'=>'LAST_NAME',
    'telephone'=>'TELEPHONE',
    'company'=>'COMPANY',
    'address'=>'ADDRESS',
    'postal'=>'POSTAL_CODE',
    'city'=>'CITY',
    'country'=>'COUNTRY_ID',
    'total_price'=>'TOTAL_PRICE',
    'shipping_cost'=>'SHIPPING_COST',
    'member_discounts'=>'MEMBER_DISCOUNTS',
    'payment_type'=>'PAYMENT_TYPE',
    'payment_status'=>'PAYMENT_STATUS',
    'date_paid'=>'DATE_PAID',
    'shipping_method'=>'SHIPPING_METHOD',    
    'delivery_status'=>'DELIVERY_STATUS',
    'comments'=>'COMMENTS'  
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
  
  var $rentTable = "rented";
  var $tbFields6 = array(
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
  
  var $dataFields = array(              // fields of $data to be passed in to add into database    
    'customer_id' => 'customer_id',
    'password' => 'password',
    'first_name' => 'first_name',
    'last_name' => 'last_name',
    'gender' => 'gender',
    'dob'=>'dob',
    'telephone'=>'telephone',
    'fax'=>'fax',    
    'email' => 'email',
    'newsletter_flag'=>'newsletter_flag',
    'company' => 'company',   
    'address' => 'address',
    'postal' => 'postal',
    'city' => 'city',
    'country' => 'country',
    'primary_flag' => 'primary_flag'
  ); 
      
  var $cookieName = "boarduser";
  var $cookieLife = 2592000; // 1 month of cookie duration (in seconds) = 60*60*24*30
  
  var $userID = "";                 // userID of user to be initialized
  var $dbConn;                 // database connection handler
  var $userData;               // database information about user (Array consisting of all infos EXCEPT addresses)
  var $userAddress;            // Array containing Addresses
  
  /**
  * To be called as a constructor - setup and initialization
  **/
  function userAccess() 
  {
    //setup database connector
    $this->dbConn = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
    if(!$this->dbConn) die(mysql_error($this->dbConn));
    mysql_select_db($this->dbName, $this->dbConn) or die(mysql_error($this->dbConn));    
    
    if(isset($_COOKIE[$this->cookieName]) && !$this->is_logged_in()){  // retrieve cookie if any
      $cookieInfo = unserialize(base64_decode($_COOKIE[$this->cookieName])); // unserialize cookie     
      $this->login($cookieInfo['uname'],$cookieInfo['password']); // call login function
    }    

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
  
  /**
  * Is the user logged in? Return Boolean.
  **/  
  function is_logged_in()  
  {
    return (empty($this->userID) || $this->userID == "") ? false : true;
  }
  
  function refreshData()
  {
    if($this->userID != ""){
      $res = $this->query("SELECT * FROM `{$this->userTable}` WHERE `{$this->tbFields1['id']}` = '".$this->userID."' LIMIT 1");      
      $this->userData = mysql_fetch_array($res);
    }
  }
  
  function getMemberType()
  {        
    if($this->is_logged_in()){
      $res = $this->query("SELECT * FROM `{$this->memberTable}` WHERE `{$this->tbFields11['id']}`='".$this->userData[$this->tbFields1['discount_type']]."' LIMIT 1");
      
      if($res && mysql_num_rows($res) > 0){
        return mysql_fetch_array($res);      
      }else{
        return false;
      }
    }
    return false;  
  }
  
  function getRP()
  {
    if($this->is_logged_in()){
      $res = $this->query("SELECT `{$this->tbFields1['rental_points']}` AS `RP` FROM `{$this->userTable}` WHERE `{$this->tbFields1['id']}` = '".$this->userID."' LIMIT 1");
      
      if($res && mysql_num_rows($res) > 0){
        $res = mysql_fetch_array($res);
        return $res['RP'];      
      }else{
        return 0;
      }
    }
    return 0;    
  
  }
  
  function minusRP($rp){
    return $this->addRP(-1 * $rp);
  }
  
  function addRP($rp)
  {
    if($this->is_logged_in()){
      $final_rp = $this->getRP() + $rp;
      if($final_rp < 0){
        return -1;
      }else{
        $res = $this->query("UPDATE `{$this->userTable}` SET `{$this->tbFields1['rental_points']}`=".$final_rp." WHERE `{$this->tbFields1['id']}` = '".$this->userID."' LIMIT 1");    
        if(mysql_affected_rows($this->dbConn)){
          return $final_rp;
        }
      }
    }
    return -1;
  }
  
  function resetPassword($email,$telephone)
  {
    if($this->is_logged_in()){
      echo "You are already logged in!";
      exit;
    }else{
      $new_pwd = $this->randomPass(8);
      $enc_new = $this->encrypt_password($new_pwd);
      $res = $this->query("UPDATE `{$this->userTable}` SET `{$this->tbFields1['password']}`='".$enc_new."', `{$this->tbFields1['last_pw_change']}`= CURRENT_TIMESTAMP WHERE `{$this->tbFields1['email']}`='".$email."' AND `{$this->tbFields1['telephone']}`='".$telephone."' LIMIT 1");    
        
      if(mysql_affected_rows($this->dbConn)){
          //send email
        $our_email = WELCOME_EMAIL;
        
        //define the subject of the email
        $subject = "Password Reset at ---!";
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
        echo "You have reset your password at --- \nYour new password is: ".$new_pwd;     
        echo "\n";
              
        echo "\n--PHP-alt-".$random_hash."\n"; 
        echo "Content-Type: text/html; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: 7bit\n";
        //HTML
        echo "\n<html><body>";
        echo "You have reset your password at <a href='---'>---</a>.<p>Your new password is: <b>".$new_pwd."</b>";
        echo "</body></html>\n";
        echo "\n--PHP-alt-".$random_hash."--\n";

        //copy current buffer contents into $message variable and delete current output buffer
        $message = ob_get_clean();      
        //send the email
        $mail_sent = @mail( $email, $subject, $message, $headers );
        //if the message is sent successfully print "Mail sent". Otherwise print "Mail failed" 
        
        if($mail_sent)
          return 1;
        else
          return -2;
                                  
      }else
        return -1;
    }  
  }
  
  
  
////
// This funstion validates a plain text password with an
// encrpyted password
  function validate_password($plain, $encrypted) {
    if ($plain != "" && $encrypted != "") {
// split apart the hash / salt
      $stack = explode(':', $encrypted);

      if (sizeof($stack) != 2) return false;

      if (md5($stack[1] . $plain) == $stack[0]) {
        return true;
      }
    }

    return false;
  }

////
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

  /**
  * Login a user. Return Boolean - success or unsuccessful
  **/    
  function login($uname, $pwd){

    $res = $this->query("SELECT * FROM `{$this->userTable}` WHERE `{$this->tbFields1['email']}` = '".$uname."' LIMIT 1");
    
    if(!$res || mysql_num_rows($res) == 0){  // no records
      return false;
    }else{
    
      $res = mysql_fetch_array($res);
      
      if($this->validate_password($pwd,$res[$this->tbFields1['password']])){  //check password
        $this->userData = $res;
        $this->userID = $this->userData[$this->tbFields1['id']]; // initialize user
      
        //create or renew cookie
        $cookieInfo = base64_encode(serialize(array('uname'=>$uname,'password'=>$pwd))); 
        $a = setcookie($this->cookieName, $cookieInfo, time() + $this->cookieLife);
        
      }else{
        return false;
      }
      
    }    
    if(!$this->checkRentalMember(38)){
      $this->giveRP(0);
    }
    //$this->checkMemberships();
    return true;             
  }
  
  function checkRentalMember($days){
    if($this->isRentalMember()){
      //check for cancel
      $res = $this->query("SELECT `ID` FROM `ipn_notifications` WHERE (`DATE_CREATED` > DATE_SUB(NOW(),INTERVAL ".$days." DAY)) AND `CUSTOMER_ID`=".$this->userID." AND `TRANSACTION_TYPE`='subscr_payment'");
      if($res && mysql_num_rows($res) > 0){
        return true;
      }else{
        $res = $this->query("SELECT `ID`, `TRANSACTION_TYPE` FROM `ipn_notifications` WHERE `CUSTOMER_ID`=".$this->userID." ORDER BY `DATE_CREATED` DESC LIMIT 1");
        if($res && mysql_num_rows($res) > 0){
          $res = mysql_fetch_array($res);
          if($res['TRANSACTION_TYPE'] == "subscr_cancel"){
            //remove Rental Membership
            $res = $this->query("DELETE FROM `{$this->rentalplanTable}` WHERE `{$this->rplanFields["customer_id"]}`=".$this->userID);  
            $this->checkMemberships();          
            return false;
          }
        }
        $res = $this->query("DELETE FROM `{$this->rentalplanTable}` WHERE `{$this->rplanFields["customer_id"]}`=".$this->userID);
        $this->checkMemberships();
        return false;
        //notify failure to pay in 8 days      
      }
    }else{
      return false;
    }
    return false;
  }
  
  function isRentalMember(){
    $cid = $this->userID;
    if($cid != ""){
      $res = $this->query("SELECT `{$this->rplanFields["id"]}` FROM `{$this->rentalplanTable}` WHERE `{$this->rplanFields["customer_id"]}`=".$cid." LIMIT 1");
      if($res && mysql_num_rows($res) > 0){
        return true;
      }   
    }
    return false;
  }
  
  function giveRP($rp){
    $cid = $this->userID;
    $res = $this->query("UPDATE `{$this->userTable}` SET `{$this->tbFields1['rental_points']}`=".$rp." WHERE `{$this->tbFields1['id']}` = ".$cid." LIMIT 1");    
    if(mysql_affected_rows($this->dbConn)){
      return true;
    }
    return false;
  }

  function checkMemberships($days=""){
    //TODOO!!!
    if($this->spentEnough(2000,$days)){  //check amount spent for discount membership
      $this->giveMembership(10);
    }else if($this->spentEnough(1500,$days)){
      $this->giveMembership(7);
    }else if($this->spentEnough(800,$days)){
      $this->giveMembership(5);
    }
    return true;
  }
  
  function giveMembership($discountbundle){
       $cid = $this->userID;
          
       if($mem_tmp = $this->getMember($cid)){          
          $dis_data = $this->getMemberTypeInfo($mem_tmp);
          if($dis_data[$this->tbFields11["discount"]] >= $discountbundle){
              return true;
          }else{
            //if($dis_data[$this->tbFields11["exclusive_discount"]] != 0)
              $this->storeOldDiscountType($cid,$mem_tmp,$dis_data[$this->tbFields11["type"]],$dis_data[$this->tbFields11["discount"]],$dis_data[$this->tbFields11["exclusive_discount"]]);
            
            $this->setCustomerMember($cid, $this->findCMember($discountbundle,$dis_data[$this->tbFields11["exclusive_discount"]],$dis_data[$this->tbFields11["type"]]."_S"));
            return true;
          }          
       }else{
          $this->setCustomerMember($cid, $this->findCMember($discountbundle,0,$discountboundle."%_Member"));       
          return true;
       }
  
  }
  
  function spentEnough($amount=0,$days=""){
    
    if($days == ""){
      $res = $this->query("SELECT SUM(`TOTAL_PRICE`) AS `SUM` FROM `order` WHERE `CUSTOMER_ID`=".$this->userID);
    }else{
      $res = $this->query("SELECT SUM(`TOTAL_PRICE`) AS `SUM` FROM `order` WHERE (`DATE_CREATED` > DATE_SUB(CURDATE(),INTERVAL ".$days." DAY)) AND `CUSTOMER_ID`=".$this->userID);
    }
    if($res && mysql_num_rows($res) > 0){
      $res = mysql_fetch_array($res);
      if($res['SUM'] >= $amount){
        return true;
      }    
    }
    return false;
  }
  
  
  /**
  * Logout a user.
  **/   
  function logout()
  {
    $this->userID = "";
    if(isset($_COOKIE[$this->cookieName])){
      setcookie($this->cookieName, '', time() - 3600);      
    }
    session_destroy();   
    //header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    
  }
  
  function getName($id = "")
  {
    if($id == ""){
      $id = $this->userID;
    }
    $res = $this->query("SELECT `{$this->tbFields1["first_name"]}` AS `NAME` FROM `{$this->userTable}` WHERE `{$this->tbFields1["id"]}` = '".$id."' LIMIT 1");
    if($res && mysql_num_rows($res) > 0){
      $res = mysql_fetch_array($res);
      return $res["NAME"];
    }else{
      return false;
    }
  }
  
  function getLastName($id = "")
  {
    if($id == ""){
      $id = $this->userID;
    }
    $res = $this->query("SELECT `{$this->tbFields1["last_name"]}` AS `NAME` FROM `{$this->userTable}` WHERE `{$this->tbFields1["id"]}` = '".$id."' LIMIT 1");
    if($res && mysql_num_rows($res) > 0){
      $res = mysql_fetch_array($res);
      return $res["NAME"];
    }else{
      return false;
    }
  }
  
  function transformDate($date_str)
  {
    if($date_str != "" && $date_str != "NULL" && $date_str != NULL){
      $tmp_date = explode("-",$date_str);
      if(sizeof($tmp_date) == 3){
        $date_str = $tmp_date[2]."/".$tmp_date[1]."/".$tmp_date[0];
      }
    }
    return $date_str;
  }      
  
  /**
  * Get Addresses. Return Boolean
  **/
  function getAddress($id = "",$limit = -1){
    if($id == ""){
      $sql = "SELECT * FROM `{$this->userAddressTable}` WHERE `{$this->tbFields2['customer_id']}` = '".$this->userID."' ORDER BY `{$this->tbFields2['primary_flag']}` DESC, `{$this->tbFields2['id']}` ASC";
      if($limit != -1){
        $sql = $sql." LIMIT ".$limit;
      }
      $res = $this->query($sql);            
      
      if($res && mysql_num_rows($res) > 0){
        if($limit == -1){
          $this->userAddress = $this->SqlToArray($res);
          return true;
        }else{
          return mysql_fetch_array($res);//$this->SqlToArray($res);
        }
      }
      return false;
    }else{
      $res = $this->query("SELECT * FROM `{$this->userAddressTable}` WHERE `{$this->tbFields2['id']}` = ".$id." LIMIT 1");
      if($res && mysql_num_rows($res) > 0){
        return mysql_fetch_array($res);
      }
      return false;
    }
  }
  
  /**
  * Activate Account
  **/
  function activate($email, $activation)
  {
    $res = $this->query("SELECT * FROM `{$this->userTempTable}` WHERE `{$this->tbTmpFields['email']}` = '".$email."' AND `{$this->tbTmpFields['activation_code']}` = '".$activation."' LIMIT 1");
       
    if(!$res || mysql_num_rows($res) == 0){
      return "";
    }else{
      $res_tmp = mysql_fetch_array($res);
      $data = array(//CHANGE        
        $this->dataFields['password'] => $res_tmp[$this->tbTmpFields['password']],
        $this->dataFields['first_name'] => $res_tmp[$this->tbTmpFields['first_name']],
        $this->dataFields['last_name'] => $res_tmp[$this->tbTmpFields['last_name']],
        $this->dataFields['gender'] => $res_tmp[$this->tbTmpFields['gender']],
        $this->dataFields['dob'] => $res_tmp[$this->tbTmpFields['dob']],
        $this->dataFields['telephone'] => $res_tmp[$this->tbTmpFields['telephone']],
        $this->dataFields['fax'] => $res_tmp[$this->tbTmpFields['fax']],        
        $this->dataFields['email'] => $res_tmp[$this->tbTmpFields['email']],
        $this->dataFields['newsletter_flag'] => $res_tmp[$this->tbTmpFields['newsletter_flag']],
        $this->dataFields['company'] => $res_tmp[$this->tbTmpFields['company']],
        $this->dataFields['address'] => $res_tmp[$this->tbTmpFields['address']],
        $this->dataFields['postal'] => $res_tmp[$this->tbTmpFields['postal']],
        $this->dataFields['city'] => $res_tmp[$this->tbTmpFields['city']],
        $this->dataFields['country'] => $res_tmp[$this->tbTmpFields['country']],
        $this->dataFields['primary_flag'] => $res_tmp[$this->tbTmpFields['primary_flag']]      
      );
      $this->addUser($data);
      $this->query("DELETE FROM `{$this->userTempTable}` WHERE `{$this->tbTmpFields['email']}` = '".$email."'");
      return $email;
    }
  }

  /**
  * Add User TEMPORARILY BEFORE ACTIVATION
  **/  
  function addUserTemp($data)
  {
    $pwd = $this->encrypt_password($data[$this->dataFields['password']]);
    //$pwd = "'".$data[$this->dataFields['password']]."'";
    $activation = $this->randomPass();
    
    $dob_str = NULL;
    if($data[$this->dataFields["dob"]] == ""){
      $dob_str = "NULL";
    }else{
      $dob_tmp = explode("/", $data[$this->dataFields["dob"]]);
      if(sizeof($dob_tmp) == 3)
        $dob_str = "'".$dob_tmp[2]."-".$dob_tmp[1]."-".$dob_tmp[0]."'";
    }            
    if($data[$this->dataFields["newsletter_flag"]] != '1' && $data[$this->dataFields["newsletter_flag"]] != '0'){
      $data[$this->dataFields["newsletter_flag"]] = 1;
    }
    
    $res = $this->query("INSERT INTO `{$this->userTempTable}` (`{$this->tbTmpFields['password']}`, `{$this->tbTmpFields['first_name']}`, `{$this->tbTmpFields['last_name']}`, `{$this->tbTmpFields['gender']}`, `{$this->tbTmpFields['dob']}`, `{$this->tbTmpFields['telephone']}`,`{$this->tbTmpFields['fax']}`,`{$this->tbTmpFields['email']}`, `{$this->tbTmpFields['newsletter_flag']}`, `{$this->tbTmpFields['company']}`, `{$this->tbTmpFields['address']}`, `{$this->tbTmpFields['postal']}`, `{$this->tbTmpFields['city']}`, `{$this->tbTmpFields['country']}`, `{$this->tbTmpFields['primary_flag']}`, `{$this->tbTmpFields['activation_code']}`, `{$this->tbTmpFields['date_registered']}`) VALUES ('".$pwd."', '".$data[$this->dataFields["first_name"]]."', '".$data[$this->dataFields["last_name"]]."', '".$data[$this->dataFields["gender"]]."', ".$dob_str.", '".$data[$this->dataFields["telephone"]]."', '".$data[$this->dataFields["fax"]]."', '".$data[$this->dataFields["email"]]."', '".$data[$this->dataFields["newsletter_flag"]]."', '".$data[$this->dataFields["company"]]."', '".$data[$this->dataFields["address"]]."', '".$data[$this->dataFields["postal"]]."', '".$data[$this->dataFields["city"]]."', '".$data[$this->dataFields["country"]]."', ".$data[$this->dataFields["primary_flag"]].", '".$activation."', CURRENT_TIMESTAMP)");
    
    if($res){
      $activation_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'&email='.$data[$this->dataFields["email"]].'&activate='.$activation;
      //echo $activation_link."<p>";       

      // SEND ACTIVATION EMAIL
      return $this->sendActivation($data[$this->dataFields["first_name"]], $activation_link, $data[$this->dataFields['email']]);
    }else{
      return false;
    }      
  }
  
  
  /**
  * Add User CONFIRMED - ONLY TO BE CALLED BY ACTIVATE FUNCTION. - encrypting of password done once upon addition to temp table.
  **/
  function addUser($data)
  {    
    $this->query("INSERT INTO `{$this->userTable}` (`{$this->tbFields1['first_name']}`, `{$this->tbFields1['last_name']}`, `{$this->tbFields1['gender']}`, `{$this->tbFields1['dob']}`, `{$this->tbFields1['telephone']}`, `{$this->tbFields1['fax']}`, `{$this->tbFields1['email']}`, `{$this->tbFields1['newsletter_flag']}`, `{$this->tbFields1['password']}`, `{$this->tbFields1['date_created']}`) VALUES ('".$data[$this->dataFields['first_name']]."', '".$data[$this->dataFields['last_name']]."', '".$data[$this->dataFields['gender']]."', '".$data[$this->dataFields['dob']]."', '".$data[$this->dataFields['telephone']]."', '".$data[$this->dataFields['fax']]."', '".$data[$this->dataFields['email']]."', '".$data[$this->dataFields['newsletter_flag']]."', '".$data[$this->dataFields['password']]."', CURRENT_TIMESTAMP)");        
    
    $data[$this->dataFields['customer_id']] = mysql_insert_id($this->dbConn);
        
    if($data[$this->dataFields['address']] != ""){
      $this->addAddress($data);
    }
    
  }    
  
  function editUserInfo($data)
  {
    $dob_str = NULL;
    if($data[$this->dataFields["dob"]] == ""){
      $dob_str = "NULL";
    }else{
      $dob_tmp = explode("/", $data[$this->dataFields["dob"]]);
      if(sizeof($dob_tmp) == 3)
        $dob_str = "'".$dob_tmp[2]."-".$dob_tmp[1]."-".$dob_tmp[0]."'";
    }            
    if($data[$this->dataFields["newsletter_flag"]] != '1' && $data[$this->dataFields["newsletter_flag"]] != '0'){
      $data[$this->dataFields["newsletter_flag"]] = 1;
    }
    
    $res = $this->query("UPDATE `{$this->userTable}` SET `{$this->tbFields1['first_name']}`='".$data[$this->dataFields['first_name']]."', `{$this->tbFields1['last_name']}`='".$data[$this->dataFields['last_name']]."', `{$this->tbFields1['gender']}`='".$data[$this->dataFields['gender']]."', `{$this->tbFields1['dob']}`=".$dob_str.", `{$this->tbFields1['telephone']}`='".$data[$this->dataFields['telephone']]."', `{$this->tbFields1['fax']}`='".$data[$this->dataFields['fax']]."', `{$this->tbFields1['newsletter_flag']}`='".$data[$this->dataFields['newsletter_flag']]."' WHERE `{$this->tbFields1['id']}`='".$this->userID."'");
    
    return $res;
  }
      
  /**
  * Add Address
  **/  
  function addAddress($data)
  {
  
    // Check for primary_flag and change the previous primary to non-primary
    if($data[$this->dataFields['primary_flag']] == 1){
      $res = $this->query("UPDATE `{$this->userAddressTable}` SET `{$this->tbFields2['primary_flag']}`= 0 WHERE `{$this->tbFields2['customer_id']}`='".$data[$this->dataFields['id']]."'");
    }
  
    $res = $this->query("INSERT INTO `{$this->userAddressTable}` (`{$this->tbFields2['customer_id']}`, `{$this->tbFields2['first_name']}`, `{$this->tbFields2['last_name']}`, `{$this->tbFields2['company']}`, `{$this->tbFields2['address']}`, `{$this->tbFields2['postal']}`, `{$this->tbFields2['city']}`, `{$this->tbFields2['country']}`, `{$this->tbFields2['primary_flag']}`) VALUES ('".$data[$this->dataFields['customer_id']]."', '".$data[$this->dataFields['first_name']]."', '".$data[$this->dataFields['last_name']]."', '".$data[$this->dataFields['company']]."', '".$data[$this->dataFields['address']]."', '".$data[$this->dataFields['postal']]."', '".$data[$this->dataFields['city']]."', '".$data[$this->dataFields['country']]."', '".$data[$this->dataFields['primary_flag']]."')");
    if($res){
      return mysql_insert_id($this->dbConn);
    }else{
      return false;
    }
  }     
  
  
  /**
  * Add Address
  **/  
  function deleteAddress($id)
  {  
    // ****** TODO - Check for primary_flag and change the previous non-primary to primary
  
    $res = $this->query("DELETE FROM `{$this->userAddressTable}` WHERE `{$this->tbFields2['id']}`=".$id);
    return $res;
  }     
  
  /**
  * Change Password
  **/
  function changePassword($oldpwd, $newpwd)
  {    
    $success = 0;
    
    $res = $this->query("SELECT `{$this->tbFields1['password']}` AS `PASS` FROM `{$this->userTable}` WHERE `{$this->tbFields1['id']}` = '".$this->userID."' LIMIT 1");
    
    if($res && mysql_num_rows($res) > 0){
      $res = mysql_fetch_array($res);
      if($this->validate_password($oldpwd, $res['PASS'])){
        $newpwd = $this->encrypt_password($newpwd);
        $res2 = $this->query("UPDATE `{$this->userTable}` SET `{$this->tbFields1['password']}`= '".$newpwd."', `{$this->tbFields1['last_pw_change']}`= CURRENT_TIMESTAMP WHERE `{$this->tbFields1['id']}` = '".$this->userID."'");
        
        $success = mysql_affected_rows($this->dbConn);
      }    
    }
    
            
    if($success != 1){  // wrong
      return false;
    }
    
    return true;
  }
  
  /**
  * Creates a random key for user activation.
  **/
  function randomPass($length=10, $chrs = '1234567890qwertyuiopasdfghjklzxcvbnm')
  {
    $pwd = "";
    for($i = 0; $i < $length; $i++) {
        $pwd .= $chrs{mt_rand(0, strlen($chrs)-1)};
    }
    return $pwd;
  }
  ////
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
  
  /**
  * Check if username exist. Return Boolean.
  **/
  function user_exist($uname)
  {
    $this->removeOldTemps();
  
    $res = $this->query("SELECT * FROM `{$this->userTable}` WHERE `{$this->tbFields1['email']}` = '".$uname."'");
    $res2 = $this->query("SELECT * FROM `{$this->userTempTable}` WHERE `{$this->tbTmpFields['email']}` = '".$uname."'");  
      
    if((!$res && !$res2) || (mysql_num_rows($res) == 0 && mysql_num_rows($res2) == 0)){  // no records            
      return false;
    }
    return true;       
  }
  
  function removeOldTemps()
  {
    $res = $this->query("DELETE FROM `{$this->userTempTable}` WHERE DATE_SUB(CURDATE(), INTERVAL 30 DAY) > `{$this->tbTmpFields['date_registered']}`");
  
  }  
  
  function getCountries()
  {    
    $res = $this->query("SELECT `{$this->tbFields3['id']}` AS `ID`, `{$this->tbFields3["name"]}` AS `NAME` FROM `{$this->countriesTable}`");
    return $this->SqlToArray($res);
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

  function getTransactions($pending = 0)
  {
    if($this->userID != ""){
       if($pending != 0){
          $sql = " AND (`{$this->tbFields5['payment_status']}` <> 1 OR `{$this->tbFields5['delivery_status']}` <> 1)";       
       }
       $res = $this->query("SELECT `{$this->tbFields5['id']}`, `{$this->tbFields5['date_created']}`, `{$this->tbFields5['invoice_number']}`,`{$this->tbFields5['delivery_status']}`,`{$this->tbFields5['payment_status']}`, `{$this->tbFields5['total_price']}` FROM `{$this->orderTable}` WHERE `{$this->tbFields5['customer_id']}`=".$this->userID.$sql);
       if($res && mysql_num_rows($res) > 0){
          return $this->SqlToArray($res);
       }else return false;    
    }else{
      echo "You are not logged in!";
      return false;
    }
  }
  
    
  function getOrder($invoice_number = "", $order_id = "")
  {
    if($invoice_number != ""){
      $res = $this->query("SELECT * FROM `{$this->orderTable}` WHERE `{$this->tbFields5['invoice_number']}`='".$invoice_number."' LIMIT 1");  
    }else if($order_id != ""){
      $res = $this->query("SELECT * FROM `{$this->orderTable}` WHERE `{$this->tbFields5['id']}`='".$order_id."' LIMIT 1"); 
    }      
    if($res && mysql_num_rows($res)>0){
      $res = mysql_fetch_array($res);
      return $res;       
    }else{
      return false;
    }        
  }
  
  
  function getOrderProductsAttributes($order_products_id)
  {
    $res = $this->query("SELECT * FROM `{$this->order_products_attribTable}` WHERE `{$this->tbFields41['order_products_id']}`=".$order_products_id);
    if($res && mysql_num_rows($res) > 0){
      return $this->SqlToArray($res);
    }else
      return false;      
  }
  
  function getOrderProducts($order_id)
  {
    $res = $this->query("SELECT * FROM `{$this->order_productTable}` WHERE `{$this->tbFields4['order_id']}`=".$order_id);
    if($res && mysql_num_rows($res) > 0){
      return $this->SqlToArray($res);
    }else
      return false;    
  }
  
  function sendActivation($name, $activation_link, $email)
  {
      $our_email = WELCOME_EMAIL;
      
      //define the subject of the email
      $subject = "Welcome to ---!";
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
      echo "Dear ".$name.",\n\nThank you for registering at ---! \n\n";
      
      echo "Please activate your account within 30 days by clicking on the link or by copying and pasting the url below:\n";
      echo $activation_link."\n";
      
      echo "You can now take part in the various services we have to offer you. Some of these services include:\n\n";
      echo "Permanent Cart - Any products added to your online cart remain there until you remove them, or check them out.\n";
      echo "Address Book - We can now deliver your products to another address other than yours! This is perfect to send birthday gifts direct to the birthday-person themselves.\n";
      echo "Order History - View your history of purchases that you have made with us.\n\n";
      
      echo "For help with any of our online services, please email the store-owner: ----.\n\n";

      echo "Note: If you did not signup to be a member, please ignore this email or notify us at ----.";      
      echo "\n";
            
      echo "\n--PHP-alt-".$random_hash."\n"; 
      echo "Content-Type: text/html; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: 7bit\n";
      //HTML
      echo "\n<html><body>";
      echo "Dear ".$name.",<p>Thank you for registering at <a href='---' target='_blank'><b>---</b>!</a><p>";
      
      echo "Please activate your account <b>within 30 days</b> by clicking on the link or by copying and pasting the url below:<br>";
      echo "<a href='".$activation_link."' target='_blank'>".$activation_link."</a><p>";
      
      echo "You can now take part in the various services we have to offer you. Some of these services include:<p>";
      echo "<b>Permanent Cart</b> - Any products added to your online cart remain there until you remove them, or check them out.<br>";
      echo "<b>Address Book</b> - We can now deliver your products to another address other than yours! This is perfect to send birthday gifts direct to the birthday-person themselves.<br>";
      echo "<b>Order History</b> - View your history of purchases that you have made with us.<p>";
      
      echo "For help with any of our online services, please email the store-owner: ---.<p>";

      echo "Note: If you did not signup to be a member, please ignore this email or notify us at ---.<p>";        
      echo "</body></html>\n";
      echo "\n--PHP-alt-".$random_hash."--\n";

      //copy current buffer contents into $message variable and delete current output buffer
      $message = ob_get_clean();      
      //send the email
      $mail_sent = @mail( $email, $subject, $message, $headers );
      //if the message is sent successfully print "Mail sent". Otherwise print "Mail failed" 
      
      return $mail_sent;        
  
  }
  
  function mailOrder($invoice_number)
  {
    $our_email = INVOICE_EMAIL_ONE;
    $our_email2 = INVOICE_EMAIL_TWO;
    if($this->is_logged_in()){      
      $this->checkMemberships();    
    
      $email = $this->userData[$this->tbFields1["email"]];
        
      //define the subject of the email
      $subject = "Purchase from --- [Invoice:".$invoice_number."]";
      //create a boundary string, unique    
      $random_hash = md5(date('r', time()));
      //define the headers we want passed. Note that they are separated with \r\n
      $headers = "From: ".$our_email."\nReply-To: ".$our_email."\n";
      //$headers .= "cc: ".$our_email2.",".$our_email."\n";
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
      echo "You have placed the following order(s). We will get back to you within 24-48 hours.\n";
      echo "Thank you.\n";
      $this->printPlainOrder($invoice_number);
      echo "\n";

      echo "\n--PHP-alt-".$random_hash."\n"; 
      echo "Content-Type: text/html; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: 7bit\n";
      
      echo "\n<html><body>";
      echo "You have placed the following order(s). We will get back to you within 24-48 hours.<br>Thank you.<p>";

      $this->printOrder($invoice_number);
      
      echo "</body></html>\n";
      echo "\n--PHP-alt-".$random_hash."--\n";

      //copy current buffer contents into $message variable and delete current output buffer
      $message = ob_get_clean();      
      //send the email
      $mail_sent = @mail( $email, $subject, $message, $headers );
      //if the message is sent successfully print "Mail sent". Otherwise print "Mail failed" 
      
      return $mail_sent;      
      
    }else{
      echo "Illegal Access. Please contact administrator.";
      exit;
    }
  }
  
  function mailRental($invoice_number)
  {
    $our_email = INVOICE_EMAIL_ONE;
    $our_email2 = INVOICE_EMAIL_TWO;
    if($this->is_logged_in()){      
      $email = $this->userData[$this->tbFields1["email"]];
        
      //define the subject of the email
      $subject = "Rental from --- [Rental ID(s):".$invoice_number."...]";
      //create a boundary string, unique    
      $random_hash = md5(date('r', time()));
      //define the headers we want passed. Note that they are separated with \r\n
      $headers = "From: ".$our_email."\nReply-To: ".$our_email."\n";
      $headers .= "cc: ".$our_email2.",".$our_email."\n";
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
      echo "You have placed the following rental order(s). We will get back to you within 24-48 hours.\n";
      echo "Thank you.\n";
      $this->printPlainRental($invoice_number);
      echo "\n";

      echo "\n--PHP-alt-".$random_hash."\n"; 
      echo "Content-Type: text/html; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: 7bit\n";
      
      echo "\n<html><body>";
      echo "You have placed the following rental order(s). We will get back to you within 24-48 hours.<br>Thank you.<p>";

      $this->printRental($invoice_number);
      
      echo "</body></html>\n";
      echo "\n--PHP-alt-".$random_hash."--\n";

      //copy current buffer contents into $message variable and delete current output buffer
      $message = ob_get_clean();      
      //send the email
      $mail_sent = @mail( $email, $subject, $message, $headers );
      //if the message is sent successfully print "Mail sent". Otherwise print "Mail failed" 
      
      return $mail_sent;      
      
    }else{
      echo "Illegal Access. Please contact administrator.";
      exit;
    }
  }
  
  function printPlainOrder($invoice_number = "", $order_id ="")
  {
    $data = $this->getOrder($invoice_number, $order_id);
    echo "=============================================================\n";
    echo "Invoice Number: ".$data[$this->tbFields5['invoice_number']]."\nDate: ".$data[$this->tbFields5['date_created']]."\n\n";
    echo "Customer's Name: ".$this->getName($data[$this->tbFields5['customer_id']])." ".$this->getLastName($data[$this->tbFields5['customer_id']])." (".$data[$this->tbFields5['telephone']].")"."\nDelivery Details:\n    ".$data[$this->tbFields5['first_name']]." ".$data[$this->tbFields5['last_name']]."\n";
    
    if($data[$this->tbFields5['company']] != ""){
      echo "    ".$data[$this->tbFields5['company']]."\n";
    }
    
    echo "    ".$data[$this->tbFields5['address']]."\n";
    
    if($data[$this->tbFields5['city']] != ""){
      echo "    ".$data[$this->tbFields5['city']];
    }else{
      echo "    ".$this->getCountryName($data[$this->tbFields5['country']]); /// get country name
    }
    
    echo " ".$data[$this->tbFields5['postal']]."\n    ".$this->getCountryName($data[$this->tbFields5['country']])."\n\n";
    echo "=============================================================\n";
    echo "Items Bought:\n";
    echo "-------------\n\n";
    
    $subtotal = 0;
    if($products_data = $this->getOrderProducts($data[$this->tbFields5['id']])){      
      $i=0;
      foreach($products_data AS $product_data){      
        $i++;
        echo $i.". ".$product_data[$this->tbFields4['product_model']].", ".$product_data[$this->tbFields4['product_title']];
        
        if($product_attributes_datas = $this->getOrderProductsAttributes($product_data[$this->tbFields4['id']])){
          echo " (";
          $size_tmp = sizeof($product_attributes_datas);
          $j = 0;
          foreach($product_attributes_datas AS $product_attributes_data){
            $j++;
            echo $product_attributes_data[$this->tbFields41['options_name']];
            if($product_attributes_data[$this->tbFields41['options_values_name']] != ""){
              echo "-".$product_attributes_data[$this->tbFields41['options_values_name']];
            }
            if($j < $size_tmp) echo ", ";
          }
          echo ")";        
        }                        
        
        echo ", Qty: ".$product_data[$this->tbFields4['quantity']].", at $".$product_data[$this->tbFields4['price_per_unit']];
        
        if($product_attributes_datas = $this->getOrderProductsAttributes($product_data[$this->tbFields4['id']])){
          echo " (";          
          $j = 0;
          $attrib_price = 0;
          foreach($product_attributes_datas AS $product_attributes_data){
            $j++;
            if($product_attributes_data[$this->tbFields41['price']] >= 0) echo "+";
            echo "$".$product_attributes_data[$this->tbFields41['price']];
            if($j < $size_tmp) echo ", ";
            $attrib_price += $product_attributes_data[$this->tbFields41['price']];
          }
          echo ")";        
        }             
        
        echo " each.\n";      
  
        $subtotal += $product_data[$this->tbFields4['sub_total_price']] + ($attrib_price * $product_data[$this->tbFields4['quantity']]);
      }                    
    
    }    
    echo "=============================================================\n";        
    echo "Subtotal: $".number_format($subtotal,2)."\n";
    echo "Delivery by ".$data[$this->tbFields5['shipping_method']].": $".number_format($data[$this->tbFields5['shipping_cost']],2)."\n";
    //discount
    if($data[$this->tbFields5['member_discounts']] != 0){
      echo "Member Discount: ".$data[$this->tbFields5['member_discounts']]."%\n";
    }
    
    echo "Grand Total: $".number_format($data[$this->tbFields5['total_price']],2)."\n\n";
    
    
    echo "Payment Method: ".$data[$this->tbFields5['payment_type']]."\nPayment Status: ".$this->getStatusString($data[$this->tbFields5['payment_status']],"payment")."\n";    
    echo "Delivery Method: ".$data[$this->tbFields5['shipping_method']]."\nDelivery Status: ".$this->getStatusString($data[$this->tbFields5['delivery_status']],"delivery")."\n";
    echo "Comments: ".$data[$this->tbFields5['comments']]."\n";
    echo "=============================================================\n";         
  
  }
  
  
  function getRental($invoice_number = "")
  {
    if($invoice_number == "" && $this->is_logged_in()){
      $res = $this->query("SELECT * FROM `{$this->rentTable}` WHERE `{$this->tbFields6['customer_id']}`=".$this->userID." AND `{$this->tbFields6['delivery_status']}` <> 1 AND `{$this->tbFields6['delivery_status']}` <> -1  ORDER BY `{$this->tbFields6['invoice_number']}` DESC");
      if($res && mysql_num_rows($res) > 0){
        return $this->SqlToArray($res);
      }    
    }else{    
      $res = $this->query("SELECT * FROM `{$this->rentTable}` WHERE `{$this->tbFields6['invoice_number']}` LIKE '".$invoice_number."%' ORDER BY `{$this->tbFields6['invoice_number']}` ASC");
      if($res && mysql_num_rows($res) > 0){
        return $this->SqlToArray($res);
      }
    }
    return false;
  }
  
  function printPlainRental($invoice_number = "")
  {
    $data_full = $this->getRental($invoice_number);
    
    $data = $data_full[0];
    
      echo "=============================================================\n";
      echo "Date: ".$data[$this->tbFields6['date_created']]."\n\n";
      echo "Customer's Name: ".$this->getName($data[$this->tbFields6['customer_id']])." ".$this->getLastName($data[$this->tbFields6['customer_id']])." (".$data[$this->tbFields6['telephone']].")"."\nDelivery Details:\n    ".$data[$this->tbFields6['first_name']]." ".$data[$this->tbFields6['last_name']]."\n";
      
      if($data[$this->tbFields6['company']] != ""){
        echo "    ".$data[$this->tbFields6['company']]."\n";
      }
      
      echo "    ".$data[$this->tbFields6['address']]."\n";
      
      if($data[$this->tbFields6['city']] != ""){
        echo "    ".$data[$this->tbFields6['city']];
      }else{
        echo "    ".$this->getCountryName($data[$this->tbFields6['country']]); /// get country name
      }
      
      echo " ".$data[$this->tbFields6['postal']]."\n    ".$this->getCountryName($data[$this->tbFields6['country']])."\n\n";
      echo "=============================================================\n";
      echo "Items Rented:\n";
      echo "-------------\n\n";

    for($i=0; $i< sizeof($data_full);$i++){
      $product_data = $data_full[$i];
      echo $i.". ".$product_data[$this->tbFields6['invoice_number']]." -- ".$product_data[$this->tbFields6['product_model']].", ".$product_data[$this->tbFields6['product_title']]." at ".$product_data[$this->tbFields6['rental_points']]." RP\n";
    }    
    echo "=============================================================\n";        
   echo "Delivery Method: ".$data[$this->tbFields6['shipping_method']]."\nDelivery Status: ".$this->getStatusString($data[$this->tbFields6['delivery_status']],"rental")."\n";
    echo "Comments: ".$data[$this->tbFields6['comments']]."\n";
    echo "=============================================================\n";         
  
  }
  
  
  
  function printRental($invoice_number = "")
  {
    $data_full = $this->getRental($invoice_number);
    
    $data = $data_full[0];
    
    echo "<table style='background-color:#222222;border:2px solid #000000;width:580px;font-family:Arial,Verdana,Times New Roman;font-size:14px;line-height:25px;color:#000000;padding:0px;border-spacing:2px;width:580px;'>
<tr><td colspan=4 style='background-color:#AAAAFF;text-align:center;font-size:16px;'><b>Rental from ---</b></td></tr>";
    echo "<tr><td style='background-color:#DDDDDD;width:120px;text-align:left;'>Date</td>
<td style='background-color:#FFFFFF;width:170px;text-align:left;' colspan=3>".$data[$this->tbFields6['date_created']]."</td></tr>";
    echo "<tr><td style='background-color:#DDDDDD;text-align:left;'>Customer's Name</td><td style='background-color:#FFFFFF;width:170px;text-align:left;'>".$this->getName($data[$this->tbFields6['customer_id']])." ".$this->getLastName($data[$this->tbFields6['customer_id']])."<br>(".$data[$this->tbFields6['telephone']].")</td><td style='background-color:#DDDDDD;width:80px;text-align:left;'>Delivery Details</td><td style='background-color:#FFFFFF;width:140px;text-align:left;'>".$data[$this->tbFields6['first_name']]." ".$data[$this->tbFields6['last_name']]."<br>";
    
    if($data[$this->tbFields6['company']] != ""){
      echo $data[$this->tbFields6['company']]."<br>";
    }
    
    echo $data[$this->tbFields6['address']]."<br>";
    
    if($data[$this->tbFields6['city']] != ""){
      echo $data[$this->tbFields6['city']];
    }else{
      echo $this->getCountryName($data[$this->tbFields6['country']]); /// get country name
    }
    
    echo " ".$data[$this->tbFields6['postal']]."<br>".$this->getCountryName($data[$this->tbFields6['country']])."</td></tr>";
    echo "<tr><td colspan=4 style='background-color:#AAAAAA;height:15px;font-size:1px;line-height:5px;'>&nbsp;</td></tr><tr><td colspan=4 style='background-color:#FFFFFF;'><table style='background-color:#111177;font-family:Arial,Verdana,Times New Roman;font-size:14px;line-height:25px;color:#000000;padding:0px;border-spacing:2px;'><tr><td style='background-color:#DDDDDD;width:20px;'>&nbsp;</td><td style='background-color:#DDDDDD;width:120px;'><b>RENTAL ID</b></td><td style='background-color:#DDDDDD;width:100px;'><b>MODEL</b></td><td style='background-color:#DDDDDD;width:280px;'><b>ITEM DESCRIPTION</b></td><td style='background-color:#DDDDDD;width:40px;'><b>RP</b></td></tr>";
   
    for($i=0; $i< sizeof($data_full);$i++){
      $product_data = $data_full[$i];
      echo "<tr style='background-color:#FFFFFF;'><td>".($i+1)."</td><td>".$product_data[$this->tbFields6['invoice_number']]."</td><td>".$product_data[$this->tbFields6['product_model']]."</td><td style='text-align:left;'>".$product_data[$this->tbFields6['product_title']]."</td><td>".$product_data[$this->tbFields6['rental_points']]."</td></tr>";           
    }
    
    echo "</table></td></tr><tr><td colspan=4 style='background-color:#AAAAAA;height:15px;font-size:1px;line-height:5px;'>&nbsp;</td></tr>";
    echo "<tr><td style='background-color:#DDDDDD;text-align:left;'>Delivery Method</td><td style='background-color:#FFFFFF;text-align:left;'>".$data[$this->tbFields6['shipping_method']]."</td><td style='background-color:#DDDDDD;text-align:left;'>Delivery Status</td><td style='background-color:#FFFFFF;text-align:left;'>".$this->getStatusString($data[$this->tbFields6['delivery_status']],"rental")."</td></tr>";
    echo "<tr><td colspan=4 style='background-color:#AAAAAA;height:15px;font-size:1px;line-height:5px;'>&nbsp;</td></tr>";
    echo "<tr><td style='background-color:#DDDDDD;text-align:left;'>Comments</td><td colspan=3 style='background-color:#FFFFFF;text-align:left;'>".$data[$this->tbFields6['comments']]."</td>";
    echo "</table>";
      
  }
  
  function printOrder($invoice_number = "", $order_id = "")
  {
    $data = $this->getOrder($invoice_number, $order_id);
    
    echo "<table style='background-color:#222222;border:2px solid #000000;width:580px;font-family:Arial,Verdana,Times New Roman;font-size:14px;line-height:25px;color:#000000;padding:0px;border-spacing:2px;width:580px;'>
<tr><td colspan=4 style='background-color:#CCCCFF;text-align:center;font-size:16px;'><b>Purchase from ---</b></td></tr>";
    echo "<tr><td style='background-color:#DDDDDD;width:120px;text-align:left;'>Invoice Number</td>
<td style='background-color:#FFFFFF;width:170px;text-align:left;'>".$data[$this->tbFields5['invoice_number']]."</td>
<td style='background-color:#DDDDDD;width:80px;text-align:left;'>Date</td><td style='background-color:#FFFFFF;text-align:left;width:140px'>".$data[$this->tbFields5['date_created']]."</td></tr>";
    echo "<tr><td style='background-color:#DDDDDD;text-align:left;'>Customer's Name</td><td style='background-color:#FFFFFF;text-align:left;'>".$this->getName($data[$this->tbFields5['customer_id']])." ".$this->getLastName($data[$this->tbFields5['customer_id']])."<br>(".$data[$this->tbFields5['telephone']].")</td><td style='background-color:#DDDDDD;text-align:left;'>Delivery Details</td><td style='background-color:#FFFFFF;text-align:left;'>".$data[$this->tbFields5['first_name']]." ".$data[$this->tbFields5['last_name']]."<br>";
    
    if($data[$this->tbFields5['company']] != ""){
      echo $data[$this->tbFields5['company']]."<br>";
    }
    
    echo $data[$this->tbFields5['address']]."<br>";
    
    if($data[$this->tbFields5['city']] != ""){
      echo $data[$this->tbFields5['city']];
    }else{
      echo $this->getCountryName($data[$this->tbFields5['country']]); /// get country name
    }
    
    echo " ".$data[$this->tbFields5['postal']]."<br>".$this->getCountryName($data[$this->tbFields5['country']])."</td></tr>";
    
    echo "<tr><td colspan=4 style='background-color:#AAAAAA;height:15px;font-size:1px;line-height:5px;'>&nbsp;</td></tr><tr><td colspan=4 style='background-color:#FFFFFF;'><table style='background-color:#111177;font-family:Arial,Verdana,Times New Roman;font-size:14px;line-height:25px;color:#000000;padding:0px;border-spacing:2px;'><tr><td style='background-color:#DDDDDD;width:20px;'>&nbsp;</td><td style='background-color:#DDDDDD;width:100px;'><b>MODEL</b></td><td style='background-color:#DDDDDD;width:280px;'><b>ITEM DESCRIPTION</b></td><td style='background-color:#DDDDDD;width:40px;'><b>QTY</b></td><td style='background-color:#DDDDDD;width:120px;'><b>PRICE PER UNIT</b></td></tr>";
    $subtotal = 0;
    if($products_data = $this->getOrderProducts($data[$this->tbFields5['id']])){      
      $i=0;
      foreach($products_data AS $product_data){      
        $i++;
        echo "<tr style='background-color:#FFFFFF;'><td>".$i."</td><td>".$product_data[$this->tbFields4['product_model']]."</td><td style='text-align:left;'>".$product_data[$this->tbFields4['product_title']];
        
        if($product_attributes_datas = $this->getOrderProductsAttributes($product_data[$this->tbFields4['id']])){
          echo " (";
          $size_tmp = sizeof($product_attributes_datas);
          $j = 0;
          foreach($product_attributes_datas AS $product_attributes_data){
            $j++;
            echo $product_attributes_data[$this->tbFields41['options_name']];
            if($product_attributes_data[$this->tbFields41['options_values_name']] != ""){
              echo "-".$product_attributes_data[$this->tbFields41['options_values_name']];
            }
            if($j < $size_tmp) echo ", ";
          }
          echo ")";        
        }                        
        
        echo "</td><td>".$product_data[$this->tbFields4['quantity']]."</td><td>$".$product_data[$this->tbFields4['price_per_unit']];
        $attrib_price = 0;
        if($product_attributes_datas = $this->getOrderProductsAttributes($product_data[$this->tbFields4['id']])){
          echo " (";          
          $j = 0;
          //$attrib_price = 0;
          foreach($product_attributes_datas AS $product_attributes_data){
            $j++;
            if($product_attributes_data[$this->tbFields41['price']] >= 0) echo "+";
            echo "$".$product_attributes_data[$this->tbFields41['price']];
            if($j < $size_tmp) echo ", ";
            $attrib_price += $product_attributes_data[$this->tbFields41['price']];
          }
          echo ")";        
        }             
        
        echo "</td></tr>";      
  
        $subtotal += $product_data[$this->tbFields4['sub_total_price']] + ($attrib_price * $product_data[$this->tbFields4['quantity']]);
      }                    
    
    }    
        
    echo "<tr><td colspan=5 style='background-color:#aaaaaa;height:7px;font-size:1px;line-height:5px;'>&nbsp;</td></tr>";
    echo "<tr style='background-color:#FFFFFF;'><td></td><td colspan=3 style='text-align:right;padding:0px 15px 0px 0px'>Subtotal</td><td>$".number_format($subtotal,2)."</td></tr>";
    echo "<tr style='background-color:#FFFFFF;'><td></td><td colspan=3 style='text-align:right;padding:0px 15px 0px 0px'>Delivery by ".$data[$this->tbFields5['shipping_method']]."</td><td>$".number_format($data[$this->tbFields5['shipping_cost']],2)."</td></tr>";
    //discount
    if($data[$this->tbFields5['member_discounts']] != 0){
      echo "<tr style='background-color:#FFFFFF;'><td></td><td colspan=3 style='text-align:right;padding:0px 15px 0px 0px'>Member Discount</td><td>".$data[$this->tbFields5['member_discounts']]."%</td></tr>";
    }
    
    echo "<tr style='background-color:#FFFFFF;'><td></td><td colspan=3 style='text-align:right;padding:0px 15px 0px 0px'><b>Grand Total</b></td><td>$".number_format($data[$this->tbFields5['total_price']],2)."</td></tr>";
    echo "</table></td></tr><tr><td colspan=4 style='background-color:#AAAAAA;height:15px;font-size:1px;line-height:5px;'>&nbsp;</td></tr>";
    echo "<tr><td style='background-color:#DDDDDD;text-align:left;'>Payment Method</td><td style='background-color:#FFFFFF;text-align:left;'>".$data[$this->tbFields5['payment_type']]."</td><td style='background-color:#DDDDDD;text-align:left;'>Payment Status</td><td style='background-color:#FFFFFF;text-align:left;'>".$this->getStatusString($data[$this->tbFields5['payment_status']],"payment")."</td></tr>";
    echo "<tr><td colspan=4 style='background-color:#AAAAAA;height:15px;font-size:1px;line-height:5px;'>&nbsp;</td></tr>";
    echo "<tr><td style='background-color:#DDDDDD;text-align:left;'>Delivery Method</td><td style='background-color:#FFFFFF;text-align:left;'>".$data[$this->tbFields5['shipping_method']]."</td><td style='background-color:#DDDDDD;text-align:left;'>Delivery Status</td><td style='background-color:#FFFFFF;text-align:left;'>".$this->getStatusString($data[$this->tbFields5['delivery_status']],"delivery")."</td></tr>";
    echo "<tr><td colspan=4 style='background-color:#AAAAAA;height:15px;font-size:1px;line-height:5px;'>&nbsp;</td></tr>";
    echo "<tr><td style='background-color:#DDDDDD;text-align:left;'>Comments</td><td colspan=3 style='background-color:#FFFFFF;text-align:left;'>".$data[$this->tbFields5['comments']]."</td>";
    echo "</table>";
          
  }
  
  function getStatusString($status, $type = "payment")
  {
    if($type == "payment"){
      switch($status){
        case -1:
          return "Not Paid (Reason Unknown)";
          break;
        case 0:
          return "Not Paid";
          break;
        case 1:
          return "Paid";
          break;
        case 2:
          return "Pending";
          break;
          default;
          return false;
          break;
      }
    
    }else if($type == "delivery"){
      switch($status){
        case 1:
          return "Delivered";
          break;
        case 2:
          return "In Process";
          break;
        case -1:
          return "Not Delivered (Reason Unknown)";
          break;
        case 0:
          return "Not Delivered";
          break;
          default;
          return false;
          break;                
      }
    
    }else if($type == "rental"){
      switch($status){
        case 1:
          return "Returned";
          break;
        case 2:
          return "Request Return";
          break;
        case 3:
          return "Rented";
          break;
        case -1:
          return "Cancelled";
          break;
        case 0:
        default;
          return "Rented (Delivering)";
          break;
      
      }    
    }
  }
  
  //member functions
    
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
  
}

?>