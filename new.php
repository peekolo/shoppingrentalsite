<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
?>
<div class="box_big" style="position:relative;text-align:left;">
<div style="text-align:left;padding:0px 10px 0px 10px;background-color:#FFFFFF;border:2px solid #AAAAAA;font-family:Verdana, Arial, sans-serif;font-size:12px;line-height:1.5;">
&nbsp;<p>
<h1>New</h1>
<p>
<?php
  $new_obj = $store_obj->getNew(30);
  if($new_obj && $new_obj != ""){    
    echo "<table class='plain1' width=99% style='padding:2px 2px 2px 2px;'>";
    
    for($i=0; $i<(sizeof($new_obj));$i++){
      if($i == 0){ echo "<tr>"; }
      else if($i % 3 == 0){ echo "</tr><tr>";}
      echo "<td width=33% align='center'>";
      $store_obj->mediumView($new_obj[$i]["ID"]);      
      echo "</td>";      
    }
    echo "</tr></table>";
    
  }
?>
<p>&nbsp;<p></div>
</div>