<?php

function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

function constructURL($keys, $vals, $viewby = -1, $startnum = -1, $pagestart = -1, $alpha = "", $orderby = "", $ordertype = ""){
  $i = 0;
  $url_tmp = "";
  $start_set = 0;
  $view_set = 0;
  $page_set = 0;
  $alpha_set = 0;
  $orderby_set = 0;
  $ordertype_set = 0;
  
  foreach($keys as $keys_single){
    if($i == 0){
      $url_tmp = "?";
    }else{
      $url_tmp = $url_tmp."&";
    }
    if($keys_single == "startnum"){     
      if($startnum != -1) $vals[$i] = $startnum;       
      $start_set=1;
    }else if($keys_single == "viewby"){ 
      if($viewby != -1) $vals[$i] = $viewby; 
      $view_set=1;
    }else if($keys_single == "pagestart"){ 
      if($pagestart != -1) $vals[$i] = $pagestart; 
      $page_set=1;
    }else if($keys_single == "alpha"){
      if($alpha != "") $vals[$i] = $alpha;
      $alpha_set=1;
    }else if($keys_single == "orderby"){
      if($orderby != "") $vals[$i] = $orderby;
      $orderby_set=1;
    }else if($keys_single == "ordertype"){
      if($ordertype != "") $vals[$i] = $ordertype;
      $ordertype_set=1;
    }
    
    $val_tmp=urlencode($vals[$i]);
    //echo $val_tmp;
    $url_tmp = $url_tmp.$keys_single."=".$val_tmp;
    //echo urlencode($vals[$i])." ".$vals[$i];
     $i++;
  }
  
  if($start_set == 0 && $startnum != -1) $url_tmp = $url_tmp."&startnum=".$startnum;
  if($view_set == 0 && $viewby != -1) $url_tmp = $url_tmp."&viewby=".$viewby;
  if($page_set == 0 && $pagestart != -1) $url_tmp = $url_tmp."&pagestart=".$pagestart;
  if($alpha_set == 0 && $alpha != "") $url_tmp = $url_tmp."&alpha=".$alpha;
  if($orderby_set == 0 && $orderby != "") $url_tmp = $url_tmp."&orderby=".$orderby;
  if($ordertype_set == 0 && $ordertype != "") $url_tmp = $url_tmp."&ordertype=".$ordertype;
  //echo $url_tmp;
  return $url_tmp;
}


?>