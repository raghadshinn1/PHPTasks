<?php
require_once "db.php";
require_once "user.php";

$message ="";

if ($_SERVER["REQUEST_METHOD"] === "POST") {//request method
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"];

    if (!empty($name) && !empty($email) && strlen($password) >=8 ){
        $db = (new Database())->connect();//اعمل كونكت مع ااداتا بيس
        $userModel= new user($db); //بيعمله row جديد بتيبل يوزر 

        if ($userModel->register($name, $email, $password)){
            header("location: login.php");
            exit;
        } else{
            $message= "Faild";
        }

    }else{
        $message="fill correct";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        form {
            background: white;
            padding: 30px;
            width: 350px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }

        h2 {
            text-align: center;
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
            background: #0d1c42;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <form action="register.php" method="POST">
        <h2>Create Account</h2>
        
        <?php if (!empty($message)): ?>
            <p class="error"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <label for="name">Name</label>
        <input
            type="text"
            id="name"
            name="name"
            required
        >

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
            minlength="8"
        >

        <button type="submit">Register</button>
    </form>

</body>
</html>
