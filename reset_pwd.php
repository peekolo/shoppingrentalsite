<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 

if (isset($_POST['reset_password']))
{
  if(trim($_POST['email']) != "" && trim($_POST['number']) != ""){
    switch($user_obj->resetPassword($_POST['email'],$_POST['number'])){
      case 1:
        echo "<b>Your password has been reset and the new password is mailed to your email account!</b>";
        break;
      case -1:
        echo "<b>There is no account associated with the email and telephone entered. Please retry or contact us for help.</b>";
        break;
      case -2:
        echo "<b>An error has occured. Please retry or contact us for help.</b>";
        break;        
    }  
  }
}
?>

<div class="box_big" style="position:relative;text-align:left;">
<div style="text-align:left;padding:0px 95px 0px 10px;background-color:#DBE8F0;border:2px solid #AAAAAA;font-family:Verdana, Arial, sans-serif;font-size:12px;line-height:1.5;">
&nbsp;<p>
<h1>Reset Password</h1>
<p>
  Please key in your <b>Email</b> and <b>Telephone Number</b> used during the <u>registration of your account</u> to verify your identity.
  <form method='post' action=''>
    <table class="form_table">
   <tr><td>Email</td><td><input type="text" name="email" value=""></td></tr>
   <tr><td>Telephone Number</td><td><input type="text" name="number" value=""></td></tr>
   <tr><td colspan=2 align="right"><input type="submit" name="reset_password" value="Reset Password"></td></tr>
    </table>
  </form>
<p>&nbsp;<p></div>
</div>