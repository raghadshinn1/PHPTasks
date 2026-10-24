<?php
require_once "db.php";
require_once "user.php";
if ($_SERVER["REQUEST_METHOD"] === "POST") 
    {
        $id = $_POST['id'];
        $database =new Database();
        $db = $database->connect();
        $categoryModel = new category($db);
        $categoryModel->deleteCategory($id);
        if (!$categoryModel ->deleteCategory($id)) {
            header("Location: categories.php");
        exit();
    }
else {
    echo "Failed to delete category.";

    }
    }
    else {
        header("Location: categories.php");
        exit();
    }

?>