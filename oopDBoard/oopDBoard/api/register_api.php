<?php
header('Content-Type: application/json; charset=UTF-8');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $inputData = json_decode(file_get_contents('php://input'), true);
    
    $name = trim($inputData["name"] ?? $_POST["name"] ?? "");
    $email = trim($inputData["email"] ?? $_POST["email"] ?? "");
    $password = $inputData["password"] ?? $_POST["password"] ?? "";

    if (empty($name) || empty($email) || strlen($password) < 8) {
        http_response_code(400); // Bad Request
        echo json_encode([
            "status" => "error", 
            "message" => "Please fill in all fields correctly, and ensure the password is at least 8 characters."
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    require_once "../db.php";
    require_once "../user.php";

    $db = (new Database())->connect();
    $userModel = new User($db); 

    $result = $userModel->register($name, $email, $password);

    if ($result) {
        http_response_code(201); // Created
        echo json_encode([
            "status" => "success", 
            "message" => "User registered successfully."
        ], JSON_UNESCAPED_UNICODE);
        exit();
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            "status" => "error", 
            "message" => "Registration failed. Email might already exist."
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
} else {
    http_response_code(405);

    echo json_encode([
        "status" => "error", 
        "message" => "Method not allowed."
    ], JSON_UNESCAPED_UNICODE);
}
?>