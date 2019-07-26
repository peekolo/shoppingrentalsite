<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
require_once('db_config.php');


// Make file in download folder

		//echo 'delete("download/*.*")';
    // change to use the file details instead of save.
    // $current_date = date("YmdHis");
    //$ourFileName = "download/products" . $current_date . ".csv";
    $ourFileName = "download/temp.csv";
    $fh = fopen($ourFileName, 'w') or exit("Can't open file");
    echo "File opened...". "<br />";

    $conn = get_db_conn();


    // Get Max number of categories
    $dat3 = mysql_query("SELECT count( * ) FROM product_category GROUP BY PRODUCT_ID ORDER BY count( * ) DESC LIMIT 1");
    while ($row3 = mysql_fetch_assoc($dat3)) {
      $max_cat = $row3["count( * )"];
    }

		echo "Number of Categories: " . $max_cat . "<br />";
		
		// Get Max number of attributes
    $dat3 = mysql_query("SELECT count( * ) FROM products_attributes GROUP BY PRODUCT_ID ORDER BY count( * ) DESC LIMIT 1");
    while ($row3 = mysql_fetch_assoc($dat3)) {
      $max_attrib = $row3["count( * )"];
    }
    
    echo "Number of Attributes: " . $max_attrib . "<br />";
    
  // Start Get Product

    $stringData = "PRODUCT-MODEL,PRODUCT-TITLE,PRODUCT-DESCRIPTION,PRODUCT-PICTURE_PATH,PRODUCT-IMAGES,PRODUCT-PRICE,PRODUCT-QUANTITY,PRODUCT-WEIGHT,PRODUCT-DATE_AVAIL,PRODUCT-DATE_ADDED,MANUFACTURER-LABEL,PRODUCT-STATUS,PRODUCT-MIN_PLAYERS,PRODUCT-MAX_PLAYERS,PRODUCT-YEAR,PRODUCT-DISCOUNT_FLAG,PRODUCT-DISCOUNT_PRICE,PRODUCT-RENTAL_FLAG,PRODUCT-RENTAL_POINTS,PRODUCT-PRODUCTS_SOLD";
    $count = 1;
    while ($count <= $max_cat) {
      $stringData = $stringData . ",CATEGORY-" . $count;
      $count = $count + 1;
    }
    $count = 1;
    while ($count <= $max_attrib) {
      $stringData = $stringData . ",ATTRIBUTE-" . $count . "-NAME";
      $stringData = $stringData . ",ATTRIBUTE-" . $count . "-VALUES";
      $stringData = $stringData . ",ATTRIBUTE-" . $count . "-PRICE";
      $count = $count + 1;
    }
    
    $stringData = $stringData . "\n";
    fwrite($fh, $stringData);    

    $dat = mysql_query("SELECT * from product order by ID");
    while ($row = mysql_fetch_assoc($dat)) {
      $prod_id = $row['ID'];
      $data["TITLE"] = str_replace('"','""',$row["TITLE"]);
      $data["DESCRIPTION"] = str_replace('"','""',$row["DESCRIPTION"]);
      $data["MODEL"] = str_replace('"','""',$row["MODEL"]);
      $data["MIN_PLAYERS"] = $row["MIN_PLAYERS"];
      $data["MAX_PLAYERS"] = $row["MAX_PLAYERS"];
      $data["YEAR"] = $row["YEAR"];
      $manu_id = $row["MANUFACTURER_ID"];
      $data["PICTURE_PATH"] = str_replace('"','""',$row["PICTURE_PATH"]);
      $data["IMAGES"] = str_replace('"','""',$row["IMAGES"]);
      $data["PRICE"] = $row["PRICE"];
      $data["QUANTITY"] = $row["QUANTITY"];
      $data["WEIGHT"] = $row["WEIGHT"];
      $data["DISCOUNT_FLAG"] = $row["DISCOUNT_FLAG"];
      $data["DISCOUNT_PRICE"] = $row["DISCOUNT_PRICE"];
      $data["DATE_AVAIL"] = $row["DATE_AVAIL"];
      $data["DATE_ADDED"] = $row["DATE_ADDED"];
      //$data["NEW_FLAG"] = $row["NEW_FLAG"];
      $data["RENTAL_FLAG"] = $row["RENTAL_FLAG"];
      $data["RENTAL_POINTS"] = $row["RENTAL_POINTS"];
      $data["STATUS"] = $row["STATUS"];
      $data["PRODUCTS_SOLD"] = $row["PRODUCTS_SOLD"];

      // enum status
      if ($data["STATUS"]==1) {
	$status="Active";
      }
      else
	$status="Inactive";
      // enum discount_flag
      if ($data["DISCOUNT_FLAG"]==1) 
	$discount_flag="Y";
      
      else
	$discount_flag="N";	
      // enum new flag
      //if ($data["NEW_FLAG"]==1)
	//$new_flag="Y";
      
      //else
	//$new_flag="N";
      // enum rental flag
      if ($data["RENTAL_FLAG"]==1)
				$rental_flag="Y";
      else
				$rental_flag="N";
	
      // Get manufacturer title
			if ($manu_id==0) {
				$data["MANUFACTURER"] = "";
			}
      else {
				$dat2 = mysql_query("SELECT TITLE FROM manufacturer where ID=$manu_id");
				while ($row2 = mysql_fetch_assoc($dat2)) {
					$data["MANUFACTURER"] = $row2["TITLE"];
				}
			}

      // category
      $dat3 = mysql_query("SELECT CATEGORY FROM product_category where PRODUCT_ID=$prod_id");
      $count2 = 1;
      while ($row3 = mysql_fetch_assoc($dat3)) {
        $data["CATEGORY-" . $count2] = $row3["CATEGORY"];
        $count2 = $count2 + 1;
      }
      
      // attribute
      $dat3 = mysql_query("SELECT * FROM products_attributes where PRODUCT_ID=$prod_id");
      $count3 = 1;
      while ($row3 = mysql_fetch_assoc($dat3)) {
        $data["ATTRIBUTE-" . $count3 . "-NAME"] = $row3["OPTIONS_NAME"];
        $data["ATTRIBUTE-" . $count3 . "-VALUES"] = $row3["OPTIONS_VALUES_NAME"];
        $data["ATTRIBUTE-" . $count3 . "-PRICE"] = $row3["OPTIONS_VALUES_PRICE"];
        $count3 = $count3 + 1;
      }
      
      // For every category, add to $stringData
      $stringData = "\"" . $data["MODEL"] . "\",\"" . $data["TITLE"] . "\",\"" . $data["DESCRIPTION"] . "\",\"" . $data["PICTURE_PATH"] . "\",\"" . $data["IMAGES"] . "\"," . $data["PRICE"] . "," . $data["QUANTITY"] . "," . $data["WEIGHT"] . "," . $data["DATE_AVAIL"] . "," . $data["DATE_ADDED"] . ",\"" . $data["MANUFACTURER"] . "\"," . $status . "," . $data["MIN_PLAYERS"] . "," . $data["MAX_PLAYERS"] . "," . $data["YEAR"] . "," . $discount_flag . "," . $data["DISCOUNT_PRICE"] . "," . $rental_flag . "," . $data["RENTAL_POINTS"] . "," . $data["PRODUCTS_SOLD"];
      $count = 1;
			while ($count < $count2) {
      	$stringData = $stringData . ",\"" . $data["CATEGORY-" . $count] . "\"";
      	$count = $count + 1;
      }
      while ($count <= $max_cat) {
      	$stringData = $stringData . ",";
      	$count = $count + 1;
      }
      
      $count = 1;
			while ($count < $count3) {
      	$stringData = $stringData . ",\"" . $data["ATTRIBUTE-" . $count . "-NAME"] . "\"";
      	$stringData = $stringData . ",\"" . $data["ATTRIBUTE-" . $count . "-VALUES"] . "\"";
      	$stringData = $stringData . "," . $data["ATTRIBUTE-" . $count . "-PRICE"];
      	$count = $count + 1;
      }
      while ($count < $max_attrib) {
      	$stringData = $stringData . ",";
      	$stringData = $stringData . ",";
      	$stringData = $stringData . ",";
      	$count = $count + 1;
      }
      
      $stringData = $stringData . "\n";
      fwrite($fh, $stringData);

      // End of 1 product, next product
    }

    mysql_close($conn);
    fclose($fh);
    echo "File closed...". "<br />";

    $file_ready_for_download = 1;
    
?>