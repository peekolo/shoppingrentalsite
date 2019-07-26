<?php
/**
* PHP class - cartAccess
*
* For Shopping Cart, Wish List related function (Either database cart for logged in, or cookie cart for anonymous)
*
*
* Copyright: 1st May 2009, Ho Wen Yang
*
* FUNCTIONS:
*
*     1.  cartAccess()                    -- Constructor.
*     2.  is_logged_in()                  -- Returns Boolean.
*     3.  query($sql)                     -- Perform SQL query using $dbConn.
*     4.  readCartCookie()                -- Reads Cart Cookie. Return array containing - 1st element - product id, 2nd element - qty, etc...
*     5.  writeCartCookie($data)          -- Writes $data into Cart Cookie.
*
**/

class cartAccess{
  
  var $dbName = DB_DATABASE;   // name of database
  
	var $cartTable = "customer_cart";    // name of table containing login info
  var $tbFields = array(       // fields of $userTable
    'id'=>'ID',
    'customer_id'=> 'CUSTOMER_ID',   	
  	'product_id'  => 'PRODUCT_ID',
  	'quantity' => 'QUANTITY',
  	'wishlist' => 'WISHLIST'
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
  	'products_sold' => 'PRODUCTS_SOLD'  	
  );  
 
  
  var $orderTable = "order";
  var $tbFields3 = array(
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
  
  var $shippingTable = "shipping_options";
  var $tbFields5 = array(
      'id' => 'ID',
      'zone_id' => 'ZONE_ID',
      'shipping_method' => 'SHIPPING_METHOD',
      'cost' => 'COST',
      'min' => 'MIN',
      'max' => 'MAX'
  );  
  
  var $zoneTable = "payment_zone_countries";
  var $tbFields6 = array(
    'id' => 'ID',
    'zone_id' => 'ZONE_ID',
    'country_id' => 'COUNTRY_ID'
  );
  
  var $cart_attribTable = "cart_attributes";
  var $tbFields7 = array(
    'id'=>'ID',
    'cart_id'=>'CART_ID',
    'attributes_id'=>'ATTRIBUTES_ID'
  );
    
  var $product_attribTable = "products_attributes";
  var $tbFields8 = array(
    'id'=>'ID',
    'product_id'=>'PRODUCT_ID',
    'options_name'=>'OPTIONS_NAME',
    'options_values_name'=>'OPTIONS_VALUES_NAME',
    'options_values_price'=>'OPTIONS_VALUES_PRICE'
  );
  
  var $dataFields = array(              // fields of $data to be passed in to add into database
    'customer_id' => 'customer_id',
   	'product_id'  => 'PRODUCT_ID',
  	'quantity' => 'QUANTITY',
  	'wishlist' => 'WISHLIST'
  );   
              
  var $cookieName = "boardcart";
  var $cookieLife = 2592000; // 1 month of cookie duration (in seconds) = 60*60*24*30
  var $cookieCartData = array();      // contains the Cart Items stored in Cookie
  var $cartweight = 0;
  
  var $userID;                 // userID of user to be initialized
  var $dbConn;                 // database connection handler
  var $fx_rate;     //exchange rate - NOT USED

  /**
  * To be called as a constructor - setup and initialization
  **/
  function cartAccess($userID = '', $rate = 1) 
  {
    $this->userID = $userID;
    $this->fx_rate = $rate;
    //setup database connector
    $this->dbConn = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
    if(!$this->dbConn) die(mysql_error($this->dbConn));
    mysql_select_db($this->dbName, $this->dbConn) or die(mysql_error($this->dbConn));            
    
    
    if(isset($_COOKIE[$this->cookieName])){ // retrieve cookie if any
      $this->cookieCartData = $this->readCartCookie();
      
      if($this->is_logged_in()){           // if logged in, move cookie to cart
        $a = sizeof($this->cookieCartData);
        for ($i=0; $i < $a; $i=$i+3){
          $tmp1 = "";
          if($this->cookieCartData[$i+2] != ""){
            $tmp1 = explode("/,/",$this->cookieCartData[$i+2]);
          }
          $this->addtoCart($this->cookieCartData[$i],$this->cookieCartData[$i+1],$tmp1);
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
  * Add to shopping cart
  **/  
  function addtoCart($product_id, $qty, $attrib_id = "")
  {
  
    if($this->is_logged_in()){  // if logged in, put into database cart
      
      $tmp = $this->alreadyinCart($product_id, $attrib_id);
      if($tmp != -1){             
          $res = $this->query("UPDATE `{$this->cartTable}` SET `{$this->tbFields["quantity"]}` = (`{$this->tbFields["quantity"]}`+".$qty.") WHERE `{$this->tbFields["id"]}`=".$tmp." LIMIT 1");          
      }else{
            
        $res = $this->query("INSERT INTO `{$this->cartTable}` (`{$this->tbFields['customer_id']}`, `{$this->tbFields['product_id']}`, `{$this->tbFields['quantity']}`) VALUES ('".$this->userID."', ".$product_id.", ".$qty.")");   
        $tmp_id = mysql_insert_id($this->dbConn);
        
        if($attrib_id != ""){
          foreach($attrib_id AS $attrib_id_single){
            $res = $this->query("INSERT INTO `{$this->cart_attribTable}` (`{$this->tbFields7['cart_id']}`, `{$this->tbFields7['attributes_id']}`) VALUES (".$tmp_id.",".$attrib_id_single.")");
          }
        }
      }
              
    }else{
      $tmp = $this->alreadyinCart($product_id, $attrib_id);
      if($tmp == -1){
        $attrib_str = "";
        $first_time = 1;
        if($attrib_id != ""){
          foreach($attrib_id AS $attrib_single){
            if($first_time != 1){
              $attrib_str = $attrib_str."/,/".$attrib_single;
            }else{
              $attrib_str = $attrib_single;
              $first_time = 0;
            }
          }
        }      
        array_push($this->cookieCartData, $product_id, $qty, $attrib_str);
      }else{
        $this->cookieCartData[$tmp+1] = $this->cookieCartData[$tmp+1] + $qty;
      }     
      //create or renew cookie
      $this->writeCartCookie($this->cookieCartData);    
    }
  }    
  
  function initCartWeight()
  {
    $this->cartweight = 0;
  }
  
  function getCartWeight()
  {
    return $this->cartweight;
  }
    
  
  function alreadyinCart($product_id, $attrib_id)
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
  
  function getAttribPrice($cart_id)
  {
    $res = 0;
    if($this->is_logged_in()){
      $res = $this->query("SELECT SUM(`{$this->tbFields8['options_values_price']}`) AS `SUM` FROM `{$this->product_attribTable}`  WHERE `{$this->tbFields8['id']}` IN (SELECT `{$this->tbFields7['attributes_id']}` FROM `{$this->cart_attribTable}` WHERE `{$this->tbFields7['cart_id']}`=".$cart_id.")");
      if($res && mysql_num_rows($res) > 0){
        $data = mysql_fetch_array($res);
        return $data['SUM'];
      }else{
        return 0;
      }
    }else{
      
      if($this->cookieCartData[$cart_id+2] != ""){
        $attrib_arr = explode("/,/",$this->cookieCartData[$cart_id+2]);
        $first_time = 1;
        foreach($attrib_arr AS $attrib_arr_e){
          if($first_time == 1){
            $attrib_str = " WHERE `{$this->tbFields8['id']}`=".$attrib_arr_e;
            $first_time = 0;
          }else{
            $attrib_str = $attrib_str." OR `{$this->tbFields8['id']}`=".$attrib_arr_e;
          }          
        }
        $res = $this->query("SELECT SUM(`{$this->tbFields8['options_values_price']}`) AS `SUM` FROM `{$this->product_attribTable}`".$attrib_str);
        if($res && mysql_num_rows($res) > 0){
          $data = mysql_fetch_array($res);
          return $data['SUM'];
        }else{
          return 0;
        }        
      }else{
        return 0;
      }     
    }
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
  
  function deletefromCart($id, $qty = -88)  //changeD to UPDATE!!!
  {
    //$orig_qty = $this->getQty($id);    
    if($this->is_logged_in()){
      if($qty <= 0){
        $res = $this->query("DELETE FROM `{$this->cartTable}` WHERE `{$this->tbFields["id"]}`=".$id);   
        $res = $this->query("DELETE FROM `{$this->cart_attribTable}` WHERE `{$this->tbFields7['cart_id']}`=".$id);    
      }else if($qty > 0){        
        $res = $this->query("UPDATE `{$this->cartTable}` SET `{$this->tbFields["quantity"]}`=".$qty." WHERE `{$this->tbFields["id"]}`=".$id." LIMIT 1");       
      }else{
        $res = false;
      }
    }else{    
      if($qty <= 0){
        unset($this->cookieCartData[$id]);
        unset($this->cookieCartData[$id+1]);
        unset($this->cookieCartData[$id+2]);
        $this->cookieCartData = array_values($this->cookieCartData);        
        $res = true;
      }else if($qty > 0){
        $this->cookieCartData[$id+1] = $qty;
        $res = true;
      }else{
        $res = false;
      }      
    }
    if(!$this->is_logged_in()){
      //create or renew cookie
      $this->writeCartCookie($this->cookieCartData);
    }
    return $res;
  }
  
  function getQty($id)
  {
    if($this->is_logged_in()){
      $res = $this->query("SELECT `{$this->tbFields["quantity"]}` AS `QTY` FROM `{$this->cartTable}` WHERE `{$this->tbFields['id']}`=".$id." LIMIT 1");
      if($res && mysql_num_rows($res) > 0){      
        $data = mysql_fetch_array($res);
        return $data["QTY"];
      }else
        return 0;
    }else{
      if(sizeof($this->cookieCartData) > ($id + 1))
        return $this->cookieCartData[$id + 1];
      else
        return 0;
    }
  }
  
  function getCartNum()
  {
    if($this->is_logged_in()){
      $res = $this->query("SELECT SUM(`{$this->tbFields["quantity"]}`) AS `COUNT` FROM `{$this->cartTable}` WHERE `{$this->tbFields["customer_id"]}`='".$this->userID."'");
      if($res && mysql_num_rows($res) > 0){      
        $data = mysql_fetch_array($res);
        return $data["COUNT"];
      }else
        return 0;    
    }else{
      if($this->cookieCartData == ""){
        return 0;
      }else{
        return (sizeof($this->cookieCartData)/3);
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
      // -1 = Not Available, 0 = Backorder, 1 = Buy Now, 2 = Preorder
  function getProductStatus($productID)
  {
    $data = '';
    $res = $this->query("SELECT `{$this->tbFields2["quantity"]}`, `{$this->tbFields2["date_avail"]}`, `{$this->tbFields2["status"]}` FROM `{$this->productTable}` WHERE `{$this->tbFields2["id"]}` = ".$productID." LIMIT 1");
    if($res && mysql_num_rows($res) > 0){                                                             
      $data = mysql_fetch_array($res);     
      
      if($data[$this->tbFields2["status"]] == 0){
        return -1;
      }else if($data[$this->tbFields2["quantity"]] > 0){
        if(($data[$this->tbFields2["quantity"]] - $this->getNumInCart($productID)) >= 0)
          return 1;
        else
          return 300 + $data[$this->tbFields2["quantity"]];
      }else if(isset($data[$this->tbFields2["date_avail"]]) && !$this->isBefore($data[$this->tbFields2["date_avail"]])){
        return 2;     
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
      $res = $this->query("SELECT SUM(`{$this->tbFields["quantity"]}`) AS `SUM` FROM `{$this->cartTable}` WHERE `{$this->tbFields["customer_id"]}`='".$this->userID."' AND `{$this->tbFields["wishlist"]}`=0 AND `{$this->tbFields["product_id"]}`=".$productID);
      
      if($res && mysql_num_rows($res) > 0){                                                             
        $data = mysql_fetch_array($res);  
        return $data['SUM'];
      }else{
        return 0;
      }
    }else{
      $a = sizeof($this->cookieCartData);       
      $q_tmp = 0;     
      for ($i=0; $i < $a; $i=$i+3){
        if($this->cookieCartData[$i] == $productId){          
          $q_tmp += $this->cookieCartData[$i+1];
        }
      }
      return $q_tmp;                                
    
    }
  }
  
  function cartView($productID, $id, $qty, $memdiscount = 0, $excldiscount = 0)
  {
      $data = '';
    $res = $this->query("SELECT `{$this->tbFields2["id"]}`, `{$this->tbFields2["title"]}`, `{$this->tbFields2["price"]}`, `{$this->tbFields2["discount_flag"]}`, `{$this->tbFields2["discount_price"]}` FROM `{$this->productTable}` WHERE `{$this->tbFields2["id"]}` = ".$productID." LIMIT 1");
    
    $attribs = $this->getAttribs($id);
    $attrib_price = array();
    
    if($res && mysql_num_rows($res) > 0){
      $data = mysql_fetch_array($res);    
      echo "<tr><td width='110px'><a href='javascript:decartthis(".$id.");'>update</a><br>";
      echo "<a href='javascript:waitthis(".$id.");'>save for later</a></td>";  
      echo "<td><a href='?p=search&t=detailed&p_id=".$data[$this->tbFields2["id"]]."'>".$data[$this->tbFields2["title"]]."</a>";      
      if($attribs && sizeof($attribs)>0){
        foreach($attribs AS $attrib){
          $attrib_info = $this->getAttribInfo($attrib[$this->tbFields7['attributes_id']]);
          echo "<br><i>".$attrib_info[$this->tbFields8['options_name']]." - ".$attrib_info[$this->tbFields8['options_values_name']]."</i>";
          $attrib_price[] = $attrib_info[$this->tbFields8['options_values_price']];
        }
      }
      switch ($tmp_stat = $this->getProductStatus($productID)){
        case 0:
          echo "<p><span class='notes1'>*Backorder</span>";
          break;
        case 1:          
          break;
        case 2:
          echo "<p><span class='notes1'>*Preorder</span>";
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
      if($data[$this->tbFields2["discount_flag"]] == 1){    //discount
        echo "<s>$".$data[$this->tbFields2["price"]]."</s><br><font color='#FF0000'>$".$data[$this->tbFields2["discount_price"]]."</font>";
        $subprice = $data[$this->tbFields2["discount_price"]];
        
      }else{
        $tmp_price = $this->fetchPrice($productID, $memdiscount, $excldiscount);
        if($data[$this->tbFields2["price"]] != $tmp_price){ // member
          echo "<s>$".$data[$this->tbFields2["price"]]."</s><br><font color='#5555FF'>$".number_format($tmp_price,2)."</font>";
          $subprice = number_format($tmp_price,2);
        }else{              // normal
          echo "$".$data[$this->tbFields2["price"]];
          $subprice = $data[$this->tbFields2["price"]];      
        }
      }
      
      if($attribs && sizeof($attrib_price)>0){
        foreach($attrib_price AS $attrib_pr){
          echo "<br>";
          if($attrib_pr >= 0){         
            echo "+";
            $subprice += $attrib_pr;
          }else{
            echo "-";
            $subprice -= $attrib_pr;
          }
          echo $attrib_pr;
        }
      }               
      echo "</td>";
      echo "<td width='50px'><form><input id='".$id."_cart_qty' type='text' size=2 value=".$qty."></form></td></tr>";
      return $qty*$subprice;
    }    
    return 0;
  }
  
  function wishView($productID, $id, $qty)
  {
    $data = '';
    $res = $this->query("SELECT `{$this->tbFields2["id"]}`, `{$this->tbFields2["title"]}`, `{$this->tbFields2["price"]}`, `{$this->tbFields2["discount_flag"]}`, `{$this->tbFields2["discount_price"]}` FROM `{$this->productTable}` WHERE `{$this->tbFields2["id"]}` = ".$productID." LIMIT 1");
    
    $attribs = $this->getAttribs($id);
    $attrib_price = array();
    
    if($res && mysql_num_rows($res) > 0){
      $data = mysql_fetch_array($res);    
      echo "<tr><td width='110px'><a href='javascript:decartthis(".$id.");'>delete</a><br>";
      echo "<a href='javascript:unwaitthis(".$id.");'>move to cart</a></td>";  
      echo "<td><a href='?p=search&t=detailed&p_id=".$data[$this->tbFields2["id"]]."'>".$data[$this->tbFields2["title"]]."</a>";
      if($attribs && sizeof($attribs)>0){
        foreach($attribs AS $attrib){
          $attrib_info = $this->getAttribInfo($attrib[$this->tbFields7['attributes_id']]);
          echo "<br><i>".$attrib_info[$this->tbFields8['options_name']]." - ".$attrib_info[$this->tbFields8['options_values_name']]."</i>";
          $attrib_price[] = $attrib_info[$this->tbFields8['options_values_price']];
        }
      }
      echo "</td>";      
      echo "<td width='80px'>";
      if($data[$this->tbFields2["discount_flag"]] == 0){
        echo "$".$data[$this->tbFields2["price"]];
      }else{
        echo "<s>$".$data[$this->tbFields2["price"]]."</s><br><font color='#FF0000'>$".$data[$this->tbFields2["discount_price"]]."</font>";
      }          
      if($attribs && sizeof($attrib_price)>0){
        foreach($attrib_price AS $attrib_pr){
          echo "<br>";
          if($attrib_pr >= 0){         
            echo "+";
            $subprice += $attrib_pr;
          }else{
            echo "-";
            $subprice -= $attrib_pr;
          }
          echo $attrib_pr;
        }
      }                     
      echo "</td>";
      echo "<td width='50px'><form><input id='".$id."_cart_qty' type='text' size=2 value=".$qty."></form></td></tr>";
    }    
  }
  
  function confirmView($productID, $id, $qty, $memdiscount=0, $excldiscount=0)
  {
    $data = '';
    $res = $this->query("SELECT `{$this->tbFields2["id"]}`, `{$this->tbFields2["title"]}`, `{$this->tbFields2["price"]}`, `{$this->tbFields2["weight"]}`, `{$this->tbFields2["discount_flag"]}`, `{$this->tbFields2["discount_price"]}` FROM `{$this->productTable}` WHERE `{$this->tbFields2["id"]}` = ".$productID." LIMIT 1");
    
    $attribs = $this->getAttribs($id);
    $attrib_price = array();
    
    if($res && mysql_num_rows($res) > 0){
      $data = mysql_fetch_array($res);    
      echo "<tr><td><a href='?p=search&t=detailed&p_id=".$data[$this->tbFields2["id"]]."' target='_blank'>".$data[$this->tbFields2["title"]]."</a>";
      if($attribs && sizeof($attribs)>0){
        foreach($attribs AS $attrib){
          $attrib_info = $this->getAttribInfo($attrib[$this->tbFields7['attributes_id']]);
          echo "<br><i>".$attrib_info[$this->tbFields8['options_name']]." - ".$attrib_info[$this->tbFields8['options_values_name']]."</i>";
          $attrib_price[] = $attrib_info[$this->tbFields8['options_values_price']];
        }
      }
      switch ($tmp_stat = $this->getProductStatus($productID)){
        case 0:
          echo "<p><span class='notes1'>*Backorder</span>";
          break;
        case 1:          
          break;
        case 2:
          echo "<p><span class='notes1'>*Preorder</span>";
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
      
      if($data[$this->tbFields2["discount_flag"]] == 1){    //discount
        echo "<s>$".$data[$this->tbFields2["price"]]."</s><br><font color='#FF0000'>$".$data[$this->tbFields2["discount_price"]]."</font>";
        $subprice = $data[$this->tbFields2["discount_price"]];
        
      }else{
        $tmp_price = $this->fetchPrice($productID, $memdiscount, $excldiscount);
        if($data[$this->tbFields2["price"]] != $tmp_price){ // member
          echo "<s>$".$data[$this->tbFields2["price"]]."</s><br><font color='#5555FF'>$".number_format($tmp_price,2)."</font>";
          $subprice = number_format($tmp_price,2);
        }else{              // normal
          echo "$".$data[$this->tbFields2["price"]];
          $subprice = $data[$this->tbFields2["price"]];      
        }
      }      
      
      if($attribs && sizeof($attrib_price)>0){
        foreach($attrib_price AS $attrib_pr){
          echo "<br>";
          if($attrib_pr >= 0){         
            echo "+";
            $subprice += $attrib_pr;
          }else{
            echo "-";
            $subprice -= $attrib_pr;
          }
          echo $attrib_pr;
        }
      }
            
      echo "</td>";
      echo "<td width='50px'>".$qty."</td></tr>";
      $this->cartweight += ($qty * $data[$this->tbFields2["weight"]]);
      return $qty*$subprice;
    }    
    return 0;
  }
  
  function fetchPrice($id, $memdiscount=0, $exclusive_d=0) //round up?
  {
    $res = $this->query("SELECT `{$this->tbFields2["discount_flag"]}`, `{$this->tbFields2["discount_price"]}` ,`{$this->tbFields2["price"]}` FROM `{$this->productTable}` WHERE `{$this->tbFields2["id"]}` = ".$id." LIMIT 1");
    
    if($res && mysql_num_rows($res) > 0){
      $data = mysql_fetch_array($res);
      if($data[$this->tbFields2["discount_flag"]] == 1){
        return $data[$this->tbFields2["discount_price"]];
      }else if($memdiscount != 0){
        return $data[$this->tbFields2["price"]] * (100-$memdiscount)/100;
      }else{
        return $data[$this->tbFields2["price"]];
      }
    }else
      return false;
      
  }
  
  function getAttribs($cart_id)
  {
    if($this->is_logged_in()){
      $res=$this->query("SELECT * FROM `{$this->cart_attribTable}` WHERE `{$this->tbFields7['cart_id']}`=".$cart_id);
      
      if($res && mysql_num_rows($res) > 0){
        return $this->SqlToArray($res);
      }else
        return false;
  
    }else{      
      if($this->cookieCartData[$cart_id+2] != ""){
        $result = array();
        $attrib_arr = explode("/,/",$this->cookieCartData[$cart_id+2]);
        foreach($attrib_arr AS $attrib_arr_single){          
          $result[] = array($this->tbFields7['attributes_id']=>$attrib_arr_single);
        }
        return $result;
      }else{
        return false;
      }
    }
  
  }

  function getAttribInfo($id)
  {
    $res=$this->query("SELECT * FROM `{$this->product_attribTable}` WHERE `{$this->tbFields8['id']}`=".$id." LIMIT 1");
    if($res && mysql_num_rows($res) > 0){
      return mysql_fetch_array($res);
    }else
      return false; 
  }
  
  
  function minusQty($id, $qty)
  {
    if($qty > 0){
      $res = $this->query("SELECT `{$this->tbFields2['quantity']}` AS `QTY`, `{$this->tbFields2["products_sold"]}` AS `SOLD` FROM `{$this->productTable}` WHERE `{$this->tbFields2['id']}`=".$id." LIMIT 1");      
      if($res && mysql_num_rows($res) > 0){
        $data = mysql_fetch_array($res);
        $orig_qty = $data['QTY'];
        $orig_sold = $data['SOLD'];
      }else return false;
    
      
        $new_qty = $orig_qty - $qty;
        $new_sold = $orig_sold + $qty;
        if($new_qty < 0) $new_qty = 0;
        $res = $this->query("UPDATE `{$this->productTable}` SET `{$this->tbFields2["quantity"]}`=".$new_qty.",  `{$this->tbFields2["products_sold"]}`=".$new_sold." WHERE `{$this->tbFields2["id"]}`=".$id." LIMIT 1");
        if($res){          
          return true;
        }else
          return false;            
      
    }else{
      return false;
    }
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
      
      $invoice = $invoice."_S";      
      //echo $invoice;
      $res = $this->query("SELECT `{$this->tbFields3['invoice_number']}` AS `INVNUM` FROM `{$this->orderTable}` WHERE `{$this->tbFields3['invoice_number']}` LIKE '".$invoice."%' ORDER BY `{$this->tbFields3['id']}` DESC LIMIT 1");
      if($res && mysql_num_rows($res) > 0){
        $res = mysql_fetch_array($res);
        $invoice_prev = $res['INVNUM'];
        $num_tmp = 1 + substr($invoice_prev, strlen($invoice));
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
  
  function placeOrder($invoice_number, $first_name, $last_name, $telephone, $company, $address, $postal, $city, $country, $total_price, $shipping_cost=0, $member_discount=0, $payment_type, $payment_status=0, $shipping_method, $delivery_status = 0, $comments)
  {
    if($this->is_logged_in()){      
      
      if($this->orderExist($invoice_number)){
        echo "Duplicate Transaction! Please do not refresh or use the Back or Forward buttons.";
        exit;
      }
    
    
      if($payment_status == 1){
        $date_paid = "CURRENT_TIMESTAMP";
      }else{
        $date_paid = "NULL";
      }
      $orders = $this->getCart();
      // START TRANSACTION
      $res = $this->query("START TRANSACTION");
      // ADD GENERAL ORDER INFO
      
      //echo "Add order<br>";
      
      $res = $this->query("INSERT INTO `{$this->orderTable}` (`{$this->tbFields3['customer_id']}`, `{$this->tbFields3['invoice_number']}`, `{$this->tbFields3['date_created']}`, `{$this->tbFields3['first_name']}`, `{$this->tbFields3['last_name']}`, `{$this->tbFields3['telephone']}`, `{$this->tbFields3['company']}`, `{$this->tbFields3['address']}`, `{$this->tbFields3['postal']}`, `{$this->tbFields3['city']}`, `{$this->tbFields3['country']}`, `{$this->tbFields3['total_price']}`, `{$this->tbFields3['shipping_cost']}`, `{$this->tbFields3['member_discounts']}`,`{$this->tbFields3['payment_type']}`, `{$this->tbFields3['payment_status']}`, `{$this->tbFields3['date_paid']}`, `{$this->tbFields3['shipping_method']}`, `{$this->tbFields3['delivery_status']}`, `{$this->tbFields3['comments']}`) VALUES ('".$this->userID."','".$invoice_number."',CURRENT_TIMESTAMP,'".mysql_real_escape_string($first_name)."','".mysql_real_escape_string($last_name)."','".$telephone."','".mysql_real_escape_string($company)."','".mysql_real_escape_string($address)."','".mysql_real_escape_string($postal)."','".mysql_real_escape_string($city)."',".$country.",".$total_price.",".$shipping_cost.",".$member_discount.",'".$payment_type."','".$payment_status."',".$date_paid.",'".$this->getShippingMethodName($shipping_method)."','".$delivery_status."','".mysql_real_escape_string($comments)."')");
      
      if($res){
        $order_id = mysql_insert_id($this->dbConn);
      }else{
        $res = $this->query("ROLLBACK");
        return false;
      }
      
      //echo "Add products<br>";
      
      // ADD INDIVIDUAL PRODUCT ORDERS
      foreach($orders AS $order){
        $price_per_unit = $this->fetchPrice($order[$this->tbFields['product_id']], $member_discount);
        $productModelTmp = mysql_real_escape_string($this->getProductModel($order[$this->tbFields['product_id']]));
        $productNameTmp = mysql_real_escape_string($this->getProductTitle($order[$this->tbFields['product_id']]));
        $res = $this->query("INSERT INTO `{$this->order_productTable}` (`{$this->tbFields4['order_id']}`,`{$this->tbFields4['product_model']}`, `{$this->tbFields4['product_id']}`, `{$this->tbFields4['product_title']}`, `{$this->tbFields4['quantity']}`, `{$this->tbFields4['price_per_unit']}`, `{$this->tbFields4['sub_total_price']}`) VALUES (".$order_id.",'".$productModelTmp."',".$order[$this->tbFields['product_id']].",'".$productNameTmp."',".$order[$this->tbFields['quantity']].",".$price_per_unit.",".($price_per_unit*$order[$this->tbFields['quantity']]).")"); 
        if($res){
          $order_product_id = mysql_insert_id($this->dbConn);
        }else{
          $res = $this->query("ROLLBACK");
          return false;
        }
        //echo "Add attrib_products<br>";
        //ADD INDIVIDUAL PRODUCT ORDERS ATTRIBS
        $order_attributes = $this->getAttribs($order[$this->tbFields['id']]);
        
        if($order_attributes && sizeof($order_attributes) > 0){
          foreach($order_attributes AS $order_attribute){
            $attribinfo = $this->getAttribInfo($order_attribute[$this->tbFields7['attributes_id']]);
            $res = $this->query("INSERT INTO `{$this->order_products_attribTable}` (`{$this->tbFields41['order_products_id']}`, `{$this->tbFields41['attributes_id']}`,`{$this->tbFields41['options_name']}`,`{$this->tbFields41['options_values_name']}`,`{$this->tbFields41['price']}`) VALUES (".$order_product_id.",".$order_attribute[$this->tbFields7['attributes_id']].",'".$attribinfo[$this->tbFields8['options_name']]."','".$attribinfo[$this->tbFields8['options_values_name']]."',".$attribinfo[$this->tbFields8['options_values_price']].")");
          
            if(!$res){
              $res = $this->query("ROLLBACK");
              return false;            
            }          
          }
        }
         
        //echo "Remove from cart<br>";
        //REMOVE FROM CART
        if(!$this->deletefromCart($order[$this->tbFields['id']])){
          $res = $this->query("ROLLBACK");
          return false;  
        }
        
        //echo "Remove from product listing<br>";
        //REMOVE FROM PRODUCT LISTING
        if(!$this->minusQty($order[$this->tbFields['product_id']], $order[$this->tbFields['quantity']])){
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
          
  function setPaymentStatus($invoice_number = "", $payment_status = 1, $order_id = "")
  {
    if($invoice_number != ""){
      $res = $this->query("UPDATE `{$this->orderTable}` SET `{$this->tbFields3['payment_status']}`='".$payment_status."' WHERE `{$this->tbFields3['invoice_number']}`='".$invoice_number."' LIMIT 1");  
    }else if($order_id != ""){
      $res = $this->query("UPDATE `{$this->orderTable}` SET `{$this->tbFields3['payment_status']}`='".$payment_status."' WHERE `{$this->tbFields3['id']}`='".$order_id."' LIMIT 1"); 
    }
  }
  
  function setDeliveryStatus($invoice_number = "", $delivery_status = 1, $order_id = "")
  {
    if($invoice_number != ""){
      $res = $this->query("UPDATE `{$this->orderTable}` SET `{$this->tbFields3['delivery_status']}`='".$delivery_status."' WHERE `{$this->tbFields3['invoice_number']}`='".$invoice_number."' LIMIT 1");  
    }else if($order_id != ""){
      $res = $this->query("UPDATE `{$this->orderTable}` SET `{$this->tbFields3['delivery_status']}`='".$delivery_status."' WHERE `{$this->tbFields3['id']}`='".$order_id."' LIMIT 1"); 
    }  
  }
  
  function getShippingCost($coststr, $weight=0)
  {
    
    $cost_arr = explode(",",$coststr);    
    if(sizeof($cost_arr) == 1){
      $cost_tmp = explode(":",$cost_arr[0]);
      if(sizeof($cost_tmp) == 1){
        return $coststr;
      }else{        
        return $cost_tmp[1];
      }    
    }else{
      $result_tmp = 0;     
      $first = 1;
      foreach($cost_arr AS $cost_arr_single){
        $cost_tmp = explode(":", $cost_arr_single);
        if($first == 1){
            $first = 0;
            $result_tmp = $cost_tmp[1];
        }
        
        if($weight < $cost_tmp[0]){
          return $cost_tmp[1];
        }else{
          $result_tmp = $cost_tmp[1];
        }      
      }
      return $result_tmp;
    }
    return 0;
  
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
    
    }
  }
  
}

?>