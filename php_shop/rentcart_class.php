<?php
/**
* PHP class - rentcartAccess
*
* For RENTAL Cart, Wish List related function (Either database cart for logged in, or cookie cart for anonymous)
*
*
* Copyright: 1st May 2009, Ho Wen Yang
*
*
**/

class rentcartAccess{
  
  var $dbName = DB_DATABASE;   // name of database
  
	var $cartTable = "rent_cart";    // name of table containing login info
  var $tbFields = array(       // fields of $userTable
    'id'=>'ID',
    'customer_id'=> 'CUSTOMER_ID',   	
  	'product_id'  => 'PRODUCT_ID',
  	'quantity' => 'QUANTITY',
  	'wishlist' => 'WISHLIST',
  	'date_wished' => 'DATE_WISHED'
  );  
  
  var $productTable = "product";    // name of table containing product info
  var $tbFields2 = array(       // fields of $productTable
    'id'=>'ID',
    'title'=> 'TITLE',   	
  	'description'  => 'DESCRIPTION',
  	'model' => 'MODEL',
  	'min_players' => 'MIN_PLAYERS',
  	'max_players' => 'MAX_PLAYERS',
  	'year' => 'YEAR',
  	'manufacturer_id' => 'MANUFACTURER_ID',
  	'picture_path' => 'PICTURE_PATH',
  	'price' => 'PRICE',
  	'quantity' => 'QUANTITY',
  	'weight' => 'WEIGHT',
  	'discount_flag' => 'DISCOUNT_FLAG',
  	'discount_price' => 'DISCOUNT_PRICE',
  	'date_avail' => 'DATE_AVAIL',
  	'date_added' => 'DATE_ADDED',
  	'new_flag' => 'NEW_FLAG',
  	'status' => 'STATUS',
  	'rental_quantity' => 'RENTAL_QUANTITY',
  	'rental_flag' => 'RENTAL_FLAG',
  	'rental_points' => 'RENTAL_POINTS',
  	'products_rented' => 'PRODUCTS_RENTED',
  	'products_sold' => 'PRODUCTS_SOLD'  		
  );  
 
  
  var $orderTable = "rented";
  var $tbFields3 = array(
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
  
  var $shippingTable = "shipping_options";
  var $tbFields5 = array(
      'id' => 'ID',
      'zone_id' => 'ZONE_ID',
      'shipping_method' => 'SHIPPING_METHOD',
      'cost' => 'COST',
      'min' => 'MIN',
      'max' => 'MAX'
  );
  
  var $dataFields = array(              // fields of $data to be passed in to add into database
    'customer_id' => 'customer_id',
   	'product_id'  => 'PRODUCT_ID',
  	'quantity' => 'QUANTITY',
  	'wishlist' => 'WISHLIST'
  );   
              
  var $cookieName = "boardrentcart";
  var $cookieLife = 2592000; // 1 month of cookie duration (in seconds) = 60*60*24*30
  var $cookieCartData = array();      // contains the Cart Items stored in Cookie
  var $available = 1; //default false;
  
  var $userID;                 // userID of user to be initialized
  var $dbConn;                 // database connection handler

  /**
  * To be called as a constructor - setup and initialization
  **/
  function rentcartAccess($userID = '') 
  {
    $this->userID = $userID;

    //setup database connector
    $this->dbConn = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
    if(!$this->dbConn) die(mysql_error($this->dbConn));
    mysql_select_db($this->dbName, $this->dbConn) or die(mysql_error($this->dbConn));            
    
    
    if(isset($_COOKIE[$this->cookieName])){ // retrieve cookie if any
      $this->cookieCartData = $this->readCartCookie();
      
      if($this->is_logged_in()){           // if logged in, move cookie to cart
        $a = sizeof($this->cookieCartData);
        for ($i=0; $i < $a; $i++){
          $this->addtoCart($this->cookieCartData[$i]);
        }                
        $this->cookieCartData = "";
        $this->cookieCartData = array();  // erase cookieCartData
        $this->writeCartCookie($this->cookieCartData);
      }                          
      
    }else{
      if(!$this->is_logged_in()){
        //create or renew cookie
        $this->writeCartCookie($this->cookieCartData);
      }
    }    
  }    
  
  
  /**
  * Is the user logged in? Return Boolean.
  **/  
  function is_logged_in()  
  {
    return (empty($this->userID) || $this->userID == "") ? false : true;
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
  * Add to renting cart
  **/  
  function addtoCart($product_id)
  {
  
    if($this->is_logged_in()){  // if logged in, put into database cart
        $res = $this->query("INSERT INTO `{$this->cartTable}` (`{$this->tbFields['customer_id']}`, `{$this->tbFields['product_id']}`) VALUES ('".$this->userID."', ".$product_id.")");   
        $tmp_id = mysql_insert_id($this->dbConn);           
    }else{
       
       array_push($this->cookieCartData, $product_id);
      //create or renew cookie
      $this->writeCartCookie($this->cookieCartData);    
    }
  }    
    
  
  function alreadyinCart($product_id, $attrib_id)//ABANDONED
  {
    $res = -1;
    if($this->is_logged_in()){
      if($attrib_id != ""){
        $attrib_str = "";        
        foreach($attrib_id AS $attrib_single){
          $attrib_str = $attrib_str." AND `{$this->tbFields["id"]}` IN (SELECT `{$this->tbFields7['cart_id']}` FROM `{$this->cart_attribTable}` WHERE `{$this->tbFields7['attributes_id']}`=".$attrib_single.")";          
        }      
        $res = $this->query("SELECT `{$this->tbFields["id"]}` AS `ID` FROM `{$this->cartTable}` WHERE `{$this->tbFields["product_id"]}`=".$product_id."  AND `{$this->tbFields["customer_id"]}`='".$this->userID."'".$attrib_str." LIMIT 1");    
            
      }else{
        $res = $this->query("SELECT `{$this->tbFields["id"]}` AS `ID` FROM `{$this->cartTable}` WHERE `{$this->tbFields["product_id"]}`=".$product_id."  AND `{$this->tbFields["customer_id"]}`='".$this->userID."' AND `{$this->tbFields["id"]}` NOT IN (SELECT `{$this->tbFields7['cart_id']}` FROM `{$this->cart_attribTable}`) LIMIT 1");   
      }
      if($res && mysql_num_rows($res) > 0){
        $data = mysql_fetch_array($res);
        $res = $data["ID"];
      }else{
        $res = -1;
      }
    }else{    
      $a = sizeof($this->cookieCartData);            
      for ($i=0; $i < $a; $i=$i+3){
        if($this->cookieCartData[$i] == $product_id){          
          if($attrib_id != "" && $this->cookieCartData[$i+2]!=""){
            $tmp1 = explode("/,/", $this->cookieCartData[$i+2]);
            $found = 0;
            foreach($attrib_id AS $attrib_single){
              if(in_array($attrib_single, $tmp1)) $found++;
            }
            if($found == sizeof($tmp1)){
              $res = $i;          
              $i = $a;
            }
          }else if($attrib_id == "" && $this->cookieCartData[$i+2] == ""){
            $res = $i;          
            $i = $a;
          }
        }
      }                          
    }
    return $res;
  }
  
  
  function saveforLater($id)
  {
    if($this->is_logged_in()){
      $res = $this->query("UPDATE `{$this->cartTable}` SET `{$this->tbFields["wishlist"]}`=1 WHERE `{$this->tbFields["id"]}`=".$id." LIMIT 1");
    }else{
      echo "<script>alert('You must be logged in to use this function.');</script>";
    }  
  }
  
  function movetoCart($id)
  {
    if($this->is_logged_in()){
      $res = $this->query("UPDATE `{$this->cartTable}` SET `{$this->tbFields["wishlist"]}`=0 WHERE `{$this->tbFields["id"]}`=".$id." LIMIT 1");
    }else{
      echo "<script>alert('You must be logged in to use this function.');</script>";
    }  
  }
  
  
  /**
  * Read Shopping Cart Cookie
  **/
  function readCartCookie()
  {
    return unserialize(base64_decode($_COOKIE[$this->cookieName])); // unserialize cookie    
  }
  
  /**
  * Write Shopping Cart Cookie
  **/
  function writeCartCookie($data)
  {
    $cookieInfo = base64_encode(serialize($data)); 
    $a = setcookie($this->cookieName, $cookieInfo, time() + $this->cookieLife);
  }
  
  function deletefromCart($id)  
  {  
    if($this->is_logged_in()){
      $res = $this->query("DELETE FROM `{$this->cartTable}` WHERE `{$this->tbFields["id"]}`=".$id);   
    }else{    
        unset($this->cookieCartData[$id]);
        $this->cookieCartData = array_values($this->cookieCartData); 
        $this->writeCartCookie($this->cookieCartData);       
        $res = true;  
    }
    
    return $res;
  }
   
  function getCartNum()
  {
    if($this->is_logged_in()){
      $res = $this->query("SELECT COUNT(`{$this->tbFields["id"]}`) AS `COUNT` FROM `{$this->cartTable}` WHERE `{$this->tbFields["customer_id"]}`='".$this->userID."'");
      if($res && mysql_num_rows($res) > 0){      
        $data = mysql_fetch_array($res);
        return $data["COUNT"];
      }else
        return 0;    
    }else{
      if($this->cookieCartData == ""){
        return 0;
      }else{
        return (sizeof($this->cookieCartData));
      }
    }  
  }
  
  function getWish()
  {
    if($this->is_logged_in()){
      $res = $this->query("SELECT * FROM `{$this->cartTable}` WHERE `{$this->tbFields['customer_id']}`='".$this->userID."' AND `{$this->tbFields['wishlist']}`=1");   
      $res = $this->SqlToArray($res);
    }else{
      $res = "";
    }    
    return $res;  
  }
  
  
  function getCart()
  {
    if($this->is_logged_in()){
      $res = $this->query("SELECT * FROM `{$this->cartTable}` WHERE `{$this->tbFields['customer_id']}`='".$this->userID."' AND `{$this->tbFields['wishlist']}`=0");   
      $res = $this->SqlToArray($res);
    }else{
      $res = $this->cookieCartData;
    }    
    return $res;  
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
  
  function getProductModel($productID)
  {
    $res = $this->query("SELECT `{$this->tbFields2["model"]}` AS `MODEL` FROM `{$this->productTable}` WHERE `{$this->tbFields2["id"]}` = ".$productID." LIMIT 1");
    
    if($res && mysql_num_rows($res) > 0){
      $data = mysql_fetch_array($res);  
      return $data['MODEL'];
    }else{
      return "";
      
    }    
  }
  
  function getProductTitle($productID)
  {
    $res = $this->query("SELECT `{$this->tbFields2["title"]}` AS `TITLE` FROM `{$this->productTable}` WHERE `{$this->tbFields2["id"]}` = ".$productID." LIMIT 1");
    
    if($res && mysql_num_rows($res) > 0){
      $data = mysql_fetch_array($res);  
      return $data['TITLE'];
    }else{
      return "";
      
    }    
  }
      // -1 = Not Available, 0 = Out on Loan, 1 = Rent Now
  function getProductStatus($productID)
  {
    $data = '';
    $res = $this->query("SELECT `{$this->tbFields2["rental_quantity"]}`, `{$this->tbFields2["rental_flag"]}` FROM `{$this->productTable}` WHERE `{$this->tbFields2["id"]}` = ".$productID." LIMIT 1");
    if($res && mysql_num_rows($res) > 0){                                                             
      $data = mysql_fetch_array($res);     
      
      if($data[$this->tbFields2["rental_flag"]] == 0){
        return -1;
      }else if($data[$this->tbFields2["rental_quantity"]] > 0){
        if(($data[$this->tbFields2["rental_quantity"]] - $this->getNumInCart($productID)) >= 0)
          return 1;
        else
          return 300 + $data[$this->tbFields2["rental_quantity"]];
              
      }else{
        return 0;
      }           
    }
    return -1;
  }
  function isBefore($datetime)
  {
    $res = $this->query("SELECT TIMESTAMPDIFF(MINUTE,CURRENT_TIMESTAMP,'".$datetime."') AS `diff`");         
    if($res[0]["diff"] > 0){
      return false;
    }else
      return true;
  }
  
  function getNumInCart($productID){
    if($this->is_logged_in()){
      $res = $this->query("SELECT COUNT(`{$this->tbFields["id"]}`) AS `SUM` FROM `{$this->cartTable}` WHERE `{$this->tbFields["customer_id"]}`='".$this->userID."' AND `{$this->tbFields["wishlist"]}`=0 AND `{$this->tbFields["product_id"]}`=".$productID);
      
      if($res && mysql_num_rows($res) > 0){                                                             
        $data = mysql_fetch_array($res);  
        return $data['SUM'];
      }else{
        return 0;
      }
    }else{
      $a = sizeof($this->cookieCartData);       
      $q_tmp = 0;     
      for ($i=0; $i < $a; $i++){
        if($this->cookieCartData[$i] == $productId){          
          $q_tmp++;
        }
      }
      return $q_tmp;                                
    
    }
  }
  
  function startcheckAvail(){
    $this->available = 0;
  }
  
  function isAvail(){
    if($this->available == 0)
      return true;
    else
      return false;
  }
  
  function cartView($productID, $id)
  {
    $data = '';
    $res = $this->query("SELECT `{$this->tbFields2["id"]}`, `{$this->tbFields2["title"]}`, `{$this->tbFields2["rental_points"]}`, `{$this->tbFields2["rental_quantity"]}` FROM `{$this->productTable}` WHERE `{$this->tbFields2["id"]}` = ".$productID." LIMIT 1");

    if($res && mysql_num_rows($res) > 0){
      $data = mysql_fetch_array($res);    
      echo "<tr><td width='110px'><a href='?p=rentcart&derentcartthis=".$id."'>delete</a><br>";
      echo "<a href='?p=rentcart&rentwaitthis=".$id."'>save for later</a></td>";  
      echo "<td><a href='?p=search&t=detailed&p_id=".$data[$this->tbFields2["id"]]."'>".$data[$this->tbFields2["title"]]."</a>";      

      switch ($tmp_stat = $this->getProductStatus($productID)){
        case 0:
          echo "<p><span class='notes1'>*Out on Loan</span>";
          $this->available++;
          break;
        case 1:          
          break;
        case -1:
        default:
          $this->available++;
          if($tmp_stat > 300){
            echo "<p><span class='notes1'>*Only ".($tmp_stat-300)." left in stock</span>";
          }else
            echo "<p><span class='notes1'>*Not Available</span>";
          break;                      
      }
      echo "</td>";      
      echo "<td width='80px'>";
            // normal
      echo $data[$this->tbFields2["rental_points"]]." RP";
                
      echo "</td></tr>";
      return $data[$this->tbFields2["rental_points"]];
    }    
    return 0;
  }
  
  function wishView($productID, $id)
  {
    $data = '';
    $res = $this->query("SELECT `{$this->tbFields2["id"]}`, `{$this->tbFields2["title"]}`, `{$this->tbFields2["rental_points"]}`, `{$this->tbFields2["rental_quantity"]}` FROM `{$this->productTable}` WHERE `{$this->tbFields2["id"]}` = ".$productID." LIMIT 1");

    
    if($res && mysql_num_rows($res) > 0){
      $data = mysql_fetch_array($res);    
      echo "<tr><td width='110px'><a href='?p=rentcart&derentcartthis=".$id."'>delete</a><br>";
      echo "<a href='?p=rentcart&rentunwaitthis=".$id."'>move to cart</a></td>";  
      echo "<td><a href='?p=search&t=detailed&p_id=".$data[$this->tbFields2["id"]]."'>".$data[$this->tbFields2["title"]]."</a>";

      echo "</td>";      
      echo "<td width='80px'>";
             // normal
      echo $data[$this->tbFields2["rental_points"]]." RP";
                
      echo "</td></tr>";
    }    
  }
  
  //TILL HERE
  
  function confirmView($productID, $id)
  {
    $data = '';
    $res = $this->query("SELECT `{$this->tbFields2["id"]}`, `{$this->tbFields2["title"]}`, `{$this->tbFields2["rental_points"]}`, `{$this->tbFields2["rental_quantity"]}`FROM `{$this->productTable}` WHERE `{$this->tbFields2["id"]}` = ".$productID." LIMIT 1");

    if($res && mysql_num_rows($res) > 0){
      $data = mysql_fetch_array($res);    
      echo "<tr><td><a href='?p=search&t=detailed&p_id=".$data[$this->tbFields2["id"]]."' target='_blank'>".$data[$this->tbFields2["title"]]."</a>";

      switch ($tmp_stat = $this->getProductStatus($productID)){
        case 0:
          echo "<p><span class='notes1'>*Out on Loan</span>";
          break;
        case 1:          
          break;
        case -1:
        default:
          if($tmp_stat > 300){
            echo "<p><span class='notes1'>*Only ".($tmp_stat-300)." left in stock</span>";
          }else
            echo "<p><span class='notes1'>*Not Available</span>";
          break;                      
      }
      echo "</td>"; 
      echo "<td width='80px'>";
      
            // normal
      echo $data[$this->tbFields2["rental_points"]]." RP";
                
      echo "</td></tr>";
      return $data[$this->tbFields2["rental_points"]];
    }    
    return 0;
  }
  
  
  function minusQty($id, $qty)
  {
    if($qty > 0){
      $res = $this->query("SELECT `{$this->tbFields2['rental_quantity']}` AS `QTY`, `{$this->tbFields2["products_rented"]}` AS `SOLD` FROM `{$this->productTable}` WHERE `{$this->tbFields2['id']}`=".$id." LIMIT 1");      
      if($res && mysql_num_rows($res) > 0){
        $data = mysql_fetch_array($res);
        $orig_qty = $data['QTY'];
        $orig_sold = $data['SOLD'];
      }else return false;
    
      
        $new_qty = $orig_qty - $qty;
        $new_sold = $orig_sold + $qty;
        if($new_qty < 0) return false;
        $res = $this->query("UPDATE `{$this->productTable}` SET `{$this->tbFields2["rental_quantity"]}`=".$new_qty.",  `{$this->tbFields2["products_rented"]}`=".$new_sold." WHERE `{$this->tbFields2["id"]}`=".$id." LIMIT 1");
        if($res){          
          return true;
        }else
          return false;            
      
    }else{
      return false;
    }
  }
  
  function addQty($id, $qty)
  {
    if($qty > 0){
      $res = $this->query("SELECT `{$this->tbFields2['rental_quantity']}` AS `QTY` FROM `{$this->productTable}` WHERE `{$this->tbFields2['id']}`=".$id." LIMIT 1");      
      if($res && mysql_num_rows($res) > 0){
        $data = mysql_fetch_array($res);
        $orig_qty = $data['QTY'];
      }else return false;
        $new_qty = $orig_qty + $qty;
        if($new_qty < 0) return false;
        $res = $this->query("UPDATE `{$this->productTable}` SET `{$this->tbFields2["rental_quantity"]}`=".$new_qty." WHERE `{$this->tbFields2["id"]}`=".$id." LIMIT 1");
        if($res){          
          return true;
        }else
          return false;            
      
    }else{
      return false;
    }
  }
  
  function fetchPrice($id)
  {
    $res = $this->query("SELECT `{$this->tbFields2["rental_points"]}` FROM `{$this->productTable}` WHERE `{$this->tbFields2["id"]}` = ".$id." LIMIT 1");
    
    if($res && mysql_num_rows($res) > 0){
      $data = mysql_fetch_array($res);
      return $data[$this->tbFields2["rental_points"]];      
    }else
      return false;
      
  }
  
  
  function getInvoiceNum($num="")
  {
    if($this->is_logged_in()){
      $invoice = "BGLS_C".$this->userID.$num;
      $res = $this->query("SELECT CURRENT_DATE AS `DATE`");
      if($res && mysql_num_rows($res)>0){
        $res = mysql_fetch_array($res);
        $date_tmp = $res['DATE'];        
        $invoice = $invoice.substr($date_tmp,-2,2).substr($date_tmp,5,2).substr($date_tmp,2,2);
      }else{
        $invoice = $invoice."E00000";
      }
      
      $invoice = $invoice."_R";      
      //echo $invoice;
      $res = $this->query("SELECT `{$this->tbFields3['invoice_number']}` AS `INVNUM` FROM `{$this->orderTable}` WHERE `{$this->tbFields3['invoice_number']}` LIKE '".$invoice."%' ORDER BY `{$this->tbFields3['id']}` DESC LIMIT 1");
      if($res && mysql_num_rows($res) > 0){
        $res = mysql_fetch_array($res);
        $invoice_prev = $res['INVNUM'];
        $num_tmp = 1 + substr($invoice_prev, strlen($invoice),1);
        $num_tmp = $num_tmp + "";
        $invoice = $invoice.$num_tmp;
      }else{      
        $invoice = $invoice."1";
      }            
      
      return $invoice;
        
    }else{
      echo "Fatal Error. Not Logged In.";
      return -1;
    }
    
  }
  
  function orderExist($invoice_number)
  {
    $res = $this->query("SELECT `{$this->tbFields3['invoice_number']}` FROM `{$this->orderTable}` WHERE `{$this->tbFields3['invoice_number']}`='".$invoice_number."' LIMIT 1");
    
    if($res && mysql_num_rows($res) > 0){
      return true;
    }else
      return false;
  }
  
  function requestReturn($invoice_number)
  {
    if($this->is_logged_in()){
      if($this->orderExist($invoice_number)){
        if($this->setDeliveryStatus($invoice_number,2)){
          $res = $this->query("SELECT `{$this->tbFields3['product_id']}` AS `PID`, `{$this->tbFields3['rental_points']}` AS `RP` FROM `{$this->orderTable}` WHERE `{$this->tbFields3['invoice_number']}`='".$invoice_number."' LIMIT 1");
          if($res && mysql_num_rows($res) > 0){
            $res = mysql_fetch_array($res);
            $this->addQty($res['PID'],1);
            return $res['RP']; 
          }
        }      
      }
    }
    return false;  
  }
  
  
  function placeOrder($invoice_number, $first_name, $last_name, $telephone, $company, $address, $postal, $city, $country, $total_price, $shipping_method, $delivery_status = 0, $comments)
  {
    if($this->is_logged_in()){      
      
      if($this->orderExist($invoice_number)){
        echo "Duplicate Transaction! Please do not refresh or use the Back or Forward buttons.";
        exit;
      }
       
      $date_created = "CURRENT_TIMESTAMP";
 
      $orders = $this->getCart();
      // START TRANSACTION
      $res = $this->query("START TRANSACTION");
      // ADD GENERAL ORDER INFO
      
      //echo "Add order<br>";
      
      $start_str = $invoice_number;
      $end_num = 1;
      
      foreach($orders AS $order){
        $price_per_unit = $this->fetchPrice($order[$this->tbFields['product_id']]);
        $productModelTmp = mysql_real_escape_string($this->getProductModel($order[$this->tbFields['product_id']]));
        $productNameTmp = mysql_real_escape_string($this->getProductTitle($order[$this->tbFields['product_id']]));
        
        
        //place order             
        $res = $this->query("INSERT INTO `{$this->orderTable}` (`{$this->tbFields3['customer_id']}`, `{$this->tbFields3['invoice_number']}`, `{$this->tbFields3['date_created']}`, `{$this->tbFields3['first_name']}`, `{$this->tbFields3['last_name']}`, `{$this->tbFields3['telephone']}`, `{$this->tbFields3['company']}`, `{$this->tbFields3['address']}`, `{$this->tbFields3['postal']}`, `{$this->tbFields3['city']}`, `{$this->tbFields3['country']}`, `{$this->tbFields3['product_id']}`, `{$this->tbFields3['product_model']}`, `{$this->tbFields3['product_title']}`, `{$this->tbFields3['rental_points']}`, `{$this->tbFields3['shipping_method']}`, `{$this->tbFields3['delivery_status']}`, `{$this->tbFields3['comments']}`) VALUES ('".$this->userID."','".$start_str.$end_num."',CURRENT_TIMESTAMP,'".mysql_real_escape_string($first_name)."','".mysql_real_escape_string($last_name)."','".$telephone."','".mysql_real_escape_string($company)."','".mysql_real_escape_string($address)."','".mysql_real_escape_string($postal)."','".mysql_real_escape_string($city)."',".$country.",'".$order[$this->tbFields['product_id']]."','".$productModelTmp."','".$productNameTmp."',".$price_per_unit.",'".$this->getShippingMethodName($shipping_method)."','".$delivery_status."','".mysql_real_escape_string($comments)."')");
        $end_num++;
        if($res){
          $order_id = mysql_insert_id($this->dbConn);
          
          //echo "Remove from product listing<br>";
          //REMOVE FROM PRODUCT LISTING
          if(!$this->minusQty($order[$this->tbFields['product_id']], 1)){
            $res = $this->query("ROLLBACK");
            return false;  
          }   
            
          //echo "Remove from cart<br>";
          //REMOVE FROM CART
          if(!$this->deletefromCart($order[$this->tbFields['id']])){
            $res = $this->query("ROLLBACK");
            return false;  
          }
                           
          
        }else{
          $res = $this->query("ROLLBACK");
          return false;
        }
      
      }
    
      // COMMIT CHANGES
      $res = $this->query("COMMIT");
      return true;                    
    
    }else{
      echo "Fatal Error. Not Logged In.";
      return -1;
    }        
  }
  
  function setDeliveryStatus($invoice_number = "", $delivery_status = 1, $order_id = "")
  {
    if($invoice_number != ""){
      $res = $this->query("UPDATE `{$this->orderTable}` SET `{$this->tbFields3['delivery_status']}`='".$delivery_status."' WHERE `{$this->tbFields3['invoice_number']}`='".$invoice_number."' LIMIT 1");  
    }else if($order_id != ""){
      $res = $this->query("UPDATE `{$this->orderTable}` SET `{$this->tbFields3['delivery_status']}`='".$delivery_status."' WHERE `{$this->tbFields3['id']}`='".$order_id."' LIMIT 1"); 
    }  
    if($res){
      return true;
    }else
      return false;
  }
 
  function getShippingDetails($id)
  {
    $res = $this->query("SELECT * FROM `{$this->shippingTable}` WHERE `{$this->tbFields5['id']}`='".$id."' LIMIT 1");
    if($res && mysql_num_rows($res) > 0){
      return mysql_fetch_array($res);
    }else{
      return false;
    }  
  }
  
  function getShippingMethodName($id)
  {
    $res = $this->query("SELECT `{$this->tbFields5['shipping_method']}` AS `SM` FROM `{$this->shippingTable}` WHERE `{$this->tbFields5['id']}`=".$id." LIMIT 1");
     if($res && mysql_num_rows($res) > 0){
      $res = mysql_fetch_array($res);
      return $res['SM'];  
    }else
      return $id;    
  }
  
  function getShippingMethods($country_id, $subtotal=0)
  {
    $zones = $this->getZone($country_id);
    $sql = "SELECT * FROM `{$this->shippingTable}` WHERE ((`{$this->tbFields5['min']}` <= ".$subtotal." OR `{$this->tbFields5['min']}` IS NULL) AND (`{$this->tbFields5['max']}` > ".$subtotal." OR `{$this->tbFields5['max']}` IS NULL)) AND (`{$this->tbFields5['zone_id']}` = -1";
    
    if($zones){
      foreach($zones as $zone){
        $sql = $sql." OR `{$this->tbFields5['zone_id']}` =".$zone['ZONES'];
      }
    }
 
    $res = $this->query($sql.") ORDER BY `{$this->tbFields5['id']}` ASC");
    if($res && mysql_num_rows($res) > 0){
      return $this->SqlToArray($res);  
    }else
      return false;
  }
  
  function getZone($country_id)
  {
    $res = $this->query("SELECT `{$this->tbFields6['zone_id']}` AS `ZONES` FROM `{$this->zoneTable}` WHERE `{$this->tbFields6['country_id']}` = ".$country_id);
    if($res && mysql_num_rows($res) > 0){
      return $this->SqlToArray($res);  
    }else
      return false;
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
  
}

?>