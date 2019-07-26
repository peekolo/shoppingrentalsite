<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
?>
<div class="box_big" style="position:relative;text-align:left;">
<h1>Advanced Search</h1>
<p>
<form action="?" name="advancedsearchform" method="GET">
<input type="hidden" name="p" value="search"><input type="hidden" name="t" value="advanced">
<table class="form_table">
<tr><td>Search Criteria</td><td><input type="text" name="searchfield" value="" size=30></td></tr>
<tr><td>Categories</td><td><select name="category">
<option value="***">All Categories</option>
<?php
  $cat_obj = $store_obj->getCategories(); 
  foreach($cat_obj as $cat_single){
    echo "<option value='".$cat_single["CAT"]."'>".$cat_single["CAT"]."</option>";
  }
?>
</select></td></tr>
<tr><td>Manufacturers</td><td><select name="manufacturer">
<option value="***">All Manufacturers</option>
<?php
  $manu_obj = $store_obj->getManufacturers();
  if(isset($_GET["manufacturer"])){
    $manu_tmp = $_GET["manufacturer"];
    foreach($manu_obj as $manu_single){    
      echo "<option value='".$manu_single["MANU"]."' ";
      if($manu_single["MANU"] == $manu_tmp){
        echo "SELECTED";
      }
      echo ">".$manu_single["MANU"]."</option>";
    }  
  }else{
    foreach($manu_obj as $manu_single){    
      echo "<option value='".$manu_single["MANU"]."'>".$manu_single["MANU"]."</option>";
    }
  }
?>
</select></td></tr>
<tr><td>Price</td><td>Min: $<input type="text" name="pricemin" size=6>&nbsp;&nbsp;&nbsp;Max: $<input type="text" name="pricemax" size=6></td></tr>
<tr><td>Number of Players</td><td>Min: &nbsp;<input type="text" name="playersmin" size=6>&nbsp;&nbsp;&nbsp;Max: &nbsp;<input type="text" name="playersmax" size=6></td></tr>
<tr><td align="right" colspan=2><input type="submit" name="submit" value="Search"></td></tr>
</table>
</form>
<p>&nbsp;<p>
</div>