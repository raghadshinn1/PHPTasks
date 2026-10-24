<?php
require_once "db.php";
require_once "user.php"; 

$db = (new Database())->connect();
$productModel = new Product($db);
$categoryModel = new category($db);
$categories = $categoryModel->getAllCategories();
$errors = [];
$editProduct = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    if (isset($_POST["delete_product"])) {
        $id = filter_var($_POST["id"] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            exit("Invalid product ID.");
        }

        $product = $productModel->getProductById($id);
        if ($product && !empty($product['image']) && file_exists($product['image'])) {
            unlink($product['image']); 
        }

        $productModel->deleteProduct($id);
        header("Location: products.php");
        exit();
    }

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $categoryId = filter_var($_POST["category_id"] ?? null, FILTER_VALIDATE_INT);

    if ($name === "") {
        $errors[] = "Product name is required.";
    }
    if ($price === "" || !is_numeric($price) || (float) $price < 0) {
        $errors[] = "Enter a valid price greater than or equal to zero.";
    }
    if ($categoryId === false || $categoryId < 1 || !$categoryModel->getCategoryById($categoryId)) {
        $errors[] = "Select a valid category.";
    }

    if (isset($_POST["update_product"])) {
        $id = filter_var($_POST["id"] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            $errors[] = "Invalid product ID.";
        } else {
            $editProduct = $productModel->getProductById($id);
            if (!$editProduct) {
                $errors[] = "Product not found.";
            }
        }
    }

    $imagePath = $editProduct['image'] ?? ""; 

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        
        if (in_array($_FILES['image']['type'], $allowedTypes)) {
            $uploadDir = "uploads/";
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $imageName = time() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $imageName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                if ($editProduct && !empty($editProduct['image']) && file_exists($editProduct['image'])) {
                    unlink($editProduct['image']);
                }
                $imagePath = $targetPath; 
            } else {
                $errors[] = "Failed to upload image file.";
            }
        } else {
            $errors[] = "Invalid image type. Only JPG, PNG, and WEBP are allowed.";
        }
    }

    if (empty($errors)) {
        if (isset($_POST["update_product"])) {
            $productModel->updateProduct($id, $name, $description, $price, $imagePath, $categoryId);
        } else {
            $productModel->create($name, $description, $price, $imagePath, $categoryId);
        }

        header("Location: products.php");
        exit();
    }
} elseif (isset($_GET["edit"])) {
    $id = filter_var($_GET["edit"], FILTER_VALIDATE_INT);
    if ($id === false || $id < 1) {
        header("Location: products.php");
        exit();
    }

    $editProduct = $productModel->getProductById($id);
    if (!$editProduct) {
        header("Location: products.php");
        exit();
    }
}

$products = $productModel->getAllProducts();
$formName = $_POST["name"] ?? ($editProduct["name"] ?? "");
$formDescription = $_POST["description"] ?? ($editProduct["description"] ?? "");
$formPrice = $_POST["price"] ?? ($editProduct["price"] ?? "");
$formCategoryId = $_POST["category_id"] ?? ($editProduct["category_id"] ?? "");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management & Image Upload</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f4f7f6; color: #333; }
        .container { max-width: 1100px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { border-bottom: 2px solid #eee; padding-bottom: 8px; }
        form.product-form { margin-bottom: 30px; background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button, .action-btn { border: 0; border-radius: 3px; color: white; cursor: pointer; padding: 7px 10px; text-decoration: none; font-size: 14px; }
        .submit-btn { background: #4caf50; padding: 10px 15px; }
        .edit-btn { background: #f0ad4e; }
        .delete-btn { background: #d9534f; }
        .cancel-btn { margin-left: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; vertical-align: middle; }
        th { background: #f2f2f2; }
        .inline-form { display: inline; }
        .errors { color: #a94442; list-style-type: none; padding: 0; background: #fdd; padding: 10px; border-radius: 4px; }
        .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc; }
    </style>
</head>
<body>
<div class="container">
    <h2><?= $editProduct ? "Update Product" : "Add New Product" ?></h2>

    <?php if ($errors): ?>
        <ul class="errors">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form class="product-form" action="products.php" method="POST" enctype="multipart/form-data">
        <?php if ($editProduct): ?>
            <input type="hidden" name="id" value="<?= (int) $editProduct["id"] ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="name">Product Name:</label>
            <input type="text" id="name" name="name" maxlength="150" value="<?= htmlspecialchars($formName, ENT_QUOTES, "UTF-8") ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="3"><?= htmlspecialchars($formDescription, ENT_QUOTES, "UTF-8") ?></textarea>
        </div>

        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" min="0" step="0.01" value="<?= htmlspecialchars((string) $formPrice, ENT_QUOTES, "UTF-8") ?>" required>
        </div>

        <div class="form-group">
            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id" required>
                <option value="">Select a category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category["id"] ?>" <?= (string) $formCategoryId === (string) $category["id"] ? "selected" : "" ?>>
                        <?= htmlspecialchars($category["name"], ENT_QUOTES, "UTF-8") ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="image">Product Image:</label>
            <?php if (!empty($editProduct["image"])): ?>
                <div style="margin-bottom: 8px;">
                    <img src="<?= htmlspecialchars($editProduct["image"], ENT_QUOTES, "UTF-8") ?>" class="product-img" alt="Current Image">
                    <small style="display:block; color: #666;">Current image (leave empty to keep)</small>
                </div>
            <?php endif; ?>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <button class="submit-btn" type="submit" name="<?= $editProduct ? "update_product" : "add_product" ?>">
            <?= $editProduct ? "Save Changes" : "Add Product" ?>
        </button>
        <?php if ($editProduct): ?>
            <a class="action-btn delete-btn cancel-btn" href="products.php">Cancel</a>
        <?php endif; ?>
    </form>

    <h2>Products List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr><td colspan="7" style="text-align: center;">No products found.</td></tr>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= (int) $product["id"] ?></td>
                        <td>
                            <?php if (!empty($product["image"])): ?>
                                <img src="<?= htmlspecialchars($product["image"], ENT_QUOTES, "UTF-8") ?>" class="product-img" alt="Product">
                            <?php else: ?>
                                <span style="color: #999; font-size: 12px;">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($product["name"], ENT_QUOTES, "UTF-8") ?></td>
                        <td><?= htmlspecialchars($product["description"] ?? "", ENT_QUOTES, "UTF-8") ?></td>
                        <td><?= htmlspecialchars((string) $product["price"], ENT_QUOTES, "UTF-8") ?></td>
                        <td><?= htmlspecialchars($product["category_name"] ?? "", ENT_QUOTES, "UTF-8") ?></td>
                        <td>
                            <a class="action-btn edit-btn" href="products.php?edit=<?= (int) $product["id"] ?>">Edit</a>
                            <form class="inline-form" action="products.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                <input type="hidden" name="id" value="<?= (int) $product["id"] ?>">
                                <button class="delete-btn" type="submit" name="delete_product">Delete</button>
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