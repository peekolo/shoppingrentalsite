<?php
require_once("../php_shop/includes/configure.php");
$db_ip = DB_SERVER;           
$db_user = DB_SERVER_USERNAME;
$db_pass = DB_SERVER_PASSWORD;
$db_name = DB_DATABASE;


$db_poweruser = $db_user;
$db_powerpass = $db_pass;


function get_db_conn() {
  $conn = mysql_connect($GLOBALS['db_ip'], $GLOBALS['db_user'], $GLOBALS['db_pass']);
  mysql_select_db($GLOBALS['db_name'], $conn);
  return $conn;
}

function get_db_write() {
  $conn = mysql_connect($GLOBALS['db_ip'], $GLOBALS['db_poweruser'], $GLOBALS['db_powerpass']) or die ('Cannot connect to the database because: ' . mysql_error());
  mysql_select_db($GLOBALS['db_name'], $conn);
  return $conn;
}


  function get_table($table, $id){

    if ($table = "product") {
      $data = get_product_table($id);
    }

    return $data;
  }

  function get_product_table($id){
    $conn = get_db_conn();

    $dat = mysql_query("SELECT * FROM product where ID=$id");

    while ($row = mysql_fetch_assoc($dat)) {
      $data["ID"] = $row["ID"];
      $data["TITLE"] = $row["TITLE"];
      $data["DESCRIPTION"] = $row["DESCRIPTION"];
      $data["MODEL"] = $row["MODEL"];
      $data["MIN_PLAYERS"] = $row["MIN_PLAYERS"];
      $data["MAX_PLAYERS"] = $row["MAX_PLAYERS"];
      $manu_id = $row["MANUFACTURER_ID"];
      $data["PICTURE_PATH"] = $row["PICTURE_PATH"];
      $data["PRICE"] = $row["PRICE"];
      $data["QUANTITY"] = $row["QUANTITY"];
      $data["WEIGHT"] = $row["WEIGHT"];
      $data["DISCOUNT_FLAG"] = $row["DISCOUNT_FLAG"];
      $data["DISCOUNT_PRICE"] = $row["DISCOUNT_PRICE"];
      $data["DATE_AVAIL"] = $row["DATE_AVAIL"];
      $data["DATE_ADDED"] = $row["DATE_ADDED"];
      $data["NEW_FLAG"] = $row["NEW_FLAG"];
      $status = $row["STATUS"]; 
    }

    // enum status
    if ($status==1) {
      $data["STATUS"] = "Active";
    }
    else {
      $data["STATUS"] = "Passive";
    }

    // get manufacturer title
    $dat2 = mysql_query("SELECT * FROM manufacturer where ID=$manu_id");
    while ($row2 = mysql_fetch_assoc($dat2)) {
      $data["MANUFACTURER"] = $row2["TITLE"];
    } 

    mysql_close($conn);

    return $data;
  }



  function get_all_product_table(){
    $conn = get_db_conn();

    $dat = mysql_query("SELECT * FROM product");
    $count = 1;

    while ($row = mysql_fetch_assoc($dat)) {
      $data[$count]["ID"] = $row["ID"];
      $data[$count]["TITLE"] = $row["TITLE"];
      $data[$count]["DESCRIPTION"] = $row["DESCRIPTION"];
      $data[$count]["MODEL"] = $row["MODEL"];
      $data[$count]["MIN_PLAYERS"] = $row["MIN_PLAYERS"];
      $data[$count]["MAX_PLAYERS"] = $row["MAX_PLAYERS"];
      $manu_id = $row["MANUFACTURER_ID"];
      $data[$count]["PICTURE_PATH"] = $row["PICTURE_PATH"];
      $data[$count]["PRICE"] = $row["PRICE"];
      $data[$count]["QUANTITY"] = $row["QUANTITY"];
      $data[$count]["WEIGHT"] = $row["WEIGHT"];
      $data[$count]["DISCOUNT_FLAG"] = $row["DISCOUNT_FLAG"];
      $data[$count]["DISCOUNT_PRICE"] = $row["DISCOUNT_PRICE"];
      $data[$count]["DATE_AVAIL"] = $row["DATE_AVAIL"];
      $data[$count]["DATE_ADDED"] = $row["DATE_ADDED"];
      $data[$count]["NEW_FLAG"] = $row["NEW_FLAG"];
      $status = $row["STATUS"]; 

      // enum status
      if ($status==1) {
        $data[$count]["STATUS"] = "Active";
      }
      else {
        $data[$count]["STATUS"] = "Passive";
      }

      // get manufacturer title
      $dat2 = mysql_query("SELECT * FROM manufacturer where ID=$manu_id");
      while ($row2 = mysql_fetch_assoc($dat2)) {
        $data[$count]["MANUFACTURER"] = $row2["TITLE"];
      } 

      $count = $count + 1;
    }



    mysql_close($conn);

    return $data;
  }


  function get_all_product_category_table(){
    $conn = get_db_conn();

    $dat = mysql_query("SELECT * FROM product_category");
    $count = 1;

    while ($row = mysql_fetch_assoc($dat)) {
      $data[$count]["ID"] = $row["ID"];
      $prod_id = $row["PRODUCT_ID"];
      $data[$count]["CATEGORY"] = $row["CATEGORY"];

      // get product title
      $dat2 = mysql_query("SELECT * FROM product where ID=$prod_id");
      while ($row2 = mysql_fetch_assoc($dat2)) {
        $data[$count]["PRODUCT"] = $row2["TITLE"];
      } 

      $count = $count + 1;
    }

    mysql_close($conn);

    return $data;
  }

  function get_product_ids(){
    $conn = get_db_conn();

    $dat = mysql_query("SELECT ID FROM product");
    $count = 1;
  
    while ($row = mysql_fetch_assoc($dat)) {
      $data[$count] = $row["ID"];
      $count = $count + 1;
    }

    mysql_close($conn);

    return $data;
  }

?>