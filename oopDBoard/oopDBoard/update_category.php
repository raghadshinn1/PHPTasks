<?php
require_once "db.php";
require_once "user.php";
$database = new database();
$db = $database->connect();
$categoryModel = new category($db);


$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!filter_var($id, FILTER_VALIDATE_INT) || (int) $id < 1) {
    header("Location: categories.php");
    exit();
}

$id = (int) $id;
$category = $categoryModel ->getCategoryById($id);
if (!$category){
    header("Location: categories.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($name === "") {
        exit("Category name is required.");
    }

    $categoryModel->updateCategory($id, $name, $description);

    header("Location: categories.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Category</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background-color: #f4f7f6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h2 {
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        button {
            background-color: #f0ad4e;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            flex: 1;
        }
        button:hover {
            background-color: #ec971f;
        }
        .cancel-btn {
            background-color: #d9534f;
            text-align: center;
            line-height: 36px;
            text-decoration: none;
            color: white;
            border-radius: 4px;
            flex: 1;
        }
        .cancel-btn:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Update Category</h2>
    
    <form action="update_category.php" method="POST">
        <!-- حقل مخفي لتمرير الـ ID الخاص بالقسم -->
        <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">

        <div class="form-group">
            <label for="category_name">Category Name:</label>
            <input type="text" id="category_name" name="name" value="<?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>" maxlength="100" required>
        </div>

        <div class="form-group">
            <label for="category_description">Description:</label>
            <textarea id="category_description" name="description" rows="3"><?= htmlspecialchars($category['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="btn-group">
            <button type="submit" name="update_category">Save Changes</button>
            <a href="categories.php" class="cancel-btn">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>