<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
?>
<div class="box_big" style="position:relative;">
&nbsp;<p>
<h1>Account</h1>
<table class="table1" height="500px" width='95%'>
<tr><td>
<a href="?p=account&t=info">Account Information</a><p>
<a href="?p=account&t=edit">Edit Account</a><p>
<a href="?p=account&t=change_pass">Change Password</a><p>
<a href="?p=account&t=address">Add/Edit Address</a><p>
<a href="?p=account&t=transaction">Transaction History</a><p>
<a href="?p=account&t=transaction&pending=1">Pending Transactions</a><p>
</td>
<td>
<?php
$tab_type = "";
if(isset($_GET["t"])){
  $tab_type = $_GET["t"];
}

switch($tab_type){
  case "transaction":   //****************************** View Transactions History ******************************************  
    if(isset($_GET["tid"])){
      $user_obj->printOrder("",$_GET["tid"]);
    }else{      
      $pending_var = 0;
      if(isset($_GET["pending"])){
        if($_GET["pending"] == 1){
          $pending_var = 1;
        }
      }
      if($transacts = $user_obj->getTransactions($pending_var)){      
        echo "<table class='table2'><tr><td><b>Invoice Number</b></td><td><b>Date</b></td><td><b>Grand Total</b></td><td><b>Payment Status</b></td><td><b>Delivery Status</b></td></tr>";
        foreach($transacts AS $transact){        
          echo "<tr><td><a href='?p=account&t=transaction&tid=".$transact['ID']."'>".$transact['INVOICE_NUMBER']."</a></td>";
          echo "<td>".$transact['DATE_CREATED']."</td>";
          echo "<td>$".$transact['TOTAL_PRICE']."</td>";
          echo "<td>".$user_obj->getStatusString($transact['PAYMENT_STATUS'], "payment")."</td>";
          echo "<td>".$user_obj->getStatusString($transact['DELIVERY_STATUS'], "delivery")."</td>";
          echo "</tr>";
        }    
        echo "</table>";
      }else{
      
        echo "No past transactions.";
      }
      
    }    
    break;
  case "change_pass":     //****************************** Change Password ******************************************    
    if(isset($_POST['processed'])){
      if($_POST['processed'] == 1){
        if($user_obj->changePassword($_POST['old_pass'],$_POST['new_pass']))
          echo "Password changed successfully.";
        else
          echo "Password change failed. Please try again or contact the administrator.";
      }
    }    
?>
<form action="?p=account&t=change_pass" method="POST">
<input type="hidden" name="processed" value=1>
<table class="form_table">
<tr><td>
Old Password</td><td><input type="password" name="old_pass">
</td></tr>
<tr><td>
New Password</td><td><input type="password" name="new_pass"><br>
</td></tr>
<tr><td>
Confirm New Password</td><td><input type="password" name="new_pass2"><br>
</td></tr>
<tr><td colspan=2>&nbsp;</td></tr>
<tr><td colspan=2 align="center"><input type="submit" value="Submit"></td></tr>
</table>
</form>
<?php
    break;
    
  case "edit":            //****************************** EDIT ACCOUNT INFORMATION ***************************************
      $reg_dat = $user_obj->userData;
?>
<form action="?p=account&t=update_details" method="POST">
<input type="hidden" name="processed" value=2>
<table class="form_table">
<tr><td>
Email*</td><td><b><?php echo $reg_dat[$user_obj->tbFields1['email']];?></b>
</td></tr>
<tr><td>
Password*</td><td><a href="?p=account&t=change_pass"><b>Change password</b></a>
</td></tr>
<tr><td>
First Name*</td><td><input type="text" name="first_name" value="<?php echo $reg_dat[$user_obj->tbFields1['first_name']];?>">
</td></tr>
<tr><td>
Last Name*</td><td><input type="text" name="last_name" value="<?php echo $reg_dat[$user_obj->tbFields1['last_name']];?>">
</td></tr>
<tr><td>
Gender</td><td><input type="radio" name="gender" value="M" <?php if($reg_dat[$user_obj->tbFields1['gender']]=="M") echo 'checked';?>><b>Male</b>&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="gender" value="F" <?php if($reg_dat[$user_obj->tbFields1['gender']]=="F") echo 'checked';?>><b>Female</b>
</td></tr>
<tr><td>
Date of Birth</td><td><input type="text" name="dob" value="<?php echo $user_obj->transformDate($reg_dat[$user_obj->tbFields1['dob']]);?>">&nbsp;(DD/MM/YYYY)
</td></tr>
<tr><td>
Phone Number*</td><td><input type="text" name="telephone" value="<?php echo $reg_dat[$user_obj->tbFields1['telephone']];?>">
</td></tr>
<tr><td>
Fax Number</td><td><input type="text" name="fax" value="<?php echo $reg_dat[$user_obj->tbFields1['fax']];?>">
</td></tr>
<tr><td>
Receive Newsletter</td><td><input type="radio" name="newsletter_flag" value="1" <?php if($reg_dat[$user_obj->tbFields1['newsletter_flag']]=="1") echo 'checked';?>>Yes&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="newsletter_flag" value="0" <?php if($reg_dat[$user_obj->tbFields1['newsletter_flag']]=="0") echo 'checked';?>>No
</td></tr>
<tr><td colspan=2>&nbsp;</td></tr>
<tr><td colspan=2 style="text-align:right;"><input type="submit" value="Submit Changes"></td></tr>
</table>
</form>
<?php
    break;    
  case "add_address":   //****************************** PROCESS ADD ADDRESS INFORMATION ***************************************
    if(isset($_POST['processed'])){
      if($_POST['processed'] == 3){
        $reg_dat = array(
          "customer_id" => $_POST['customer_id'],
          "first_name" => $_POST['first_name'],
          "last_name" => $_POST['last_name'],
          "company" => $_POST['company'],
          "address" => $_POST['address'],
          "postal" => $_POST['postal'],
          "city" => $_POST['city'],
          "country" => $_POST['country'],
          "primary_flag" => $_POST['primary_flag']
         );
        $addy_success = -1; 
        if(trim($_POST['postal'])=="" || trim($_POST['first_name'])=="" || trim($_POST['last_name'])=="" || trim($_POST['country'])==""|| trim($_POST['address'])==""){
            echo "Please fill in all the compulsory fields marked with astericks.";
            $addy_success = 0;
        }else{
          if($user_obj->addAddress($reg_dat)){
            echo "Address added successfully.";
            $addy_success = 1;
          }else{
            echo "Address adding failed. Please try again or contact the administrator.";
            $addy_success = 0;
          }
        }
      }
    }
  case "address": //****************************** ADD/EDIT ADDRESS INFORMATION ***************************************
    if(isset($_GET["rem_add"])){
      $user_obj->deleteAddress($_GET["rem_add"]);
    }else if(!isset($_POST['processed']) || $_POST['processed'] != 3){      
        $addy_success = -1;        
    }
    
?>
<table class="form_table">
<?php
    if($user_obj->getAddress()){
      $reg_dats = $user_obj->userAddress;
      $i=0;
      foreach($reg_dats AS $reg_dat){            
?>
<tr><td colspan=2 style="background-color:#888888;height:30px;text-align:center;color:#FFFFFF;"><b><?php
        if($i==0) echo "Primary Address";
        else echo "Address ".$i;
?></b></td></tr>
<tr><td>
Name</td><td><b><?php echo $reg_dat[$user_obj->tbFields2['first_name']]." ".$reg_dat[$user_obj->tbFields2['last_name']];?></b>
</td></tr>
<tr><td>
Company</td><td><b><?php echo $reg_dat[$user_obj->tbFields2['company']];?></b>
</td></tr>
<tr><td>
Address</td><td><b><?php echo $reg_dat[$user_obj->tbFields2['address']];?></b>
</td></tr>
<tr><td>
Postal Code</td><td><b><?php echo $reg_dat[$user_obj->tbFields2['postal']];?></b>
</td></tr>
<tr><td>
City</td><td><b><?php echo $reg_dat[$user_obj->tbFields2['city']];?></b>
</td></tr>
<tr><td>
Country</td><td><b><?php echo $user_obj->getCountryName($reg_dat[$user_obj->tbFields2['country']]);?></b>
</td></tr>
<tr><td colspan=2 style="text-align:right;">edit&nbsp;<a href="?p=account&t=address&rem_add=<?php echo $reg_dat[$user_obj->tbFields2['id']];?>">delete</a></td></tr>
<?php
      $i++;
      }
  }else{
?>
<tr><td colspan=2 align="center"><b>NO ADDRESSES SPECIFIED</b></td></tr>
<?php
  }
?>
<tr><td colspan=2 align="center">&nbsp;</td></tr>
<tr><td colspan=2 style="background-color:#888888;height:30px;text-align:center;color:#FFFFFF;"><b><b>Add New Address</b></td></tr>
<form action="?p=account&t=add_address" method="POST"><input type="hidden" name="customer_id" value="<?php echo $user_obj->userID;?>">
<input type="hidden" name="processed" value=3><input type="hidden" name="primary_flag" value=1>
<tr><td>
First Name*</td><td><input type="text" name="first_name" value="<?php if($addy_success == 0) echo $_POST['first_name']; else echo $user_obj->getName();?>">
</td></tr>
<tr><td>
Last Name*</td><td><input type="text" name="last_name" value="<?php if($addy_success == 0) echo $_POST['last_name']; else echo $user_obj->getLastName();?>">
</td></tr>
<tr><td>
Company</td><td><input type="text" name="company" value="<?php if($addy_success == 0) echo $_POST['company'];?>">
</td></tr>
<tr><td>
Address*</td><td><input type="text" name="address" value="<?php if($addy_success == 0) echo $_POST['address'];?>">
</td></tr>
<tr><td>
Postal Code*</td><td><input type="text" name="postal" value="<?php if($addy_success == 0) echo $_POST['postal'];?>">
</td></tr>
<tr><td>
City</td><td><input type="text" name="city" value="<?php if($addy_success == 0) echo $_POST['city'];?>">
</td></tr>
<tr><td>
Country*</td><td><select name="country">
<option value="">Please Select</option>
<?php
  $countries = $user_obj->getCountries();
  
  foreach($countries as $country_single){
    echo "<option value=".$country_single["ID"]." ";
    if($addy_success == 0 && $_POST['country'] == $country_single["ID"]){
      echo "SELECTED";
    }
    echo ">".$country_single["NAME"]."</option>";
  }
?>
</select>
</td></tr>
<tr><td colspan=2 align="center"><input type="submit" value="Submit Address">
</td></tr>
</form>
</table>
<?php
    break;
  case "update_details": //****************************** PROCESS EDIT ACCOUNT INFORMATION ***************************************
    if(isset($_POST['processed'])){
      if($_POST['processed'] == 2){
        if(!isset($_POST['gender']))
          $tmp_gender = "U";
        else
          $tmp_gender = $_POST['gender'];
                                   
        $tmp_dat = array(
          "first_name" => $_POST['first_name'],
          "last_name" => $_POST['last_name'],
          "gender" => $tmp_gender,
          "dob" => $_POST['dob'],          
          "telephone" => $_POST['telephone'],
          "fax" => $_POST['fax'],
          "newsletter_flag" => $_POST['newsletter_flag']
         );
         
        $dob_correct = 0;
        
        if(trim($_POST['dob']) != ""){
          $tmp_dob = explode("/", trim($_POST['dob']));
          if(sizeof($tmp_dob) == 3 && checkdate($tmp_dob[1] ,$tmp_dob[0] ,$tmp_dob[2]))
            $dob_correct = 1;
        }
        
        if(trim($_POST['telephone'])=="" || trim($_POST['first_name'])=="" || trim($_POST['last_name'])==""){
          echo "Please fill in all the compulsory fields marked with astericks.";
          
          /** ERROR CHECKING **/
        }else if(trim($_POST['dob']) != "" && $dob_correct == 0){
          echo "Invalid Date for Date of Birth. Please follow format <i>dd/mm/yyyy</i>.";
          
        }else{
          if($user_obj->editUserInfo($tmp_dat))
            echo "Details changed successfully.";
          else
            echo "Details change failed. Please try again or contact the administrator.";
        }  
        $user_obj->refreshData();          
      }
    }    
    
  case "info":      //****************************** DISPLAY ACCOUNT INFORMATION (DEFAULT) *************************************
    default;
    $reg_dat = $user_obj->userData;
?>
<table class="form_table">
<tr><td>
Email*</td><td><b><?php echo $reg_dat[$user_obj->tbFields1['email']];?></b>
</td></tr>
<tr><td>
Password*</td><td><b>**********</b>
</td></tr>
<tr><td>
First Name*</td><td><b><?php echo $reg_dat[$user_obj->tbFields1['first_name']];?></b>
</td></tr>
<tr><td>
Last Name*</td><td><b><?php echo $reg_dat[$user_obj->tbFields1['last_name']];?></b>
</td></tr>
<tr><td>
Gender</td><td><b><?php 
if($reg_dat[$user_obj->tbFields1['gender']]=="M") 
  echo 'Male';
else if($reg_dat[$user_obj->tbFields1['gender']]=="F") 
  echo 'Female';
else echo 'Unspecified';
?></b>
</td></tr>
<tr><td>
Date of Birth</td><td><b><?php echo $user_obj->transformDate($reg_dat[$user_obj->tbFields1['dob']]);?></b>&nbsp;(DD/MM/YYYY)
</td></tr>
<tr><td>
Phone Number*</td><td><b><?php echo $reg_dat[$user_obj->tbFields1['telephone']];?></b>
</td></tr>
<tr><td>
Fax Number</td><td><b><?php echo $reg_dat[$user_obj->tbFields1['fax']];?></b>
</td></tr>
<tr><td>
Receive Newsletter</td><td><b><?php if($reg_dat[$user_obj->tbFields1['newsletter_flag']]=="1") echo 'Yes'; else echo 'No';?>
</b></td></tr>
<tr><td>
Membership</td><td><b><?php 
$meminfo_tmp = $user_obj->getMemberType();
if($meminfo_tmp){
  echo $meminfo_tmp[$user_obj->tbFields11['type']]." (".$meminfo_tmp[$user_obj->tbFields11['discount']]."%)";
}
?>
</b></td></tr>
<tr><td colspan=2>&nbsp;</td></tr>
</table>
<?php  
  break;
}
?>
</td></tr></table>

<p>&nbsp;<p>
</div>