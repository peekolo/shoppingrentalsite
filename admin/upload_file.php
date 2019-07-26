<?php
require_once('db_config.php');

$exit = 0;

/* Upload file
if ($_FILES["file"]["type"] != "/csv")
  {
*/
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Error: " . $_FILES["file"]["error"] . "<br />";
    }
  else
    {
    echo "Upload: " . $_FILES["file"]["name"] . "<br />";
    echo "Type: " . $_FILES["file"]["type"] . "<br />";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
    echo "Stored in: " . $_FILES["file"]["tmp_name"] . "<br />";

    // change to use the file details instead of save.
    //    $ourFileName = "upload/" . $_FILES["file"]["name"];
    $ourFileName = $_FILES["file"]["tmp_name"];
    $fh = fopen($ourFileName, 'r') or exit("Can't open file");
    echo "File opened...". "<br />";

    $conn = get_db_conn();
    $rowcount =0;
    while (($data = fgetcsv($fh, 65536, ",")) !== FALSE) {
      $num = count($data);
      //echo "<p> $num fields in line $rowcount: <br /></p>\n";
      $store = NULL;
      $rowcount++;
      for ($c=0; $c < $num ; $c++) {
				$store[$c] = mysql_real_escape_string($data[$c]);
      }

			// first line, get column numbers of important fields
			if ($rowcount==1) {
      	for ($i=0; $i < $num ; $i++) {
      		if ($store[$i] == 'PRODUCT-MODEL')
   			$PRODUCT_MODEL = $i;
      		else if ($store[$i] == 'PRODUCT-TITLE')
      			$PRODUCT_TITLE = $i;
      		else if ($store[$i] == 'PRODUCT-DESCRIPTION')
      			$PRODUCT_DESCRIPTION = $i;
      		else if ($store[$i] == 'PRODUCT-PICTURE_PATH')
      			$PRODUCT_PICTURE_PATH = $i;
      		else if ($store[$i] == 'PRODUCT-PRICE')
      			$PRODUCT_PRICE = $i;
      		else if ($store[$i] == 'PRODUCT-QUANTITY')
      			$PRODUCT_QUANTITY = $i;
      		else if ($store[$i] == 'PRODUCT-WEIGHT')
      			$PRODUCT_WEIGHT = $i;
      		else if ($store[$i] == 'PRODUCT-DATE_AVAIL')
      			$PRODUCT_DATE_AVAIL = $i;
      		else if ($store[$i] == 'PRODUCT-DATE_ADDED')
      			$PRODUCT_DATE_ADDED = $i;
      		else if ($store[$i] == 'MANUFACTURER-LABEL')
      			$MANUFACTURER_LABEL = $i;
      		else if ($store[$i] == 'PRODUCT-STATUS')
      			$PRODUCT_STATUS = $i;
      		else if ($store[$i] == 'PRODUCT-MIN_PLAYERS')
      			$PRODUCT_MIN_PLAYERS = $i;
      		else if ($store[$i] == 'PRODUCT-MAX_PLAYERS')
      			$PRODUCT_MAX_PLAYERS = $i;
      		else if ($store[$i] == 'PRODUCT-YEAR')
      			$PRODUCT_YEAR = $i;
      		else if ($store[$i] == 'PRODUCT-DISCOUNT_FLAG')
      			$PRODUCT_DISCOUNT_FLAG = $i;
      		else if ($store[$i] == 'PRODUCT-DISCOUNT_PRICE')
      			$PRODUCT_DISCOUNT_PRICE = $i;
      		else if ($store[$i] == 'PRODUCT-IMAGES')
      			$PRODUCT_IMAGES = $i;
      		else if ($store[$i] == 'PRODUCT-RENTAL_FLAG')
      			$PRODUCT_RENTAL_FLAG = $i;
          else if ($store[$i] == 'PRODUCT-RENTAL_QUANTITY')
      			$PRODUCT_RENTAL_QUANTITY = $i;
      		else if ($store[$i] == 'PRODUCT-RENTAL_POINTS')
      			$PRODUCT_RENTAL_POINTS = $i;    	
		else if ($store[$i] == 'PRODUCT-PRODUCTS_SOLD')
      			$PRODUCT_PRODUCTS_SOLD = $i;
      			
      		// temporary category
      		// sub-string? check category
      		else if ($store[$i] == 'CATEGORY-1')
      			// $cat_num = substr($store[$i],9);
      			$CATEGORY_1 = $i;
      		else if ($store[$i] == 'CATEGORY-2')
      			$CATEGORY_2 = $i;
      		else if ($store[$i] == 'CATEGORY-3')
      			$CATEGORY_3 = $i;	
      		else if ($store[$i] == 'CATEGORY-4')
      			$CATEGORY_4 = $i;	
      		else if ($store[$i] == 'CATEGORY-5')
      			$CATEGORY_5 = $i;	
      		else if ($store[$i] == 'CATEGORY-6')
      			$CATEGORY_6 = $i;
      		else if ($store[$i] == 'CATEGORY-7')
      			$CATEGORY_7 = $i;	
      		else if ($store[$i] == 'CATEGORY-8')
      			$CATEGORY_8 = $i;	
      		else if ($store[$i] == 'CATEGORY-9')
      			$CATEGORY_9 = $i;	
      		
      		//temporary attributes	
      			// sub-string? check attributes
      			// each attribute has a few columns
      		else if ($store[$i] == 'ATTRIBUTE-1-NAME')
      			$ATTRIBUTE_1_NAME = $i;
      		else if ($store[$i] == 'ATTRIBUTE-1-VALUES')
      			$ATTRIBUTE_1_VALUES = $i;
      		else if ($store[$i] == 'ATTRIBUTE-1-PRICE')
      			$ATTRIBUTE_1_PRICE = $i;
      		else if ($store[$i] == 'ATTRIBUTE-2-NAME')
      			$ATTRIBUTE_2_NAME = $i;
      		else if ($store[$i] == 'ATTRIBUTE-2-VALUES')
      			$ATTRIBUTE_2_VALUES = $i;
      		else if ($store[$i] == 'ATTRIBUTE-2-PRICE')
      			$ATTRIBUTE_2_PRICE = $i;
      		else if ($store[$i] == 'ATTRIBUTE-3-NAME')
      			$ATTRIBUTE_3_NAME = $i;
      		else if ($store[$i] == 'ATTRIBUTE-3-VALUES')
      			$ATTRIBUTE_3_VALUES = $i;
      		else if ($store[$i] == 'ATTRIBUTE-3-PRICE')
      			$ATTRIBUTE_3_PRICE = $i;
      		else if ($store[$i] == 'ATTRIBUTE-4-NAME')
      			$ATTRIBUTE_4_NAME = $i;
      		else if ($store[$i] == 'ATTRIBUTE-4-VALUES')
      			$ATTRIBUTE_4_VALUES = $i;
      		else if ($store[$i] == 'ATTRIBUTE-4-PRICE')
      			$ATTRIBUTE_4_PRICE = $i;

      		else if ($store[$i] == '')
      			$EOF = $i;

      		else {
      			echo "File has erroneous column name: " . $store[$i] . "<br />";
      			echo "Please check csv file" . "<br />";
      			$exit = 1;
      			break;
      		}
      	} // end for
      	
      	// max categories
    		if (is_null($CATEGORY_1))
    			$max_cat = 0;
				else if (is_null($CATEGORY_2))
 		     	$max_cat = 1;
 	  	  else if (is_null($CATEGORY_3))
      		$max_cat = 2;
      	else if (is_null($CATEGORY_4))
      		$max_cat = 3;
     		else if (is_null($CATEGORY_5))
      		$max_cat = 4;
      	else if (is_null($CATEGORY_6))
      		$max_cat = 5;
      	else if (is_null($CATEGORY_7))
      		$max_cat = 6;
      	else if (is_null($CATEGORY_8))
      		$max_cat = 7;
      	else if (is_null($CATEGORY_9))
      		$max_cat = 8;
      	else
      		$max_cat = 9;
      	
      	// correctness of category
      	if ($max_cat > 1) {
      		if (is_null($CATEGORY_1))
    				$exit = 1;
    		}
				else if ($max_cat > 2) {
					if (is_null($CATEGORY_2))
      			$exit = 1;
      	}
      	else if ($max_cat > 3) {
					if (is_null($CATEGORY_3))
      			$exit = 1;
      	}
      	else if ($max_cat > 4) {
					if (is_null($CATEGORY_4))
      			$exit = 1;
      	}
      	else if ($max_cat > 5) {
					if (is_null($CATEGORY_5))
      			$exit = 1;
      	}
      	else if ($max_cat > 6) {
					if (is_null($CATEGORY_6))
      			$exit = 1;
      	}
      	else if ($max_cat > 7) {
					if (is_null($CATEGORY_7))
      			$exit = 1;
      	}
      	else if ($max_cat > 8) {
					if (is_null($CATEGORY_8))
      			$exit = 1;
      	}
      
      	// atttributes checking
      	if (is_null($ATTRIBUTE_1_NAME) || is_null($ATTRIBUTE_1_VALUES) || is_null($ATTRIBUTE_1_PRICE))
      		$max_attrib = 0;
      	else if (is_null($ATTRIBUTE_2_NAME) || is_null($ATTRIBUTE_2_VALUES) || is_null($ATTRIBUTE_2_PRICE))
 		     	$max_attrib = 1;
      	else if (is_null($ATTRIBUTE_3_NAME) || is_null($ATTRIBUTE_3_VALUES) || is_null($ATTRIBUTE_3_PRICE))
 		     	$max_attrib = 2;
      	else if (is_null($ATTRIBUTE_4_NAME) || is_null($ATTRIBUTE_4_VALUES) || is_null($ATTRIBUTE_4_PRICE))
 		     	$max_attrib = 3;
      	else
      		$max_attrib = 4;
      	
      	//echo "Max attrib=" . $max_attrib . ",product_new_flag=" . $PRODUCT_NEW_FLAG;
    	} // end first line
    	
    	
      // if title exists, update
      else if ($rowcount>1 && $exit==0) {
        $date_avail = date("YmdHis",strtotime($store[$PRODUCT_DATE_AVAIL]));
        //$date_added = date("YmdHis",strtotime($store[$PRODUCT_DATE_ADDED]));
				$date_added = date("YmdHis");
				$year = date("Y",strtotime($store[$PRODUCT_YEAR] . "-1-1 0:0:00"));

				//enum Status
				if ($store[$PRODUCT_STATUS]=='Active') {
	  			$status = 1;
				}
				else {
	  			$status = 0;
				}
				// enum discount flag
				if ($store[$PRODUCT_DISCOUNT_FLAG]=='Y') {
	  			$discount_flag = 1;
				}
				else {
	  			$discount_flag = 0;
				}
				// enum new flag
				//if ($store[$PRODUCT_NEW_FLAG]=='Y') {
	 			//	$new_flag = 1;
				//}
				//else {
	  			//$new_flag = 0;
				//}
				// enum rental flag
				if ($store[$PRODUCT_RENTAL_FLAG]=='Y') {
	 				$rental_flag = 1;
				}
				else {
	  			$rental_flag = 0;
				}

				// discount price - allow for %
				$discount_price = $store[$PRODUCT_DISCOUNT_PRICE];
				$last = $discount_price[strlen($discount_price) - 1];
				$percent = substr($discount_price, 0, -1);
				//echo "$discount_price , $last , $percent";				

				if (empty($store[$PRODUCT_RENTAL_POINTS]))
				  $store[$PRODUCT_RENTAL_POINTS] = 0;
				if (empty($store[$PRODUCT_DISCOUNT_PRICE]))
				  $store[$PRODUCT_DISCOUNT_PRICE] = 0;
				if (empty($store[$PRODUCT_MAX_PLAYERS]))
				  $store[$PRODUCT_MAX_PLAYERS] = 0;
				if (empty($store[$PRODUCT_MIN_PLAYERS]))
				  $store[$PRODUCT_MIN_PLAYERS] = 0;
				if (empty($store[$PRODUCT_WEIGHT]))
				  $store[$PRODUCT_WEIGHT] = 0;
				if (empty($store[$PRODUCT_QUANTITY]))
				  $store[$PRODUCT_QUANTITY] = 0;
				if (empty($store[$PRODUCT_PRICE]))
				  $store[$PRODUCT_PRICE] = 0;
				if (empty($store[$PRODUCT_PRODUCTS_SOLD]))
				  $store[$PRODUCT_PRODUCTS_SOLD] = 0;
				if (empty($store[$PRODUCT_RENTAL_QUANTITY]))
          $store[$PRODUCT_RENTAL_QUANTITY] = 0;
				

				// check fields
				if (!(is_numeric($store[$PRODUCT_PRICE]))) {
					echo "Line $rowcount, PRODUCT-PRICE is not a numeric: " . $store[$PRODUCT_PRICE] . "<br />";
					echo "Line $rowcount will not be uploaded." . "<br />";
					continue;
				}
				else if (!(is_int((int)$store[$PRODUCT_QUANTITY]))) {
					echo "Line $rowcount, PRODUCT-QUANTITY is not an integer: " . $store[$PRODUCT_QUANTITY] . "<br />";
					echo "Line $rowcount will not be uploaded." . "<br />";
					continue;
				}
				else if (!(is_numeric($store[$PRODUCT_WEIGHT]))) {
					echo "Line $rowcount, PRODUCT-WEIGHT is not numeric: " . $store[$PRODUCT_WEIGHT] . "<br />";
					echo "Line $rowcount will not be uploaded." . "<br />";
					continue;
				}
				else if (!(is_int((int)$store[$PRODUCT_MIN_PLAYERS]))) {
					echo "Line $rowcount, PRODUCT-MIN_PLAYERS is not an integer: " . $store[$PRODUCT_MIN_PLAYERS] . "<br />";
					echo "Line $rowcount will not be uploaded." . "<br />";
					continue;
				}
				else if (!(is_int((int)$store[$PRODUCT_MAX_PLAYERS]))) {
					echo "Line $rowcount, PRODUCT-MAX_PLAYERS is not an integer: " . $store[$PRODUCT_MAX_PLAYERS] . "<br />";
					echo "Line $rowcount will not be uploaded." . "<br />";
					continue;
				}
				else if ($last == '%' && (is_numeric($percent) || is_int($percent))) {
					$store[$PRODUCT_DISCOUNT_PRICE] = $store[$PRODUCT_PRICE] * $percent / 100;
				}
				else if (!(is_numeric($store[$PRODUCT_DISCOUNT_PRICE]))) {
					echo "Line $rowcount, PRODUCT-DISCOUNT_PRICE is not numeric: " . $store[$PRODUCT_DISCOUNT_PRICE] . "<br />";
					echo "Line $rowcount will not be uploaded." . "<br />";
					continue;
				}
				else if (!(is_int((int)$store[$PRODUCT_RENTAL_POINTS]))) {
					echo "Line $rowcount, PRODUCT-RENTAL_POINTS] is not an integer: " . $store[$PRODUCT_RENTAL_POINTS]. "<br />";
					echo "Line $rowcount will not be uploaded." . "<br />";
					continue;
				}	
				else if (!(is_int((int)$store[$PRODUCT_RENTAL_QUANTITY]))) {
					echo "Line $rowcount, PRODUCT-RENTAL_QUANTITY] is not an integer: " . $store[$PRODUCT_RENTAL_QUANTITY]. "<br />";
					echo "Line $rowcount will not be uploaded." . "<br />";
					continue;
				}	
				else if (!(is_int((int)$store[$PRODUCT_PRODUCTS_SOLD]))) {
					echo "Line $rowcount, PRODUCT-PRODUCTS_SOLD] is not an integer: " . $store[$PRODUCT_PRODUCTS_SOLD]. "<br />";
					echo "Line $rowcount will not be uploaded." . "<br />";
					continue;
				}	

  			// Start Add Manufacturer
				$manu_id=0;
				// check if manufacturer field == blank
	  		// remove white space
				$trimManu=str_replace(array("\n", "\r", "\t", " ", "\o", "\xOB"), '', $store[$MANUFACTURER_LABEL]);
				if ($trimManu=="") {
	  			$manu_id=0;
				}
				else {
	  			// check if manufacturer exists in manufacturer table
	  			$dat = mysql_query("SELECT ID from manufacturer where TITLE='$store[$MANUFACTURER_LABEL]'") or die(mysql_error());
	  			while ($row = mysql_fetch_assoc($dat)) {
	    			$manu_id = $row['ID'];
	  			}
	  			// if exists, do nothing
	  			if ($manu_id >= 1) {
	    			//echo "Manufacturer Updated" . "<br />";
	  			}
	  			// else insert manufacturer
      	  else {
            mysql_query("INSERT INTO manufacturer (TITLE) VALUES ('$store[$MANUFACTURER_LABEL]')") or die(mysql_error());
	    			//echo "Manufacturer inserted: INSERT INTO manufacturer (TITLE) VALUES ('$store[$MANUFACTURER_LABEL]')" . "<br />";
	    			$dat = mysql_query("SELECT ID from manufacturer where TITLE='$store[$MANUFACTURER_LABEL]'") or die(mysql_error());
	    			while ($row = mysql_fetch_assoc($dat)) {
	      			$manu_id = $row['ID'];
	    			}
      	  }
				}
  			// End Add Manufacturer

  			// Start Add Product
				// check if model no. exists in product table
				$prod_id=0;
//				$dat = mysql_query("SELECT ID from product where TITLE='$store[$PRODUCT_TITLE]'") or die(mysql_error());
//echo $store[$PRODUCT_MODEL]."<br>";
				$dat = mysql_query("SELECT ID from product where MODEL='$store[$PRODUCT_MODEL]'") or die(mysql_error());
				while ($row = mysql_fetch_assoc($dat)) {
	  			$prod_id = $row['ID'];
				}

				// if exists, update
				if ($prod_id >= 1) {
				//echo "UPDATE product SET DESCRIPTION='$store[$PRODUCT_DESCRIPTION]',TITLE='$store[$PRODUCT_TITLE]', MANUFACTURER_ID=$manu_id, PICTURE_PATH='$store[$PRODUCT_PICTURE_PATH]', IMAGES='$store[$PRODUCT_IMAGES]', PRICE=$store[$PRODUCT_PRICE], QUANTITY=$store[$PRODUCT_QUANTITY], WEIGHT=$store[$PRODUCT_WEIGHT], DATE_AVAIL=$date_avail, DATE_ADDED=$date_added, STATUS=$status, MIN_PLAYERS=$store[$PRODUCT_MIN_PLAYERS], MAX_PLAYERS=$store[$PRODUCT_MAX_PLAYERS], YEAR=$year, DISCOUNT_FLAG=$discount_flag, DISCOUNT_PRICE=$store[$PRODUCT_DISCOUNT_PRICE],RENTAL_FLAG=$rental_flag, RENTAL_POINTS=$store[$PRODUCT_RENTAL_POINTS] WHERE ID = $prod_id";
	  			//mysql_query("UPDATE product SET DESCRIPTION='$store[$PRODUCT_DESCRIPTION]',TITLE='$store[$PRODUCT_TITLE]', MANUFACTURER_ID=$manu_id, PICTURE_PATH='$store[$PRODUCT_PICTURE_PATH]', IMAGES='$store[$PRODUCT_IMAGES]', PRICE=$store[$PRODUCT_PRICE], QUANTITY=$store[$PRODUCT_QUANTITY], WEIGHT=$store[$PRODUCT_WEIGHT], DATE_AVAIL=$date_avail, DATE_ADDED=$date_added, STATUS=$status, MIN_PLAYERS=$store[$PRODUCT_MIN_PLAYERS], MAX_PLAYERS=$store[$PRODUCT_MAX_PLAYERS], YEAR=$year, DISCOUNT_FLAG=$discount_flag, DISCOUNT_PRICE=$store[$PRODUCT_DISCOUNT_PRICE], RENTAL_FLAG=$rental_flag, RENTAL_POINTS=$store[$PRODUCT_RENTAL_POINTS], PRODUCTS_SOLD=$store[$PRODUCT_PRODUCTS_SOLD] WHERE ID = $prod_id") or die(mysql_error());
				// automate date_added
				mysql_query("UPDATE product SET DESCRIPTION='$store[$PRODUCT_DESCRIPTION]',TITLE='$store[$PRODUCT_TITLE]', MANUFACTURER_ID=$manu_id, PICTURE_PATH='$store[$PRODUCT_PICTURE_PATH]', IMAGES='$store[$PRODUCT_IMAGES]', PRICE=$store[$PRODUCT_PRICE], QUANTITY=$store[$PRODUCT_QUANTITY], WEIGHT=$store[$PRODUCT_WEIGHT], DATE_AVAIL=$date_avail, STATUS=$status, MIN_PLAYERS=$store[$PRODUCT_MIN_PLAYERS], MAX_PLAYERS=$store[$PRODUCT_MAX_PLAYERS], YEAR=$year, DISCOUNT_FLAG=$discount_flag, DISCOUNT_PRICE=$store[$PRODUCT_DISCOUNT_PRICE], RENTAL_FLAG=$rental_flag, RENTAL_QUANTITY=$store[$PRODUCT_RENTAL_QUANTITY], RENTAL_POINTS=$store[$PRODUCT_RENTAL_POINTS], PRODUCTS_SOLD=$store[$PRODUCT_PRODUCTS_SOLD] WHERE ID = $prod_id") or die(mysql_error());
	  			//echo "Product updated: Model = '$store[$PRODUCT_MODEL]'" . "<br />";
				}
				// else insert product
      	else {
      	//echo "INSERT INTO product (TITLE, DESCRIPTION, MODEL, MIN_PLAYERS, MAX_PLAYERS, YEAR, MANUFACTURER_ID, PICTURE_PATH, IMAGES, PRICE, QUANTITY, WEIGHT, DISCOUNT_FLAG, DISCOUNT_PRICE, DATE_AVAIL, DATE_ADDED, RENTAL_FLAG, RENTAL_QUANTITY, RENTAL_POINTS, STATUS, PRODUCTS_SOLD) VALUES ('$store[$PRODUCT_TITLE]','$store[$PRODUCT_DESCRIPTION]','$store[$PRODUCT_MODEL]',$store[$PRODUCT_MIN_PLAYERS],$store[$PRODUCT_MAX_PLAYERS],$year,$manu_id,'$store[$PRODUCT_PICTURE_PATH]','$store[$PRODUCT_IMAGES]',$store[$PRODUCT_PRICE],$store[$PRODUCT_QUANTITY],$store[$PRODUCT_WEIGHT],$discount_flag,$store[$PRODUCT_DISCOUNT_PRICE],$date_avail,$date_added,$rental_flag,$store[$PRODUCT_RENTAL_QUANTITY],$store[$PRODUCT_RENTAL_POINTS],$status,$store[$PRODUCT_PRODUCTS_SOLD])";
      	
          mysql_query("INSERT INTO product (TITLE, DESCRIPTION, MODEL, MIN_PLAYERS, MAX_PLAYERS, YEAR, MANUFACTURER_ID, PICTURE_PATH, IMAGES, PRICE, QUANTITY, WEIGHT, DISCOUNT_FLAG, DISCOUNT_PRICE, DATE_AVAIL, DATE_ADDED, RENTAL_FLAG, RENTAL_QUANTITY, RENTAL_POINTS, STATUS, PRODUCTS_SOLD) VALUES ('$store[$PRODUCT_TITLE]','$store[$PRODUCT_DESCRIPTION]','$store[$PRODUCT_MODEL]',$store[$PRODUCT_MIN_PLAYERS],$store[$PRODUCT_MAX_PLAYERS],$year,$manu_id,'$store[$PRODUCT_PICTURE_PATH]','$store[$PRODUCT_IMAGES]',$store[$PRODUCT_PRICE],$store[$PRODUCT_QUANTITY],$store[$PRODUCT_WEIGHT],$discount_flag,$store[$PRODUCT_DISCOUNT_PRICE],$date_avail,$date_added,$rental_flag,$store[$PRODUCT_RENTAL_QUANTITY],$store[$PRODUCT_RENTAL_POINTS],$status,$store[$PRODUCT_PRODUCTS_SOLD])") or die(mysql_error());
	  			//echo "Product inserted: Model = '$store[$PRODUCT_MODEL]'" . "<br />";
				
	  			$dat = mysql_query("SELECT ID from product where MODEL='$store[$PRODUCT_MODEL]'") or die(mysql_error());
	  			while ($row = mysql_fetch_assoc($dat)) {
	    			$prod_id = $row['ID'];
	  			}

      	}
  			// End Add Product

  			// Start Add category
				$start = 0;
				//echo "max_cat:".$max_cat . "<br />";
				while ($start < $max_cat) {
					$start = $start + 1; 
					$temp_title = "CATEGORY_" . $start;
					$current_cat = $store[$$temp_title];
					//echo $current_cat . "<br />";
					if ($current_cat != "" ) {
	  				$categ_id=0;
 	  				$dat = mysql_query("SELECT ID from product_category where PRODUCT_ID=$prod_id and CATEGORY='$current_cat'") or die(mysql_error());
	  				while ($row = mysql_fetch_assoc($dat)) {
		    			$categ_id = $row['ID'];
	  				}
	  				if ($categ_id < 1) {
	    				mysql_query("INSERT INTO product_category (PRODUCT_ID,CATEGORY) VALUES ($prod_id,'$current_cat')") or die(mysql_error()); 
	    				//echo "Product Cateogory inserted: INSERT INTO product_category (PRODUCT_ID,CATEGORY) VALUES ($prod_id,'$current_cat')" . "<br />";
	  				}
	  			}
				}
  			// End add category

  			// Check all categories are correct or delete category
				$dat = mysql_query("SELECT CATEGORY from product_category where PRODUCT_ID=$prod_id") or die(mysql_error());
				while ($row = mysql_fetch_assoc($dat)) {
				  $temp_category = $row['CATEGORY'];
				  $found = 0;
				  $start = 0;
				  while ($start < $max_cat) {
				  	$start = $start + 1;
				  	$temp_title = "CATEGORY_" . $start;
	  			  if ($temp_category == $store[$$temp_title]) {
	  			    $found = 1;
	 			   	}
	  			}
	  			if ($found == 0) {
				  $temp_category = str_replace("'","\'",$temp_category);
	    			mysql_query("DELETE from product_category where PRODUCT_ID=$prod_id and CATEGORY='$temp_category'") or die(mysql_error());
	    			//echo "Product category deleted: DELETE from product_category where PRODUCT_ID=$prod_id and CATEGORY='$temp_category'" . "<br />";
	 			 	}
				}
  			// End check all categories are correct or delete category

				// Start Add attribute
				$start=0;
				while ($start < $max_attrib) {
					$start = $start + 1; 
					$temp_name = "ATTRIBUTE_" . $start . "_NAME";
					$temp_values = "ATTRIBUTE_" . $start . "_VALUES";
					$temp_price = "ATTRIBUTE_" . $start . "_PRICE";
					$attrib_name = $store[$$temp_name];
					$attrib_values = $store[$$temp_values];
					$attrib_price = $store[$$temp_price];
					//echo $attrib_name . "  " . $attrib_values . "  " . $attrib_price . "<br />";
					
					if (!(is_numeric($attrib_price)) || empty($attrib_price))
					  $attrib_price = 0;

					/*
					if (!(is_numeric($attrib_price)) && $attrib_price != "") {
						echo "Line $rowcount, $temp_price is not a numeric: " . $attrib_price . "<br />";
						echo "Line $rowcount, attribute  " . $start . " will not be uploaded." . "<br />";
						break;
					}
					*/
					if (!(empty($attrib_name))) {
	  				$categ_id=0;
	  				
 	  				$dat = mysql_query("SELECT ID from products_attributes where PRODUCT_ID=$prod_id and OPTIONS_NAME='$attrib_name' and OPTIONS_VALUES_NAME='$attrib_values' and OPTIONS_VALUES_PRICE=$attrib_price") or die(mysql_error());
	  				while ($row = mysql_fetch_assoc($dat)) {
		    			$categ_id = $row['ID'];
		    			//echo "Category id: " . $categ_id . "<br />";
	  				}
	  				if ($categ_id < 1) {
	  				//echo "Product attribute inserted: INSERT INTO products_attributes (PRODUCT_ID,OPTIONS_NAME,OPTIONS_VALUES_NAME,OPTIONS_VALUES_PRICE) VALUES ($prod_id,'$attrib_name','$attrib_values',$attrib_price)" . "<br />";	
	    				mysql_query("INSERT INTO products_attributes (PRODUCT_ID,OPTIONS_NAME,OPTIONS_VALUES_NAME,OPTIONS_VALUES_PRICE) VALUES ($prod_id,'$attrib_name','$attrib_values',$attrib_price)") or die(mysql_error());   
	    				
	    				//echo "inserted" . "<br />";
	    				
	  				}
	  			}
				}	
				// End add attribute
				
				// Check all attributes are correct or delete category
				$dat = mysql_query("SELECT * from products_attributes where PRODUCT_ID=$prod_id") or die(mysql_error());
				while ($row = mysql_fetch_assoc($dat)) {
				  $attrib_name = $row['OPTIONS_NAME'];
				  $attrib_values = $row['OPTIONS_VALUES_NAME'];
				  $attrib_price = $row['OPTIONS_VALUES_PRICE'];
				  $found = 0;
				  $start = 0;

				  while ($start < $max_attrib) {
				  	$start = $start + 1;
				  	$temp_name = "ATTRIBUTE_" . $start . "_NAME";
						$temp_values = "ATTRIBUTE_" . $start . "_VALUES";
						$temp_price = "ATTRIBUTE_" . $start . "_PRICE";

				  if (!(is_numeric($store[$$temp_price])) || empty($store[$$temp_price]))
					  $store[$$temp_price] = 0;

						if ($attrib_name == $store[$$temp_name] && $attrib_values == $store[$$temp_values] && $attrib_price == $store[$$temp_price]) {
	  			    $found = 1;
	 			   	}
	  			}
	  			if ($found == 0) {
				  $attrib_name = str_replace("'","\'",$attrib_name);
				  $attrib_values = str_replace("'","\'",$attrib_values);
				
	    			mysql_query("DELETE from products_attributes where PRODUCT_ID=$prod_id and OPTIONS_NAME='$attrib_name' and OPTIONS_VALUES_NAME='$attrib_values' and OPTIONS_VALUES_PRICE=$attrib_price") or die(mysql_error());
	    			//echo "Product attribute deleted: DELETE from products_attributes where PRODUCT_ID=$prod_id and OPTIONS_NAME='$attrib_name' and OPTIONS_VALUES_NAME='$attrib_values' and OPTIONS_VALUES_PRICE=$attrib_price" . "<br />";
	 			 	}
				}
  			// End check all categories are correct or delete category
  			
      } // end else if ($rowcount>1 && $exit==0)

    } // next line - end while (($data = fgetcsv($fh, 65536, ",")) !== FALSE)

    mysql_close($conn);
    fclose($fh);
    echo "File closed...". "<br />";
    echo "<a href='index.php'>RETURN TO PAGE</a>";
    // delete uploaded file
    unlink($ourFileName);
    
	}

?>