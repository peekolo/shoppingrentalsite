<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
require_once('db_config.php');
$max_per_page = 30;

if(empty($_POST['change'])){
  $page_no=0;
}
else {
  $page_no=($_POST['change']);
}

$conn = get_db_conn();
$dat = mysql_query("SELECT count(*) FROM product order by ID asc");
while ($row = mysql_fetch_assoc($dat)) {
  $temp = $row["count(*)"];
}
$totalPages = ceil($temp / $max_per_page);
mysql_close($conn);

$page_up = $page_no + 1;
$page_down = $page_no - 1;
?>

<table class=no_border align="left"><tr>
<?php
echo "<td>Page ";
echo $page_no+1 . " of " . $totalPages . "\t</td>";
if ($temp!='') {
  echo "<td>";
  echo ("<form action='?p=products' method='post'/>");
  echo ("  <input name='change' type='hidden' value=$page_up>");
  echo ("  <input type='submit' value='Next page'/> ");
  echo (" </form> ");
  echo "</td>";
}

if ($page_no!=0) {
  echo "<td>";
  echo ("<form action='?p=products' method='post'/>");
  echo ("  <input name='change' type='hidden' value=$page_down>");
  echo ("  <input type='submit' value='Prev page'/> ");
  echo (" </form> ");
  echo "</td>";
}
?>
</tr></table>

 <table class="simplelist">
  <tr>
   <td>ID</td>
   <td>TITLE</td>
   <td>DESCRIPTION</td>
   <td>MODEL</td>
   <td>MIN_PLAYERS</td>
   <td>MAX_PLAYERS</td>
   <td>MANUFACTURER</td>
   <td>PRICE</td>
   <td>QUANTITY</td>
   <td>WEIGHT</td>
   <td>DISCOUNT?</td>
   <td>DISCOUNT_PRICE</td>
   <td>DATE AVAILABLE</td>
   <td>DATE ADDED</td>
   <td>NEW_FLAG</td>
   <td>STATUS</td>
  </tr>

  <?php
   
   $start_limit = $page_no * $max_per_page;

   $conn = get_db_conn();

   $dat = mysql_query("SELECT * FROM product order by ID asc LIMIT " . $start_limit . " , " . $max_per_page);


   while ($data = mysql_fetch_assoc($dat)) {
      echo "<tr>";
      echo "<td>". $data["ID"] . "</td>";
      echo "<td>". $data["TITLE"] . "</td>";
      echo "<td>". $data["DESCRIPTION"] . "</td>";
      echo "<td>". $data["MODEL"] . "</td>";
      echo "<td>". $data["MIN_PLAYERS"] . "</td>";
      echo "<td>". $data["MAX_PLAYERS"] . "</td>";

      $dat2 = mysql_query("SELECT TITLE FROM manufacturer where ID=" . $data['MANUFACTURER_ID']);
      while ($data2 = mysql_fetch_assoc($dat2)) {
        echo "<td>". $data2["TITLE"] . "</td>";
      }

      echo "<td>". $data["PRICE"] . "</td>";
      echo "<td>". $data["QUANTITY"] . "</td>";
      echo "<td>". $data["WEIGHT"] . "</td>";

      if ($data["DISCOUNT_FLAG"]==1)
	echo "<td>Y</td>";
      else
	echo "<td>N</td>";

      echo "<td>". $data["DISCOUNT_PRICE"] . "</td>";
      echo "<td>". $data["DATE_AVAIL"] . "</td>";
      echo "<td>". $data["DATE_ADDED"] . "</td>";

      if ($data["NEW_FLAG"]==1)
	echo "<td>Y</td>";
      else
	echo "<td>N</td>";

      if ($data["STATUS"]==1)
	echo "<td>Active</td>";
      else
	echo "<td>Passive</td>";
      
      echo "</tr>";
   }

   mysql_close($conn);

  ?>

 </table>
