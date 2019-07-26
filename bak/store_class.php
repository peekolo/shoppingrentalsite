<?php
/**
* PHP class - storeAccess
*
* For Database related function (Views, etc)
*
*
* Copyright: 1st May 2009, Ho Wen Yang
*
* FUNCTIONS:
*
*     1.  storeAccess()                    -- Constructor.
*     2.  query($sql)                     -- Perform SQL query using $dbConn.
*
**/

class storeAccess{
  
  var $dbName = DB_DATABASE;   // name of database
  
	var $productTable = "product";    // name of table containing product info
  var $tbFields = array(       // fields of $productTable
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
  
  var $manuTable = "manufacturer";    // name of table containing manufacturer's info
  var $tbmanuFields = array(       // fields of $manuTable
    'id'=>'ID',
    'title'=> 'TITLE',   	
  	'description'  => 'DESCRIPTION'
  );  
  
    
	var $genreTable = "product_genre";    // name of table containing product genre info
  var $tbgenreFields = array(       // fields of $productTable
    'id'=>'ID',
    'product_id'=> 'PRODUCT_ID',   	
  	'genre'  => 'GENRE'  	
  );  
  
  var $categoryTable = "product_category";    // name of table containing product category info
  var $tbcategoryFields = array(       // fields of $categoryTable
    'id'=>'ID',
    'product_id'=> 'PRODUCT_ID',   	
  	'category'  => 'CATEGORY'  	
  );  
  
  var $productCountTable = "product_count";    // name of table containing product Counts info
  var $tbcountFields = array(       // fields of $productCountTable
    'product_id'=> 'PRODUCT_ID',   	
  	'views' => 'VIEWS',
  	'last_viewed' => 'LAST_VIEWED',
  	'number_sold' => 'NUMBER_SOLD',
  	'last_sold' => 'LAST_SOLD',
  	'number_in_wishlist' => 'NUMBER_IN_WISHLIST'
  );  
  
  var $productsAttributesTable = "products_attributes";
  var $tbattribFields = array(      // eg. options_name = colour, options_values_name = blue, red
    'id'=>'ID',                     // thus 1 options_name can have many different options_value or even NULL options_value
    'product_id'=>'PRODUCT_ID',
    'options_name'=>'OPTIONS_NAME',
    'options_values_name'=>'OPTIONS_VALUES_NAME',
    'options_values_price'=>'OPTIONS_VALUES_PRICE'
  );
  
  var $productImageTable = "product_image";
  var $tbimageFields = array(
    'id'=>'ID',
    'product_id'=>'PRODUCT_ID',
    'picture_path'=>'PICTURE_PATH'
  );
  
  var $fxTable = "fx_rate";
  var $tbfxFields = array(
    'id'=>'ID',
    'currency'=>'CURRENCY',
    'rate'=>'RATE',
    'last_modified'=>'LAST_MODIFIED'
  );
  
  var $dataFields = array(              // fields of $data to be passed in to add into database
    'username' => 'username',
   	'product_id'  => 'PRODUCT_ID',
  	'quantity' => 'QUANTITY',
  	'wishlist' => 'WISHLIST'
  );
  
  var $dbConn;                 // database connection handler  
  var $fx_rate; // exchange rate. NOT USED

  /**
  * To be called as a constructor - setup and initialization
  **/
  function storeAccess($rate = 1) 
  {
    //setup database connector
    $this->dbConn = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
    if(!$this->dbConn) die(mysql_error($this->dbConn));
    mysql_select_db($this->dbName, $this->dbConn) or die(mysql_error($this->dbConn));    
    
    $this->fx_rate = $rate;                
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
  
  function getCurrencies()
  {
    $res = $this->query("SELECT * FROM `{$this->fxTable}` ORDER BY `{$this->tbfxFields['id']}` ASC");
    if($res && mysql_num_rows($res) > 0){
      return $this->SqlToArray($res);
    }else
      return false;
  }
  
  function getName($productID)
  {
    $res = $this->query("SELECT `{$this->tbFields["title"]}` AS `TITLE` FROM `{$this->productTable}` WHERE `{$this->tbFields["id"]}` = ".$productID." LIMIT 1");
    
    if($res && mysql_num_rows($res) > 0){
      $data = mysql_fetch_array($res);
      return  $data['TITLE'];
    }else
      return false;
  }
  
  function getPrice($productID)
  {
    $res = $this->query("SELECT `{$this->tbFields["discount_flag"]}`, `{$this->tbFields["discount_price"]}` ,`{$this->tbFields["price"]}` FROM `{$this->productTable}` WHERE `{$this->tbFields["id"]}` = ".$productID." LIMIT 1");
    
    if($res && mysql_num_rows($res) > 0){
      $data = mysql_fetch_array($res);
      if($data[$this->tbFields["discount_flag"]] == 1){
        return $data[$this->tbFields["discount_price"]];
      }else{
        return $data[$this->tbFields["price"]];
      }
    }else
      return false;
  }
  
    // CHAMGEEEE!!!!
  function getImages($productID)
  {
    $res = $this->query("SELECT * FROM `{$this->productImageTable}` WHERE `{$this->tbimageFields['product_id']}`=".$productID);
    
    if($res && mysql_num_rows($res) > 0){
      return $this->SqlToArray($res);
    }else
      return false;  
  }
  
  
  function getAttributes($productID)
  {
    $res = $this->query("SELECT * FROM `{$this->productsAttributesTable}` WHERE `{$this->tbattribFields['product_id']}`=".$productID." ORDER BY `{$this->tbattribFields['options_name']}` ASC, `{$this->tbattribFields['options_name']}` ASC");
    
    if($res && mysql_num_rows($res) > 0){
      return $this->SqlToArray($res);
    }else
      return false;  
  }
  
  
  function detailedView($productID)
  {
    $data = '';
    $res = $this->query("SELECT * FROM `{$this->productTable}` WHERE `{$this->tbFields["id"]}` = ".$productID." LIMIT 1");
    if($res && mysql_num_rows($res) > 0){
      $data = mysql_fetch_array($res);      
      echo "<table class='plain1' width=80%><tr><td valign='top' align='left'><span class='bigwords'>".$data[$this->tbFields["title"]]."</span><br><b>[".$data[$this->tbFields["model"]]."]</b></td>";
      if($data[$this->tbFields["discount_flag"]] == 0){
        echo "<td valign='top' align='right' class='bigwords'>$".$data[$this->tbFields["price"]]."</td>";
      }else{
        echo "<td valign='top' align='right' class='bigwords'><s>$".$data[$this->tbFields["price"]]."</s><br><font color='#FF0000'>$".$data[$this->tbFields["discount_price"]]."</font></td>";
      }
      echo "</tr></table><p>";
      echo "<table class='plain1' width=80%><tr><td align='left'><table class='plain1' align='right'><tr><td><img height='120px' src='".IMG_FOLDER.$data[$this->tbFields["picture_path"]]."' alt='".$data[$this->tbFields["title"]]."' height='80px'></td></tr></table><p>";
      
      if($data[$this->tbFields["status"]] == 0){
        echo "<b>Not Available</b><p>";
      }else{
        echo "<a href='javascript:cartthis(".$data[$this->tbFields["id"]].");'><img src='img/addtocart.gif' alt='Add to Cart'></a>&nbsp;&nbsp;<b>Quantity:</b><input id='cart_qty' type='text' size='2' value=1><p>";
      }
       // -1 = Not Available, 0 = Backorder, 1 = Buy Now, 2 = Preorder
      switch($this->getProductStatus($productID)){
        case "0":
          echo "<span class='notes1'>*Backorder</span><p>";
          break;
        case "2":
          echo "<span class='notes1'>*Preorder</span><p>";
          break;
          default;
          break;
      }
      // ATTRIBUTES
      $j = 0;
      if($attribs = $this->getAttributes($productID)){        
        echo "<b>Options</b><br>";
        $tmp = "";            
        $closed = 1;
        foreach($attribs AS $attrib){
          if($attrib[$this->tbattribFields['options_name']] != $tmp){
            
            if($closed != 1) echo "</select>";
            $tmp = $attrib[$this->tbattribFields['options_name']];            
            echo "<br><input type='checkbox' id='options".$j."' name='options".$j."'>".$tmp." ";                        
            if($attrib[$this->tbattribFields['options_values_name']] != NULL && $attrib[$this->tbattribFields['options_values_name']] != ""){
              echo "<select id='attrib".$j."' name='attrib".$j."'>";
              $closed = 0;
              echo "<option value=".$attrib[$this->tbattribFields['id']].">".$attrib[$this->tbattribFields['options_values_name']]." (";
              if($attrib[$this->tbattribFields['options_values_price']] >= 0) echo "+";
              else echo "-";
              echo "$".$attrib[$this->tbattribFields['options_values_price']].")</option>";              
            }else{
              echo "<input type='hidden' id='attrib".$j."' name='attrib".$j."' value='".$attrib[$this->tbattribFields['id']]."'>(";
              if($attrib[$this->tbattribFields['options_values_price']] >= 0) echo "+";
              else echo "-";
              echo "$".$attrib[$this->tbattribFields['options_values_price']].")";
            
            }
            $j++;
          }else if($attrib[$this->tbattribFields['options_values_name']] != NULL || $attrib[$this->tbattribFields['options_values_name']] != ""){
            
            echo "<option value=".$attrib[$this->tbattribFields['id']].">".$attrib[$this->tbattribFields['options_values_name']]." (";
            if($attrib[$this->tbattribFields['options_values_price']] >= 0) echo "+";
            else echo "-";
            echo "$".$attrib[$this->tbattribFields['options_values_price']].")</option>";
          }
        }
        if($closed != 1){
          echo "</select>";
        }
      }
      echo "<input type='hidden' id='attrib_num' name='attrib_num' value=".$j.">";
      // ATTRIBUTES END
      echo "<p>&nbsp;<p>".$data[$this->tbFields["description"]]."</td></tr></table>";            
    }    
  }
  
  function smallView($productID)
  {  
    $data = '';
    $res = $this->query("SELECT `{$this->tbFields["id"]}`, `{$this->tbFields["title"]}`,`{$this->tbFields["picture_path"]}`, `{$this->tbFields["price"]}`, `{$this->tbFields["discount_flag"]}`, `{$this->tbFields["discount_price"]}` FROM `{$this->productTable}` WHERE `{$this->tbFields["id"]}` = ".$productID." LIMIT 1");
    if($res && mysql_num_rows($res) > 0){
      $data = mysql_fetch_array($res);      
      echo "<a href='?p=search&t=detailed&p_id=".$data[$this->tbFields["id"]]."'><img src='".IMG_FOLDER.$data[$this->tbFields["picture_path"]]."' alt='".$data[$this->tbFields["title"]]."' height='80px'><br>".$data[$this->tbFields["title"]]."<br>";
      if($data[$this->tbFields["discount_flag"]] == 0){
        echo "$".$data[$this->tbFields["price"]];
      }else{
        echo "<s>$".$data[$this->tbFields["price"]]."</s><br><font color='#FF0000'>$".$data[$this->tbFields["discount_price"]]."</font>";
      }                
      echo "</a>";
    }    
  }
  
  function searchView($productID)
  {
    $data = '';
    $res = $this->query("SELECT `{$this->tbFields["id"]}`, `{$this->tbFields["title"]}`,`{$this->tbFields["picture_path"]}`, `{$this->tbFields["price"]}`, `{$this->tbFields["quantity"]}`, `{$this->tbFields["discount_flag"]}`, `{$this->tbFields["discount_price"]}`, `{$this->tbFields["date_avail"]}`, `{$this->tbFields["status"]}` FROM `{$this->productTable}` WHERE `{$this->tbFields["id"]}` = ".$productID." LIMIT 1");
    if($res && mysql_num_rows($res) > 0){                                                             
      $data = mysql_fetch_array($res);      
      echo "<td class='sc1'><a href='?p=search&t=detailed&p_id=".$data[$this->tbFields["id"]]."'><img src='".IMG_FOLDER.$data[$this->tbFields["picture_path"]]."' alt='".$data[$this->tbFields["title"]]."' height='80px'></a></td><td class='sc2'><a href='?p=search&t=detailed&p_id=".$data[$this->tbFields["id"]]."'>".$data[$this->tbFields["title"]]."</a></td>";
      if($data[$this->tbFields["discount_flag"]] == 0){
        echo "<td class='sc3'>$".$data[$this->tbFields["price"]]."</td>";
      }else{
        echo "<td class='sc3'><s>$".$data[$this->tbFields["price"]]."</s><br><font color='#FF0000'>$".$data[$this->tbFields["discount_price"]]."</font></td>";
      }  
      
      if($data[$this->tbFields["status"]] == 0){
        echo "<td class='sc4'>Not Available</td>";
      }else if($data[$this->tbFields["quantity"]]>0){
        echo "<td class='sc4'><a href='?p=search&t=detailed&p_id=".$data[$this->tbFields["id"]]."'><img src='".BUYNOW."' alt='Buy Now!'></a></td>";
      }else if(isset($data[$this->tbFields["date_avail"]]) && !$this->isBefore($data[$this->tbFields["date_avail"]])){
        echo "<td class='sc4'><a href='?p=search&t=detailed&p_id=".$data[$this->tbFields["id"]]."'><img src='".PREORDER."' alt='Preorder'></a></td>";     
      }else{
        echo "<td class='sc4'><a href='?p=search&t=detailed&p_id=".$data[$this->tbFields["id"]]."'><img src='".BACKORDER."' alt='Backorder'></a></td>";
      }
    }    
  }
  
  // -1 = Not Available, 0 = Backorder, 1 = Buy Now, 2 = Preorder
  function getProductStatus($productID)
  {
    $data = '';
    $res = $this->query("SELECT `{$this->tbFields["quantity"]}`, `{$this->tbFields["date_avail"]}`, `{$this->tbFields["status"]}` FROM `{$this->productTable}` WHERE `{$this->tbFields["id"]}` = ".$productID." LIMIT 1");
    if($res && mysql_num_rows($res) > 0){                                                             
      $data = mysql_fetch_array($res);     
      
      if($data[$this->tbFields["status"]] == 0){
        return -1;
      }else if($data[$this->tbFields["quantity"]]>0){
        return 1;
      }else if(isset($data[$this->tbFields["date_avail"]]) && !$this->isBefore($data[$this->tbFields["date_avail"]])){
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
  
  
  function count_rows($str, $col = "", $extra_attrib = NULL, $extra_str = NULL, $alpha = "")
  {   
    $str = trim($str);
    if($col == "") $col = $this->tbFields["title"];
    
    $query = "SELECT COUNT(`{$this->tbFields["id"]}`) as `COUNT` from `{$this->productTable}` WHERE `{$this->tbFields["status"]}` <> 0 AND (`{$col}` LIKE '%".$str."%'";
    
    if(strrpos($str, " ")){
      $str_parts = explode(" ", $str);
      $first_tmp = 1;
      foreach ($str_parts AS $str_part){
        if($first_tmp == 1){
          $query = $query." OR (`{$col}` LIKE '%".$str_part."%'";
          $first_tmp = 0;
        }
        $query = $query." AND `{$col}` LIKE '%".$str_part."%'";            
      }
      $query = $query.")) ";
    }else{
      $query = $query.") ";
    }
    
    if($alpha != ""){
      $query = $query."AND `{$this->tbFields["title"]}` LIKE '".$alpha."%' ";
    }
    
    if($extra_str != NULL && $extra_attrib != NULL){    
      $i=0;
      foreach($extra_attrib as $attrib_single){                
        
        switch($attrib_single){
         case "category":
            if($extra_str[$i] != "***"){
              $query = $query."AND (`{$this->tbFields["id"]}` IN (SELECT `{$this->tbcategoryFields["product_id"]}` FROM `{$this->categoryTable}` WHERE `{$this->tbcategoryFields["category"]}` = '".$extra_str[$i]."')) ";              
            }
            break;
          case "manufacturer":
            if($extra_str[$i] != "***"){
              $query = $query."AND (`{$this->tbFields["manufacturer_id"]}` IN (SELECT `{$this->tbmanuFields["id"]}` FROM `{$this->manuTable}` WHERE `{$this->tbmanuFields["title"]}` = '".$extra_str[$i]."')) ";
            }
            break;
          case "genre":
            if($extra_str[$i] != ""){
              $query = $query."AND (`{$this->tbFields["id"]}` IN (SELECT `{$this->tbgenreFields["product_id"]}` FROM `{$this->genreTable}` WHERE `{$this->tbgenreFields["genre"]}` = '".$extra_str[$i]."')) ";
            }
            break;
          case "playersmin":
            if($extra_str[$i] != ""){
              $query = $query."AND (`{$this->tbFields["min_players"]}` <= ".$extra_str[$i].") ";
            }
            break;
          case "playersmax":
            if($extra_str[$i] != ""){
              $query = $query."AND (`{$this->tbFields["max_players"]}` >= ".$extra_str[$i].") ";
            }
            break;              
          case "pricemin":
            if($extra_str[$i] != ""){
              $query = $query."AND (`{$this->tbFields["price"]}` >= ".$extra_str[$i].") ";
            }
            break;
          case "pricemax":
            if($extra_str[$i] != ""){
              $query = $query."AND (`{$this->tbFields["price"]}` <= ".$extra_str[$i].") ";
            }
            break;
          case "":
            default;
            break;                
        }                        
        $i++;
      }              
    }        
    //echo $query;
    $res = $this->query($query);
    if(!$res || mysql_num_rows($res) == 0){
      return "";
    }else{
      $res = $this->SqlToArray($res);
      return $res[0]['COUNT'];      
    }
  }          
  
  function searchDB2($str, $col = "", $retCol = "", $orderby = "", $extra_attrib = NULL, $extra_str = NULL, $from = 0, $num = DEFAULT_SEARCH_NUM, $alpha = "", $ordertype ="")
  {    
    $str = trim($str);
    if($col == "") $col = $this->tbFields["title"];
    if($retCol == "") $retCol = $this->tbFields["id"];
    if($orderby == "") $orderby = $this->tbFields["title"];
    if($ordertype == "") $ordertype = "ASC";
    
    $query = "SELECT `{$retCol}` from `{$this->productTable}` WHERE `{$this->tbFields["status"]}` <> 0 AND (`{$col}` LIKE '%".$str."%'";
    
    if(strrpos($str, " ")){
      $str_parts = explode(" ", $str);
      $first_tmp = 1;
      foreach ($str_parts AS $str_part){
        if($first_tmp == 1){
          $query = $query." OR (`{$col}` LIKE '%".$str_part."%'";
          $first_tmp = 0;
        }
        $query = $query." AND `{$col}` LIKE '%".$str_part."%'";            
      }
      $query = $query.")) ";
    }else{
      $query = $query.") ";
    }
    
    if($alpha != ""){
      $query = $query."AND `{$this->tbFields["title"]}` LIKE '".$alpha."%' ";
    }
    
    if($extra_str != NULL && $extra_attrib != NULL){    
      $i=0;
      foreach($extra_attrib as $attrib_single){                
        
        switch($attrib_single){
          case "category":
            if($extra_str[$i] != "***"){
              $query = $query."AND (`{$this->tbFields["id"]}` IN (SELECT `{$this->tbcategoryFields["product_id"]}` FROM `{$this->categoryTable}` WHERE `{$this->tbcategoryFields["category"]}` = '".$extra_str[$i]."')) ";              
            }
            break;
          case "manufacturer":
            if($extra_str[$i] != "***"){
              $query = $query."AND (`{$this->tbFields["manufacturer_id"]}` IN (SELECT `{$this->tbmanuFields["id"]}` FROM `{$this->manuTable}` WHERE `{$this->tbmanuFields["title"]}` = '".$extra_str[$i]."')) ";
            }
            break;
          case "genre":
            if($extra_str[$i] != ""){
              $query = $query."AND (`{$this->tbFields["id"]}` IN (SELECT `{$this->tbgenreFields["product_id"]}` FROM `{$this->genreTable}` WHERE `{$this->tbgenreFields["genre"]}` = '".$extra_str[$i]."')) ";
            }
            break;
          case "playersmin":
            if($extra_str[$i] != ""){
              $query = $query."AND (`{$this->tbFields["min_players"]}` <= ".$extra_str[$i].") ";
            }
            break;
          case "playersmax":
            if($extra_str[$i] != ""){
              $query = $query."AND (`{$this->tbFields["max_players"]}` >= ".$extra_str[$i].") ";
            }
            break;              
          case "pricemin":
            if($extra_str[$i] != ""){
              $query = $query."AND (`{$this->tbFields["price"]}` >= ".$extra_str[$i].") ";
            }
            break;
          case "pricemax":
            if($extra_str[$i] != ""){
              $query = $query."AND (`{$this->tbFields["price"]}` <= ".$extra_str[$i].") ";
            }
            break;                        
          case "":
            default;
            break;                
        }                        
        $i++;
      }              
    }
    
    $query = $query." ORDER BY `{$orderby}` ".$ordertype." LIMIT ".$from.", ".$num;
    //echo $query;
    $res = $this->query($query);
     if(!$res || mysql_num_rows($res) == 0){
      return "";
     }else
      return $res;      
  }  
  
  
  function getSpecial($num)
  {  
    $res = $this->query("SELECT `{$this->tbFields["id"]}` FROM `{$this->productTable}` WHERE `{$this->tbFields["discount_flag"]}` = 1");
    
    if(!$res || mysql_num_rows($res) == 0){
      $res = "";
    }else{
      $total_num = mysql_num_rows($res);
      $res_tmp = $this->SqlToArray($res);
      
      if($total_num > $num){                    
        $res = array();
        $rand_num = $this->getRandom($total_num, $num);      
        if($num > 1){
          foreach($rand_num as $i){
            $res[] = $res_tmp[i];
          }
        }else{
          $res[] = $res_tmp[$rand_num];
        }
      }else{        
        $res = $res_tmp;
      }
    }    return $res;  
  }
  
  function getBestSellers($num)
  {  
    $res = $this->query("SELECT `{$this->tbFields["id"]}` AS `ID` FROM `{$this->productTable}` ORDER BY `{$this->tbFields["products_sold"]}` DESC LIMIT ".$num);
    
    if(!$res || mysql_num_rows($res) == 0){
      $res = "";
    }else{      
      $res = $this->SqlToArray($res);      
    }    
    return $res;  
  }
  
  
  
  function getNew($days, $num)
  {    
    $res = $this->query("SELECT `{$this->tbFields["id"]}` FROM `{$this->productTable}` WHERE DATE_SUB(CURDATE(), INTERVAL ".$days." DAY) <= `{$this->tbFields["date_added"]}`");
   
    if(!$res || mysql_num_rows($res) < $num){
      $res = $this->getNew($days + 30,$num);      
    }else{    
      $total_num = mysql_num_rows($res);      
      $res_tmp = $this->SqlToArray($res);
      
      if($total_num > $num){                    
        $res = array();
        $rand_num = $this->getRandom($total_num, $num);      
        if($num > 1){
          foreach($rand_num as $i){            
            $res[] = $res_tmp[$i];
          }
        }else{
          $res[] = $res_tmp[$rand_num];
        }
      }else{        
        $res = $res_tmp;
      }
    }
    return $res;  
  }
  
  function getRandom($max_num, $num = 1)// our of max_num choose num
  {
    srand(time());
    $rand_num = '';
    if($num <= 1){
      $rand_num = (rand()%$max_num);
    }else{
      $rand_num = array();
      for($i=0;$i<$num;$i++){
        $rand_num_tmp = (rand()%$max_num);
        if(in_array($rand_num_tmp,$rand_num)){
          $i--;
        }else{
          $rand_num[] = $rand_num_tmp;
        }      
      }              
    }
    return $rand_num;
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
  
  function getCategories()
  {
    $res = $this->query("SELECT `{$this->tbcategoryFields["category"]}` as `CAT`, COUNT(*) as `COUNT` FROM `{$this->categoryTable}` GROUP BY `{$this->tbcategoryFields["category"]}`");
    return $this->SqlToArray($res);    
  }
  
  function getManufacturers()
  {
    $res = $this->query("SELECT `{$this->tbmanuFields["title"]}` as `MANU` FROM `{$this->manuTable}`");
    return $this->SqlToArray($res);    
  }
  
  function getComing()
  {
    $res = $this->query("SELECT `{$this->tbFields["title"]}`, `{$this->tbFields["id"]}`, `{$this->tbFields["date_avail"]}` FROM `{$this->productTable}` WHERE '{$this->tbFields["date_added"]}' = '' OR '{$this->tbFields["date_added"]}' IS NULL");
    return $this->SqlToArray($res);  
  }

}

?>