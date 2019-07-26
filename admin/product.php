<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
require_once('db_config.php');
if(isset($_GET["operation"]) && $_GET["operation"] == "deletecategory"){
  $product_search = 0;
  echo "<b>Category '" . $_GET["category"] . "' Deleted!</b>";

  $dat = mysql_query("DELETE FROM product_category WHERE CATEGORY='".$_GET["category"]."'") or die(mysql_error());

}
require_once("../php_shop/store_class.php");
require_once("../php_shop/common_func.php");
$store_obj = new storeAccess();
$product_search = 0;
$product_edit = 0;

$cat_obj = $store_obj->getCategories();

if(isset($_POST["operation"]) && $_POST["operation"] == "prod_delete"){
  $product_search = 1;

  $get_title = mysql_query("SELECT TITLE FROM product WHERE ID=".$_POST["prod_id"]) or die(mysql_error());
  while ($row = mysql_fetch_assoc($get_title)) {
    $temp_title = $row["TITLE"];
  }

  mysql_query("DELETE FROM products_attributes WHERE PRODUCT_ID=".$_POST["prod_id"]) or die(mysql_error());
  mysql_query("DELETE FROM product_category WHERE PRODUCT_ID=".$_POST["prod_id"]) or die(mysql_error());
  mysql_query("DELETE FROM product WHERE ID=".$_POST["prod_id"]) or die(mysql_error());
  echo "<b>" . $temp_title . "</b> has been deleted<br><br>" ;
  
}

if(isset($_POST["operation"]) && $_POST["operation"] == "addproduct"){
  if (($_POST["model"])<>"" && ($_POST["title"])<>"") {
  $product_search = 1;
  $date_avail = date("YmdHis");
  $date_added = date("YmdHis");
  $year = date("Y",strtotime($date_added));

  mysql_query("INSERT INTO product (TITLE, DESCRIPTION, MODEL, MIN_PLAYERS, MAX_PLAYERS, YEAR, MANUFACTURER_ID, PICTURE_PATH, IMAGES, PRICE, QUANTITY, WEIGHT, DISCOUNT_FLAG, DISCOUNT_PRICE, DATE_AVAIL, DATE_ADDED, RENTAL_FLAG, RENTAL_POINTS, STATUS, PRODUCTS_SOLD) VALUES ('".$_POST["title"]."','','".$_POST["model"]."',0,0,$year,0,'','',0,0,0,0,0,$date_avail,$date_added,0,0,0,0)") or die(mysql_error());

  $dat = mysql_query("SELECT ID FROM product WHERE MODEL = '" . $_POST["model"] . "'") or die(mysql_error());
	while ($row = mysql_fetch_assoc($dat)) { 
	  $id = $row["ID"];
	}
  $_POST["prod_id"]= array($id);

  $dat = mysql_query("SELECT ID, MODEL, TITLE, PRICE, QUANTITY, PRODUCTS_SOLD FROM product WHERE MODEL = '" . $_POST["model"] . "'") or die(mysql_error());
  }
  else
    echo "<b>Both Model & Title must be entered</b>";

  $_POST["operation"] = "prod_func";

}

else if (isset($_POST["operation"]) && $_POST["operation"] == "prod_edit"){
    if (isset($_POST["deletecat"])){
        echo "<p><b>".$_POST["cat_name"]." Category deleted from product details.</b>";
	mysql_query("DELETE from product_category WHERE PRODUCT_ID=".$_POST['prod_id']." AND CATEGORY='".$_POST["cat_name"]."'") or die(mysql_error());
    }
    else if (isset($_POST["insertcat"])){
        echo "<p><b>".$_POST["newcat"]." has been added as a category of this product.</b>";
	mysql_query("INSERT INTO product_category (PRODUCT_ID,CATEGORY) VALUES (".$_POST['prod_id'].",'".$_POST["newcat"]."')") or die(mysql_error()); 
    }
    else if (isset($_POST["deleteattr"])){
        echo "<p><b>Attribute deleted from product details.</b>";
	mysql_query("DELETE from products_attributes WHERE ID=".$_POST['attr_id']) or die(mysql_error());
    }
    else if (isset($_POST["insertattr"])){
        echo "<p><b>Attribute has been added to this product.</b>";
	mysql_query("INSERT INTO products_attributes (PRODUCT_ID,OPTIONS_NAME,OPTIONS_VALUES_NAME,OPTIONS_VALUES_PRICE) VALUES (".$_POST["prod_id"].",'".$_POST['attr_name']."','".$_POST["attr_value"]."',".$_POST["attr_price"].")") or die(mysql_error()); 
    }
    else if (isset($_POST["edit_title"])){
        echo "<p><b>Product Updated</b>";
	mysql_query("UPDATE product SET TITLE='".$_POST["name"]."' WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
    }
    else if (isset($_POST["edit_model"])){
        echo "<p><b>Product Updated</b>";
	mysql_query("UPDATE product SET MODEL='".$_POST["name"]."' WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
    }
    else if (isset($_POST["edit_desc"])){
        echo "<p><b>Product Updated</b>";
	mysql_query("UPDATE product SET DESCRIPTION='".$_POST["name"]."' WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
    }
    else if (isset($_POST["edit_picpath"])){
        echo "<p><b>Product Updated</b>";
	mysql_query("UPDATE product SET PICTURE_PATH='".$_POST["name"]."' WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
    }
    else if (isset($_POST["edit_images"])){
        echo "<p><b>Product Updated</b>";
	mysql_query("UPDATE product SET IMAGES='".$_POST["name"]."' WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
    }
    else if (isset($_POST["edit_price"])){
        echo "<p><b>Product Updated</b>";
	mysql_query("UPDATE product SET PRICE=".$_POST["name"]." WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
    }
    else if (isset($_POST["edit_quantity"])){
        echo "<p><b>Product Updated</b>";
	mysql_query("UPDATE product SET QUANTITY=".$_POST["name"]." WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
    }
    else if (isset($_POST["edit_rent_quantity"])){
        echo "<p><b>Product Updated</b>";
	mysql_query("UPDATE product SET RENTAL_QUANTITY=".$_POST["name"]." WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
    }
    else if (isset($_POST["edit_weight"])){
        echo "<p><b>Product Updated</b>";
	mysql_query("UPDATE product SET WEIGHT=".$_POST["name"]." WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
    }
    else if (isset($_POST["edit_disc_sts"])){
        echo "<p><b>Product Updated</b>";
	if ($_POST["name"]==1)
 	  mysql_query("UPDATE product SET DISCOUNT_FLAG=0 WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
	else
	  mysql_query("UPDATE product SET DISCOUNT_FLAG=1 WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
    }
    else if (isset($_POST["edit_disc_price"])){
        echo "<p><b>Product Updated</b>";
	mysql_query("UPDATE product SET DISCOUNT_PRICE=".$_POST["name"]." WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
    }
    else if (isset($_POST["edit_sts"])){
        echo "<p><b>Product Updated</b>";
	if ($_POST["name"]==1)
 	  mysql_query("UPDATE product SET STATUS=0 WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
	else
	  mysql_query("UPDATE product SET STATUS=1 WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
    }
    else if (isset($_POST["edit_rent_sts"])){
        echo "<p><b>Product Updated</b>";
	if ($_POST["name"]==1)
 	  mysql_query("UPDATE product SET RENTAL_FLAG=0 WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
	else
	  mysql_query("UPDATE product SET RENTAL_FLAG=1 WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
    }
    else if (isset($_POST["edit_rent_points"])){
        echo "<p><b>Product Updated</b>";
	mysql_query("UPDATE product SET RENTAL_POINTS=".$_POST["name"]." WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
    }
    else if (isset($_POST["edit_date_avail"])){
        echo "<p><b>Product Updated</b>";
	$str = $_POST["year"] . "-" . $_POST["month"] . "-" . $_POST["day"] . " 0:0:00";
	$temp_date = date("YmdHis",strtotime($str));
	mysql_query("UPDATE product SET DATE_AVAIL=$temp_date WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
    }
    else if (isset($_POST["edit_manu"])){
        echo "<p><b>Product Updated</b>";
  			// Start Add Manufacturer
				$manu_id=0;
				// check if manufacturer field == blank
	  		// remove white space
				$trimManu=str_replace(array("\n", "\r", "\t", " ", "\o", "\xOB"), '', $_POST["name"]);
				if ($trimManu=="") {
	  			  $manu_id=0;
				}
				else {
	  			// check if manufacturer exists in manufacturer table
	  			$dat = mysql_query("SELECT ID from manufacturer where TITLE='".$_POST["name"]."'") or die(mysql_error());
	  			while ($row = mysql_fetch_assoc($dat)) {
	    			  $manu_id = $row['ID'];
	  			}
	  			// if exists, do nothing
	  			if ($manu_id >= 1) {
	    			//echo "Manufacturer Updated" . "<br />";
	  			}
	  			// else insert manufacturer
      	  else {
            mysql_query("INSERT INTO manufacturer (TITLE) VALUES ('".$_POST["name"]."')") or die(mysql_error());
	    			//echo "Manufacturer inserted: INSERT INTO manufacturer (TITLE) VALUES ('$store[$MANUFACTURER_LABEL]')" . "<br />";
	    			$dat = mysql_query("SELECT ID from manufacturer where TITLE='".$_POST["name"]."'") or die(mysql_error());
	    			while ($row = mysql_fetch_assoc($dat)) {
	      			  $manu_id = $row['ID'];
	    			}
      	  }
				}
  			// End Add Manufacturer

	mysql_query("UPDATE product SET MANUFACTURER_ID=$manu_id WHERE ID=".$_POST["prod_id"]) or die(mysql_error()); 
    }
}

$view_flag = "ALL";
$cat_tmp = "***";
if(isset($_GET["operation"]) && $_GET["operation"] == "searchproducts"){
  $product_search = 1;
  $cat_tmp = $_GET["category"];

  /*
  if ($_GET["category"]=='***')
    $dat = mysql_query("SELECT ID, MODEL, TITLE, PRICE, QUANTITY, PRODUCTS_SOLD FROM product WHERE TITLE like '%" . $_GET['searchfield'] . "%'") or die(mysql_error());
  else
    $dat = mysql_query("SELECT ID, MODEL, TITLE, PRICE, QUANTITY, PRODUCTS_SOLD FROM product WHERE TITLE like '%" . $_GET['searchfield'] . "%' AND ID in (SELECT PRODUCT_ID from product_category where CATEGORY='" . $_GET["category"] . "')") or die(mysql_error());
  */
  $view_flag = $_GET["view_type"];
  $dat = $store_obj->searchDB2($_GET['searchfield'],"","","",NULL,NULL,0,-1,"","",$view_flag);
}


?>
<table class="invis"><tr><td>
<table class="admintable1">
<tr><td><b>Product Search</b><form action="" method="GET">
<input type="hidden" name="p" value="products">
<input type="hidden" name="operation" value="searchproducts">
<input type="text" name="searchfield" size=30>
<select name="category">
<option value="***">All Categories&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
<?php
  foreach($cat_obj as $cat_single){
    echo "<option value='".$cat_single["CAT"]."' ";
    if($cat_single["CAT"] == $cat_tmp) echo "selected";
    echo ">".$cat_single["CAT"]."</option>";
  }
?>
</select><input type="submit" value="Search"><br>
<input type="radio" name="view_type" value="Complete" <?php if($view_flag == "Complete") echo "checked";?>>All (even inactive items)<input type="radio" name="view_type" value="ALL" <?php if($view_flag == "ALL") echo "checked";?>>Both Sale and Rent&nbsp;&nbsp;<br><input type="radio" name="view_type" value="Sale" <?php if($view_flag == "Sale") echo "checked";?>>For Sale&nbsp;&nbsp;<input type="radio" name="view_type" value="Rent" <?php if($view_flag == "Rent") echo "checked";?>>For Rent&nbsp;&nbsp;<br>
</form>
</td></tr></table></td><td>
<table class="admintable1"><tr><td>
<b>Delete Product Category</b>
<form action="" method="GET">
<input type="hidden" name="p" value="products">
<input type="hidden" name="operation" value="deletecategory">
<select name="category">
<?php
  foreach($cat_obj as $cat_single){
    echo "<option value='".$cat_single["CAT"]."'>".$cat_single["CAT"]."</option>";
  }
?>
</select><input type="submit" value="Delete Category"><br>
</form>

</td></tr></table>
</td>
<td>
<table class="admintable1">
<tr><td><b>Add New Product</b><form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="p" value="products">
<input type="hidden" name="operation" value="addproduct">
<b>Model </b><input type="text" name="model" size=20 maxlength=20><input type="submit" value="Add New Product"><br>
<b>Title </b><input type="text" name="title" size=30 maxlength=80><br>

</form>
</td></tr></table></td>
</tr></table>

<?php
if ($product_search==1) {
if (mysql_num_rows($dat)==0){
  echo "<p><b>Unable to find any records with search string: ".$_GET['searchfield']."</b></p>";
}
else {
?>

<table class="invis"><tr><td>
<div style="overflow:auto;height:500px;width:500px;padding:3px 3px 3px 3px;">
 <table class="simplelist">
  <tr>
   <td></td>
   <td><b>MODEL</b></td>
   <td><b>TITLE</b></td>
   <td><b>PRICE</b></td>
   <td><b>STOCK</b></td>
   <td><b>SOLD</b></td>
  </tr>
<form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="operation" value="prod_func">
  <?php
	while ($row = mysql_fetch_array($dat)) {

	  $id = $row["ID"];

  	  if ($_GET["category"]=='***')
    	    $dat5 = mysql_query("SELECT ID, MODEL, TITLE, PRICE, QUANTITY, PRODUCTS_SOLD FROM product WHERE ID=$id") or die(mysql_error());
  	  else
    	    $dat5 = mysql_query("SELECT ID, MODEL, TITLE, PRICE, QUANTITY, PRODUCTS_SOLD FROM product WHERE ID=$id AND ID in (SELECT PRODUCT_ID from product_category where CATEGORY='" . $_GET["category"] . "')") or die(mysql_error());

	  //$dat5 = mysql_query("SELECT ID, MODEL, TITLE, PRICE, QUANTITY, PRODUCTS_SOLD FROM product WHERE ID=$id") or die(mysql_error());

	  $found = 0;
  	  while ($row5 = mysql_fetch_assoc($dat5)) {
	    $found = 1;
  	    $title = $row5["TITLE"];
  	    $model = $row5["MODEL"];
  	    $price = $row5["PRICE"];
  	    $qty = $row5["QUANTITY"];
  	    $sold = $row5["PRODUCTS_SOLD"];
	  }

	  if ($found == 1) {
	    echo "<tr><td><input type='checkbox' name='prod_id[]' value=".$id."></td>";
	    echo "<td>".$model."</td>";
	    echo "<td>".$title."</td>";
	    echo "<td>".$price."</td>";
	    echo "<td>".$qty."</td>";
	    echo "<td>".$sold."</td>";
            echo "</tr>";
	  }
	}
?>
   
 </table></div>
</td><td>
<table class="admintable1"><tr><td><input type="submit" name="viewdetails" value="View Details"></form></td></tr></table>
</td>
<?php
  if(isset($_POST["operation"]) && $_POST["operation"] == "prod_func"){
    if (isset($_POST["prod_id"])){
      $prod_tmps = $_POST["prod_id"];
      if(count($prod_tmps) > 0){
        foreach($prod_tmps AS $prod_tmp){
      	  echo "<td><div style='overflow:auto;height:500px;width:500px;padding:3px 3px 3px 3px;'><table class='formtable'>";
          $dat1 = mysql_query("SELECT * FROM product WHERE ID=".$prod_tmp) or die(mysql_error());
	  while ($data = mysql_fetch_assoc($dat1)) {

	    echo "<tr><td>ID</td><td><b>".$data["ID"]."</b></td></tr>";
	    echo "<tr><td>Title</td><td><b>".$data["TITLE"]."</b></td></tr>";
	    echo "<tr><td>Model</td><td><b>".$data["MODEL"]."</b></td></tr>";
	    echo "<tr><td>Description</td><td><b>".$data["DESCRIPTION"]."</b></td></tr>";
	    $dat3 = mysql_query("SELECT CATEGORY FROM product_category WHERE PRODUCT_ID=".$prod_tmp) or die(mysql_error());
	      echo "<tr><td>Category</td><td><b>";
	      $first = 1;
	    while ($row3 = mysql_fetch_assoc($dat3)) {
	      if ($first > 1)
	        echo ", ";
	      echo $row3["CATEGORY"]; 
	      $first = $first + 1;
	    }
	      echo "</b></td></tr>";
	    $dat4 = mysql_query("SELECT * FROM products_attributes WHERE PRODUCT_ID=".$prod_tmp) or die(mysql_error());
	      echo "<tr><td>Attributes</td><td><b>";
	      echo "<table class='invisible'><tr><td align=center>Attribute Name</td><td align=center>Attribute Value</td><td>Price</td></tr>";
	    while ($row4 = mysql_fetch_assoc($dat4)) {
	      echo "<tr><td><b>" . $row4["OPTIONS_NAME"] . "</b></td><td><b>" . $row4["OPTIONS_VALUES_NAME"] . "</b></td><td><b>" . $row4["OPTIONS_VALUES_PRICE"] . "</b></td>"; 
	    }
	      echo "</table>";
	      echo "</b></td></tr>";
	    echo "<tr><td>Min Players</td><td><b>".$data["MIN_PLAYERS"]."</b></td></tr>";
	    echo "<tr><td>Max Players</td><td><b>".$data["MAX_PLAYERS"]."</b></td></tr>";
	    $dat2 = mysql_query("SELECT TITLE FROM manufacturer WHERE ID=" . $data["MANUFACTURER_ID"]) or die(mysql_error());
	    while ($row2 = mysql_fetch_assoc($dat2)) {
	      echo "<tr><td>Manufacturer</td><td><b>".$row2['TITLE']."</b></td></tr>";
	    }
	    echo "<tr><td>Picture Path</td><td><b>".$data["PICTURE_PATH"]."</b></td></tr>";
	    echo "<tr><td>Images</td><td><b>".$data["IMAGES"]."</b></td></tr>";
	    echo "<tr><td>Price</td><td><b>".$data["PRICE"]."</b></td></tr>";
	    echo "<tr><td>Sale Quantity</td><td><b>".$data["QUANTITY"]."</b></td></tr>";
	    echo "<tr><td>Weight</td><td><b>".$data["WEIGHT"]."</b></td></tr>";

	    if ($data["DISCOUNT_FLAG"]==1)
	      echo "<tr><td>Discount Status</td><td><b>Discount Available</b></td></tr>";
	    else
	      echo "<tr><td>Discount Status</td><td><b>No Discount Available</b></td></tr>";

	    echo "<tr><td>Discount Price</td><td><b>".$data["DISCOUNT_PRICE"]."</b></td></tr>";
	    echo "<tr><td>Date Available</td><td><b>".$data["DATE_AVAIL"]."</b></td></tr>";
	    echo "<tr><td>Date Added</td><td><b>".$data["DATE_ADDED"]."</b></td></tr>";

	    if ($data["STATUS"]==1)
	      echo "<tr><td>Sale Status</td><td><b>Available for Sale</b></td></tr>";
	    else
	      echo "<tr><td>Sale Status</td><td><b>Not Available for Sale</b></td></tr>";

	    echo "<tr><td>Products Sold</td><td><b>".$data["PRODUCTS_SOLD"]."</b></td></tr>";
	    echo "<tr><td colspan=2 style='background-color:#AAAAFF;'><b>RENTAL DETAILS</b></td></tr>";
	    if ($data["RENTAL_FLAG"]==1)
	      echo "<tr><td>Rental Status</td><td><b>Available for Rent</b></td></tr>";
	    else
	      echo "<tr><td>Rental Status</td><td><b>Not Available for Rent</b></td></tr>";

	    echo "<tr><td>Rental Points</td><td><b>".$data["RENTAL_POINTS"]."</b></td></tr>";
      echo "<tr><td>Rental Quantity</td><td><b>".$data["RENTAL_QUANTITY"]."</b></td></tr>";
      echo "<tr><td>Products Rented</td><td><b>".$data["PRODUCTS_RENTED"]."</b></td></tr>";
	  }
          echo "</table></div>";
?>

<p><table class="admintable1"><tr><td>
<form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="operation" value="prod_edit">
<input type="submit" name="editprod" value="Edit">
<?php
echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";

?>
</form>
</td><td>
<form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="operation" value="prod_delete">
<input type="submit" name="deleteprod" value="Delete">
<?php
echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";

?>
</form>
</td>
</tr></table>
<?php
	  echo "</td>";
        }
      }
    }
    else
      echo "<p><b>No product selected!</b>";
  }
  else if (isset($_POST["operation"]) && $_POST["operation"] == "prod_edit"){

    if (isset($_POST["prod_id"])){
      $prod_tmp = $_POST["prod_id"];
      	  echo "<td><div style='overflow:auto;height:500px;width:600px;padding:3px 3px 3px 3px;'><table class='formtable'>";
          $dat1 = mysql_query("SELECT * FROM product WHERE ID=".$prod_tmp) or die(mysql_error());
	  while ($data = mysql_fetch_assoc($dat1)) {

	    echo "<tr><td>ID</td><td><b>".$data["ID"]."</b></td></tr>";
	    ?><tr><td>Title</td><td>
<form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="operation" value="prod_edit">
<input type="submit" name="edit_title" value="Update">
<?php
echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
?>
<input name="name" type="text" size=30 maxlength=80 value="<?php echo $data["TITLE"]; ?>"></form></td></tr>
	    <tr><td>Model</td><td>
<form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="operation" value="prod_edit">
<input type="submit" name="edit_model" value="Update">
<?php
echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
?>
<input name="name" type="text" size=20 maxlength=20 value="<?php echo $data["MODEL"]; ?>"></form></td></tr><?php
	    echo "<tr><td>Description</td><td><form action=".$_SERVER["REQUEST_URI"]." method='POST'><textarea rows=15 cols=56 wrap='physical' name='name'>".$data["DESCRIPTION"]."</textarea>";
?>
<input type="hidden" name="operation" value="prod_edit">
<input type="submit" name="edit_desc" value="Update">
<?php
echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . "></form>";
echo "</b></td></tr>";
	    $dat3 = mysql_query("SELECT CATEGORY FROM product_category WHERE PRODUCT_ID=".$prod_tmp) or die(mysql_error());
	      echo "<tr><td>Category</td><td><b>";
	    while ($row3 = mysql_fetch_assoc($dat3)) {
	        
?>	      
<form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="operation" value="prod_edit">
<input type="submit" name="deletecat" value="Delete">
<?php
echo "<input type='hidden' name='cat_name' value='" . $row3["CATEGORY"] . "'>";
echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
echo "</form>";

	      echo $row3["CATEGORY"];
	      echo "<br>";
	    }
          echo "<br>";
?>
<form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="operation" value="prod_edit">
<input type="submit" name="insertcat" value="Insert Category">
<select name="newcat">
<?php
  foreach($cat_obj as $cat_single){
    echo "<option value='".$cat_single["CAT"]."'>".$cat_single["CAT"]."</option>";
  }
?>
</select>
<?php
echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
?>
</form>

<form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="operation" value="prod_edit">
<input type="submit" name="insertcat" value="Insert New Category">
<input type="text" name="newcat" maxlength=30 size=30>
<?php
echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
?>
</form>
<?php
	      echo "</b></td></tr>";
	    $dat4 = mysql_query("SELECT * FROM products_attributes WHERE PRODUCT_ID=".$prod_tmp) or die(mysql_error());
	      echo "<tr><td>Attributes</td><td><b>";
	      echo "<table class='invisible'><tr><td></td><td align=center>Attribute Name</td><td align=center>Attribute Value</td><td>Price</td></tr>";
	    while ($row4 = mysql_fetch_assoc($dat4)) {
	      echo "<tr><td>";
?>
<form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="operation" value="prod_edit">
<input type="submit" name="deleteattr" value="Delete">
<?php
echo "<input type='hidden' name='attr_id' value='" . $row4["ID"] . "'>";
echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
echo "</form>";

	      echo "</td><td><b>" . $row4["OPTIONS_NAME"] . "</b></td><td><b>" . $row4["OPTIONS_VALUES_NAME"] . "</b></td><td><b>" . $row4["OPTIONS_VALUES_PRICE"] . "</b></td>"; 
	    }

?>
<tr><form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="operation" value="prod_edit">
<td><input type="submit" name="insertattr" value="Insert"></td>
<td><input type="text" name="attr_name" maxlength=64 size=20></td>
<td><input type="text" name="attr_value" maxlength=64 size=20></td>
<td><input type="text" name="attr_price" maxlength=8 size=5></td>
<?php
echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
echo "</form>";

	      echo "</tr></table>";
	      echo "</b></td></tr>";
	    echo "<tr><td>Min Players</td><td><b>".$data["MIN_PLAYERS"]."</b></td></tr>";
	    echo "<tr><td>Max Players</td><td><b>".$data["MAX_PLAYERS"]."</b></td></tr>";

	    echo "<tr><td>Manufacturer</td><td>";?>
		<form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
		<input type="hidden" name="operation" value="prod_edit">
		<input type="submit" name="edit_manu" value="Update">
		<?php
		  echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
		?>
		<input name="name" type="text" size=30 maxlength=50 value="<?php
	    $dat2 = mysql_query("SELECT TITLE FROM manufacturer WHERE ID=" . $data["MANUFACTURER_ID"]) or die(mysql_error());
	    while ($row2 = mysql_fetch_assoc($dat2)) {
		echo $row2['TITLE'];
	    }
	    ?>"><?php
	    echo "</form></td></tr>";

	    ?><tr><td>Picture Path</td><td>
<form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="operation" value="prod_edit">
<input type="submit" name="edit_picpath" value="Update">
<?php
echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
?>
<input name="name" type="text" size=50 maxlength=100 value="<?php echo $data["PICTURE_PATH"]; ?>"></form></td></tr>
	    <tr><td>Images</td><td>
<form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="operation" value="prod_edit">
<input type="submit" name="edit_images" value="Update">
<?php
echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
?>
<input name="name" type="text" size=50 maxlength=1000 value="<?php echo $data["IMAGES"]; ?>"></form></td></tr>

	    <tr><td>Price</td><td>
	      <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
	      <input type="hidden" name="operation" value="prod_edit">
	      <input type="submit" name="edit_price" value="Update">
	      <?php
	      echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
	      ?>
	      <input name="name" type="text" size=7 maxlength=7 value="<?php echo $data["PRICE"]; ?>"></form></td></tr>

	    <tr><td>Sale Quantity</td><td>
	      <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
	      <input type="hidden" name="operation" value="prod_edit">
	      <input type="submit" name="edit_quantity" value="Update">
	      <?php
	      echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
	      ?>
	      <input name="name" type="text" size=6 maxlength=6 value="<?php echo $data["QUANTITY"]; ?>"></form></td></tr>

	    <tr><td>Weight</td><td>
	      <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
	      <input type="hidden" name="operation" value="prod_edit">
	      <input type="submit" name="edit_weight" value="Update">
	      <?php
	      echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
	      ?>
	      <input name="name" type="text" size=7 maxlength=7 value="<?php echo $data["WEIGHT"]; ?>"></form></td></tr>

	    <tr><td>Discount Status</td><td>
	      <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
	      <input type="hidden" name="operation" value="prod_edit">
	      <input type="submit" name="edit_disc_sts" value="Toggle">
	      <?php
	      echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
	      ?>
	      <input type="hidden" name="name" value="<?php echo $data["DISCOUNT_FLAG"]; ?>"></form><?php
	      if ($data["DISCOUNT_FLAG"]==1)
	        echo "<b>Discount Available</b></td></tr>";
	      else
	        echo "<b>No Discount Available</b></td></tr>";?>

	    <tr><td>Discount Price</td><td>
	      <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
	      <input type="hidden" name="operation" value="prod_edit">
	      <input type="submit" name="edit_disc_price" value="Update">
	      <?php
	      echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
	      ?>
	      <input name="name" type="text" size=7 maxlength=7 value="<?php echo $data["DISCOUNT_PRICE"]; ?>"></form></td></tr>

	    <tr><td>Date Available</td><td>
	      <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
	      <input type="hidden" name="operation" value="prod_edit">
	      <input type="submit" name="edit_date_avail" value="Update">
	      <?php
	      echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";

	      $year = date("Y",strtotime($data["DATE_AVAIL"]));
	      $month = date("m",strtotime($data["DATE_AVAIL"]));
	      $day = date("d",strtotime($data["DATE_AVAIL"]));
	      ?>
<b>Year</b><input name="year" type="text" size=4 maxlength=4 value="<?php echo $year; ?>">
<b>Month</b><input name="month" type="text" size=2 maxlength=2 value="<?php echo $month; ?>">
<b>Day</b><input name="day" type="text" size=2 maxlength=2 value="<?php echo $day; ?>">
</form></td></tr>
<?php

	    echo "<tr><td>Date Added</td><td><b>".$data["DATE_ADDED"]."</b></td></tr>";?>

	    <tr><td>Status</td><td>
	      <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
	      <input type="hidden" name="operation" value="prod_edit">
	      <input type="submit" name="edit_sts" value="Toggle">
	      <?php
	      echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
	      ?>
	      <input type="hidden" name="name" value="<?php echo $data["STATUS"]; ?>"></form><?php
	      if ($data["STATUS"]==1)
	        echo "<b>Available for Sale</b></td></tr>";
	      else
	        echo "<b>Not Available for Sale</b></td></tr>";?>
	    <?php    
      echo "<tr><td>Products Sold</td><td><b>".$data["PRODUCTS_SOLD"]."</b></td></tr>";
      echo "<tr><td colspan=2 style='background-color:#AAAAFF;'><b>RENTAL DETAILS</b></td></tr>";
      ?>
	    <tr><td>Rental Status</td><td>
	      <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
	      <input type="hidden" name="operation" value="prod_edit">
	      <input type="submit" name="edit_rent_sts" value="Toggle">
	      <?php
	      echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
	      ?>
	      <input type="hidden" name="name" value="<?php echo $data["RENTAL_FLAG"]; ?>"></form><?php
	      if ($data["RENTAL_FLAG"]==1)
	        echo "<b>Available for Rent</b></td></tr>";
	      else
	        echo "<b>Not Available for Rent</b></td></tr>";?>

	    <tr><td>Rental Points</td><td>
	      <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
	      <input type="hidden" name="operation" value="prod_edit">
	      <input type="submit" name="edit_rent_points" value="Update">
	      <?php
	      echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
	      ?>
	      <input name="name" type="text" size=7 maxlength=7 value="<?php echo $data["RENTAL_POINTS"]; ?>"></form></td></tr>

        <tr><td>Rental Quantity</td><td>
	      <form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
	      <input type="hidden" name="operation" value="prod_edit">
	      <input type="submit" name="edit_rent_quantity" value="Update">
	      <?php
	      echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";
	      ?>
	      <input name="name" type="text" size=6 maxlength=6 value="<?php echo $data["RENTAL_QUANTITY"]; ?>"></form></td></tr>

        <?php
        echo "<tr><td>Products Rented</td><td><b>".$data["PRODUCTS_RENTED"]."</b></td></tr>";
	  }
          echo "</table></div>";
?>

<?php
	  echo "</td>";
    }
    else
      echo "<p><b>No product selected!</b>";
  }
?>


</tr>
</table>
<?php
}
} // end product_search==1
?>