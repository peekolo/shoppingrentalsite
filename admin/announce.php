<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 

$edit_fields  = 0;

if ( !function_exists('htmlspecialchars_decode') )
{
    function htmlspecialchars_decode($text)
    {
        return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
    }
}

if(isset($_POST["operation"])){
  switch($_POST["operation"]){
    case "previewadmincontent":
      if(trim($_POST["title"]) == "" || trim($_POST["contents"]) == "" || trim($_POST["type"]) == ""){
          echo "<b>Fields cannot be empty</b><p>";
          $edit_fields = 1;
      }else{
?>
<table class="admintable1">
<tr><td>
<?php 
echo "Type: <i>".$_POST['type']."</i><p>";
echo "<b>".trim($_POST["title"])."</b><p>&nbsp<p>";
if(get_magic_quotes_gpc()){
echo nl2br(stripslashes(trim($_POST["contents"])));
}else{
echo nl2br(trim($_POST["contents"]));
}
?>
<form action="?p=announce" method="POST">
<input type="hidden" name="operation" value="editadmincontent">
<input type="hidden" name="type" value="<?php echo trim($_POST["type"]);?>">
<input type="hidden" name="title" value="<?php echo trim($_POST["title"]);?>">
<textarea name="contents" COLS=5 ROWS=1 style="visibility:hidden;">
<?php if(isset($_POST["contents"])){
if(get_magic_quotes_gpc()){
echo htmlspecialchars_decode(stripslashes(trim($_POST["contents"])));
}else{
echo htmlspecialchars_decode(trim($_POST["contents"]));
}
}?>
</textarea><p>
<input type="submit" name="edit" value="Edit Post"><input type="submit" name="publish" value="Publish Post"><input type="checkbox" name="newsletter"> Mail as Newsletter
</form>
</td></tr>
</table>
<?php
      }
      break;
    case "addadmincontent"://not used?!

      break;
      
    case "editadmincontent":  //edit n add posts
     if(isset($_POST["publish"])){
      if(trim($_POST["title"]) == "" || trim($_POST["contents"]) == "" || trim($_POST["type"]) == ""){
          echo "<b>Fields cannot be empty</b><p>";
          $edit_fields = 1;
      }else{
        if($id_tmp = $admin_obj->addEntry($_POST["title"],nl2br(stripslashes(trim($_POST["contents"]))),$_POST["type"])){
          echo "<b>Published Successfully!</b><p>";
        
          if(isset($_POST["newsletter"])){
            require_once("../php_shop/customer_class.php");
            $cust_obj = new customerAccess();
            if($cust_obj->emailNewsletter($id_tmp)){
              echo "<b>Newsletter Sent</b><p>";
            }else{
              echo "<b>Sending Failed</b><p>";
            }
          }
        }
       }
      
     }else if(isset($_POST["edit"])){ //edit
        
      $data_tmp = array(
        "title" => $_POST["title"],
        "type" => $_POST["type"],
        "content" => $_POST["contents"]
      )
?>
<p>
<form action="?p=announce" method="POST">
<input type="hidden" name="operation" value="previewadmincontent">
<table class="admintable1">
<tr><td>
Title&nbsp;<input type="text" name="title" value="<?php echo $data_tmp["title"];?>">&nbsp;&nbsp;
Type&nbsp;<select name="type" style="width:200px;">
<option value="Announcements" <?php if(isset($_POST["type"]) && $_POST["type"]=="Announcements") echo "selected";?>>Announcements</option>
<option value="Notice" <?php if(isset($_POST["type"]) && $_POST["type"]=="Notice") echo "selected";?>>Notice</option>
</select><p>
<textarea name="contents" COLS=80 ROWS=20>
<?php 
if(get_magic_quotes_gpc()){
echo stripslashes(trim($_POST["contents"]));
}else{
echo trim($_POST["contents"]);
}
?>
</textarea>
<p>
<input type="submit" value="Preview">
</td></tr></table>
</form>
<?php    
      }
      default;
      break;
  }
}else{  //beginning
  $edit_fields = 1;
}

if($edit_fields == 1){
?>
<p>
<form action="?p=announce" method="POST">
<input type="hidden" name="operation" value="previewadmincontent">
<table class="admintable1">
<tr><td>
Title&nbsp;<input type="text" name="title" value="<?php if(isset($_POST["title"])) echo trim($_POST["title"]);?>">&nbsp;&nbsp;
Type&nbsp;<select name="type" style="width:200px;">
<option value="Announcements" <?php if(isset($_POST["type"]) && $_POST["type"]=="Announcements") echo "selected";?>>Announcements</option>
<option value="Notice" <?php if(isset($_POST["type"]) && $_POST["type"]=="Notice") echo "selected";?>>Notice</option>
</select><p>
<textarea name="contents" COLS=80 ROWS=20>
<?php if(isset($_POST["contents"])){
if(get_magic_quotes_gpc()){
echo stripslashes(trim($_POST["contents"]));
}else{
echo trim($_POST["contents"]);
}
}?>
</textarea>
<p>
<input type="submit" value="Preview">
</td></tr></table>
</form>
<?php
}
?>