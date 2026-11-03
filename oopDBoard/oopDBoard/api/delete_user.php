<?php
session_start();
header('Content-Type : application/json; charset=UTF-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST" || $_SERVER['REQUEST_METHOD'] === "DELETE") {
    $inputData = json_decode(file_get_contents("php://input"), true);
    $userId = $inputData['id'] ?? $_POST['id'] ?? null;
    $userId = filter_var($user_id, FILTER_VALIDATE_INT);
    if (!$userId) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "invalid user id"], JSON_UNESCAPED_UNICODE);
        exit();
    }
    require_once "db.php";
    require_once "user.php";

    $db = (new Database())->connect();
    $userModel = new User($db);
    $result = $userModel->deleteUser($userId);
    if ($result) {
        echo json_encode(["status" => "success", "message" => "user deleted successfully"], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "failed to delete user"], JSON_UNESCAPED_UNICODE);

    }



}