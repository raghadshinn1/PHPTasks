<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location:login.php");
    exit();
}
require_once 'db.php';

$product_error = '';
$product_success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $product_name = trim($_POST['product_name']);
    $description = trim($_POST['description']); 
    $price = trim($_POST['price']);
    $quantity = trim($_POST['quantity']);
    $created_date = date('Y-m-d');

    if (empty($product_name) || empty($description) || empty($price) || empty($quantity)) {
        $product_error = "Please fill in all product fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, quantity, created_date) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$product_name, $description, $price, $quantity, $created_date])) {
            $product_success = "Product added successfully!";
        } else {
            $product_error = "Error adding product.";
        }
    }
}

$user_count = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$product_count = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$users = $conn->query("SELECT id, username, email FROM users")->fetchAll(PDO::FETCH_ASSOC);
$products = $conn->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin dashboard</title>
    <style>
        body { font-family: Tahoma, sans-serif; background: #f4f4f9; margin: 0; padding: 20px; }
        .container { max-width: 1100px; margin: auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        .stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-box { background: #007bff; color: white; padding: 15px 25px; border-radius: 6px; flex: 1; text-align: center; }
        .stat-box h3 { margin: 0 0 10px 0; font-size: 18px; }
        .stat-box p { margin: 0; font-size: 24px; font-weight: bold; }
        section { margin-bottom: 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #343a40; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .form-box { background: #f8f9fa; padding: 15px; border: 1px solid #ddd; border-radius: 6px; margin-top: 15px; }
        input, textarea { width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { padding: 8px 15px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #218838; }
        .logout-btn { background: #dc3545; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; }
        .logout-btn:hover { background: #c82333; }
        .btn-edit { background: #ffc107; color: black; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 13px; }
        .btn-delete { background: #dc3545; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 13px; }
        .error { color: red; font-size: 14px; }
        .success { color: green; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> (Admin Dashboard)</h2>
            <a href="logout.php" class="logout-btn">Log Out</a>
        </div>
        <div class="stats">
            <div class="stat-box">
                <h3>Total Users</h3>
                <p><?php echo $user_count; ?></p>
            </div>
            <div class="stat-box" style="background: #28a745;">
                <h3>Total Products</h3>
                <p><?php echo $product_count; ?></p>
            </div>

        </div>
        <section id="users">
            <h3>Registered Users</h3>
            <table>
                <thead>
                    <tr>
                        <th>id</th>
                        <th>username</th>
                        <th>Email</th>
                        <th>actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(users as $u) 
                    <tr>
                <td> <?php </td>foreach
                <td></td>
                <td></td>
                <td></td>
                </tr>
                </tbody>
            </table>
        </section>
    </div>

</body>
</html>