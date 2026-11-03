<?php
session_start();
require_once 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    if (empty($user) || empty($email) || empty($pass)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Email address is already in use.";
        } else {
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

            $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($insert_stmt->execute([$user, $email, $hashed_password])) {
                $success = "Registration successful! You can <a href='login.php'>log in now</a>.";
            } else {
                $error = "An error occurred, please try again later.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        body { font-family: Tahoma, sans-serif; background: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .form-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #218838; }
        .error { color: red; font-size: 14px; margin-bottom: 10px; }
        .success { color: green; font-size: 14px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Register</h2>
        <?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <?php if(!empty($success)) echo "<div class='success'>$success</div>"; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign Up</button>
        </form>
        <p style="text-align: center; margin-top: 15px; font-size: 14px;">Already have an account? <a href="login.php">Log In</a></p>
    </div>
</body>
</html>