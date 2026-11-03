<?php
session_start();
require_once "db.php";
require_once "user.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if(!empty($email) && !empty($password)){
        $db = (new Database())->connect();
        $userModel = new User($db); 

        $user = $userModel->login($email, $password);

        if ($user){
            $_SESSION["user_id"] = $user["id"]; //validation 
            header("location: dashboard.php");  
            exit;
        } else {
            $error = "not correct";
        }
    } else {
        $error = "please write ur email and password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #0D1C42;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        form {
            background: #FCF1D0;
            padding: 30px;
            width: 320px;
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            color: #010736;
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #010736;
            color: white;
            border: none;
            cursor: pointer;
        }

        .error {
            color: #c62828;
            text-align: center;
        }
    </style>
</head>

<body>

    <form action="login.php" method="POST">

        <h2>Login</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>



        <label for="email">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            required
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            required
        >

        <button type="submit">Login</button>

    </form>

</body>
</html>