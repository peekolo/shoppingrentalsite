<?php
/**
* PHP class - storeAccess
*
* For Database related function (Views, etc)
*
*
* Copyright: 1st May 2009, Ho Wen Yang
*
* FUNCTIONS:
*
*     1.  storeAccess()                    -- Constructor.
*     2.  query($sql)                     -- Perform SQL query using $dbConn.
*
**/

class miscAccess{
  
  var $dbName = DB_DATABASE;   // name of database
  
	var $adminTable = "admin_content";    // name of table containing product info
  var $tbFields = array(       // fields of $productTable
    'id'=>'ID',
    'title'=> 'TITLE',   	
  	'content'  => 'CONTENT',
  	'type' => 'TYPE',
  	'date_added' => 'DATE_ADDED'
  );  
  
  var $dbConn;                 // database connection handler  
 

  /**
  * To be called as a constructor - setup and initialization
  **/
  function miscAccess() 
  {
    //setup database connector
    $this->dbConn = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
    if(!$this->dbConn) die(mysql_error($this->dbConn));
    mysql_select_db($this->dbName, $this->dbConn) or die(mysql_error($this->dbConn));    
                   
  }  
  
  /**
  * SQL query function. Return Result Set.
  **/
  function query($sql)    // REMEMBER TO REMOVE ERRROR DIE!!!
  {
    $res = mysql_query($sql, $this->dbConn) or die(mysql_error($this->dbConn));
    //echo $sql.'<p>';
    return $res;
  }

  function SqlToArray($res)
  {
    $result = array();
    if(!$res && mysql_num_rows($res) == 0){
      $result = "";
    }else{
      while($row = mysql_fetch_assoc($res)){
        $result[] = $row;
      }
    }      
    return $result;
  }
  
  function getContent($type = "", $id = "")
  {
    if($type == "" && $id != "" && $id !=-1){
      $sql = "SELECT * FROM `{$this->adminTable}` WHERE `{$this->tbFields['id']}` = ".$id." LIMIT 1";     
    }else if($type != ""){
      $sql = "SELECT * FROM `{$this->adminTable}` WHERE `{$this->tbFields['type']}` = '".$type."'";     
    }else{
      return false;
    }
    
    if($id == -1){
      $sql = $sql." ORDER BY `{$this->tbFields['id']}` DESC LIMIT 1";
    }
    
    $res = $this->query($sql);
    
    if($res && mysql_num_rows($res) > 0){
      return $this->SqlToArray($res);
    }else
      return false;
  }
  
}

?>