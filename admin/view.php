<?php
require_once('db_config.php');
?>
 <table border="1" align="left">
  <tr>
   <td>ID</td>
   <td width=150>TITLE</td>
   <td width=700>DESCRIPTION</td>
   <td>MODEL</td>
   <td>MANUFACTURER</td>
   <td>PRICE</td>
   <td>QUANTITY</td>
   <td>WEIGHT</td>
   <td>DATE AVAILABLE</td>
   <td>DATE ADDED</td>
   <td>STATUS</td>
  </tr>

  <?php

   $ids = get_product_ids();

   $max_id = count($ids);

   $counter = 1;

   while ($counter <= $max_id) {
      $data = get_product_table($ids[$counter]);
      echo "<tr>";
      echo "<td>". $data["ID"] . "</td>";
      echo "<td>". $data["TITLE"] . "</td>";
      echo "<td>". $data["DESCRIPTION"] . "</td>";
      echo "<td>". $data["MODEL"] . "</td>";
      echo "<td>". $data["MANUFACTURER"] . "</td>";
      echo "<td>". $data["PRICE"] . "</td>";
      echo "<td>". $data["QUANTITY"] . "</td>";
      echo "<td>". $data["WEIGHT"] . "</td>";
      echo "<td>". $data["DATE_AVAIL"] . "</td>";
      echo "<td>". $data["DATE_ADDED"] . "</td>";
      echo "<td>". $data["STATUS"] . "</td>";
      echo "</tr>";

    $counter = $counter + 1;     
   }

  ?>

 </table>
