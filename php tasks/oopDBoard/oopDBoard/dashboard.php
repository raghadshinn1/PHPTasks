<?php

session_start();
require_once"db.php";
require_once"user.php";

if (!isset($_SESSION["user_id"])) {
    echo "No user session found.";
    exit;
}
$db=(new Database())->connect();
$userModel = new user($db);
$result=$userModel -> getAllUsers();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>


    <h1>Welcome to dash</h1>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Delete</th>
                <th>Update</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($result as $user) : ?>
            <tr>
                
                <td>
                    <?= htmlspecialchars($user['name'])?>
                </td>

                <td>
                    <?= htmlspecialchars($user['email'])?>
                </td>

                <td>
                  <form action="delete.php" method="POST"
                      onsubmit="return confirm('Are you sure?');">
                    <input type="hidden" name="id" value="<?= htmlspecialchars((string) $user['id']) ?>">>
                    <button type="submit">Delete</button>
                </form>
                </td>

                <td>
                    <form action="update.php" method="GET">
                    <input type="hidden" name="id" value="<?= htmlspecialchars((string) $user['id']) ?>">>
                    <button type="submit">Update</button>
                </form>
                </td>
                
            </tr>
            <?php endforeach;?>
</tbody>
    </table>

    
    
</body>
</html>