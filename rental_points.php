<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 

$test_site = 0;
if($secure_site ==0 && $test_site == 1){
  $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
  $paypal_user = "----";
  $paypal_return = "---?p=subscribecomplete";
  $paypal_cancel = "---?p=rentalpoints";
  $paypal_notify = "---/ipn_listener.php";
}else{
  $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
  $paypal_user = " ----";
  $paypal_return = "---?p=subscribecomplete";
  $paypal_cancel = "---?p=rentalpoints";
  $paypal_notify = "---/ipn_listener.php";
}

?>


<div class="box_big" style="position:relative;text-align:left;">
<div style="text-align:left;padding:0px 95px 0px 10px;background-color:#DBE8F0;border:2px solid #AAAAAA;font-family:Verdana, Arial, sans-serif;font-size:12px;line-height:1.5;">
&nbsp;<p>
<h1>Rental Points (RP)</h1>
<p>
Subscribe to one of our Rental Plans now to rent and try as many games as you want!<br>
You will also automatically gain the <a href='?p=membership'><b>High Warlord</b></a> membership to enjoy <b>10% off</b> non-promotional items!<p>
Sign Up for a<p>
<ul>
<li><big><b>Basic Subscription</b></big> - to receive <b>3 Rental Points</b>. <i>(at only <b>SGD$19.95/month</b>)</i></li>
<li><big><b>Silver Subscription</b></big> - to receive <b>5 Rental Points</b>. <i>(at only <b>SGD$29.95/month</b>)</i></li>
<li><big><b>Gold Subscription</b></big> - to receive <b>7 Rental Points</b>. <i>(at only <b>SGD$39.95/month</b>)</i></li>
<li><big><b>Titanium Subscription</b></big> - to receive <b>10 Rental Points</b>. <i>(at only <b>SGD$49.95/month</b>)</i></li>
</ul>
<p>&nbsp;<p>
<u><b>To Rent</b></u><br>
With the <b>Rental Points (RP)</b>, you can rent as many games as your <b>RP</b> allows you to. <b>Free delivery</b> is provided for the rented games. <p>
<u><b>To Return</b></u><br>
When you're done with the games and would like to return them, just submit a <i>Return Request</i> from your <a href='?p=rentcart'><b>Rental Cart</b></a> and the spent <b>RP</b> will be returned to your account immediately. Afterwhich you may then select other games to rent with the points that was returned to you. When we deliver your newly selected games to you, we will then collect back the games you have designated to be returned to us! Simple right?
<p>&nbsp;<p>
<script>
function finalizeform(){

<?php
  if(!$user_obj->is_logged_in()){
    echo "alert('Please Log In First!');\n";
    echo "return false;";
  }
?>

  var obj = getE("a3cost");
  var obj1 = getE("basicsub");
  var obj2 = getE("silversub");
  var obj3 = getE("goldsub");
  var obj4 = getE("titaniumsub");
  if(obj1.checked){
    obj.value="19.95";
    return true;
  }else if(obj2.checked){
    obj.value="29.95";
    return true;
  }else if(obj3.checked){
    obj.value="39.95";
    return true;
  }else if(obj4.checked){
    obj.value="49.95";
    return true;
  }
  alert("Error! Please try again or contact Administrator");
  return false;
}
</script>
<form action="<?php echo $paypal_url;?>" method="POST" onsubmit="return finalizeform();">
<input type="hidden" name="cmd" value="_xclick-subscriptions">
<input type="hidden" name="business" value="<?php echo $paypal_user;?>">
<input type="hidden" name="image_url"
value="http://www.boardgamelifestyle.com/img/top_01.gif">
  <input type="hidden" name="no_shipping" value="1">
  <input type="hidden" name="return"
value="<?php echo $paypal_return;?>">
  <input type="hidden" name="rm" value="2">
  <input type="hidden" name="cancel_return"
value="<?php echo $paypal_cancel;?>">
  <input type="hidden" name="a3" id="a3cost" value="0">
  <input type="hidden" name="p3" value="1">
  <input type="hidden" name="t3" value="M">
  <input type="hidden" name="src" value="1">
  <input type="hidden" name="sra" value="1">
  <input type="hidden" name="no_note" value="1">  
  <input type="hidden" name="currency_code" value="SGD">
  <input type="hidden" name="notify_url" value="<?php echo $paypal_notify;?>">
  <input type="hidden" name="custom" value="<?php echo $user_obj->userID;?>">
<table>
<tr><td><big><b>Sign Up Now!</b></big></td><tr>
<tr><td>
<table style="font-weight:bold;text-align:left;padding:5px 10px 5px 10px;background-color:#FFDDAA;border:2px solid #AAAAAA;font-family:Verdana, Arial, sans-serif;font-size:12px;line-height:1.5;">
<tr height="30px"><td><input id="basicsub" type="radio" name="item_name" value="Basic Subscription (with 3RP) for Board Game Lifestyle Rental" checked></td><td>Basic Subscription</td><td>&nbsp;&nbsp;&nbsp;3 RP&nbsp;&nbsp;&nbsp;</td><td>SGD$19.95/month</td></tr>
<tr height="30px"><td><input id="silversub" type="radio" name="item_name" value="Silver Subscription (with 5RP) for Board Game Lifestyle Rental"></td><td>Silver Subscription</td><td>&nbsp;&nbsp;&nbsp;5 RP&nbsp;&nbsp;&nbsp;</td><td>SGD$29.95/month</td></tr>
<tr height="30px"><td><input id="goldsub" type="radio" name="item_name" value="Gold Subscription (with 7RP) for Board Game Lifestyle Rental"></td><td>Gold Subscription</td><td>&nbsp;&nbsp;&nbsp;7 RP&nbsp;&nbsp;&nbsp;</td><td>SGD$39.95/month</td></tr>
<tr height="30px"><td><input id="titaniumsub" type="radio" name="item_name" value="Titanium Subscription (with 10RP) for Board Game Lifestyle Rental"></td><td>Titanium Subscription</td><td>&nbsp;&nbsp;&nbsp;10 RP&nbsp;&nbsp;&nbsp;</td><td>SGD$49.95/month</td></tr>
</table>
</td></tr>
<tr><td align='right'>  <input type="image"
     src="http://images.paypal.com/images/x-click-but01.gif"
border="0" name="submit"
alt="Make payments with PayPal - it’s fast, free and secure!"></td></tr>
<tr><td>&nbsp;<p><i><b>Secure Payment will be processed and completed by Paypal!</b><i></td></tr>
</table>
</form>
<p>&nbsp;<p></div>
</div>