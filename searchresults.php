<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 
?>
<div class="box_big" style="position:relative;">
&nbsp;<p>
<?php
$search_type = "";
if(isset($_GET["t"])){
  $search_type = $_GET["t"];
}

require_once("php_shop/store_class.php");
require_once("php_shop/common_func.php");
$store_obj = new storeAccess();
$res = "";

switch($search_type){
  case "advanced":
  
  case "basic":
    default;    
    if(!empty($_GET['viewby'])){
      $viewby = $_REQUEST['viewby'];
    }else{
      $viewby = 10;
    }
    if(!empty($_GET['pagestart'])){
      $pagestart = $_REQUEST['pagestart'];
    }else{
      $pagestart= 0;
    }
    if(!empty($_GET['startnum'])){
      $startnum= $_REQUEST['startnum'];
    }else{
      $startnum = 0;
    } 
    if(!empty($_GET['alpha'])){
      $alpha= $_REQUEST['alpha'];
    }else{
      $alpha = "";
    }                   
    if(isset($_GET['orderby'])){
      $orderby = $_GET['orderby'];
    }else{
      $orderby = "";
    }
    if(isset($_GET['ordertype'])){
      $ordertype = $_GET['ordertype'];
    }else{
      $ordertype = "";
    }
    
    if(isset($_GET["searchfield"])){
      $res = $store_obj->searchDB2($_GET["searchfield"],"" ,"" ,$orderby,array_keys($_GET), array_values($_GET),$startnum,$viewby,$alpha,$ordertype,$view_flag);
      $total_num = $store_obj->count_rows($_GET["searchfield"],"",array_keys($_GET), array_values($_GET),$alpha,$view_flag);
      $searchfield = $_GET["searchfield"];
    }else{
      $searchfield = '';
    }      
    
    if($res == ""){
      echo "No Direct matches with our titles. Here are possible items that you might be looking for!";
      $res = $store_obj->searchDB2($_GET["searchfield"], $store_obj->tbFields["description"] ,"" ,$orderby,array_keys($_GET), array_values($_GET),$startnum,$viewby,$alpha,$ordertype,$view_flag);
    }
?>
    <h1>Results</h1>
<?php    
    
    if($res == ""){
      echo "No results found. Please try another search term.<p>&nbsp;<p>&nbsp;<p>&nbsp;<p>&nbsp;<p>&nbsp;<p>&nbsp;<p>&nbsp;<p>";
    }else{      
?>
 <table width=95% class='plain1'><tr><td align="left">Showing <?php echo ($startnum+1)."-".($startnum+mysql_num_rows($res));?> out of <?php echo $total_num;?> results.<br>
 <?php
 foreach(range('a', 'z') as $letter) {
    echo "<a href='".constructURL(array_keys($_GET),array_values($_GET),$viewby ,0,0,$letter)."'>".$letter."</a>";
    if($letter != 'z') echo " | ";
}
 ?>
 </td><td align="right">
    (View by <a href="<?php echo constructURL(array_keys($_GET), array_values($_GET),5,0,0);?>">5</a>, <a href="<?php echo constructURL(array_keys($_GET), array_values($_GET),10,0,0);?>">10</a>, <a href="<?php echo constructURL(array_keys($_GET), array_values($_GET),20,0,0);?>">20</a>, <a href="<?php echo constructURL(array_keys($_GET), array_values($_GET),50,0,0);?>">50</a>)<br>
<?php 

$limit_page = 5;
$num_pages = ceil($total_num/$viewby);
if($num_pages > $limit_page+$pagestart){
  $num_stop = $limit_page+$pagestart;  
}else{
  $num_stop = $num_pages;
}

$new_pagestart = $startnum/$viewby - 1;
$prev_pagestart = $new_pagestart - 2;
if($new_pagestart < 1) $new_pagestart = 0;
else if($new_pagestart > ($num_pages - $limit_page)) $new_pagestart = $pagestart;
if($prev_pagestart < 1) $prev_pagestart = 0;
else if($prev_pagestart > ($num_pages - $limit_page)) $prev_pagestart = $pagestart;

if($startnum/$viewby > 0){
  echo "<a href='".constructURL(array_keys($_GET),array_values($_GET), $viewby,($startnum - $viewby),$prev_pagestart)."'>< </a>&nbsp;";
}
for($i=$pagestart;$i<$num_stop;$i++){
   if($i != $pagestart) echo ',';
   if($i == $startnum/$viewby){
    echo "&nbsp;<font color='#FF0000'>".($i+1)."</font>";
   }else{
    $tmp_ps = $i - 2;
    if($tmp_ps < 1) $tmp_ps =0;
    else if($tmp_ps > ($num_pages-$limit_page)) $tmp_ps = $num_pages-$limit_page;
    $url_tmp = constructURL(array_keys($_GET), array_values($_GET),$viewby,$i*$viewby,$tmp_ps);

?>&nbsp;<a href="<?php echo $url_tmp;?>"><?php echo $i+1;?></a><?php 
}
}
if($num_pages > ($startnum/$viewby +1)){
  echo "&nbsp;<a href='".constructURL(array_keys($_GET),array_values($_GET), $viewby, ($startnum + $viewby), $new_pagestart)."'> ></a>";
}?></td></tr></table>
<?php
  if($ordertype == 'DESC'){
    $ordertype = 'ASC';
  }else{
    $ordertype = 'DESC';
  }
?>
    <table class="searchresultstable">
    <tr class="searchhead"><td>&nbsp;</td><td><?php echo "<a id='noformat2' href='".constructURL(array_keys($_GET),array_values($_GET),-1,-1,-1,'',$store_obj->tbFields['title'],$ordertype)."'>";?>Product Name</a></td><td><?php echo "<a id='noformat2' href='".constructURL(array_keys($_GET),array_values($_GET),-1,-1,-1,'',$store_obj->tbFields['price'],$ordertype)."'>";?>Price</a></td><td>Buy Now</td></tr>    
<?php  
      while($res_single=mysql_fetch_array($res)){
        echo "<tr>";
        $store_obj->searchView($res_single['ID'],$view_flag);
        echo "</tr>";
      }
    echo "</table>";
?>

<table width=95% class='plain1'><tr><td align="left">Showing <?php echo ($startnum+1)."-".($startnum+mysql_num_rows($res));?> out of <?php echo $total_num;?> results.<br>
 <?php
 foreach(range('a', 'z') as $letter) {
    echo "<a href='".constructURL(array_keys($_GET),array_values($_GET),$viewby ,0,0,$letter)."'>".$letter."</a>";
    if($letter != 'z') echo " | ";
}
 ?>
 </td><td align="right">
    (View by <a href="<?php echo constructURL(array_keys($_GET), array_values($_GET),5,0,0);?>">5</a>, <a href="<?php echo constructURL(array_keys($_GET), array_values($_GET),10,0,0);?>">10</a>, <a href="<?php echo constructURL(array_keys($_GET), array_values($_GET),20,0,0);?>">20</a>, <a href="<?php echo constructURL(array_keys($_GET), array_values($_GET),50,0,0);?>">50</a>)<br>
<?php 

$limit_page = 5;
$num_pages = ceil($total_num/$viewby);
if($num_pages > $limit_page+$pagestart){
  $num_stop = $limit_page+$pagestart;  
}else{
  $num_stop = $num_pages;
}

$new_pagestart = $startnum/$viewby - 1;
$prev_pagestart = $new_pagestart - 2;
if($new_pagestart < 1) $new_pagestart = 0;
else if($new_pagestart > ($num_pages - $limit_page)) $new_pagestart = $pagestart;
if($prev_pagestart < 1) $prev_pagestart = 0;
else if($prev_pagestart > ($num_pages - $limit_page)) $prev_pagestart = $num_pages-$limit_page;

if($startnum/$viewby > 0){
  echo "<a href='".constructURL(array_keys($_GET),array_values($_GET), $viewby,($startnum - $viewby),$prev_pagestart)."'>< </a>&nbsp;";
}
for($i=$pagestart;$i<$num_stop;$i++){
   if($i != $pagestart) echo ',';
   if($i == $startnum/$viewby){
    echo "&nbsp;<font color='#FF0000'>".($i+1)."</font>";
   }else{
    $tmp_ps = $i - 2;
    if($tmp_ps < 1) $tmp_ps =0;
    else if($tmp_ps > ($num_pages-$limit_page)) $tmp_ps = $pagestart;
    $url_tmp = constructURL(array_keys($_GET), array_values($_GET),$viewby,$i*$viewby,$tmp_ps);

?>&nbsp;<a href="<?php echo $url_tmp;?>"><?php echo $i+1;?></a><?php 
}
}
if($num_pages > ($startnum/$viewby +1)){
  echo "&nbsp;<a href='".constructURL(array_keys($_GET),array_values($_GET), $viewby, ($startnum + $viewby), $new_pagestart)."'> ></a>";
}?></td></tr></table>

<?php    
    }
    
    break;
    
  case "detailed":
    if(isset($_GET["p_id"])){
      $store_obj->detailedView($_GET["p_id"]);
    }
    break;        
}
?> 
<p>&nbsp;<p>
</div>