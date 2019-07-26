<?php
require_once('db_config.php');
$max_per_page = 30;

if(empty($_POST['change'])){
  $page_no=0;
}
else {
  $page_no=($_POST['change']);
}

?>

<html>
<head>
</head>

<body>

<?php
$conn = get_db_conn();
$dat = mysql_query("SELECT count(*) FROM customer");
while ($row = mysql_fetch_assoc($dat)) {
  $temp = $row["count(*)"];
}
$totalPages = ceil($temp / $max_per_page);
mysql_close($conn);

$page_up = $page_no + 1;
$page_down = $page_no - 1;
?>

<table class=no_border align="center"><tr>
<?php
echo "<td>Page ";
echo $page_no+1 . " of " . $totalPages . "\t</td>";
if ($page_no+1 < $totalPages) {
  echo "<td>";
  echo ("<form action='view_products.php' method='post'/>");
  echo ("  <input name='change' type='hidden' value=$page_up>");
  echo ("  <input type='submit' value='Next page'/> ");
  echo (" </form> ");
  echo "</td>";
}

if ($page_no!=0) {
  echo "<td>";
  echo ("<form action='view_products.php' method='post'/>");
  echo ("  <input name='change' type='hidden' value=$page_down>");
  echo ("  <input type='submit' value='Prev page'/> ");
  echo (" </form> ");
  echo "</td>";
}
?>
</tr></table>

 <table border="1" align="left">
  <tr>
   <td>USERNAME</td>
   <td>FIRST_NAME</td>
   <td>LAST_NAME</td>
   <td>GENDER</td>
   <td>COMPANY</td>
   <td>EMAIL</td>
   <td>PRIMARY ADDRESS</td>
   <td>POSTAL CODE</td>
   <td>CITY</td>
   <td>COUNTRY_ID</td>
  </tr>

  <?php
   
   $start_limit = $page_no * $max_per_page;

   $conn = get_db_conn();

   $dat = mysql_query("SELECT * FROM customer LIMIT " . $start_limit . " , " . $max_per_page);

   while ($data = mysql_fetch_assoc($dat)) {
      echo "<tr>";
      echo "<td>". $data["USERNAME"] . "</td>";
      echo "<td>". $data["FIRST_NAME"] . "</td>";
      echo "<td>". $data["LAST_NAME"] . "</td>";
      echo "<td>". $data["GENDER"] . "</td>";
      echo "<td>". $data["COMPANY"] . "</td>";
      echo "<td>". $data["EMAIL"] . "</td>";

      $dat2 = mysql_query("SELECT * FROM customer_address where PRIMARY_FLAG=1 and USERNAME='" . $data["USERNAME"] . "'");
      while ($data2 = mysql_fetch_assoc($dat2)) {

        echo "<td>". $data2["ADDRESS"] . "</td>";
        echo "<td>". $data2["POSTAL_CODE"] . "</td>";
        echo "<td>". $data2["CITY"] . "</td>";
        echo "<td>". $data2["COUNTRY_ID"] . "</td>";
      }     
      echo "</tr>";
   }

   mysql_close($conn);

  ?>

 </table>

</body>
</html>