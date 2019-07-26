<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
require_once("../php_shop/includes/configure.php");
require_once("../php_shop/transaction_class.php");

$transact_obj = new transactionAccess();
$transact_result = 0;

if(isset($_POST["update_invoice"])){
  $tmp = 0;
  if($transact_obj->setPaymentStatus("",$_POST["payment_status"] ,$_POST["order_id"])){
    echo "<b>Payment Status Updated Successfully.</b> ";
    $tmp = 1;
  }
  if($transact_obj->setDeliveryStatus("",$_POST["delivery_status"] ,$_POST["order_id"])){
    echo "<b>Delivery Status Updated Successfully.</b>";
    $tmp = 1;
  }
  if($tmp == 1) echo "<p>";
  
}

if(isset($_POST["update_indiv"]) && isset($_POST["oid"])){

  if($transact_obj->setIndivDeliveryStatus($_POST["oid"], $_POST['delivery_status'], $_POST['qty'])){
    echo "<b>Individual Product Delivery Status Updated Successfully.</b><p>";
  }else
    echo "<b>Individual Product Delivery Status Update Failed.</b><p>";
}

if(isset($_GET["operation"]) && $_GET["operation"] == "searchtransacts"){
  $options_arr = $_GET["options"];
  $paid_tmp = "";
  $delivered_tmp = "";
  if(count($options_arr) > 0){
    if(in_array("paid", $options_arr) && in_array("unpaid", $options_arr)){
      $paid_tmp = "";
    }else if(in_array("paid", $options_arr)){
      $paid_tmp = 1;
    }else if(in_array("unpaid", $options_arr)){
      $paid_tmp = 0;
    }

    if(in_array("delivered", $options_arr) && in_array("undelivered", $options_arr)){
      $delivered_tmp = "";
    }else if(in_array("delivered", $options_arr)){
      $delivered_tmp = 1;
    }else if(in_array("undelivered", $options_arr)){
      $delivered_tmp = 0;
    }
  
  }else{
    $paid_tmp = "";
    $delivered_tmp = "";
  }    
  $transacts = $transact_obj->getTransactions($_GET["searchfield"],$_GET["searchby"], $paid_tmp, $delivered_tmp);
  $transact_result = 1;

}
?>

<table class="admintable1">
<tr><td><form action="" method="GET">
<input type="hidden" name="p" value="transactions">
<input type="hidden" name="operation" value="searchtransacts">
<input type="text" name="searchfield" value="">
<select name="searchby">
<option value="customer">Customer</option>
<option value="product">Products</option>
</select><input type="submit" value="Search"><br>
<input type="checkbox" name="options[]" value="paid"> Paid&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" name="options[]" value="unpaid"> Unpaid<br>
<input type="checkbox" name="options[]" value="delivered"> Delivered&nbsp;&nbsp;&nbsp;
<input type="checkbox" name="options[]" value="undelivered" checked> Undelivered
</form>
</td></tr></table>
<?php
     if(isset($_GET["op"]) && $_GET["op"] == "view"){
        if(isset($_GET["tid"]) && $_GET["tid"] != ""){    
          $order_objs = $transact_obj->getOrderProducts($_GET["tid"]);
          if($order_objs && count($order_objs)>0){
            foreach($order_objs AS $order_single){
              $oid_tmp = $order_single[$transact_obj->tbFields4['id']];
              echo "<div id='div_".$oid_tmp."' style='position:absolute;background-color:#CCCCCC;padding:25px 25px 5px 25px;z-index:99;right:500px;top:200px;border:5px solid #000000;visibility:hidden;'>";
              echo "<form action='".$_SERVER["REQUEST_URI"]."' method='POST'>";         
              echo "<input type='hidden' name='oid' value=".$oid_tmp.">";                  
              echo "<b>".$order_single[$transact_obj->tbFields4['product_title']]."</b><br>";
              echo "<select name='qty'>";
              $qty_tmp = $order_single[$transact_obj->tbFields4['quantity']];
              echo "<option value=".$qty_tmp." selected>ALL</option>";
              for($i = $qty_tmp - 1;$i > 0;$i--){
                echo "<option value=".$i.">".$i."</option>";              
              }              
              echo "</select>";
              
              $deliver_array = array(2,1,-2,-3,0);                       
              echo "<select name='delivery_status'>";
              foreach($deliver_array AS $deliver_single){
                echo "<option value=".$deliver_single;
                if($deliver_single == $order_single[$transact_obj->tbFields4['delivery_status']]) echo " selected";
                echo ">".$transact_obj->getStatusString($deliver_single, "delivery")."</option>";
              }
              echo "</select>";     
              echo "<input type='submit' name='update_indiv' value='Update'>";
              echo "<p><a href=\"javascript:hide_div('div_".$oid_tmp."');\" id='noformat'><small>(close)</small></a>";
              echo "</form>";
              echo "</div>";
            }                    
          }
        
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

<div style="position:absolute;top:30px;right:3px;" id="invoice_div"><a href="javascript:hide_div('invoice_div');" id="noformat2">(close)</a>&nbsp;&nbsp;&nbsp;<a href="javascript:popup();" id="noformat2">(popup to print)</a><br><form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST"><table class='noclass'><tr><td><?php echo $message;?></td></tr></table><input type="submit" name="update_invoice" value="Update"></form></div>

<?php          
        }
    } 

     if($transact_result == 1){      
        echo "<div style='overflow:auto;height:500px;width:630px;padding:3px 3px 3px 3px;'><table class='simplelist'><tr><td><b>Invoice Number</b></td><td><b>Date</b></td><td><b>Customer</b></td><td><b>Grand Total</b></td><td><b>Payment Status</b></td><td><b>Delivery Status</b></td></tr>";
        if($transacts && count($transacts) > 0){
          foreach($transacts AS $transact){        
            echo "<tr><td><a href='".$_SERVER["REQUEST_URI"]."&op=view&tid=".$transact['ID']."'>".$transact['INVOICE_NUMBER']."</a></td>";
            echo "<td>".$transact['DATE_CREATED']."</td>";
            echo "<td>".$transact_obj->getName($transact['CUSTOMER_ID'])." ".$transact_obj->getLastName($transact['CUSTOMER_ID'])."</td>";
            echo "<td>$".$transact['TOTAL_PRICE']."</td>";
            echo "<td>".$transact_obj->getStatusString($transact['PAYMENT_STATUS'], "payment")."</td>";
            echo "<td>".$transact_obj->getStatusString($transact['DELIVERY_STATUS'], "delivery")."</td>";
            echo "</tr>";
          }
         }else{
          echo "<td colspan=6>No Results</td>";
         }   
        echo "</table></div>";
     }        

?>

