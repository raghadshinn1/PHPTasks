
<?php
require_once "oop.php";

$db = new DB_con();

// Get user ID from URL
$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id) {
    exit("User ID is required.");
}

// Get user data
$Result = $db->fetchonerecord($id);

if (!$Result) {
    exit("User not found.");
}

$error = "";

$name = $Result['name'];
$email = $Result['email'];

// Update data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name == "" || $email == "" || $password == "") {
        $error = "Please fill in all fields.";
    } else {

        // Hash the password
      

        // Call the update method from oop.php
        $db->update($name, $email, $password, $id);

        header("Location: index.php");
        exit;
    }
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

    <?php if ($error != ""): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form action="update.php" method="POST">

        <input type="hidden" name="id" value="<?= (int)$id ?>">

        <label>Name:</label>
        <input
            type="text"
            name="name"
            value="<?= htmlspecialchars($name) ?>"
            required
        >

        <br><br>

        <label>Email:</label>
        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($email) ?>"
            required
        >

        <br><br>

        <label>New Password:</label>
        <input
            type="password"
            name="password"
            required
        >

        <br><br>

        <button type="submit">Update User</button>

        <a href="index.php">Cancel</a>

    </form>

</body>
</html>
