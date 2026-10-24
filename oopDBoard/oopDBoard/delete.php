<?php
session_start();
require_once"db.php";
require_once"user.php";

if(!isset($_SESSION["user_id"])){
    exit("Unauthorized access");
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $db = (new Database())->connect();
    $userModel = new User($db);
    $userModel ->deleteuser($_POST['id']);
}
header("location: dashboard.php");
exit;
?>