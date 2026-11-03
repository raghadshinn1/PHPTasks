<?php
header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");

require_once "../db.php";
require_once "../user.php";

$db = (new Database())->connect();
$categoryModel = new Category($db);

$method = $_SERVER["REQUEST_METHOD"];
$inputData = json_decode(file_get_contents('php://input'), true);
$action = $_GET["action"] ?? $_POST["action"] ?? ($inputData["action"] ?? "");

if ($method === "GET") {
    $id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);
    if ($id && $id > 0) {
        $category = $categoryModel->getCategoryById($id);
        if ($category) {
            http_response_code(200);
            echo json_encode(["status" => "success", "data" => $category], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Category not found."], JSON_UNESCAPED_UNICODE);
        }
    } else {
        $categories = $categoryModel->getAllCategories();
        http_response_code(200);
        echo json_encode(["status" => "success", "data" => $categories], JSON_UNESCAPED_UNICODE);
    }
    exit();
}

if ($method === "POST") {
    if ($action === "add") {
        $name = trim($inputData["name"] ?? $_POST["name"] ?? "");
        $description = trim($inputData["description"] ?? $_POST["description"] ?? "");

        if ($name === "") {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Category name is required."], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $result = $categoryModel->create($name, $description);
        if ($result) {
            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "Category added successfully."], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to add category."], JSON_UNESCAPED_UNICODE);
        }
        exit();
    }

    if ($action === "update") {
        $id = filter_var($inputData["id"] ?? $_POST["id"] ?? null, FILTER_VALIDATE_INT);
        $name = trim($inputData["name"] ?? $_POST["name"] ?? "");
        $description = trim($inputData["description"] ?? $_POST["description"] ?? "");

        if ($id === false || $id < 1) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid category ID."], JSON_UNESCAPED_UNICODE);
            exit();
        }

        if ($name === "") {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Category name is required."], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $result = $categoryModel->updateCategory($id, $name, $description);
        if ($result) {
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Category updated successfully."], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to update category."], JSON_UNESCAPED_UNICODE);
        }
        exit();
    }

    if ($action === "delete") {
        $id = filter_var($inputData["id"] ?? $_POST["id"] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid category ID."], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $result = $categoryModel->deleteCategory($id);
        if ($result) {
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Category deleted successfully."], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to delete category."], JSON_UNESCAPED_UNICODE);
        }
        exit();
    }

    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid action."], JSON_UNESCAPED_UNICODE);
    exit();
}

http_response_code(405);
echo json_encode(["status" => "error", "message" => "Method not allowed."], JSON_UNESCAPED_UNICODE);
?>