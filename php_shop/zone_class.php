<?php
/**
* PHP class - zoneAccess
*
*
* Copyright: 1st Nov 2009, pieceofcake.com.sg
*
**/

class zoneAccess{
  
  var $dbName = DB_DATABASE;   // name of database
  
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

  var $countriesTable = "countries"; // name of table containing zones
  var $tbFields3 = array(                     // fields of $zoneTable
    'id' => 'ID',
    'name' => 'NAME',
    'iso_code_2' => 'ISO_CODE_2',
    'iso_code_3' => 'ISO_CODE_3',
    'address_format_id' => 'ADDRESS_FORMAT_ID'
  );    
 
  var $dbConn;                 // database connection handler

  /**
  * To be called as a constructor - setup and initialization
  **/
  function zoneAccess() 
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
 
  function addCountrytoZone($country, $zone)
  {
    $this->query("INSERT INTO `{$this->zoneTable}` (`{$this->tbFields6["zone_id"]}`, `{$this->tbFields6["country_id"]}`) VALUES(".$zone.",".$country.")");
    return mysql_affected_rows($this->dbConn);
  
  }
  
  function deleteCountryfromZone($country,$zone)
  {
    $this->query("DELETE FROM `{$this->zoneTable}` WHERE `{$this->tbFields6["zone_id"]}`=".$zone." AND `{$this->tbFields6["country_id"]}`=".$country);
    return mysql_affected_rows($this->dbConn);
  }
  
  function deleteMethod($id)
  {
    $this->query("DELETE FROM `{$this->shippingTable}` WHERE `{$this->tbFields5["id"]}`=".$id." LIMIT 1");
    return mysql_affected_rows($this->dbConn);  
  }
 
  function addMethod($zone, $method_name, $cost, $min = "", $max = "")
  {
    if($min == ""){
      $min = "NULL";
    }
    if($max == ""){
      $max = "NULL";
    }
    $this->query("INSERT INTO `{$this->shippingTable}` (`{$this->tbFields5["zone_id"]}`, `{$this->tbFields5["shipping_method"]}`, `{$this->tbFields5["cost"]}`, `{$this->tbFields5["min"]}`, `{$this->tbFields5["max"]}`) VALUES(".$zone.",'".$method_name."','".$cost."',".$min.",".$max.")");
    return mysql_affected_rows($this->dbConn);      
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
          return $result_tmp;
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
  
  function getShippingMethodsZone($zone="")
  {
    $res = $this->query("SELECT * FROM `{$this->shippingTable}` WHERE `{$this->tbFields5['zone_id']}` = ".$zone);
    if($res && mysql_num_rows($res) > 0){
      return $this->SqlToArray($res);  
    }else
      return false;    
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
  
  function getCountries($zone = "")
  {
    if($zone != ""){
      $res = $this->query("SELECT `{$this->tbFields6['country_id']}` AS `COUNTRY_ID` FROM `{$this->zoneTable}` WHERE `{$this->tbFields6['zone_id']}` = ".$zone);
    }else{
      $res = $this->query("SELECT `{$this->tbFields6['country_id']}` AS `COUNTRY_ID` FROM `{$this->zoneTable}` GROUP BY `{$this->tbFields6['country_id']}`");
    }
    
    if($res && mysql_num_rows($res) > 0){
      return $this->SqlToArray($res);  
    }else
      return false;
  
  }
  
  
  function getZone($country_id="")
  {
    if($country_id == ""){
      $res = $this->query("SELECT `{$this->tbFields6['zone_id']}` AS `ZONES` FROM `{$this->zoneTable}` UNION SELECT `{$this->tbFields5['zone_id']}` AS `ZONES` FROM `{$this->shippingTable}` WHERE `{$this->tbFields5['zone_id']}` <> -1");
    }else{
      $res = $this->query("SELECT `{$this->tbFields6['zone_id']}` AS `ZONES` FROM `{$this->zoneTable}` WHERE `{$this->tbFields6['country_id']}` = ".$country_id);
    }
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
  
  function getAllCountries()
  {
    $res = $this->query("SELECT `{$this->tbFields3['id']}` AS `ID`, `{$this->tbFields3["name"]}` AS `NAME` FROM `{$this->countriesTable}`");
    return $this->SqlToArray($res);
  
  }

  
}

?>