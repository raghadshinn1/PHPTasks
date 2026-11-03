<?php
header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");

require_once "../db.php";
require_once "../user.php";

$db = (new Database())->connect();
$productModel = new Product($db);
$categoryModel = new Category($db);

$inputData = json_decode(file_get_contents('php://input'), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? $inputData["action"] ?? "";

    if (isset($_POST["delete_product"]) || $action === "delete") {
        $id = filter_var($_POST["id"] ?? $inputData["id"] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid product ID."], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $product = $productModel->getProductById($id);
        if ($product && !empty($product['image']) && file_exists("../" . $product['image'])) {
            unlink("../" . $product['image']);
        }

        $result = $productModel->deleteProduct($id);
        if ($result) {
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Product deleted successfully."], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to delete product."], JSON_UNESCAPED_UNICODE);
        }
        exit();
    }

    $id = filter_var($_POST["id"] ?? $inputData["id"] ?? null, FILTER_VALIDATE_INT);
    $isUpdate = ($id && $id > 0);
    $editProduct = null;

    if ($isUpdate) {
        $editProduct = $productModel->getProductById($id);
        if (!$editProduct) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Product not found."], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    $name = trim($_POST["name"] ?? $inputData["name"] ?? "");
    $description = trim($_POST["description"] ?? $inputData["description"] ?? "");
    $price = trim($_POST["price"] ?? $inputData["price"] ?? "");
    $categoryId = filter_var($_POST["category_id"] ?? $inputData["category_id"] ?? null, FILTER_VALIDATE_INT);

    $errors = [];
    if ($name === "") {
        $errors[] = "Product name is required.";
    }
    if ($price === "" || !is_numeric($price) || (float) $price < 0) {
        $errors[] = "Enter a valid price greater than or equal to zero.";
    }
    if ($categoryId === false || $categoryId < 1 || !$categoryModel->getCategoryById($categoryId)) {
        $errors[] = "Select a valid category.";
    }

    $imagePath = $editProduct['image'] ?? "";

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (in_array($_FILES['image']['type'], $allowedTypes)) {
            $uploadDir = "../uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $imageName = time() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $imageName;
            $dbPath = "uploads/" . $imageName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                if ($editProduct && !empty($editProduct['image']) && file_exists("../" . $editProduct['image'])) {
                    unlink("../" . $editProduct['image']);
                }
                $imagePath = $dbPath;
            } else {
                $errors[] = "Failed to upload image file.";
            }
        } else {
            $errors[] = "Invalid image type. Only JPG, PNG, and WEBP are allowed.";
        }
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "errors" => $errors], JSON_UNESCAPED_UNICODE);
        exit();
    }

    if ($isUpdate) {
        $result = $productModel->updateProduct($id, $name, $description, $price, $imagePath, $categoryId);
    } else {
        $result = $productModel->create($name, $description, $price, $imagePath, $categoryId);
    }

    if ($result) {
        http_response_code($isUpdate ? 200 : 201);
        echo json_encode(["status" => "success", "message" => $isUpdate ? "Product updated successfully." : "Product added successfully."], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database operation failed."], JSON_UNESCAPED_UNICODE);
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $editId = filter_var($_GET["edit"] ?? null, FILTER_VALIDATE_INT);
    if ($editId && $editId > 0) {
        $product = $productModel->getProductById($editId);
        if ($product) {
            http_response_code(200);
            echo json_encode(["status" => "success", "data" => $product], JSON_UNESCAPED_UNICODE);
            exit();
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Product not found."], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    $products = $productModel->getAllProducts();
    $categories = $categoryModel->getAllCategories();
    http_response_code(200);
    echo json_encode(["status" => "success", "data" => ["products" => $products, "categories" => $categories]], JSON_UNESCAPED_UNICODE);
    exit();
}

http_response_code(405);
echo json_encode(["status" => "error", "message" => "Method not allowed."], JSON_UNESCAPED_UNICODE);
?>