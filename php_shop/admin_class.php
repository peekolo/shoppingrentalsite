<?php
/**
* PHP class - adminAccess
*
* For Login, Logout, and other administrative functions - FOR ADMIN ACCOUNTS
*
*
* Copyright: 30th July 2009, pieceofcake.com.sg
*
*
**/

class adminAccess{
  
  var $dbName = DB_DATABASE;   // name of database
  
  var $adminuserTable = "admin_users";    
  var $adminuserFields = array(     
    'id'=> 'ID', 
    'username' => 'USERNAME',  	
  	'password'  => 'PASSWORD'
  );

  var $admincontentTable = "admin_content";
  var $admincontentFields = array(
    "id"=>"ID",
    "title"=>"TITLE",
    "content"=>"CONTENT",
    "type"=>"TYPE",
    "date_added"=>"DATE_ADDED"
  );
  
  var $userID = "";                 // userID of user to be initialized
  var $dbConn;                 // database connection handler
  var $sessionUser = "username";
  var $sessionPass = "pwd";
 
  /* To be called as a constructor - setup and initialization
  **/
  function adminAccess() 
  {
    //setup database connector
    $this->dbConn = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
    if(!$this->dbConn) die(mysql_error($this->dbConn));
    mysql_select_db($this->dbName, $this->dbConn) or die(mysql_error($this->dbConn));    
    
    if(isset($_SESSION[$this->sessionUser]) && isset($_SESSION[$this->sessionPass]) && !$this->is_logged_in()){  // retrieve session if any      
      $this->login($_SESSION[$this->sessionUser],$_SESSION[$this->sessionPass]); // call login function
    }    

  }  
  
  /**
  * SQL query function. Return Result Set.
  **/
  function query($sql)    // REMEMBER TO REMOVE ERRROR DIE!!!
  {
    //echo $sql.'<p>';
    $res = mysql_query($sql, $this->dbConn) or die(mysql_error($this->dbConn));    
    return $res;
  }
  
  /**
  * Is the user logged in? Return Boolean.
  **/  
  function is_logged_in()  
  {
    return (empty($this->userID) || $this->userID == "") ? false : true;
  }
  
////
// This funstion validates a plain text password with an
// encrpyted password
  function validate_password($plain, $encrypted) {
    if ($plain != "" && $encrypted != "") {
      // split apart the hash / salt
      $stack = explode(':', $encrypted);

      if (sizeof($stack) != 2) return false;

      if (md5($stack[1] . $plain) == $stack[0]) {
        return true;
      }
    }

    return false;
  }

////
// This function makes a new password from a plaintext password. 
  function encrypt_password($plain) {
    $password = '';

    for ($i=0; $i<10; $i++) {
      $password .= $this->random2();
    }

    $salt = substr(md5($password), 0, 2);

    $password = md5($salt . $plain) . ':' . $salt;

    return $password;
  }

  /**
  * Login a user. Return Boolean - success or unsuccessful
  **/    
  function login($uname, $pwd){

    $res = $this->query("SELECT * FROM `{$this->adminuserTable}` WHERE `{$this->adminuserFields['username']}` = '".$uname."' LIMIT 1");
    
    if(!$res || mysql_num_rows($res) == 0){  // no records
      return false;
    }else{
    
      $res = mysql_fetch_array($res);
      
      if($this->validate_password($pwd,$res[$this->adminuserFields['password']])){  //check password       
        $this->userID = $this->adminuserFields['id']; // initialize user
      
        //create or renew session
        $_SESSION[$this->sessionUser] = $uname;
        $_SESSION[$this->sessionPass] = $pwd;
        
      }else{
        return false;
      }
      
    }    
    return true;             
  }
  
  /**
  * Logout a user.
  **/   
  function logout()
  {
    $this->userID = "";
    if(isset($_SESSION[$this->sessionUser]) && isset($_SESSION[$this->sessionPass])){
          unset($_SESSION[$this->sessionUser]);
          unset($_SESSION[$this->sessionPass]);          
    }
    session_destroy();    
  }
 
  
  function addUser($uname, $pwd)
  {    
    if($this->user_exist($uname)){
      return false;
    }
    
    $pwd = $this->encrypt_password($pwd);
      
    $res = $this->query("INSERT INTO `{$this->adminuserTable}` (`{$this->adminuserFields['username']}`, `{$this->adminuserFields['password']}`) VALUES ('".$uname."', '".$pwd."')");        
        
    if(mysql_affected_rows($this->dbConn) == 1){
        return true;
    }else 
      return false;
  }    
  
  function deleteUser($id)
  {
    $res = $this->query("DELETE FROM `{$this->adminuserTable}` WHERE `{$this->adminuserFields['id']}`=".$id." LIMIT 1");                
  }
  
  function getUsers()
  {
    $res = $this->query("SELECT * FROM `{$this->adminuserTable}`");
    
    if($res && mysql_num_rows($res) >0){
      return $this->SqlToArray($res);
    }else{
      return false;
    }
  }
  
  /**
  * Change Password
  **/
  function changePassword($oldpwd, $newpwd)
  {    
    $success = 0;
    
    $res = $this->query("SELECT `{$this->adminuserFields['password']}` AS `PASS` FROM `{$this->adminuserTable}` WHERE `{$this->adminuserFields['id']}` = '".$this->userID."' LIMIT 1");
    
    if($res && mysql_num_rows($res) > 0){
      $res = mysql_fetch_array($res);
      if($this->validate_password($oldpwd, $res['PASS'])){
        $newpwd = $this->encrypt_password($newpwd);
        $res2 = $this->query("UPDATE `{$this->adminuserTable}` SET `{$this->adminuserFields['password']}`= '".$newpwd."' WHERE `{$this->adminuserFields['id']}` = '".$this->userID."'");
        
        $success = mysql_affected_rows($this->dbConn);
      }    
    }
    
            
    if($success != 1){  // wrong
      return false;
    }
    
    return true;
  }
  
 
  /**
  * Check if username exist. Return Boolean.
  **/
  function user_exist($uname)
  {    
  
    $res = $this->query("SELECT * FROM `{$this->adminuserTable}` WHERE `{$this->adminuserFields['username']}` = '".$uname."'");
    
      
    if($res && mysql_num_rows($res) > 0){
      return true;
    }
    return false;       
  }
 

  //$type = Announcements, Promos
  function addEntry($title, $content, $type = "Announcements")
  {    
    $content = mysql_real_escape_string($content);
    $res = $this->query("INSERT INTO`{$this->admincontentTable}` (`{$this->admincontentFields["title"]}`, `{$this->admincontentFields["content"]}`, `{$this->admincontentFields["type"]}`, `{$this->admincontentFields["date_added"]}`) VALUES ('".$title."','".$content."','".$type."',CURDATE())");
    
    return mysql_insert_id($this->dbConn);    
  
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
  
  // Return a random value
  function random2($min = null, $max = null) {
    static $seeded;

    if (!isset($seeded)) {
      mt_srand((double)microtime()*1000000);
      $seeded = true;
    }

    if (isset($min) && isset($max)) {
      if ($min >= $max) {
        return $min;
      } else {
        return mt_rand($min, $max);
      }
    } else {
      return mt_rand();
    }
  }    
}

?>