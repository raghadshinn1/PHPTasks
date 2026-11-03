
<?php
require_once "oop.php";

$db = new DB_con();

// Delete a user
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_id"])) {
    $id = filter_var($_POST["delete_id"], FILTER_VALIDATE_INT);

    if ($id && $id > 0) {
        $db->delete($id);
    }

    header("Location: index.php");
    exit;
}

// Fetch all users
$users = $db->fetchdata();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body>

<div class="container">
    <h2>Users Dashboard</h2>

    <table id="mytable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>

        <tbody>
        <?php
        $cnt = 1;

        foreach ($users as $row):
        ?>
            <tr>
                <td><?= $cnt++ ?></td>

                <td>
                    <?= htmlspecialchars($row['name']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['email']) ?>
                </td>

                <td>
                    <a
                        href="update.php?id=<?= (int)$row['id'] ?>"
                        class="btn btn-primary btn-xs"
                    >
                        Edit
                    </a>
                </td>

                <td>
                    <form
                        action="index.php"
                        method="POST"
                        onsubmit="return confirm('Do you really want to delete this user?');"
                    >
                        <input
                            type="hidden"
                            name="delete_id"
                            value="<?= (int)$row['id'] ?>"
                        >

                        <button type="submit" class="btn btn-danger btn-xs">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>

        <?php if (empty($users)): ?>
            <tr>
                <td colspan="5">No users found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
