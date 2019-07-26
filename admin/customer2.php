<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
require_once("../php_shop/includes/configure.php");
require_once("../php_shop/customer_class.php");

$customer_obj = new customerAccess();
$res = ""; 

if(isset($_GET["operation"])){
  $operation = $_GET["operation"];
}
if(isset($_POST["operation"]) && $_POST["operation"] == "cust_func" && isset($_POST["setmember"])){
  $cust_tmps = $_POST["cust_id"];
  if(count($cust_tmps) > 0){
    if($_POST["memberType"] == "***"){
      foreach($cust_tmps AS $cust_tmp){
        $customer_obj->unsetCustomerMember($cust_tmp);
      }  
    }else{
      foreach($cust_tmps AS $cust_tmp){
        $customer_obj->setCustomerMember($cust_tmp, $_POST["memberType"]);
      }
    }
  }
}else if(isset($_POST["operation"]) && $_POST["operation"] == "cust_func" && isset($_POST["setrent"])){
  $cust_tmps = $_POST["cust_id"];
  if(count($cust_tmps) > 0){
    if($_POST["rentType"] == "***"){
      foreach($cust_tmps AS $cust_tmp){
        $customer_obj->removeRentalMember($cust_tmp);
      }  
    }else{
      foreach($cust_tmps AS $cust_tmp){
         //add Rental Manual         
        $customer_obj->addRentalMember($cust_tmp, $_POST["rentType"], 10);
        $customer_obj->manualRentPay($cust_tmp, $customer_obj->getRentalPlan($cust_tmp));
      }
    }
  }
}else if(isset($_POST["operation"]) && $_POST["operation"] == "cust_func" && isset($_POST["resetpwd"])){
  $cust_tmps = $_POST["cust_id"];
  if(count($cust_tmps) > 0){
    foreach($cust_tmps AS $cust_tmp){
      if($customer_obj->resetDefaultPassword($cust_tmp,"E72MF0GG")){
          echo "<b>Password changed to default: <i>E72MF0GG</i></b>";
        }else
          echo "<b>Password reset failed</b>";      
    }
  }
}


$search_results = 0;
switch($operation){
  case "deletemember":
    if(!empty($_GET['memberType']) && $_GET['memberType'] != ""){
      if($customer_obj->deleteMemberType($_GET['memberType']))
        echo "<b>Deleted Member Type</b>";
      else
        echo "<b>Delete Error</b>";
    }
    break;
  case "createmember":
    if(isset($_GET['memberType']) && isset($_GET['discount']) && isset($_GET['exclusive_discount'])){
      if($customer_obj->createMemberType($_GET['memberType'],$_GET['discount'],$_GET['exclusive_discount'])){
        echo "<b>Created Member Type ".$_GET['memberType']." (".$_GET['discount']."%, ".$_GET['exclusive_discount']."%).</b>";
      }else{
        echo "<b>Failed to Create Member Type ".$_GET['memberType']." (".$_GET['discount']."%, ".$_GET['exclusive_discount']."%)<br>Error in Fields.</b>";
      }
    }    
    break;
  case "search":
    if(!empty($_GET['viewby'])){
      $viewby = $_GET['viewby'];
    }else{
      $viewby = 30;
    }
    if(!empty($_GET['pagestart'])){
      $pagestart = $_GET['pagestart'];
    }else{
      $pagestart= 0;
    }
    if(!empty($_GET['startnum'])){
      $startnum= $_GET['startnum'];
    }else{
      $startnum = 0;
    } 
    if(!empty($_GET['alpha'])){
      $alpha= $_GET['alpha'];
    }else{
      $alpha = "";
    }                   
    if(isset($_GET['orderby'])){
      $orderby = $_GET['orderby'];
    }else{
      $orderby = "";
    }
    if(isset($_GET['ordertype'])){
      $ordertype = $_GET['ordertype'];
    }else{
      $ordertype = "";
    }
    
    if(isset($_GET["searchfield"])){
      $results = $customer_obj->searchCustomer($_GET["searchfield"],"","",$orderby,$ordertype,$startnum,-1,$alpha);
      $total_num = $customer_obj->count_rows($_GET["searchfield"],"",$alpha);
      $searchfield = $_GET["searchfield"];
    }else{
      $searchfield = '';
    }    
    if(!$results){
      echo "No Matches Found!";
    }else{
      $search_results = 1;
    }    
    default;
    break;
}    

$mem_obj = $customer_obj->getMemberTypes(); 
?>


<table class="admintable1">
<tr><td>
<form action="" method="GET">
<input type="hidden" name="p" value="customers">
<input type="hidden" name="operation" value="createmember">
<b>Member Type:</b>&nbsp;<input type="text" name="memberType" value="">&nbsp;&nbsp;<input type="submit" value="Create Member Type"><br>
<b>Discount:</b>&nbsp;<input type="text" name="discount" size=5 value="">%&nbsp;&nbsp;&nbsp;
<b>Exclusive Discount:</b>&nbsp;<input type="text" name="exclusive_discount" size=5 value="">%
</form>
</td><td> 
<form action="" method="GET">
<input type="hidden" name="p" value="customers">
<input type="hidden" name="operation" value="deletemember">
<b>Member Type:</b>&nbsp;<select name="memberType">
<option value="">&nbsp;</option>
<?php  
  foreach($mem_obj as $mem_single){
    echo "<option value='".$mem_single[$customer_obj->tbFields11['id']]."'>".$mem_single[$customer_obj->tbFields11['type']]." (".$mem_single[$customer_obj->tbFields11['discount']]."%, ".$mem_single[$customer_obj->tbFields11['exclusive_discount']]."%)</option>";
  }
?>
</select>
<input type="submit" value="Delete Member Type"></form>
</td></tr>
<tr><td colspan=2><form action="" method="GET">
<input type="hidden" name="p" value="customers">
<input type="hidden" name="operation" value="search">
<input type="text" name="searchfield" value="">
<input type="submit" value="Search Customer"></form>
</td></tr></table>

<p>

<?php    
if($search_results == 1){    
      
?>
Showing <?php echo ($startnum+1)."-".($startnum + sizeof($results));?> out of <?php echo $total_num;?> results.<br>

<form action="<?php echo $_SERVER["REQUEST_URI"];?>" method="POST">
<input type="hidden" name="operation" value="cust_func">
<table class="invis"><tr><td>
<div style="overflow:auto;height:500px;padding:3px 3px 3px 3px;">
<table class="simplelist">
<?php
  echo "<tr><td></td><td width='30px'><b>Name</b></td><td width='30px'><b>Email</b></td><td><b width='30px'>Membership</b></td><td width='30px'><b>Rental Plan</b></td></tr>";
foreach($results AS $result){
  $meminfo_tmp = $customer_obj->getMemberTypeInfo($result[$customer_obj->tbFields1["discount_type"]],$mem_obj);  
  echo "<tr><td><input type='checkbox' name='cust_id[]' value=".$result[$customer_obj->tbFields1["id"]]."></td><td>".$result[$customer_obj->tbFields1["first_name"]]." ".$result[$customer_obj->tbFields1["last_name"]]."</td><td>".$result[$customer_obj->tbFields1["email"]]."</td><td>";
  if($meminfo_tmp != "")
    echo $meminfo_tmp[$customer_obj->tbFields11['type']]." (".$meminfo_tmp[$customer_obj->tbFields11['discount']]."%, ".$meminfo_tmp[$customer_obj->tbFields11['exclusive_discount']]."%)";
  echo "</td><td>";
    echo $customer_obj->getRentalPlan($result[$customer_obj->tbFields1["id"]]);
  echo "</td></tr>";
}
?>
</table>
</div>
</td><td><table class="admintable1"><tr><td><input type="submit" name="viewdetails" value="View Details"></td></tr></table><br>
<table class="admintable1"><tr><td>
<select name="memberType">
<option value="***">&lt;Unset Member&gt;</option>
<?php  
  foreach($mem_obj as $mem_single){
    echo "<option value='".$mem_single[$customer_obj->tbFields11['id']]."'>".$mem_single[$customer_obj->tbFields11['type']]." (".$mem_single[$customer_obj->tbFields11['discount']]."%, ".$mem_single[$customer_obj->tbFields11['exclusive_discount']]."%)</option>";
  }
?>
</select>
<br><input type="submit" name="setmember" value="Set As Member..."></td></tr></table><br>
<table class="admintable1"><tr><td>
<select name="rentType">
<option value="***">&lt;Unset Rent&gt;</option>
<option value='Basic Subscription (with 3RP) for Board Game Lifestyle Rental'>Basic - 3RP</option>
<option value='Silver Subscription (with 5RP) for Board Game Lifestyle Rental'>Silver - 5RP</option>
<option value='Gold Subscription (with 7RP) for Board Game Lifestyle Rental'>Gold - 7RP</option>
<option value='Titanium Subscription (with 10RP) for Board Game Lifestyle Rental'>Titanium - 10RP</option>
</select>
<br><input type="submit" name="setrent" value="Set As Rental Member..."></td></tr></table><br>
<table class="admintable1"><tr><td><input type="submit" name="resetpwd" value="Reset Password"></td></tr></table><br>
</td>
<td>
<?php
  if(isset($_POST["operation"]) && $_POST["operation"] == "cust_func" && isset($_POST["viewdetails"])){
    $cust_tmps = $_POST["cust_id"];
    if(count($cust_tmps) > 0){
      foreach($cust_tmps AS $cust_tmp){
        $reg_dat = $customer_obj->searchCustomer("","",$cust_tmp);
?>
<table class="formtable">
<tr><td>
Email*</td><td><b><?php echo $reg_dat[$customer_obj->tbFields1['email']];?></b>
</td></tr>
<tr><td>
Password*</td><td><b>**********</b>
</td></tr>
<tr><td>
First Name*</td><td><b><?php echo $reg_dat[$customer_obj->tbFields1['first_name']];?></b>
</td></tr>
<tr><td>
Last Name*</td><td><b><?php echo $reg_dat[$customer_obj->tbFields1['last_name']];?></b>
</td></tr>
<tr><td>
Gender</td><td><b><?php 
if($reg_dat[$customer_obj->tbFields1['gender']]=="M") 
  echo 'Male';
else if($reg_dat[$customer_obj->tbFields1['gender']]=="F") 
  echo 'Female';
else echo 'Unspecified';
?></b>
</td></tr>
<tr><td>
Date of Birth</td><td><b><?php echo $customer_obj->transformDate($reg_dat[$customer_obj->tbFields1['dob']]);?></b>&nbsp;(DD/MM/YYYY)
</td></tr>
<tr><td>
Phone Number*</td><td><b><?php echo $reg_dat[$customer_obj->tbFields1['telephone']];?></b>
</td></tr>
<tr><td>
Fax Number</td><td><b><?php echo $reg_dat[$customer_obj->tbFields1['fax']];?></b>
</td></tr>
<tr><td>
Receive Newsletter</td><td><b><?php if($reg_dat[$customer_obj->tbFields1['newsletter_flag']]=="1") echo 'Yes'; else echo 'No';?>
</b></td></tr>
<tr><td>
Membership</td><td><b><?php 
$meminfo_tmp = $customer_obj->getMemberTypeInfo($reg_dat[$customer_obj->tbFields1['discount_type']], $mem_obj);
if($meminfo_tmp != ""){
  echo $meminfo_tmp[$customer_obj->tbFields11['type']]." (".$meminfo_tmp[$customer_obj->tbFields11['discount']]."%)";
}
?>
</b></td></tr>
</table>
<p>&nbsp;<p>
<?php  
      }
    }
  }

?>
</td></tr></table>
</form>

<?php        
}

?>

