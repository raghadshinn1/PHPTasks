<?php
require_once "db.php";
require_once "user.php";

$Result = null;
$error = "";
$db = (new Database())->connect();
$userModel = new User($db);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = $_POST['id'] ?? null;

    if (!$id) {
        exit("User ID is required.");
    }

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name === "" || $email === "" || $password === "") {
        $error = "Name, email, and password are required.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        $userModel->updateUser($id, $name, $email, $password);

        header("Location: dashboard.php");
        exit;
    }

} else {
    $id = $_GET['id'] ?? null;

    if (!$id) {
        exit("User ID is required.");
    }
}

$Result = $userModel->getUserById($id);

if (!$Result) {
    exit("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
</head>
<body>

    <h1>Update User</h1>

    <?php if ($error !== ""): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form action="update.php" method="POST">

        <input type="hidden" name="id" value="<?= (int)$id ?>">

        <label for="name">Name:</label>
        <input
            type="text"
            id="name"
            name="name"
            value="<?= htmlspecialchars($_POST['name'] ?? $Result['name']) ?>"
            required
        >

        <br><br>

        <label for="email">Email:</label>
        <input
            type="email"
            id="email"
            name="email"
            value="<?= htmlspecialchars($_POST['email'] ?? $Result['email']) ?>"
            required
        >

        <br><br>

        <label for="password">New Password:</label>
        <input
            type="password"
            id="password"
            name="password"
            minlength="8"
            required
        >

        <br><br>

        <button type="submit">Save Changes</button>

        <a href="dashboard.php">Cancel</a>

    </form>

</body>
</html>