<?php
/**
* PHP class - transactionAccess
*
* For Customer Operations by Admin Accounts
*
*
* Copyright: 30th July 2009, Pieceofcake.com.sg
*
*
**/

class transactionAccess{
  
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
  	'discount_type' => 'DISCOUNT_TYPE'
  );
  
  var $memberTable = "customer_member";  // name of table containing member infor
  var $tbFields11 = array(
    'id' => 'ID',
    'type' => 'TYPE',
    'discount' => 'DISCOUNT'  
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
  	'sub_total_price'=>'SUB_TOTAL_PRICE',
  	'delivery_status'=>'DELIVERY_STATUS'
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

  var $dbConn;                 // database connection handler
 
  /**
  * To be called as a constructor - setup and initialization
  **/
  function transactionAccess() 
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
 
  function getTransactions($str, $col, $paid = "", $delivered = "")
  {
      $str = trim($str);
      $sql = "SELECT `{$this->tbFields5['id']}`, `{$this->tbFields5['date_created']}`,`{$this->tbFields5['customer_id']}`, `{$this->tbFields5['invoice_number']}`,`{$this->tbFields5['delivery_status']}`,`{$this->tbFields5['payment_status']}`, `{$this->tbFields5['total_price']}` FROM `{$this->orderTable}`";
            
      if($col == "product"){
        $sql = $sql." WHERE (`{$this->tbFields5['id']}` IN (SELECT `{$this->tbFields4['order_id']}` FROM `{$this->order_productTable}` WHERE (`{$this->tbFields4["product_title"]}` LIKE '%".$str."%'";
        $col_name = $this->tbFields4["product_title"];
      }else if($col == "customer"){      
        $sql = $sql." WHERE (`{$this->tbFields5['customer_id']}` IN (SELECT `{$this->tbFields1['id']}` FROM `{$this->userTable}` WHERE (CONCAT(`{$this->tbFields1["first_name"]}`,' ',`{$this->tbFields1["last_name"]}`) LIKE '%".$str."%'";
        $col_name = "CONCAT(`{$this->tbFields1["first_name"]}`,' ',`{$this->tbFields1["last_name"]}`)";
      }
      
        if(strrpos($str, " ")){
          $str_parts = explode(" ", $str);
          $first_tmp = 1;
          foreach ($str_parts AS $str_part){
            if($first_tmp == 1){
              $sql = $sql." OR (".$col_name." LIKE '%".$str_part."%'";
              $first_tmp = 0;
            }
            $sql = $sql." AND ".$col_name." LIKE '%".$str_part."%'";            
          }
          $sql = $sql.")) ";
        }else{
          $sql = $sql.") ";
        }       
        
        $sql = $sql."))";
    
      if($paid !== ""){      
        if($paid == 1){
          $sql = $sql." AND `{$this->tbFields5['payment_status']}` = 1";
        }else if($paid == 0){        
          $sql = $sql." AND `{$this->tbFields5['payment_status']}` <> 1";
        }
      }
      if($delivered !== ""){
        if($delivered == 1){
          $sql = $sql." AND `{$this->tbFields5['delivery_status']}` = 1";
        }else if($delivered == 0){
          $sql = $sql." AND `{$this->tbFields5['delivery_status']}` <> 1";
        }
      }
      //echo $sql;
      $res = $this->query($sql);
      
       if($res && mysql_num_rows($res) > 0){
          return $this->SqlToArray($res);
       }else return false;    
    
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
  
  
  function printOrder($invoice_number = "", $order_id = "")
  {
    $data = $this->getOrder($invoice_number, $order_id);
    
    echo "<table style='background-color:#222222;border:2px solid #000000;width:580px;font-family:Arial,Verdana,Times New Roman;font-size:14px;line-height:25px;color:#000000;padding:0px;border-spacing:2px;width:580px;'><tr><td colspan=4 style='background-color:#CCCCFF;text-align:center;font-size:16px;'><b>Purchase from Board Game Lifestyle</b></td></tr>";
    echo "<tr><td style='background-color:#DDDDDD;width:120px;text-align:left;'>Invoice Number</td><td style='background-color:#FFFFFF;width:170px;text-align:left;'>".$data[$this->tbFields5['invoice_number']]."</td><td style='background-color:#DDDDDD;width:80px;text-align:left;'>Date</td><td style='background-color:#FFFFFF;text-align:left;width:140px'>".$data[$this->tbFields5['date_created']]."</td></tr>";
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
        echo "&nbsp;&nbsp;<i><font color='#990000'>[".$this->getIndivStatusString($product_data[$this->tbFields4['delivery_status']])."]</font></i>";       
        echo "&nbsp;&nbsp;<a href=\"javascript:show_div('div_".$product_data[$this->tbFields4['id']]."');\">>></a>";
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
    echo "<tr><td style='background-color:#DDDDDD;text-align:left;'>Payment Method</td><td style='background-color:#FFFFFF;text-align:left;'>".$data[$this->tbFields5['payment_type']]."</td><td style='background-color:#DDDDDD;text-align:left;'>Payment Status</td><td style='background-color:#FFFFFF;text-align:left;'>";
    
    echo "<input type='hidden' name='order_id' value=".$data[$this->tbFields5['id']].">";
    
    //echo $this->getStatusString($data[$this->tbFields5['payment_status']],"payment")."</td></tr>";
    $pay_array = array(0,1,2,-1);
    
    echo "<select name='payment_status'>";
    foreach($pay_array AS $pay_single){
      echo "<option value=".$pay_single;
      if($pay_single == $data[$this->tbFields5['payment_status']]) echo " selected";
      echo ">".$this->getStatusString($pay_single, "payment")."</option>";
    }
    echo "</select></td></tr>"; 
     
       
    echo "<tr><td colspan=4 style='background-color:#AAAAAA;height:15px;font-size:1px;line-height:5px;'>&nbsp;</td></tr>";
    echo "<tr><td style='background-color:#DDDDDD;text-align:left;'>Delivery Method</td><td style='background-color:#FFFFFF;text-align:left;'>".$data[$this->tbFields5['shipping_method']]."</td><td style='background-color:#DDDDDD;text-align:left;'>Delivery Status</td><td style='background-color:#FFFFFF;text-align:left;'>";
    
    //echo $this->getStatusString($data[$this->tbFields5['delivery_status']],"delivery")."</td></tr>";
            
    
    $deliver_array = array(2,1,-2,-3,0);      
        
    echo "<select name='delivery_status'>";
    foreach($deliver_array AS $deliver_single){
      echo "<option value=".$deliver_single;
      if($deliver_single == $data[$this->tbFields5['delivery_status']]) echo " selected";
      echo ">".$this->getStatusString($deliver_single, "delivery")."</option>";
    }
    echo "</select></td></tr>";
    
    
    echo "<tr><td colspan=4 style='background-color:#AAAAAA;height:15px;font-size:1px;line-height:5px;'>&nbsp;</td></tr>";
    echo "<tr><td style='background-color:#DDDDDD;text-align:left;'>Comments</td><td colspan=3 style='background-color:#FFFFFF;text-align:left;'>".$data[$this->tbFields5['comments']]."</td>";
    echo "</table>";
          
  }
  
  function getIndivStatusString($status)
  {  
  //echo $status;
    $stat = explode(";",$status);        
    if($stat[0] == $status){
      return $this->getStatusString($stat[0], "delivery");
    }else{
      sort($stat);
      $i=0;
      $tmp_stat = "";
      $str = "";      
      foreach($stat AS $stat_single){
        if($stat_single != $tmp_stat){          
          if($i != 0){
            if($str != ""){
              $str = $str.", ";            
            }
            $str = $str.$i."-".$this->getStatusString($tmp_stat,"delivery");            
          }
          $tmp_stat = $stat_single;
          $i = 1;          
        }else{
          $i++;          
        }            
      }
      if($str != "") $str = $str.", ";
      $str = $str.$i."-".$this->getStatusString($tmp_stat,"delivery");
      
      return $str;    
    }  
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
        case -3:
          return "In Process (Backorder)";
          break;
        case -2:
          return "In Process (Preorder)";
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
  
  function matchStatus($invoice_number = "", $order_id = "") //FIX BUG
  {
    if($order_id == ""){
      $res = $this->query("SELECT `{$this->tbFields5['delivery_status']}` AS `DS`, `{$this->tbFields5['id']}` AS `ID` FROM `{$this->orderTable}` WHERE `{$this->tbFields5['id']}`='".$order_id."' LIMIT 1");       
      if($res && mysql_num_rows($res) > 0){
        $res = mysql_fetch_array($res);
        $order_id = $res['ID'];
        $orig_status = $res['DS'];
      }else
        return false;
    }
    
    $res = $this->query("SELECT `{$this->tbFields4['delivery_status']}` AS `DS` FROM `{$this->order_productTable}` WHERE `{$this->tbFields4['order_id']}`=".$order_id);
    
    if($res && mysql_num_rows($res) > 0){
      $orders = $this->SqlToArray($res);
      $assume = 1;
      $possible_status = 0;
      foreach($orders AS $order){
        $tmp = $order['DS'];
        $tmptmp = $tmp;
        $tmp = explode(";",$tmptmp);
        if($tmp[0] != $tmptmp){
          foreach($tmp AS $tmp_single){
            if($tmp_single != 1){
              $assume = 0;
              $possible_status = $tmp_single;
              break;
            }
          }        
        }else{
          $tmp = $tmp[0];
          if($tmp != 1){ 
            $assume = 0;
            $possible_status = $tmp;
            break;
          }
        }      
      }        
      
      if($assume == 0 && $orig_status == 1){
        $this->setDeliveryStatus("", $possible_status, $order_id);        
      }else if($assume == 1 && $orig_status != 1){
        $this->setDeliveryStatus("", 1, $order_id);        
      }
      
      return true;      
      
    }else return false;                                  
  }
  
  

  function setIndivDeliveryStatus($id, $delivery_status = 1, $qty = "")
  {
    
    $res = $this->query("SELECT `{$this->tbFields4['quantity']}` AS `QTY`, `{$this->tbFields4['delivery_status']}` AS `STATUS`, `{$this->tbFields4['order_id']}` AS `ORDER_ID` FROM `{$this->order_productTable}` WHERE `{$this->tbFields4['id']}`=".$id." LIMIT 1");
    
    if($res && mysql_num_rows($res) > 0){
      $res = mysql_fetch_array($res);
      $orig_qty = $res['QTY'];      
      $orig_status = $res['STATUS'];            
      $order_id = $res['ORDER_ID']; 
      
      $orig_status_tmp = explode(";",$orig_status);
      if($orig_status_tmp[0] != $orig_status){
        sort($orig_status_tmp);
        $orig_status = $orig_status_tmp[count($orig_status_tmp)-1];
      }
          
      
      if($qty == "" || $qty >= $orig_qty){
        $status = $delivery_status;
      }else{
        $array_tmp = array();
        for($i=0;$i<$qty;$i++){
           $array_tmp[] = $delivery_status;
        }
        for($j=$i;$j<$orig_qty;$j++){
          $array_tmp[] = $orig_status;
        }
        $status = implode(";",$array_tmp);              
      }
      
      $res = $this->query("UPDATE `{$this->order_productTable}` SET `{$this->tbFields4['delivery_status']}`='".$status."' WHERE `{$this->tbFields4['id']}`=".$id." LIMIT 1");
      if(mysql_affected_rows($this->dbConn)){
        $this->matchStatus("",$order_id);
        return true;
      }else{
        return false;
      }              
    }else
      return false;  
  }
  
  
  function setPaymentStatus($invoice_number = "", $payment_status = 1, $order_id = "")
  {
    if($invoice_number != ""){
      $res = $this->query("UPDATE `{$this->orderTable}` SET `{$this->tbFields5['payment_status']}`='".$payment_status."' WHERE `{$this->tbFields5['invoice_number']}`='".$invoice_number."' LIMIT 1");  
    }else if($order_id != ""){
      $res = $this->query("UPDATE `{$this->orderTable}` SET `{$this->tbFields5['payment_status']}`='".$payment_status."' WHERE `{$this->tbFields5['id']}`='".$order_id."' LIMIT 1"); 
    }
    if(mysql_affected_rows($this->dbConn)){
        return true;
      }else{
        return false;
      }                  
  }
  
  function setDeliveryStatus($invoice_number = "", $delivery_status = 1, $order_id = "")
  {
    if($invoice_number != ""){
      $res = $this->query("UPDATE `{$this->orderTable}` SET `{$this->tbFields5['delivery_status']}`='".$delivery_status."' WHERE `{$this->tbFields5['invoice_number']}`='".$invoice_number."' LIMIT 1");  
      
      $order_id = mysql_affected_rows($this->dbConn);      
      
    }else if($order_id != ""){
      $res = $this->query("UPDATE `{$this->orderTable}` SET `{$this->tbFields5['delivery_status']}`='".$delivery_status."' WHERE `{$this->tbFields5['id']}`='".$order_id."' LIMIT 1"); 
    }  
    
    $orderproducts_tmp = $this->getOrderProducts($order_id);
    $orderproducts_count = count($orderproducts_tmp);
    $count_tmp = 0;
    
    if($orderproducts_count > 0){
      foreach($orderproducts_tmp AS $orderproduct_tmp){  //set Indiv to 1
        if($this->setIndivDeliveryStatus($orderproduct_tmp[$this->tbFields4['id']],$delivery_status))
          $count_tmp++;
      }
    }else{
      return false;
    }    
    
    if($count_tmp == $orderproducts_count) return true;
    else return false;              
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
  
}

?>