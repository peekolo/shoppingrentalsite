<?php

require_once("../php_shop/includes/configure.php");
require_once("../php_shop/admin_class.php");

$admin_obj = new adminAccess();

$admin_obj->addUser("admin01eee", "1234567");

echo "Yes";

?>
