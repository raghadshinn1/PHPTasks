<?php
require_once "db.php";
require_once "user.php";

$database = new database();//obj 
$db = $database ->connect();
$categoryModel = new category($db); //نمرر الكونكشن تبع الداتا بيس

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_category"])) {
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($name === "") {
        exit("Category name is required.");
    }

    $categoryModel->create($name, $description);
    header("Location: categories.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_category"])) {
    $categoryId = filter_var($_POST["id"] ?? null, FILTER_VALIDATE_INT);
    if ($categoryId === false || $categoryId < 1) {
        exit("Invalid category ID.");
    }

    $categoryModel->deleteCategory($categoryId);
    header("Location: categories.php");
    exit();
}

$categories = $categoryModel->getAllCategories();



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background-color: #f4f7f6;
            color: #333;
        }
        .container {
            max-width: 900px;
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
        form {
            margin-bottom: 30px;
            background: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
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
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .action-btn {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            color: white;
            font-size: 14px;
            margin-right: 5px;
        }
        .edit-btn {
            background-color: #f0ad4e;
        }
        .edit-btn:hover {
            background-color: #ec971f;
        }
        .delete-btn {
            background-color: #d9534f;
        }
        .delete-btn:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- نموذج إضافة قسم جديد -->
    <h2>Add New Category</h2>
    <form action="categories.php" method="POST">
        <div class="form-group">
            <label for="category_name">Category Name:</label>
            <input type="text" id="category_name" name="name" maxlength="100" required>
        </div>

        <div class="form-group">
            <label for="category_description">Description:</label>
            <textarea id="category_description" name="description" rows="3"></textarea>
        </div>

        <button type="submit" name="add_category">Add Category</button>
    </form>

    <hr>

    <!-- جدول عرض الأقسام -->
    <h2>Categories List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Description</th>
                <th>Products Count</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="5">No categories found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= (int) $category["id"] ?></td>
                        <td><?= htmlspecialchars($category["name"], ENT_QUOTES, "UTF-8") ?></td>
                        <td><?= htmlspecialchars($category["description"] ?? "", ENT_QUOTES, "UTF-8") ?></td>
                        <td>-</td>
                        <td>
                            <a href="update_category.php?id=<?= (int) $category["id"] ?>" class="action-btn edit-btn">Edit</a>
                            <form action="categories.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                <input type="hidden" name="id" value="<?= (int) $category["id"] ?>">
                                <button type="submit" name="delete_category" class="action-btn delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>