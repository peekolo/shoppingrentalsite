<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
require_once('db_config.php');
require_once('db_config.php');
require_once("../php_shop/store_class.php");
$store_obj = new storeAccess();
$product_search = 0;

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

if(isset($_GET["operation"]) && $_GET["operation"] == "searchproducts"){
  $product_search = 1;

  if ($_GET["category"]=='***')
    $dat = mysql_query("SELECT ID, MODEL, TITLE, PRICE, QUANTITY, PRODUCTS_SOLD FROM product WHERE TITLE like '%" . $_GET['searchfield'] . "%'") or die(mysql_error());
  else
    $dat = mysql_query("SELECT ID, MODEL, TITLE, PRICE, QUANTITY, PRODUCTS_SOLD FROM product WHERE TITLE like '%" . $_GET['searchfield'] . "%' AND ID in (SELECT PRODUCT_ID from product_category where CATEGORY='" . $_GET["category"] . "')") or die(mysql_error());

}
?>

<table class="admintable1">
<tr><td><form action="" method="GET">
<input type="hidden" name="p" value="products">
<input type="hidden" name="operation" value="searchproducts">
<input type="text" name="searchfield" size=30>
<select name="category">
<option value="***">All Categories&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
<?php
  foreach($cat_obj as $cat_single){
    echo "<option value='".$cat_single["CAT"]."'>".$cat_single["CAT"]."</option>";
  }
?>
</select><input type="submit" value="Search"><br>
</form>
</td></tr></table>

<?php
if ($product_search==1) {
if (mysql_num_rows($dat)==0){
  echo "<p><b>Unable to find any records with search string: ".$_GET['searchfield']."</b></p>";
}
else {
?>
<p>
<table class="invis"><tr><td>
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
	while ($row = mysql_fetch_assoc($dat)) {
	  
	  $id = $row["ID"];
  	  $title = $row["TITLE"];
  	  $model = $row["MODEL"];
  	  $price = $row["PRICE"];
  	  $qty = $row["QUANTITY"];
  	  $sold = $row["PRODUCTS_SOLD"];

	  echo "<tr><td><input type='checkbox' name='prod_id[]' value=".$id."></td>";
	  echo "<td>".$model."</td>";
	  echo "<td>".$title."</td>";
	  echo "<td>".$price."</td>";
	  echo "<td>".$qty."</td>";
	  echo "<td>".$sold."</td>";
          echo "</tr>";
	}
?>
   
 </table></td><td>
<table class="admintable1"><tr><td><input type="submit" name="viewdetails" value="View Details"></form></td></tr></table>
</td>
<?php
  if(isset($_POST["operation"]) && $_POST["operation"] == "prod_func"){
    if (isset($_POST["prod_id"])){
      $prod_tmps = $_POST["prod_id"];
      if(count($prod_tmps) > 0){
        foreach($prod_tmps AS $prod_tmp){
      	  echo "<td><table class='formtable'>";
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
	    echo "<tr><td>Quantity</td><td><b>".$data["QUANTITY"]."</b></td></tr>";
	    echo "<tr><td>Weight</td><td><b>".$data["WEIGHT"]."</b></td></tr>";
	    echo "<tr><td>Discount Status</td><td><b>".$data["DISCOUNT_FLAG"]."</b></td></tr>";
	    echo "<tr><td>Discount Price</td><td><b>".$data["DISCOUNT_PRICE"]."</b></td></tr>";
	    echo "<tr><td>Date Available</td><td><b>".$data["DATE_AVAIL"]."</b></td></tr>";
	    echo "<tr><td>Date Added</td><td><b>".$data["DATE_ADDED"]."</b></td></tr>";
	    echo "<tr><td>Status</td><td><b>".$data["STATUS"]."</b></td></tr>";
	    echo "<tr><td>Rental Status</td><td><b>".$data["RENTAL_FLAG"]."</b></td></tr>";
	    echo "<tr><td>Rental Points</td><td><b>".$data["RENTAL_POINTS"]."</b></td></tr>";
	    echo "<tr><td>Products Sold</td><td><b>".$data["PRODUCTS_SOLD"]."</b></td></tr>";
	  }
          echo "</table>";
?>
<p><table class="admintable1"><tr><td>
<form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="operation" value="prod_delete">
<input type="submit" name="deleteprod" value="Delete">
<?php
echo "<input type='hidden' name='prod_id' value=" . $prod_tmp . ">";

?>
</form>
</td></tr></table>
<?php
	  echo "</td>";
        }
      }
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