<?php

require_once 'database.php';

class Account{
    public $id = '';
    public $first_name = '';
    public $last_name = '';
    public $username = '';
    public $password = '';
    public $role = '';
    public $is_staff = false;
    public $is_admin = false;
    public $is_customer = false;


    protected $db;

    function __construct(){
        $this->db = new Database();
    }

    function add(){
        $sql = "INSERT INTO account (first_name, last_name, username, password, role, is_staff, is_admin, is_customer) VALUES (:first_name, :last_name, :username, :password, :role, :is_staff, :is_admin, :is_customer);";
        $query = $this->db->connect()->prepare($sql);

        $query->bindParam(':first_name', $this->first_name);
        $query->bindParam(':last_name', $this->last_name);
        $query->bindParam(':username', $this->username);
        $hashpassword = password_hash($this->password, PASSWORD_DEFAULT);
        $query->bindParam(':password', $hashpassword);
        if ($this->role == "staff"){
           $this->is_staff = true;
        }
        elseif($this->role == "admin"){
            $this->is_admin = true;
        }else {
            $this->is_customer = true;
            $this->role= 'customer';
        }
        $query->bindParam(':role', $this->role);
        $query->bindParam(':is_staff', $this->is_staff);
        $query->bindParam(':is_admin', $this->is_admin);
        $query->bindParam(':is_customer', $this->is_customer);

        return $query->execute();
    }

    function is_strong_password($password, $first_name, $last_name) {
        if(strlen($password) < 8) {
            return false;
        }
        if(strtolower($password) == strtolower($first_name) || strtolower($password) == strtolower($last_name)) {
            return false;
        }
        if(!preg_match("#[0-9]+#", $password)) {
            return false;
        }
        if(!preg_match("#[A-Z]+#", $password)) {
            return false;
        }

        $pattern = '/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/';
        if(!preg_match("#[a-z]+#", $password)) {
            return false;
        }if(!preg_match($pattern,$password)){
            return false;
        }
        return true;
    }

 //if($password != $confirm_password){
   //     $signupErr = 'Passwords do not match';
   // }elseif($accountObj->username_exists($username)){
      //  $signupErr = 'Username already exists';
   // }elseif(!is_strong_password($password, $first_name, $last_name)){
       // $signupErr = 'Password is weak';
   // }else{
       // if($accountObj->register($username, $first_name, $last_name, $password, $role)){
          //  $_SESSION['account'] = $accountObj->fetch($username);
          //  header('location: account.php');
          //  exit;
      //  }else{
          //  $signupErr = 'Registration failed';
       // }
 //   }

 function usernameExists($username) {
    $sql = "SELECT COUNT(*) FROM account WHERE username = :username";
    $query = $this->db->connect()->prepare($sql);
    $query->bindParam(':username', $username);
    $count = $query->execute() ? $query->fetchColumn() : 0;

    return $count > 0;
}
    function usernameExist($username, $excludeID){
        $sql = "SELECT COUNT(*) FROM account WHERE username = :username";
        if ($excludeID){
            $sql .= " and id != :excludeID";
        }

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':username', $username);

        if ($excludeID){
            $query->bindParam(':excludeID', $excludeID);
        }

        $count = $query->execute() ? $query->fetchColumn() : 0;

        return $count > 0;
    }

    function login($username, $password){
        $sql = "SELECT * FROM account WHERE username = :username LIMIT 1;";
        $query = $this->db->connect()->prepare($sql);

        $query->bindParam(':username', $username);

        if($query->execute()){
            $data = $query->fetch();
            if($data && password_verify($password, $data['password'])){
                return true;
            }
        }

        return false;
    }

    function fetch($username){
        $sql = "SELECT * FROM account WHERE username = :username LIMIT 1;";
        $query = $this->db->connect()->prepare($sql);

        $query->bindParam('username', $username);
        $data = null;
        if($query->execute()){
            $data = $query->fetch();
        }

        return $data;
    }
}

// $obj = new Account();

// $obj->add();