<?php
$host = 'localhost';
$db_name = 'user_system';
$username = 'root';
$password = '';
try {
    $conn = new PDO ("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $conn -> setAttribute (PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception){
    echo "Connection error : " . $exception -> getmessage();
    exit();
}
?>
