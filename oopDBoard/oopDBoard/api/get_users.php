<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

require_once "../db.php";
require_once "../user.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "No user session found."], JSON_UNESCAPED_UNICODE);
    exit;
}

$db = (new Database())->connect();
$userModel = new User($db);
$result = $userModel->getAllUsers();

http_response_code(200);
echo json_encode(["status" => "success", "data" => $result], JSON_UNESCAPED_UNICODE);
exit;
?>