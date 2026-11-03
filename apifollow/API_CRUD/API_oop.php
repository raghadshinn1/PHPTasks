
<?php
header("Content-Type: application/json");

class DB_con
{
    private $connect;

    public function __construct()
    {
        $this->connect = new PDO(
            "mysql:host=localhost;dbname=user_system;charset=utf8mb4",
            "root",
            ""
        );

        $this->connect->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    // Get all users
    public function getUsers()
    {
        $sql = "SELECT id, name, email FROM users";
        return $this->connect->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get one user
    public function getUser($id)
    {
        $sql = "SELECT id, name, email FROM users WHERE id = ?";
        $stmt = $this->connect->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create user
    public function createUser($data)
    {
        $sql = "INSERT INTO users (name, email, password)
                VALUES (?, ?, ?)";

        $stmt = $this->connect->prepare($sql);

        return $stmt->execute([
            $data["name"],
            $data["email"],
            password_hash($data["password"], PASSWORD_DEFAULT)
        ]);
    }

    // Update user
    public function updateUser($id, $data)
    {
        $sql = "UPDATE users
                SET name = ?, email = ?, password = ?
                WHERE id = ?";

        $stmt = $this->connect->prepare($sql);

        return $stmt->execute([
            $data["name"],
            $data["email"],
            password_hash($data["password"], PASSWORD_DEFAULT),
            $id
        ]);
    }

    // Delete user
    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->connect->prepare($sql);

        return $stmt->execute([$id]);
    }
}

try {
    $db = new DB_con();

    // Get HTTP method
    $method = $_SERVER["REQUEST_METHOD"];

    // Get ID from URL query string
    $id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
    if (isset($_GET["id"])) {
        $id = (int) $_GET["id"];
    } else {
        $id = 0;
    }
    // Read JSON data sent by the client
    $data = json_decode(file_get_contents("php://input"), true);

    switch ($method) {

        case "GET":
            if ($id > 0) {
                $user = $db->getUser($id);

                if (!$user) {
                    http_response_code(404);
                    echo json_encode(["message" => "User not found"]);
                    break;
                }

                echo json_encode($user);
            } else {
                echo json_encode($db->getUsers());
            }
            break;

        case "POST":
            if (
                empty($data["name"]) ||
                empty($data["email"]) ||
                empty($data["password"])
            ) {
                http_response_code(400);
                echo json_encode(["message" => "All fields are required"]);
                break;
            }

            $db->createUser($data);

            http_response_code(201);
            echo json_encode(["message" => "User created successfully"]);
            break;

        case "PUT":
            if (
                empty($data["name"]) ||
                empty($data["email"]) ||
                empty($data["password"])
            ) {
                http_response_code(400);
                echo json_encode(["message" => "ID and all fields are required"]);
                break;
            }

            if (!$db->getUser($id)) {
                http_response_code(404);
                echo json_encode(["message" => "User not found"]);
                break;
            }

            $db->updateUser($id, $data);

            echo json_encode(["message" => "User updated successfully"]);
            break;

        case "DELETE":
            if (!$db->getUser($id)) {
                http_response_code(404);
                echo json_encode(["message" => "User not found"]);
                break;
            }

            $db->deleteUser($id);

            echo json_encode(["message" => "User deleted successfully"]);
            break;

        default:
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed"]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database error"]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["message" => "Server error"]);
}
?>