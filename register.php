<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
?>
<div class="box_big" style="position:relative;">
&nbsp;<p>
<?php
    $reg_success = 0;
    $activate_success = 0;
    $activated_user = "";
    
    if(isset($_GET['activate'])){
      if(isset($_GET['email'])){
        $activated_user = $user_obj->activate($_GET['email'], $_GET['activate']);
        if($activated_user == ""){
          $activate_success = 0;
          echo "Invalid Activation.<p>";
        }else{
          $activate_success = 1;
          echo "Account activated for user ".$activated_user.".";
        }
      }    
    }
  
    if(isset($_POST['processed'])){
      if($_POST['processed'] == 1){
        
        if(!isset($_POST['gender']))
          $tmp_gender = "U";
        else
          $tmp_gender = $_POST['gender'];
                                   
        $reg_dat = array(          
          "email" => $_POST['email'],
          "password" => $_POST['password'],
          "first_name" => $_POST['first_name'],
          "last_name" => $_POST['last_name'],
          "gender" => $tmp_gender,
          "dob" => $_POST['dob'],
          "telephone" => $_POST['telephone'],
          "fax" => $_POST['fax'],
          "company" => $_POST['company'],          
          "address" => $_POST['address'],
          "postal" => $_POST['postal'],
          "city" => $_POST['city'],
          "country" => $_POST['country'],
          "primary_flag" => $_POST['primary_flag'],
          "newsletter_flag" => $_POST['newsletter_flag']
         );
         
        $dob_correct = 0;
        
        if(trim($_POST['dob']) != ""){
          $tmp_dob = explode("/", trim($_POST['dob']));
          if(sizeof($tmp_dob) == 3 && checkdate($tmp_dob[1] ,$tmp_dob[0] ,$tmp_dob[2]))
            $dob_correct = 1;
        }
                   
        if(trim($_POST['telephone'])=="" || trim($_POST['password'])=="" || trim($_POST['first_name'])=="" || trim($_POST['last_name'])=="" || trim($_POST['email'])==""){
          echo "Please fill in all the compulsory fields marked with astericks.";
          
          /** ERROR CHECKING **/
        }else if(trim($_POST['dob']) != "" && $dob_correct == 0){
          echo "Invalid Date for Date of Birth. Please follow format <i>dd/mm/yyyy</i>.";
        
        }else if((trim($_POST['address']) != "") && (trim($_POST['postal'])=="" || trim($_POST['country'])=="")){
          echo "Incomplete Address. Please fill in Postal Code and Country";
        
        }else{
        
          if(!$user_obj->user_exist($_POST['email'])){
            if($user_obj->addUserTemp($reg_dat)){
              $reg_success = 1;
              echo "<b>Registration success!</b><br>An email has been sent to you to verify your email and to activate of your account.<br>Thank you very much.";
            }else{
              echo "<b>There is an error during your registration.</b><br>Please try again or contact us for help.<br>Thank you very much.";            
            }
          }else{
            echo "This email has already been registered.";
          }
        }
        

         
         
      }
    }else{
      $reg_dat = array(
          "email" => "",
          "password" => "",
          "first_name" => "",
          "last_name" => "",
          "gender" => "U",
          "dob" => "",
          "telephone" => "",
          "fax" => "",
          "company" => "",          
          "address" => "",
          "postal" => "",
          "city" => "",
          "country" => "",
          "newsletter_flag" => "1"
       );
    }
    
    if($reg_success == 0){
    
?>
<h1>Register</h1>
<form action="?p=register" method="POST">
<input type="hidden" name="processed" value=1>
<input type="hidden" name="primary_flag" value=1>
<table class="form_table">
<tr><td>
Email *</td><td><input type="text" name="email" value="<?php echo $reg_dat['email'];?>">&nbsp;<i>(used as username to login)</i>
</td></tr>
<tr><td>
Password *</td><td><input type="password" name="password">
</td></tr>
<tr><td>
First Name *</td><td><input type="text" name="first_name" value="<?php echo $reg_dat['first_name'];?>">
</td></tr>
<tr><td>
Last Name*</td><td><input type="text" name="last_name" value="<?php echo $reg_dat['last_name'];?>">
</td></tr>
<tr><td>
Gender</td><td><input type="radio" name="gender" value="M" <?php if($reg_dat['gender']=="M") echo 'checked';?>>Male&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="gender" value="F" <?php if($reg_dat['gender']=="F") echo 'checked';?>>Female
</td></tr>
<tr><td>
Date Of Birth</td><td><input type="text" name="dob" value="<?php echo $reg_dat['dob'];?>">&nbsp;(DD/MM/YYYY)
</td></tr>
<tr><td>
Phone Number*</td><td><input type="text" name="telephone" value="<?php echo $reg_dat['telephone'];?>">&nbsp;<i>(only for verification purposes for delivery)</i>
</td></tr>
<tr><td>
Fax Number</td><td><input type="text" name="fax" value="<?php echo $reg_dat['fax'];?>">
</td></tr>
<tr><td>
Company</td><td><input type="text" name="company" value="<?php echo $reg_dat['company'];?>">
</td></tr>
<tr><td>
Address</td><td><input type="text" name="address" value="<?php echo $reg_dat['address'];?>">
</td></tr>
<tr><td>
Postal Code</td><td><input type="text" name="postal" value="<?php echo $reg_dat['postal'];?>">
</td></tr>
<tr><td>
City</td><td><input type="text" name="city" value="<?php echo $reg_dat['city'];?>">
</td></tr>
<tr><td>
Country</td><td><select name="country">
<option value="">Please Select</option>
<?php
  $countries = $user_obj->getCountries();
  
  foreach($countries as $country_single){
    echo "<option value=".$country_single["ID"];
    if($reg_dat['country'] == $country_single["ID"]) echo " SELECTED";
    echo ">".$country_single["NAME"]."</option>";
  }
?>
</select></td></tr>
<tr><td>
Receive Newsletter</td><td><input type="radio" name="newsletter_flag" value="1" <?php if($reg_dat['newsletter_flag']=="1") echo 'checked';?>>Yes&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="newsletter_flag" value="0" <?php if($reg_dat['newsletter_flag']=="0") echo 'checked';?>>No
</td></tr>
</table>
<input type="submit" value="Submit">
</form>

<?php
    }else{
?>
<h1>Register Information</h1>
<table class="form_table">
<tr><td>
Email</td><td><b><?php echo $reg_dat['email'];?></b>
</td></tr>
<tr><td>
Password</td><td><b><?php echo $reg_dat['password'];?></b>
</td></tr>
<tr><td>
First Name</td><td><b><?php echo $reg_dat['first_name'];?></b>
</td></tr>
<tr><td>
Last Name</td><td><b><?php echo $reg_dat['last_name'];?></b>
</td></tr>
<tr><td>
Gender</td><td><input type="radio" name="gender" value="M" <?php if($reg_dat['gender']=="M") echo 'checked';?>>Male&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="gender" value="F" <?php if($reg_dat['gender']=="F") echo 'checked';?>>Female
</td></tr>
<tr><td>
Date of Birth <i>(dd/mm/yyyy)</i></td><td><b><?php echo $reg_dat['dob'];?></b>
</td></tr>
<tr><td>
Phone Number</td><td><b><?php echo $reg_dat['telephone'];?></b>
</td></tr>
<tr><td>
Fax</td><td><b><?php echo $reg_dat['fax'];?></b>
</td></tr>
<tr><td>
Company</td><td><b><?php echo $reg_dat['company'];?></b>
</td></tr>
<tr><td>
Address</td><td><b><?php echo $reg_dat['address'];?></b>
</td></tr>
<tr><td>
Postal Code</td><td><b><?php echo $reg_dat['postal'];?></b>
</td></tr>
<tr><td>
City</td><td><b><?php echo $reg_dat['city'];?></b>
</td></tr>
<tr><td>
Country</td><td><b><?php if($reg_dat['country'] != "") echo $user_obj->getCountryName($reg_dat['country']);?></b>
</td></tr>
<tr><td>
Receive Newsletter</td><td><input type="radio" name="newsletter_flag" value="1" <?php if($reg_dat['newsletter_flag']=="1") echo 'checked';?>>Yes&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="newsletter_flag" value="0" <?php if($reg_dat['newsletter_flag']=="0") echo 'checked';?>>No
</td></tr>
</table>

<?php    
    }
?>
<p>&nbsp;<p>
</div>