<?php 
require_once("php_shop/includes/configure.php");
//$tables[] = "thangka";
$tables[] = "product";
$q=strtolower($_GET["q"]);
//$hints store the strings of hints up to 10
$hints;
//tracks how many hints returned for current query
$count;
//numer of significant words of description to display
$num = 2;
//min length of significant words
$minlen = 4;

$con = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD);
if(!$con){
	die('could not connect: ' . mysql_error());
}
mysql_select_db(DB_DATABASE, $con); 
/*
$con = mysql_connect("localhost:3306","root","root");
if(!$con){
	die('could not connect: ' . mysql_error());
}
mysql_select_db("dictionary", $con);
*/
if (strlen($q) > 0){
	$words = str_word_count($q);
	$count = 0;
	$sql = "select * from `".$tables[0]."` where `STATUS` <> 0 AND `TITLE` like \"".$q."%\" order by `TITLE`;";
	$result = mysql_query($sql, $con);
	while($count<10 && $row = mysql_fetch_array($result)){
		$currstr = strtolower(trim($row['TITLE']));
		$flag = 0;
		for($i=0;$i<$count;$i++){
			if(strcmp($hints[$i], $currstr)==0){
				$flag = 1;
			}
		}
		if($flag==0){
			$hints[$count] = $currstr;
			$count ++;
		}
	}
	$sql = "select * from `".$tables[0]."` where `STATUS` <> 0 AND `TITLE` like \"%".strtok($q, " ")."%\"";
	for($i=1;$i<$words;$i++){
		$sql = $sql." and ".
			"`TITLE` like \"%".strtok(" ")."%\"";
	}$sql = $sql." order by `TITLE`;";
	$result = mysql_query($sql, $con);
	while($count<10 && $row = mysql_fetch_array($result)){
		$currstr = strtolower(trim($row['TITLE']));
		$flag = 0;
		for($i=0;$i<$count;$i++){
			if(strcmp($hints[$i], $currstr)==0){
				$flag = 1;
			}
		}
		if($flag==0){
			$hints[$count] = $currstr;
			$count ++;
		}
	}
	$sql = "select * from `".$tables[0]."` where `STATUS` <> 0 AND `DESCRIPTION` like \"".$q."%\" order by `DESCRIPTION`;";
	$result = mysql_query($sql, $con);
	while($count<10 && $row = mysql_fetch_array($result)){
		$token = strtok($row['DESCRIPTION'], " ");
		$currstr = strtolower(trim($token));
		$tmpcnt = 1;
		$token = strtok(" ");
		while($token != false && $tmpcnt<$num){
			$word = $token;	
			$currstr = $currstr." ".strtolower(trim($word));	
			if(strlen($word)>=$minlen){				
				$tmpcnt ++;
			}
			$token = strtok(" ");
		}
		$currstr = trim($currstr);
		$flag = 0;
		for($i=0;$i<$count;$i++){
			if(strcmp($hints[$i], $currstr)==0){
				$flag = 1;
			}
		}
		if($flag==0){
			$hints[$count] = $currstr;
			$count ++;
		}
	}	
	$sql = "select * from ".$tables[0]." where `STATUS` <> 0 AND 'DESCRIPTION' like \"%".$q."%\" order by 'DESCRIPTION';";
	$result = mysql_query($sql);
	while($count<10 && $row = mysql_fetch_array($result)){
		//find the token which matches our search string first!
		$tokens = explode(" ", strtolower(trim($row['DESCRIPTION'])));
		$start = -1;
		for($i=count($tokens);$i--;){
			if(stripos($tokens[$i],$q)!=false || strcmp(substr($tokens[$i], 0, strlen($q)), $q)==0){
				$start = $i;
			}
		}
		$currstr = "";
		for($i=$start;$i<count($tokens);$i++){
			$currstr = $currstr." ".$tokens[$i];
		}		
		$token = strtok($currstr, " ");
		$currstr = strtolower(trim($token));
		$tmpcnt = 1;
		$token = strtok(" ");
		while($token != false && $tmpcnt<$num){
			$word = $token;
			$currstr = $currstr." ".strtolower(trim($word));
			if(strlen($word)>=$minlen){
				$tmpcnt ++;
			}
			$token = strtok(" ");
		}
		$currstr = trim($currstr);
		$flag = 0;
		for($i=0;$i<$count;$i++){
			if(strcmp($hints[$i], $currstr)==0){
				$flag = 1;
			}
		}
		if($flag==0){
			$hints[$count] = $currstr;
			$count ++;
		}
	}
	$hint="";
	for($i=0;$i<$count;$i++){
		if ($hint==""){
			$hint=$hints[$i];			
		}
		else{
			$hint=$hint."<br />".$hints[$i];			
		}
	}
}

//Set output to "no suggestion" if no hint were found
//or to the correct values
if ($hint == ""){
	$response="";
}
else{
	$response=$hint;
}
//output the response
echo $response;
?>