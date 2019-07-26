<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 

if(isset($_POST["operation"])){
  switch($_POST["operation"]){
    case "addadmin":
      if(trim($_POST["username"]) != "" && trim($_POST["password"]) != "")
        if($admin_obj->addUser(trim($_POST["username"]),trim($_POST["password"])))
          echo "Admin User <i>".trim($_POST["username"])."</i> added.";
        else
          echo "Duplicate Username."; 
      break;
    case "removeadmin":
      if(trim($_POST["userid"]) != "")
        $admin_obj->deleteUser($_POST["userid"]);
      break;
    default;
    break;
  }
}

?>
<p>
<form action="?p=admin" method="POST">
<input type="hidden" name="operation" value="addadmin">
<table class="admintable1">
<tr><td>
Username&nbsp;<input type="text" name="username" value="">&nbsp;&nbsp;
Password&nbsp;<input type="password" name="password" value="">&nbsp;&nbsp;
<input type="submit" value="Create Admin User">
</td></tr></table>
</form>
<p>&nbsp;<p>
<form action="?p=admin" method="POST">
<input type="hidden" name="operation" value="removeadmin">
<b>Current Admin Users</b><p>
<select name="userid" size=5 style="width:200px;">
<?php
  if($users_tmp = $admin_obj->getUsers()){
    foreach($users_tmp AS $user_tmp){
      echo "<option value=".$user_tmp[$admin_obj->adminuserFields["id"]].">".$user_tmp[$admin_obj->adminuserFields["username"]]."</option>";
    }
  }
?>
</select>
<input type="submit" value="Remove User">
</form>
