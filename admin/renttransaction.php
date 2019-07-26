<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
require_once("../php_shop/includes/configure.php");
require_once("../php_shop/renttransaction_class.php");

$transact_obj = new renttransactionAccess();
$transact_result = 0;

if(isset($_POST["update_invoice"])){
  $tmp = 0;
  if($transact_obj->setDeliveryStatus("",$_POST["delivery_status"] ,$_POST["order_id"])){
    echo "<b>Delivery Status Updated Successfully.</b>";
    $tmp = 1;
  }
  if($tmp == 1) echo "<p>";  
}

if(isset($_GET["operation"]) && $_GET["operation"] == "searchtransacts"){
  $transacts = $transact_obj->getTransactions($_GET["searchfield"],$_GET["searchby"], $_GET["options"]);
  $transact_result = 1;

}
?>
<script type="text/javascript">   
function getE(id)
{
  var obj = null;
  if (document.getElementById) // DOM3 = IE5, NS6, Firefox
    obj = document.getElementById(id);
  else if(document.all) // IE 4 
    obj = document.all[id];
  else if(document.layers) // Netscape 4
    obj= document.layers[id];

  return obj;
}

function hide_div(id)
{
  obj1 = getE(id);
  obj1.style.visibility = 'hidden';
  return;
}

function show_div(id)
{
  obj1 = getE(id);
  obj1.style.visibility = 'visible';
  return;
}
</script>
<table class="admintable1">
<tr><td><form action="" method="GET">
<input type="hidden" name="p" value="renttransactions">
<input type="hidden" name="operation" value="searchtransacts">
<input type="text" name="searchfield" value="">
<select name="searchby">
<option value="customer">Customer</option>
<option value="product">Products</option>
</select><input type="submit" value="Search"><br>
<input type="checkbox" name="options[]" value=0 checked> Rented (Delivering)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" name="options[]" value=2 checked> Request Return<br>
<input type="checkbox" name="options[]" value=3> Rented&nbsp;&nbsp;&nbsp;
<input type="checkbox" name="options[]" value=1> Returned
</form>
</td></tr></table>
<?php
     if(isset($_GET["op"]) && $_GET["op"] == "view"){
        if(isset($_GET["tid"]) && $_GET["tid"] != ""){            
          echo "<script>function popup(){var win_obj = window.open('','order_win','width=700,height=650,scrollbars=1,menubar=1,toolbar=0,resizable=1');";
          ob_start(); //Turn on output buffering
          echo "<html><head></head><body>";
          $transact_obj->printOrder("",$_GET["tid"]);         
          echo "</body></html>";
          $message = ob_get_clean();                     
          echo "var content=\"".$message."\";";
          echo "content += '<p><input type=\"button\" onClick=\"javascript:window.print();\" value=\"Print Invoice\">';";
          echo "win_obj.document.open();win_obj.document.write(content);win_obj.document.close();win_obj.focus();}</script>";
?>       


<div style="position:absolute;top:30px;right:3px;" id="invoice_div"><a href="javascript:hide_div('invoice_div');" id="noformat2">(close)</a>&nbsp;&nbsp;&nbsp;<a href="javascript:popup();" id="noformat2">(popup to print)</a><br><table class='noclass'><tr><td><?php echo $message;?></td></tr></table></div>

<?php          
        }
    } 
     if($transact_result == 1){      
        echo "<div style='overflow:auto;height:500px;width:830px;padding:3px 3px 3px 3px;'><table class='simplelist'><tr><td><b>Invoice Number</b></td><td><b>Date</b></td><td><b>Customer</b></td><td><b>Game Title</b></td><td><b>Delivery Status</b></td><td>&nbsp;</td></tr>";
        if($transacts && count($transacts) > 0){
          foreach($transacts AS $transact){     
            echo "<form action='' method='POST'><input type='hidden' name='order_id' value=".$transact['ID'].">";
            echo "<tr><td><a href='".$_SERVER["REQUEST_URI"]."&op=view&tid=".$transact['ID']."'>".$transact['RENTAL_ID']."</a></td>";
            echo "<td>".$transact['DATE_CREATED']."</td>";
            echo "<td>".$transact_obj->getName($transact['CUSTOMER_ID'])." ".$transact_obj->getLastName($transact['CUSTOMER_ID'])."</td>";
            echo "<td>".$transact['PRODUCT_TITLE']."</td>";
            echo "<td><select name='delivery_status' onChange=\"show_div('div".$transact['ID']."');\">";
            $arr_tmp = array(1,3,2,0);
            foreach($arr_tmp AS $arr_tmp_single){
              echo "<option value=".$arr_tmp_single;
              if($arr_tmp_single == $transact['DELIVERY_STATUS']) echo " selected";
              echo ">".$transact_obj->getStatusString($arr_tmp_single, "rental")."</option>";
            }          
            echo "</select></td><td><div style='visibility:hidden;' id='div".$transact['ID']."'><input type='submit' name='update_invoice' value='Update'></div></td>";
            echo "</tr></form>";
          }
         }else{
          echo "<td colspan=6>No Results</td>";
         }   
        echo "</table></div>";
     }        

?>

