<?php
define('PASDNKFWIE',1);
require_once("php_shop/includes/configure.php");
require_once("php_shop/user_class.php");
require_once("php_shop/store_class.php");
require_once("php_shop/cart_class.php");
require_once("php_shop/rentcart_class.php");
require_once("php_shop/misc_class.php");

session_start();

$secure_site = SECURE_SITE_FLAG;

if(isset($_GET["view_flag"])){
  $view_flag = $_GET["view_flag"];
  $_SESSION['view_flag'] = $view_flag;
}else if(isset($_SESSION['view_flag'])){
  $view_flag = $_SESSION['view_flag'];
}else{
  $view_flag = "All";
  $_SESSION['view_flag'] = $view_flag;
}
$store_obj = new storeAccess();
$user_obj = new userAccess();
$misc_obj = new miscAccess();
$page_type = "";
$big_msg = "";
$forget_pwd = 0;

if(isset($_GET["p"])){          // Get Page Type
  $page_type = $_GET["p"];
}

if(!$user_obj->is_logged_in()){     // If user is not logged in
  if(isset($_POST["login_action"]) && $_POST["login_action"] == 1 && trim($_POST["username"]) != "" && trim($_POST["password"]) !=""){  
    if(!$user_obj->login($_POST["username"], $_POST["password"])) $forget_pwd = 1;
  }
}else if($page_type=="logout"){
  $user_obj->logout();
}

$cart_obj = new cartAccess($user_obj->userID);
$rentcart_obj = new rentcartAccess($user_obj->userID);

if(isset($_GET["cartthis"]) && isset($_GET["cartthis_qty"])){
  $attrib_id_tmp = "";
  if(isset($_GET["cartattrib1"])){
    $attrib_id_tmp = array();
    $tmp_varname = "cartattrib";
    $i=1;
    while(isset($_GET[$tmp_varname.$i])){
      $attrib_id_tmp[] = $_GET[$tmp_varname.$i];
      $i++;
    }
  }
  $cart_obj->addtoCart($_GET["cartthis"],$_GET["cartthis_qty"],$attrib_id_tmp);
  $big_msg = "Item has been added to your Shopping Cart!";
}else if(isset($_GET["decartthis"]) && isset($_GET["decartthis_qty"])){
  $cart_obj->deletefromCart($_GET["decartthis"],$_GET["decartthis_qty"]); //update
  $big_msg = "Item Quantity has been changed!";
}

if(isset($_GET["rentcartthis"])){
  $rentcart_obj->addtoCart($_GET["rentcartthis"]);
  $big_msg = "Item has been added to your Rental Cart!";
}else if(isset($_GET["derentcartthis"])){
  $rentcart_obj->deletefromCart($_GET["derentcartthis"]);
  $big_msg = "Item has been removed!";
}

if(isset($_GET["waitthis"])){
  $cart_obj->saveforLater($_GET["waitthis"]);  
}else if(isset($_GET["unwaitthis"])){
  $cart_obj->movetoCart($_GET["unwaitthis"]);  
}

if(isset($_GET["rentwaitthis"])){
  $rentcart_obj->saveforLater($_GET["rentwaitthis"]);  
}else if(isset($_GET["rentunwaitthis"])){
  $rentcart_obj->movetoCart($_GET["rentunwaitthis"]);  
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="keywords" content="Board Game, Card Game, Singapore">
<meta name="Author" content="pieceofcake">
<title>Title</title>
<style type="text/css">
<!--body {	margin-left: 0px;	margin-top: 0px;	margin-right: 0px;	margin-bottom: 0px;}--></style>
<link href="css.css" rel="stylesheet" type="text/css">
<?php
if($secure_site == 1){
?>
<script language="JavaScript" src="http://www.trustlogo.com/trustlogo/javascript/trustlogo.js" type="text/javascript">
</script>
<?php }?>

<script type="text/javascript">
window.onresize = function(){
  setHintPos();
}

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

function waitthis(id){
  var url_old = window.location.href;
  var url_part = url_old.split("?");
  url_new = url_part[0] + "?p=cart&waitthis="+id;
  
  window.location.href = url_new;
}

function unwaitthis(id){
  var url_old = window.location.href;
  var url_part = url_old.split("?");
  url_new = url_part[0] + "?p=cart&unwaitthis="+id;
  
  window.location.href = url_new;
}


function cartthis(pid){
  var url_old = window.location.href;
  var url_part = url_old.split("?");
  qty = 1;
  obj = getE('cart_qty');
  qty = obj.value;  
  obj = getE('attrib_num');  
  attribNum = obj.value;
  attribStr = "";
  j = 1;  
  for(i=0;i<attribNum;i++){
    objTmp = getE('options'+i);    
    if(objTmp.checked == true){         
      objTmp2 = getE('attrib'+i);     
      if(objTmp2.type.toLowerCase() != "hidden")      
        objTmp2 = objTmp2[objTmp2.selectedIndex];
              
      attribStr += "&cartattrib" + j + "=" + objTmp2.value;
    }
  }
  
  url_new = url_part[0]+ "?p=cart&cartthis="+pid+"&cartthis_qty="+qty + attribStr;

  window.location.href = url_new;
}

function decartthis(cid){
  var url_old = window.location.href;
  var url_part = url_old.split("?");
  qty = 1;  
  obj_id = cid + "_cart_qty";
  
  obj = getE(obj_id);
  qty = obj.value;
        
  url_new = url_part[0]+ "?p=cart&decartthis="+cid+"&decartthis_qty="+qty;
  
  window.location.href = url_new;
}

function set_view(obj){
  if(obj && obj.value != ""){
    var url_old = window.location.href;
    if(url_old.indexOf("?") == -1){
      window.location.href = url_old + "?view_flag="+obj.value;
    }else{
      window.location.href = url_old + "&view_flag="+obj.value;
    }
  }
}

function search_manu(obj){
  if(obj && obj.value != ""){
    window.location.href = "?p=search&t=basic&searchfield=&manufacturer="+obj.value;
  }
}

function findPosX(obj)
{
    var curleft = 0;    
    if(obj.offsetParent){    
        while(1) 
        {          
          curleft += obj.offsetLeft;          
          if(!obj.offsetParent){         
            break;
          }
          obj = obj.offsetParent;
        }
    }else if(obj.x){    
        curleft += obj.x;
    }
    return curleft;
}

function findPosY(obj)
{
    var curtop = 0;
    if(obj.offsetParent)
        while(1)
        {
          curtop += obj.offsetTop;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.y)
        curtop += obj.y;
    return curtop;
}

function setHintPos(){

  obj = getE('searchfield');  
  obj2 = getE('hintbox');
  
  x = findPosX(obj);
  y = findPosY(obj) + 23;  
  obj2.style.left = x+"px";
  obj2.style.top = y+"px";
    
}

function inittest(){
  document.onkeydown = keyHandler;
}

</script>
</head>
<body>
<div id="hintbox" style="position:absolute;background-color:#FFFFFF;border:1px solid #000000;width:205px;z-index:98;visibility:hidden;overflow:hidden;"><select size=10 style="width:204px;" onchange="updateSearch(this.value);" onblur="hideHint();" id="hintselect">
</select></div>

<div align="center">
<table border=0>
<script>
inittest();

function keyHandler(event){
var key;
    if (!event && window.event) event = window.event;
		if (event) key = event.keyCode;
		else key = event.which;

if(key==40){  
  obj1 = getE("hintbox");  
  if(obj1.style.visibility == 'visible'){
    obj2 = getE("hintselect");
    obj2.focus();
  }
}else if(key==13){
  obj1 = getE("hintbox");  
  obj2 = getE("searchfield");
  if(obj1.style.visibility == 'visible' && obj2.value != ""){
    document.basicsearchform.submit();
  }
}
}

function updateSearch(str){
  var obj1 = getE("searchfield");
  obj1.value = str;
}

function setOptions(data){
  var terms = data.split("<br />");
  obj1 = getE("hintselect");
  obj1.options.length=0;
  for(var i=0;i<terms.length;i++){
    obj1.options[i] = new Option(terms[i], terms[i], false, false);
  }
}

function hideHint(){
  obj1 = getE("hintbox");
  obj1.style.visibility = 'hidden'; 		
	return;
}

var xmlHttp;
function showHint(str){    

	if (str.length==0){ 
		obj1 = getE("hintbox");		
		obj1.style.visibility = 'hidden'; 		
		return;
	}
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request");
		return;
	} 
	var url="gethint.php";
	url=url+"?q="+str;
	url=url+"&sid="+Math.random();	
	xmlHttp.onreadystatechange=stateChanged;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
} 

function stateChanged(){ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
    var str = xmlHttp.responseText;
    obj1 = getE("hintbox");
    obj1.style.visibility = 'hidden';
    if(str.length != 0){      
      setHintPos();
      obj1.style.visibility = 'visible'; 		
      setOptions(xmlHttp.responseText);
		//obj1.innerHTML=xmlHttp.responseText;					
		}
	} 
}

function GetXmlHttpObject(){
	var xmlHttp=null;
	try{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}
	catch (e){
		// Internet Explorer
		try{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e){
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}
</script>

<tr><td colspan=3>

<table width="100%" height="190" cellspacing="0" cellpadding="0" style="border-spacing:0px;border-collapse:collapse;margin:0;padding:0;">
<tr>
		<td colspan="2" width="211">
			<a href="index.php"><img src="img/top_01.gif" width="211" height="147" alt=""></a></td>
		<td background="img/top_02.gif" width="355" height="147" valign="top">
		<?php


  if (!$user_obj->is_logged_in()){
?>
<table width="320px">
<tr><td colspan=3 height="20px" style="vertical-align:bottom;font-family:Verdana;font-size:10px;line-height:13px;color:#990000;"><a href='?p=membership' style='color:#BB0000;'>*Learn more about our membership now!*</a></tr>
<tr><td>
<P>
<form action="?p=login" method="POST">
<input type="hidden" name="login_action" value=1>
<span style="font-family:Verdana;font-size:11px;line-height:15px;color:#EFEFEF;">Email</span><br>
<input type="text" name="username" style="font-family:Verdana;font-size:11px;border: solid 1px #cccccc;"><br>
<span style="font-family:Verdana;font-size:11px;line-height:15px;color:#EFEFEF;">Password</span><br>
<input type="password" name="password" style="font-family:Verdana;font-size:11px;border:solid 1px #cccccc;"><br>
<input type="submit" value="Login">
</form></td>
<td style="padding:0px 0px 0px 10px;color:#EFEFEF;"><b>OR</b></td>
<td style="padding:0px 10px 0px 10px;font-family:Verdana;font-size:12px;line-height:15px;color:#EFEFEF;">&nbsp;<p>
<i>Click  <a href="?p=register" id="noformat2">here</a> to create an account now!</i><p><i><small><a href="?p=resetpwd" id="noformat2">(forgot password?)</a></small></i>
</td></tr></table>
<?php
  }else{
?>  
<table width="320px">
<tr><td height="20px" style="vertical-align:bottom;font-family:Verdana;font-size:10px;line-height:13px;color:#990000;"><a href='?p=membership' style='color:#BB0000;'>*Learn more about our membership now!*</a></tr>
<tr><td style="font-family:Verdana;font-size:11px;line-height:15px;color:#EFEFEF;">
<P>
<b>Welcome <?php echo $user_obj->getName();?>!</b><p>
<?php 
  $member_flag = 0;
  if($member_tmp = $user_obj->getMemberType()){
    $member_flag = 1;
    echo "You are a <b>".$member_tmp[$user_obj->tbFields11['type']]." Member</b><br>and enjoy <u>".$member_tmp[$user_obj->tbFields11['discount']]."%</u> off all non-promotional items";
    if($member_tmp[$user_obj->tbFields11['exclusive_discount']] != 0){
      echo "<br>and also <u>".$member_tmp[$user_obj->tbFields11['exclusive_discount']]."%</u> off exclusive items";
    }
    echo "!";
  }else{
    echo "Become a <a href='?p=membership' id='noformat2'>member</a><br>to enjoy our special perks now!";
  }

?>
<p>Click here for the latest arrival, or check out now.
</td></tr></table>
<?php
  }
  
  $cat_obj = $store_obj->getCategories($view_flag );  

?></td>
		<td width="14" height="147" background="img/top_03.gif"></td>
		<td width="460" height="147" background="img/top_04.gif">
		<table width="100%" style="border-collapse:collapse;border-spacing:0px;">
	  <tr><td><table width="90%" height="100%">	  
		<?php
  if($view_flag == "Rent"){
    $cart_data = $rentcart_obj->getCart();
    $total_price = 0;
    $j = 0;
    if($cart_obj->is_logged_in()){    
      if($cart_data != "" && sizeof($cart_data)>0){    
        foreach($cart_data AS $cart_single){
          $j++;
          $total_price = $total_price + $store_obj->getRPrice($cart_single[$cart_obj->tbFields["product_id"]]);
          if($j < 4){
            echo "<tr><td width=30px valign='top'>&nbsp;</td><td valign='top'><a href='?p=search&t=detailed&p_id=".$cart_single[$cart_obj->tbFields["product_id"]]."'>".$store_obj->getName($cart_single[$cart_obj->tbFields["product_id"]])."</a>&nbsp;";
            echo "</td></tr>";
          }else if($j==4){
            echo "<tr><td width=30px>&nbsp;</td><td><a href='?p=rentcart' id='noformat'>...(more items)</a></td></tr>";
          }
        }
        echo "<tr><td colspan=2><hr></td></tr>";
        echo "<tr><td>&nbsp;</td><td align='right'>".$total_price." RP</td></tr>";   

      }else{
        
        echo "<tr><td width='100%'>You have no items in your cart</td></tr>";     
      }
    }else{        
      if($cart_data != "" && sizeof($cart_data)>0){
        $cart_data_size = sizeof($cart_data);
        for($i=0; $i<$cart_data_size;$i++){
          $j++;
          $total_price = $total_price + $store_obj->getRPrice($cart_data[$i]);
          if($j < 4){
            echo "<tr><td width=30px valign='top'>&nbsp;</td><td valign='top'><a href='?p=search&t=detailed&p_id=".$cart_data[$i]."'>".$store_obj->getName($cart_data[$i])."</a>&nbsp;";
            echo "</td></tr>";
          }else if($j==4){
            echo "<tr><td width=30px>&nbsp;</td><td><a href='?p=rentcart' id='noformat'>...(more items)</a></td></tr>";
          }
        }    
        echo "<tr><td colspan=2><hr></td></tr>";
        echo "<tr><td>&nbsp;</td><td align='right'>".$total_price." RP</td></tr>";
      }else{
        echo "<tr><td>You have no items in your cart</td></tr>";     
      }      
    }      
    
    
  }else{
    $cart_data = $cart_obj->getCart();    
    $total_price = 0.00;
    $j = 0;
    if($cart_obj->is_logged_in()){    
      if($cart_data != "" && sizeof($cart_data)>0){    
        foreach($cart_data AS $cart_single){
          $j++;
          $tmp_cart_id = $cart_single[$cart_obj->tbFields["id"]];
          $total_price = $total_price + (($cart_obj->getAttribPrice($tmp_cart_id) + $store_obj->getPrice($cart_single[$cart_obj->tbFields["product_id"]])) * $cart_single[$cart_obj->tbFields["quantity"]]);
          if($j < 4){
            echo "<tr><td width=30px valign='top'>".$cart_single[$cart_obj->tbFields["quantity"]]." x </td><td valign='top'><a href='?p=search&t=detailed&p_id=".$cart_single[$cart_obj->tbFields["product_id"]]."'>".$store_obj->getName($cart_single[$cart_obj->tbFields["product_id"]])."</a>&nbsp;";
            if($cart_obj->getAttribs($tmp_cart_id)) echo "(+options)";
            echo "</td></tr>";
          }else if($j==4){
            echo "<tr><td width=30px>&nbsp;</td><td><a href='?p=cart' id='noformat'>...(more items)</a></td></tr>";
          }
        }
        echo "<tr><td colspan=2><hr></td></tr>";
        echo "<tr><td>&nbsp;</td><td align='right'>$".number_format($total_price,2)."</td></tr>";   
        if($member_flag == 1)
          echo "<tr><td colspan=2><span class='notes1'>*Before Discounts!</span></td><tr>";
      }else{
        
        echo "<tr><td width='100%'>You have no items in your cart</td></tr>";     
      }
    }else{        
      if($cart_data != "" && sizeof($cart_data)>0){
        $cart_data_size = sizeof($cart_data);
        for($i=0; $i<$cart_data_size;$i=$i+3){
          $j++;
          $total_price = $total_price + (($cart_obj->getAttribPrice($i) + $store_obj->getPrice($cart_data[$i])) * $cart_data[$i+1]);
          if($j < 4){
            echo "<tr><td width=30px valign='top'>".$cart_data[$i+1]." x </td><td valign='top'><a href='?p=search&t=detailed&p_id=".$cart_data[$i]."'>".$store_obj->getName($cart_data[$i])."</a>&nbsp;";
            if($cart_data[$i+2] != "") echo "(+options)";
            echo "</td></tr>";
          }else if($j==4){
            echo "<tr><td width=30px>&nbsp;</td><td><a href='?p=cart' id='noformat'>...(more items)</a></td></tr>";
          }
        }    
        echo "<tr><td colspan=2><hr></td></tr>";
        echo "<tr><td>&nbsp;</td><td align='right'>$".number_format($total_price,2)."</td></tr>";
      }else{
        echo "<tr><td>You have no items in your cart</td></tr>";     
      }      
    }  
  }      
?></table>
</td><td width="80px" align="right"><a href="?p=<?php if($view_flag == "Rent") echo "rentcart"; else echo "cart";?>" id="noformat"><img src="img/button_cart.gif"></a><br><a href="?p=checkout1" id="noformat"><img src="img/button_checkout.gif"></a><br><a href="?p=account" id="noformat"><img src="img/button_account.gif"></a><br><a href="?p=logout" id="noformat"><img src="img/button_logout.gif"></a></td></tr></table>
		</td>
		<td width="20" height="147" background="img/top_05.gif">&nbsp;</td>
	</tr>
	<tr>
		<td width="17">
			<img src="img/top_06.gif" width="17" height="43" alt=""></td>
		<td colspan="2" background="img/top_07.gif" width="549" height="43">
			<form action="?" name="basicsearchform" method="GET"><input type="hidden" name="p" value="search"><input type="hidden" name="t" value="basic"><input type="text" id="searchfield" name="searchfield" size=30 onkeyup="showHint(this.value)">
<select name="category">
<option value="***">All Categories&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
<?php
  foreach($cat_obj as $cat_single){
    echo "<option value='".$cat_single["CAT"]."'>".$cat_single["CAT"]."</option>";
  }
?>
</select>&nbsp;&nbsp;<a href="javascript:document.basicsearchform.submit();" id="noformat"><input style="position:absolute;" src="img/search.gif" type="image"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?p=advancedsearch" id="smalloption"><input style="position:absolute;" src="img/more.gif" type="image"></a>
			</form>
			</td>

		<td colspan="2" background="img/top_08.gif" height="43" align="right" style="font-family:Verdana;font-size:12px;color:#FFFFFF;"><a href="?p=rentalpoints" id="noformat2">Rental Points</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="?p=aboutus" id="noformat2">About Us</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="?p=privacy" id="noformat2">Privacy Notice</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="?p=faqs" id="noformat2">FAQs</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="?p=contactus" id="noformat2">Contact Us</a></td>
		<td>
			<img src="img/top_09.gif" width="20" height="43" alt=""></td>
	</tr>
	<tr>
		<td width="17" height="1"><img src="img/spacer.gif" height="1" alt=""></td>
		<td width="194" height="1"><img src="img/spacer.gif" height="1" alt=""></td>
		<td width="355" height="1"><img src="img/spacer.gif" height="1" alt=""></td>
		<td width ="14" height="1"><img src="img/spacer.gif" height="1" alt=""></td>
		<td height="1"><img src="img/spacer.gif" height="1" alt=""></td>
		<td width="20" height="1">
			<img src="img/spacer.gif" height="1" alt=""></td>
	</tr>

</table>

<table class="three_section">
<tr>

<!--  LEFT SECTION -->

<td width="150px" align="left" valign="top">
<p>
You have <a href='?p=rentalpoints' style='color:#CC0000;font-weight:bold;'><?php echo $user_obj->getRP();?> RP</a>
<br>&nbsp;<p>
<table width=100% cellpadding=0 cellspacing=0 style="table-layout: fixed;"><tr height="25px"><td background="img/subhead_01.gif" width="6px"></td><td background="img/subhead_02.gif" width="75px"><b>View</b></td><td background="img/subhead_03.gif" width="32px">&nbsp;</td><td background="img/subhead_04.gif"></td></tr></table>
<p><div style="padding:0px 1px 0px 5px;">
<select name="view_flag" style="width:90%;" onchange="javascript:set_view(this);">
<option value="All" <?php if($view_flag=="All") echo "selected";?>>ALL</option>
<option value="Sale" <?php if($view_flag=="Sale") echo "selected";?>>For Sale Only</option>
<option value="Rent" <?php if($view_flag=="Rent") echo "selected";?>>For Rent Only</option>
</select>
</div><p>&nbsp;<p>
<?php
  $specials_obj = $store_obj->getSpecial(1);
  if($view_flag != "Rent" && $specials_obj && $specials_obj != ""){
?>
  <table width=100% cellpadding=0 cellspacing=0 style="table-layout: fixed;"><tr height="25px"><td background="img/subhead_01.gif" width="6px"></td><td background="img/subhead_02.gif" width="75px"><b>Special</b></td><td background="img/subhead_03.gif" width="32px">&nbsp;</td><td background="img/subhead_04.gif"></td></tr></table>
<p><div style="padding:0px 1px 0px 5px;">
<?php
  foreach($specials_obj as $specials_single){    
    $store_obj->smallView($specials_single["ID"]);
  }  
  echo "</div>";
  echo "<div style='text-align:right;'>";
  echo "<a href='?p=specials' style='color:#D00000;font-style:italic;'>(see all)</a></div><p>";
  }
?>

<table width=100% cellpadding=0 cellspacing=0 style="table-layout: fixed;"><tr height="25px"><td background="img/subhead_01.gif" width="6px"></td><td background="img/subhead_02.gif" width="75px"><b>Categories</b></td><td background="img/subhead_03.gif" width="32px">&nbsp;</td><td background="img/subhead_04.gif"></td></tr></table>
<p>
<div style="padding:0px 1px 0px 5px;">
<?php
  foreach($cat_obj as $cat_single){
    echo "<a href='?p=search&searchfield=&category=".$cat_single["CAT"]."'>".$cat_single["CAT"]." (".$cat_single["COUNT"].")</a><br>";
  }
?>
</div>
<p>&nbsp;<p>
<table width=100% cellpadding=0 cellspacing=0 border=0 style="table-layout: fixed;"><tr height="25px"><td background="img/subhead_01.gif" width="6px"></td><td background="img/subhead_02.gif" width="85px"><b>Manufacturers</b></td><td background="img/subhead_03.gif" width="32px">&nbsp;</td><td background="img/subhead_04.gif"></td></tr></table>
<p><div style="padding:0px 1px 0px 5px;">
<select name="manufacturer" style="width:90%;" onchange="javascript:search_manu(this);">
<option value="">Please Select</option>
<?php
  $manu_obj = $store_obj->getManufacturers();
  if(isset($_GET["manufacturer"])){
    $manu_tmp = $_GET["manufacturer"];
    foreach($manu_obj as $manu_single){    
      echo "<option value='".$manu_single["MANU"]."' ";
      if($manu_single["MANU"] == $manu_tmp){
        echo "SELECTED";
      }
      echo ">".$manu_single["MANU"]."</option>";
    }  
  }else{
    foreach($manu_obj as $manu_single){    
      echo "<option value='".$manu_single["MANU"]."'>".$manu_single["MANU"]."</option>";
    }
  }
?>
</select></div>
<p>&nbsp;<p>
<?php
if($secure_site == 1){
?>
<a href="http://www.instantssl.com" id="comodoTL">SSL</a>
<script type="text/javascript">TrustLogo("img/secure_site.gif", "SC", "bottomleft");</script>
<p>&nbsp;<p>
<?php }else echo "<img src='img/secure_site.gif'><p>&nbsp;<p>"; ?>
<a href="http://www.decoderscafe.com" target="_blank"><img alt="Decoder Cafe" src="banners/decoder_banner.jpg" width="130px" border="0"></a>
<p>&nbsp;<p>
</td> <!-- END OF LEFT SECTION -->

<td background="img/hor_line.gif">
</td>
<!--  MIDDLE SECTION -->
<td align="center" valign="top">
<?php
if($big_msg != ""){
?>
<div class="msg_box" style="position:relative;">
<?php echo $big_msg;?></div><p>
<?php  
}
?>
<?php
switch($page_type){
  case 'new':
    require_once("new.php");
    break;
  case 'specials':
    require_once("specials.php");
    break;
  case 'resetpwd':
    require_once("reset_pwd.php");
    break;
  case 'rentalpoints':
    require_once("rental_points.php");
    break;
  case 'subscribecomplete':
    require_once("subscribe_complete.php");
    break;
  case 'membership':
    require_once("membership.php");
    break;
  case 'contactus':
    require_once("contact_us.php");
    break;
  case 'aboutus':
    require_once("aboutus.php");
    break;
  case 'privacy':
    require_once("privacy.php");
    break;
  case 'faqs':
    require_once("faqs.php");
    break;
  case 'contact':
    require_once("contact.php");
    break;            
  case 'register':
    require_once("register.php");
    break;       
  case 'account':
    if(!$user_obj->is_logged_in()){
      echo "<div class='msg_box' style='position:relative;'>You are not logged in yet. Please log in or register below.</div><p>";
      require_once("register.php");
    }else{
      require_once("account.php");
    }
    break;
  case 'advancedsearch':
    require_once("advancedsearch.php");
    break;
  case 'search':
    require_once("searchresults.php");
    break;
    
  case 'order_review':
    if(!$user_obj->is_logged_in()){
      echo "<div class='msg_box' style='position:relative;'>You are not logged in yet. Please log in or register below.</div><p>";
      require_once("register.php");
    }else{
      require_once("order_review.php");
    }
    break;
    
  case 'cart':
    echo "<a style='color:#999999;' href='?p=rentcart'><u>switch to rental cart</u></a>";
    require_once("cart.php");
    break;
  
  case 'rentcart':
    echo "<a style='color:#999999;' href='?p=cart'><u>switch to shopping cart</u></a>";
    require_once("rentcart.php");
    break;
    
  case 'checkout1':
    if(!$user_obj->is_logged_in()){
      echo "<div class='msg_box' style='position:relative;'>You are not logged in yet. Please log in or register below.</div><p>";
      require_once("register.php");
    }else{
      require_once("delivery_method.php");
    }
    break;
  
  case 'rentcheckout';
    if(!$user_obj->is_logged_in()){
      echo "<div class='msg_box' style='position:relative;'>You are not logged in yet. Please log in or register below.</div><p>";
      require_once("register.php");
    }else{
      require_once("rentdelivery_method.php");
    }
    break;

  case 'rentreview';
    if(!$user_obj->is_logged_in()){
      echo "<div class='msg_box' style='position:relative;'>You are not logged in yet. Please log in or register below.</div><p>";
      require_once("register.php");
    }else{
      require_once("rent_review.php");
    }
    break;

  case 'delivery':
    if(!$user_obj->is_logged_in()){
      echo "<div class='msg_box' style='position:relative;'>You are not logged in yet. Please log in or register below.</div><p>";
      require_once("register.php");
    }else{
      require_once("delivery_details.php");
    }
    break;
  case 'billing':
    if(!$user_obj->is_logged_in()){
      echo "<div class='msg_box' style='position:relative;'>You are not logged in yet. Please log in or register below.</div><p>";
      require_once("register.php");
    }else{
      require_once("billing_details.php");
    }
    break;
?>
<?php
  default;
    if($perm_notices = $misc_obj->getContent("Notice",-1)){
       foreach($perm_notices AS $perm_notice){
?>
<div class="box_big" style="position:relative;text-align:left;">
<div style="text-align:left;padding:0px 5px 0px 10px;background-color:#DBE8F0;border:2px solid #AAAAAA;font-family:Verdana, Arial, sans-serif;font-size:12px;line-height:1.5;">
<p>
<?php 
echo "<b>".$perm_notice[$misc_obj->tbFields['title']]."</b><p>";
echo $perm_notice[$misc_obj->tbFields['content']];?>
</div>
</div><p>
<?php
      }   
    }
?>
<?php       
    if($announcements = $misc_obj->getContent("Announcements",-1)){
      foreach($announcements AS $announcement){
?>
<div class="box_big" style="position:relative;text-align:left;">
<table width=100% cellpadding=0 cellspacing=0 style="table-layout: fixed;"><tr height="25px"><td background="img/subhead_01.gif" width="6px"></td><td background="img/subhead_02.gif" width="200px"><b><?php echo $announcement[$misc_obj->tbFields['title']];?></b></td><td background="img/subhead_03.gif" width="32px">&nbsp;</td><td background="img/subhead_04.gif"></td></tr></table><p>
<div style="text-align:left;padding:10px 10px 20px 15px;">
<?php echo $announcement[$misc_obj->tbFields['content']];?>
</div>
</div>
<?php
      }
    }
break;
}
?>
<p>
<div class="box_big" style="position:relative;">
<table width=100% cellpadding=0 cellspacing=0 style="table-layout: fixed;"><tr height="25px"><td background="img/subhead_01.gif" width="6px"></td><td background="img/subhead_02.gif" width="100px"><b><?php
if($view_flag == "Rent") echo "Best Rents";
else echo "Best Sellers";?></b></td><td background="img/subhead_03.gif" width="32px">&nbsp;</td><td background="img/subhead_04.gif"></td></tr></table>
<p>
<?php
    $best_num_tmp = 5;
    $best_objs = $store_obj->getBestSellers($best_num_tmp,$view_flag);            
    echo "<table class='plain1' width=99% style='padding:2px 2px 2px 2px;'><tr>";
    $w_tmp = floor(100/sizeof($best_objs)) - 1;
    for($i=0; $i<sizeof($best_objs);$i++){
      echo "<td width=".$w_tmp."%>";      
      $store_obj->mediumView($best_objs[$i]["ID"], $view_flag);
      echo "</td>";      
    }
    echo "</tr></table>";

?>
<p>
<div class="box_big" style="position:relative;">
<table width=100% cellpadding=0 cellspacing=0 style="table-layout: fixed;"><tr height="25px"><td background="img/subhead_01.gif" width="6px"></td><td background="img/subhead_02.gif" width="100px"><b>New</b></td><td background="img/subhead_03.gif" width="32px">&nbsp;</td><td background="img/subhead_04.gif"></td></tr></table>
<p>
<?php
    $new_objs = $store_obj->getNew(30, 9, $view_flag);            
    echo "<table class='plain1' width=99% style='padding:2px 2px 2px 2px;'>";
    for($i=0; $i<9;$i++){
      if($i == 0){ echo "<tr>"; }
      else if($i % 3 == 0){ echo "</tr><tr>";}
      echo "<td width=33%>";
      if($i < (sizeof($new_objs)) && $i<9){
        $store_obj->smallView($new_objs[$i]["ID"], $view_flag);
      }else{
        echo "&nbsp;";
      }
      echo "</td>";      
    }
    echo "</tr></table>";

?>
<p>
</div>
<p><div class="box_big" style="position:relative;text-align:center;">
<?php
if($store_obj->getRandom(2,1) == 1){
?>
<a href='?p=search&t=detailed&p_id=801'><img src="banners/Giza Banner.gif"></a>
<?php }else{?>
<a href='?p=search&t=detailed&p_id=800'><img src="banners/Dividends Banner.gif"></a>
<?php } ?>
<p>
<a href="http://www.emall.sg" target="_blank" title="Online Shopping Singapore"><img alt="Online Shopping Singapore" src="http://www.emall.sg/images/linkbanner468x50.gif" border="0"></a>
</div><p>
</td>



</tr><table>
</td></tr>
</table>
</div>
</body>
</html>