<?php
session_start();
session_start();
define('DB_SERVER','localhost');
define('DB_USER','root');
define('DB_PASS' ,'');
define('DB_NAME', 'oopscrud');
class DB_con{
    private $dbh;
    public function __construct(){
        try{
            $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . oopscrud . ";charset=utf8mb4";
            $this->dbh = new PDO($dsn, DB_USER, DB_PASS);
        }chatch(PDOException $e){
            echo "Failed to connect to MySQL: " . $e->getMessage();
        }

    }
    public function insert ($fname, $lname,$emailid, $contactno, $address ){
        $sql= INSERT INTO `tblusers`(`FirstName`, `LastName`, `EmailId`, `ContactNumber`, `Address`)
         VALUES (':fname',':lname',':emailid',':contactno',':address')
    }
}