<?php
/**
* PHP class - renttransactionAccess
*
* For Rental Operations by Admin Accounts
*
*
* Copyright: 30th July 2009, Pieceofcake.com.sg
*
*
**/

class renttransactionAccess{
  
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
  
  var $orderTable = "rented";
  var $tbFields5 = array(
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
  function renttransactionAccess() 
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
 
  function getTransactions($str, $col, $delivered = "")
  {
      $str = trim($str);
      $sql = "SELECT `{$this->tbFields5['id']}`, `{$this->tbFields5['date_created']}`,`{$this->tbFields5['customer_id']}`, `{$this->tbFields5['invoice_number']}`,`{$this->tbFields5['delivery_status']}`,`{$this->tbFields5['product_title']}` FROM `{$this->orderTable}`";
            
      if($col == "product"){
        $sql = $sql." WHERE (`{$this->tbFields5['product_title']}` LIKE '%".$str."%'";
        $col_name = $this->tbFields5["product_title"];
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
    
      if($delivered !== ""){
        $sql = $sql." AND (";
        $first_flag = 1;
        foreach($delivered AS $deliver_single){          
          if($first_flag == 1){
            $first_flag = 0;
            $sql = $sql."`{$this->tbFields5['delivery_status']}` = ".$deliver_single;
          }else{
            $sql = $sql." OR `{$this->tbFields5['delivery_status']}` = ".$deliver_single;
          }
        }
        $sql = $sql.")";
      }
      //echo $sql;
      $res = $this->query($sql);
      
       if($res && mysql_num_rows($res) > 0){
          return $this->SqlToArray($res);
       }else return false;    
    
  }
  
    
  function getRental($invoice_number = "", $order_id = "")
  {
    if($invoice_number != ""){
      $res = $this->query("SELECT * FROM `{$this->orderTable}` WHERE `{$this->tbFields5['invoice_number']}` LIKE '".$invoice_number."%' ORDER BY `{$this->tbFields5['invoice_number']}` ASC");  
    }else if($order_id != ""){
      return $this->getRental($this->getInvoiceNum($order_id));
    }
        
    if($res && mysql_num_rows($res)>0){
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
  
  function getInvoiceNum($order_id){
    $res = $this->query("SELECT `{$this->tbFields5["invoice_number"]}` AS `INV` FROM `{$this->orderTable}` WHERE `{$this->tbFields5["id"]}`=".$order_id." LIMIT 1");
    if($res && mysql_num_rows($res) > 0){
      $res = mysql_fetch_array($res);
      $str = $res['INV'];
      return substr($str, 0, strrpos($str, "R")+2);      
    }
    return false;
  }
  
  function printOrder($invoice_number = "", $order_id = "")
  {
    if($invoice_number != ""){
      $data_full = $this->getRental($invoice_number);
    }else{
      $data_full = $this->getRental($this->getInvoiceNum($order_id));
    }
    $data = $data_full[0];
    
    echo "<table style='background-color:#222222;border:2px solid #000000;width:580px;font-family:Arial,Verdana,Times New Roman;font-size:14px;line-height:25px;color:#000000;padding:0px;border-spacing:2px;width:580px;'>
<tr><td colspan=4 style='background-color:#AAAAFF;text-align:center;font-size:16px;'><b>Rental from Board Game Lifestyle</b></td></tr>";
    echo "<tr><td style='background-color:#DDDDDD;width:120px;text-align:left;'>Date</td>
<td style='background-color:#FFFFFF;width:170px;text-align:left;' colspan=3>".$data[$this->tbFields5['date_created']]."</td></tr>";
    echo "<tr><td style='background-color:#DDDDDD;text-align:left;'>Customer's Name</td><td style='background-color:#FFFFFF;width:170px;text-align:left;'>".$this->getName($data[$this->tbFields5['customer_id']])." ".$this->getLastName($data[$this->tbFields5['customer_id']])."<br>(".$data[$this->tbFields5['telephone']].")</td><td style='background-color:#DDDDDD;width:80px;text-align:left;'>Delivery Details</td><td style='background-color:#FFFFFF;width:140px;text-align:left;'>".$data[$this->tbFields5['first_name']]." ".$data[$this->tbFields5['last_name']]."<br>";
    
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
    echo "<tr><td colspan=4 style='background-color:#AAAAAA;height:15px;font-size:1px;line-height:5px;'>&nbsp;</td></tr><tr><td colspan=4 style='background-color:#FFFFFF;'><table style='background-color:#111177;font-family:Arial,Verdana,Times New Roman;font-size:14px;line-height:25px;color:#000000;padding:0px;border-spacing:2px;'><tr><td style='background-color:#DDDDDD;width:20px;'>&nbsp;</td><td style='background-color:#DDDDDD;width:120px;'><b>RENTAL ID</b></td><td style='background-color:#DDDDDD;width:100px;'><b>MODEL</b></td><td style='background-color:#DDDDDD;width:280px;'><b>ITEM DESCRIPTION</b></td><td style='background-color:#DDDDDD;width:40px;'><b>RP</b></td></tr>";
   
    for($i=0; $i< sizeof($data_full);$i++){
      $product_data = $data_full[$i];
      echo "<tr style='background-color:#FFFFFF;'><td>".($i+1)."</td><td>".$product_data[$this->tbFields5['invoice_number']]."</td><td>".$product_data[$this->tbFields5['product_model']]."</td><td style='text-align:left;'>".$product_data[$this->tbFields5['product_title']]."</td><td>".$product_data[$this->tbFields5['rental_points']]."</td></tr>";           
    }
    
    echo "</table></td></tr><tr><td colspan=4 style='background-color:#AAAAAA;height:15px;font-size:1px;line-height:5px;'>&nbsp;</td></tr>";
    echo "<tr><td style='background-color:#DDDDDD;text-align:left;'>Delivery Method</td><td style='background-color:#FFFFFF;text-align:left;'>".$data[$this->tbFields5['shipping_method']]."</td><td style='background-color:#DDDDDD;text-align:left;'>Delivery Status</td><td style='background-color:#FFFFFF;text-align:left;'>".$this->getStatusString($data[$this->tbFields5['delivery_status']],"rental")."</td></tr>";
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
 
 
  function setDeliveryStatus($invoice_number = "", $delivery_status = 1, $order_id = "")
  {
    if($invoice_number != ""){
      $res = $this->query("UPDATE `{$this->orderTable}` SET `{$this->tbFields5['delivery_status']}`='".$delivery_status."' WHERE `{$this->tbFields5['invoice_number']}`='".$invoice_number."' LIMIT 1");  
    }else if($order_id != ""){
      $res = $this->query("UPDATE `{$this->orderTable}` SET `{$this->tbFields5['delivery_status']}`='".$delivery_status."' WHERE `{$this->tbFields5['id']}`='".$order_id."' LIMIT 1");  
    }
    if($res){
      return true;
    }else
      return false;            
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