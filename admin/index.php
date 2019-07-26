<?php
define('PASDNKFWIE',1);

require_once("../php_shop/includes/configure.php");
require_once("../php_shop/admin_class.php");

session_start();

$admin_obj = new adminAccess();

//$admin_obj->addUser("admin01", "1234567");

if(isset($_POST["action"])){
  if($_POST["action"] == "login"){
    if(trim($_POST["usr"]) != "" && trim($_POST["pwd"]) != ""){
      $admin_obj->login(trim($_POST["usr"]), trim($_POST["pwd"]));
    }  
  }
}else if(isset($_GET["logout"])){
  if($_GET["logout"] == "1"){        
      $admin_obj->logout();
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="keywords" content="Board Game, Card Game, Singapore">
<meta name="Author" content="pieceofcake">
<title>Admin Page</title>
<style type="text/css">
<!--body {	margin-left: 0px;	margin-top: 0px;	margin-right: 0px;	margin-bottom: 0px;}--></style>
<link href="css.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
if(!$admin_obj->is_logged_in()){
?>
<p>&nbsp;<p>&nbsp;<p>&nbsp;<p>&nbsp;<p>&nbsp;<p>
<center>
<form action="index.php" method="post">
<input type="hidden" name="action" value="login">
<table style="font-family:Verdana,Arial;color:#000000;background-color:#FFFFFF;text-align:left;font-weight:normal;border:1px solid #AAAAAA;">
<tr><td colspan=2 style="font-family:Verdana,Arial;color:#FFFFFF;background-color:#082088;padding:10px 0px 10px 0px;text-align:center;font-weight:bold;">LOGIN</td></tr>
<tr><td style="padding:15px 10px 15px 10px;">Username:</td><td style="padding:15px 10px 15px 10px;"><input type="text" name="usr"></td></tr>
<tr><td style="padding:15px 10px 15px 10px;">Password:</td><td style="padding:15px 10px 15px 10px;"><input type="password" name="pwd"></td></tr>
</table><p>
<input TYPE="SUBMIT" Value="LOGIN">
</form></center>
<p>&nbsp;<p>&nbsp;<p>&nbsp;<p>&nbsp;<p>&nbsp;<p>
<?php
}else{
?>
<table class="mainframe">
<tr class="topbar"><td><a href="index.php" id="noformat2">Admin Page</a></td></tr>
<tr><td class="main">

<table class="pane1" height=100% width=100%>
<tr>
<td width="15%" class="linkpane">
<a href="?p=admin">Admin Users</a><br>
<a href="?p=announce">Announcements/Newsletters</a><br>
<a href="?p=populate">Populate/Get Database</a><br>
<a href="?p=products">View Products</a><br>
<a href="?p=zone">Shipping Zones and Costs</a><br>
<a href="?p=customers">View Customers</a><br>
<a href="?p=transactions">View Transactions</a><br>
<a href="?p=renttransactions">View Rental Transactions</a><br>
<a href="?p=cart">View Cart</a><br>
<a href="?logout=1">Logout</a><br>
</td>
<td width="85%">
<?php
if(isset($_GET["p"])){
  switch($_GET["p"]){   
    case "announce":
      require_once('announce.php');
      break;
    case "admin":
      require_once('admin.php');
      break;
    case "products":
      require_once('product.php');
      break;
    case "customers":
      require_once('customer2.php');
      break;
    case "transactions":
      require_once('transaction.php');
      break;
    case "renttransactions":
      require_once('renttransaction.php');
      break;
    case "cart":
      require_once('view_cart.php');
      break;
    case "zone":
      require_once('zones.php');
      break;
    case "populate":
      require_once('up_down.php');
      break;
      default;
      break;              
  }
}else{
  echo "Click on the links to select a function.";
}
?>
</td>
</tr>
</table>
</td></tr>
<tr class="bottombar"><td>&nbsp;</td></tr>
</table>
<?php }
?>
</body>
</html>