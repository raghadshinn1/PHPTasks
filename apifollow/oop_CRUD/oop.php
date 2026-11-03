
<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dash');

class DB_con
{
    // Store the connection so every method can use it
    private $connect;

    // Database connection
    public function __construct()
    {
        try {
            $this->connect = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS
            );

            $this->connect->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $this->connect->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

        } catch (PDOException $e) {
            error_log($e->getMessage());
            die("Database connection failed.");
        }
    }

    // Insert a user
    public function insert($name, $email, $password)
    {
        $sql = "INSERT INTO users (name, email, password)
                VALUES (?, ?, ?)";

        $stmt = $this->connect->prepare($sql);

        return $stmt->execute([$name, $email, $password]);
    }

    // Read all users
    public function fetchdata()
    {
        $sql = "SELECT id, name, email FROM users";

        $stmt = $this->connect->query($sql);

        return $stmt->fetchAll();
    }

    // Read one user
    public function fetchonerecord($userid)
    {
        $sql = "SELECT id, name, email
                FROM users
                WHERE id = ?";

        $stmt = $this->connect->prepare($sql);
        $stmt->execute([$userid]);

        return $stmt->fetch();
    }

    // Update a user
    public function update($name, $email, $password, $userid)
    {
        $sql = "UPDATE users
                SET name = ?, email = ?, password = ?
                WHERE id = ?";

        $stmt = $this->connect->prepare($sql);

        return $stmt->execute([
            $name,
            $email,
            $password,
            $userid
        ]);
    }

    // Delete a user
    public function delete($rid)
    {
        $sql = "DELETE FROM users WHERE id = ?";

        $stmt = $this->connect->prepare($sql);

        return $stmt->execute([$rid]);
    }
}
try {
    header("Content-Type: application/json");
    $db = new DB_con();

    // Get HTTP method
    $method = $_SERVER["REQUEST_METHOD"];

    // Get ID from URL query string
    $id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

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
                $id <= 0 ||
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
            if ($id <= 0 || !$db->getUser($id)) {
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
