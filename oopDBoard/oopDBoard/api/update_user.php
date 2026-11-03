<?php
header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

require_once "../db.php";
require_once "../user.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $inputData = json_decode(file_get_contents('php://input'), true);

    $name = trim($inputData["name"] ?? $_POST["name"] ?? "");
    $email = trim($inputData["email"] ?? $_POST["email"] ?? "");
    $password = $inputData["password"] ?? $_POST["password"] ?? "";

    if (empty($name) || empty($email) || strlen($password) < 8) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Please fill all fields correctly (password must be at least 8 characters)."], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $db = (new Database())->connect();
    $userModel = new User($db);

    if ($userModel->register($name, $email, $password)) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Registration successful."], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Registration failed."], JSON_UNESCAPED_UNICODE);
    }
    exit();
}

http_response_code(405);
echo json_encode(["status" => "error", "message" => "Method not allowed."], JSON_UNESCAPED_UNICODE);
?>