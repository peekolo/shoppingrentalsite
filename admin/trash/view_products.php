<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN""http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="Author" content="peekolo">
<title>Admin Page</title>
<style type="text/css">
<!--body {	margin-left: 0px;	margin-top: 0px;	margin-right: 0px;	margin-bottom: 0px;}--></style>
<link href="css.css" rel="stylesheet" type="text/css">
</head>
<body>

<table class="mainframe">
<tr class="topbar"><td>Admin Page</td></tr>
<tr><td class="main">

<table class="pane1" height=100%>
<tr>
<td class="linkpane">
<a href="index.php">Populate/Get Database</a><br>
<a href="view_products.php">View Products</a><br>
<a href="view_customers.php">View Customers</a><br>
<a href="view_transactions.php">View Transactions</a><br>
</td>
<td class="data">
<?php
require_once('product.php');
?>
</td>
</tr>
</table>
</td></tr>
<tr class="bottombar"><td>&nbsp;</td></tr>
</table>

</body>
</html>