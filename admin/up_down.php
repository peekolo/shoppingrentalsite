<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 

$file_ready_for_download = 0;

if(isset($_POST["operation"])){
  switch($_POST["operation"]){
    case "create":
      require_once("download_file.php");
      break;
      default;
      break;
  }
}
?>
<p>
<b>Populate Database from CSV file</b><p>
<form action="upload_file.php" method="post"
enctype="multipart/form-data">
<table class="admintable1"><tr><td>
<label for="file">Filename:</label>
<input type="file" name="file" id="file" /> 
<input type="submit" name="submit" value="Upload File" />
</td></tr></table>
</form>
<p>&nbsp;<p>&nbsp;<p>
<b>Get Database in CSV format in 2 steps (1.Create, 2.Download)</b><p>
<table class="admintable1"><tr><td>
<form action="?p=populate" method="post"
enctype="multipart/form-data">
<input type="hidden" name="operation" value="create">
<input type="submit" name="Download CSV" value="Create CSV" />
</form>&nbsp;&nbsp;
<?php
  if($file_ready_for_download == 1){
    echo '<form action="' . $ourFileName . '" method="GET"';
    echo 'enctype="multipart/form-data">';
    echo '<input type="submit" value="Download CSV" />';
    echo '</form>';
  }
?>
</td></tr></table>
