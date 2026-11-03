<?php
header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $inputData = json_decode(file_get_contents('php://input'), true);

    $email = trim($inputData["email"] ?? $_POST["email"] ?? "");
    $password = $inputData["password"] ?? $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Please provide both email and password."
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    require_once "../db.php";
    require_once "../user.php";

    $db = (new Database())->connect();
    $userModel = new User($db);

    $user = $userModel->login($email, $password);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];

        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Login successful.",
            "user" => [
                "id" => $user['id'],
                "email" => $user['email']
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit();
    } else {
        http_response_code(401);
        echo json_encode([
            "status" => "error",
            "message" => "Invalid email or password."
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