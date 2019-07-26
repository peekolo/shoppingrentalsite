<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
require_once('db_config.php');
require_once("../php_shop/includes/configure.php");

$view_cart = 0;

if(isset($_GET["operation"]) && $_GET["operation"] == "searchcart"){
  $view_cart = 1;

  if(isset($_POST["operation"]) && $_POST["operation"] == "sort"){
    if (isset($_POST["type"]) && $_POST["type"] == "both"){
      if ($_GET["type"]=="both") 
        $dat = mysql_query("select B.MODEL, B.TITLE, sum(A.QUANTITY) from customer_cart A, product B WHERE B.ID=A.PRODUCT_ID GROUP by A.PRODUCT_ID ORDER BY sum(A.QUANTITY) desc, B.TITLE asc ") or die(mysql_error());
      else if ($_GET["type"]=="wish") 
        $dat = mysql_query("select B.MODEL, B.TITLE, sum(A.QUANTITY) from customer_cart A, product B WHERE B.ID=A.PRODUCT_ID AND A.WISHLIST=1 GROUP by A.PRODUCT_ID ORDER BY sum(A.QUANTITY) desc, B.TITLE asc ") or die(mysql_error());
      else if ($_GET["type"]=="cart") 
        $dat = mysql_query("select B.MODEL, B.TITLE, sum(A.QUANTITY) from customer_cart A, product B WHERE B.ID=A.PRODUCT_ID AND A.WISHLIST=0 GROUP by A.PRODUCT_ID ORDER BY sum(A.QUANTITY) desc, B.TITLE asc ") or die(mysql_error());
    }
     else if (isset($_POST["type"]) && $_POST["type"] == "title"){
      if ($_GET["type"]=="both") 
        $dat = mysql_query("select B.MODEL, B.TITLE, sum(A.QUANTITY) from customer_cart A, product B WHERE B.ID=A.PRODUCT_ID GROUP by A.PRODUCT_ID ORDER BY B.TITLE asc ") or die(mysql_error());
      else if ($_GET["type"]=="wish") 
        $dat = mysql_query("select B.MODEL, B.TITLE, sum(A.QUANTITY) from customer_cart A, product B WHERE B.ID=A.PRODUCT_ID AND A.WISHLIST=1 GROUP by A.PRODUCT_ID ORDER BY B.TITLE asc ") or die(mysql_error());
      else if ($_GET["type"]=="cart") 
        $dat = mysql_query("select B.MODEL, B.TITLE, sum(A.QUANTITY) from customer_cart A, product B WHERE B.ID=A.PRODUCT_ID AND A.WISHLIST=0 GROUP by A.PRODUCT_ID ORDER BY B.TITLE asc ") or die(mysql_error());
    }
    else if (isset($_POST["type"]) && $_POST["type"] == "quantity"){
      if ($_GET["type"]=="both") 
        $dat = mysql_query("select B.MODEL, B.TITLE, sum(A.QUANTITY) from customer_cart A, product B WHERE B.ID=A.PRODUCT_ID GROUP by A.PRODUCT_ID ORDER BY sum(A.QUANTITY) desc ") or die(mysql_error());
      else if ($_GET["type"]=="wish") 
        $dat = mysql_query("select B.MODEL, B.TITLE, sum(A.QUANTITY) from customer_cart A, product B WHERE B.ID=A.PRODUCT_ID AND A.WISHLIST=1 GROUP by A.PRODUCT_ID ORDER BY sum(A.QUANTITY) desc ") or die(mysql_error());
      else if ($_GET["type"]=="cart") 
        $dat = mysql_query("select B.MODEL, B.TITLE, sum(A.QUANTITY) from customer_cart A, product B WHERE B.ID=A.PRODUCT_ID AND A.WISHLIST=0 GROUP by A.PRODUCT_ID ORDER BY sum(A.QUANTITY) desc ") or die(mysql_error());
    }
  }
  else {
    if ($_GET["type"]=="both") 
      $dat = mysql_query("select B.MODEL, B.TITLE, sum(A.QUANTITY) from customer_cart A, product B WHERE B.ID=A.PRODUCT_ID GROUP by A.PRODUCT_ID ORDER BY sum(A.QUANTITY) desc, B.TITLE asc ") or die(mysql_error());
    else if ($_GET["type"]=="wish") 
      $dat = mysql_query("select B.MODEL, B.TITLE, sum(A.QUANTITY) from customer_cart A, product B WHERE B.ID=A.PRODUCT_ID AND A.WISHLIST=1 GROUP by A.PRODUCT_ID ORDER BY sum(A.QUANTITY) desc, B.TITLE asc ") or die(mysql_error());
    else if ($_GET["type"]=="cart") 
      $dat = mysql_query("select B.MODEL, B.TITLE, sum(A.QUANTITY) from customer_cart A, product B WHERE B.ID=A.PRODUCT_ID AND A.WISHLIST=0 GROUP by A.PRODUCT_ID ORDER BY sum(A.QUANTITY) desc, B.TITLE asc ") or die(mysql_error());
  }

}

?>

<table class="invis"><tr><td>
<table class="admintable1">
<tr><td><b>View Carts</b><form action="" method="GET">
<input type="hidden" name="p" value="cart">
<input type="hidden" name="operation" value="searchcart">
<input type="radio" name="type" value="both" checked> Both Cart and Wishlist<br />
<input type="radio" name="type" value="wish"> Wishlist only<br />
<input type="radio" name="type" value="cart"> Cart only<br />
<input type="submit" value="View Carts"><br>
</form>
</td></tr></table>

<?php
if ($view_cart==1) {
?>
</td><td></td><td><table class="admintable1">
<tr><td><b>Sort By</b>
<form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="operation" value="sort">
<input type="radio" name="type" value="both" checked> Both Quantity and Title<br />
<input type="radio" name="type" value="title"> Title only<br />
<input type="radio" name="type" value="quantity"> Quantity only<br />
<input type="submit" value="Sort"><br>
</form>
</td></tr></table>
<?php
}
?>


</td></tr></table>

<?php
if ($view_cart==1) {
if (mysql_num_rows($dat)==0){
  echo "<p><b>Unable to find any products in carts.</b></p>";
}
else {
?>
  <p><table class="invis"><tr><td>
  <div style="overflow:auto;height:500px;width:600px;padding:3px 3px 3px 3px;">
   <table class="simplelist">
   <tr>
    <td><b>MODEL</b></td>
    <td><b>TITLE</b></td>
    <td><b>QUANTITY IN CARTS</b></td>  
   </tr>
  <?php
   while ($row = mysql_fetch_assoc($dat)) {
    //product model

    echo "<tr>";
  
//    $dat1 = mysql_query("select MODEL, TITLE from product where ID = $product_id") or die(mysql_error());
//    while ($row1 = mysql_fetch_assoc($dat1)) {
      echo "<td>".$row["MODEL"]."</td>";
      echo "<td>".$row["TITLE"]."</td>";
//    }
    echo "<td>".$row["sum(A.QUANTITY)"]."</td>";
    echo "</tr>";
   }

   echo "</table>";
   echo "</div></td></tr></table>";
}
}
?>



