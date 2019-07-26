<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 

if($user_obj->is_logged_in()){
?>
<div class="box_big" style="position:relative;text-align:left;">
<div style="text-align:left;padding:0px 95px 0px 10px;background-color:#DBE8F0;border:2px solid #AAAAAA;font-family:Verdana, Arial, sans-serif;font-size:12px;line-height:1.5;">
&nbsp;<p>
<h1>Subscription Complete!</h1>
<p>
It may take a while for the process to be completed. When the Paypal process is completed, you will receive an email from Paypal regarding your first payment, and the <b>Rental Points</b> will be allocated to you.
<p>&nbsp;<p></div>
</div>
<?php
}else{
  echo "<h1>Unauthorized Access!<br>Please do not Refresh Page or use the Forward and Back buttons during a Transaction!</h1>";
  exit;
}
?>
