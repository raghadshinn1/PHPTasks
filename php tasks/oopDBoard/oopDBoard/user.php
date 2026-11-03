<?php
// الملف الوحيد الذي يتعامل مع الداتا بيس
class User {
    private $db;
    private $table = "users";
    
    public function __construct($dbConnection){
        $this->db = $dbConnection;
    }

    public function register($name, $email, $password){
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO " . $this->table . " (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashedPassword 
        ]);
    }
    
    public function login($email, $password){
        $sql = "SELECT * FROM " . $this->table . " WHERE email = :email"; 
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if($user && password_verify($password, $user['password'])){
            return $user;
        }
        return false;
    }

    public function getAllUsers(){
        $sql = "SELECT id, name, email FROM " . $this->table; 
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function deleteUser($id){
        $sql = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getUserById($id){
        $sql = "SELECT id, name, email FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function updateUser($id, $name, $email, $password) {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $sql = "UPDATE " . $this->table . " SET name = :name, email = :email, password = :password WHERE id = :id";
            $params = [
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashedPassword,
                ':id' => $id
            ];
        } else {
            $sql = "UPDATE " . $this->table . " SET name = :name, email = :email WHERE id = :id";
            $params = [
                ':name' => $name,
                ':email' => $email,
                ':id' => $id
            ];
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}

class Category {
    private $db;
    private $table = 'categories';

    public function __construct($dbConnection){
         $this->db = $dbConnection;
    }

    public function create($name, $description = ""){
         $sql = "INSERT INTO " . $this->table . " (name, description) VALUES (:name, :description)";
         $stmt = $this->db->prepare($sql);
         return $stmt->execute([':name' => $name, ':description' => $description]);
    }

    public function getAllCategories(){
        $sql = "SELECT * FROM " . $this->table;
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getCategoryById($id){
        $sql = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function updateCategory($id, $name, $description = ""){
        $sql = "UPDATE " . $this->table . " SET name = :name, description = :description WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':name' => $name, ':description' => $description, ':id' => $id]);
    }

    public function deleteCategory($id){
        $sql = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}

class Product {
    private $db;
    private $table = "products";

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // إضافة منتج مع مسار الصورة
    public function create($name, $description, $price, $image, $categoryId) {
        $sql = "INSERT INTO " . $this->table . " (name, description, price, image, category_id)
                VALUES (:name, :description, :price, :image, :category_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':image' => $image,
            ':category_id' => $categoryId
        ]);
    }

    // جلب جميع المنتجات مع اسم التصنيف والصورة
    public function getAllProducts() {
        $sql = "SELECT p.id, p.name, p.description, p.price, p.image, p.category_id, c.name AS category_name
                FROM " . $this->table . " p
                LEFT JOIN categories c ON c.id = p.category_id
                ORDER BY p.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    // جلب منتج بواسطة الـ ID مع الصورة
    public function getProductById($id) {
        $sql = "SELECT id, name, description, price, image, category_id
                FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // تحديث المنتج مع إمكانية تحديث الصورة
    public function updateProduct($id, $name, $description, $price, $image, $categoryId) {
        $sql = "UPDATE " . $this->table . "
                SET name = :name, description = :description, price = :price, image = :image, category_id = :category_id
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':image' => $image,
            ':category_id' => $categoryId,
            ':id' => $id
        ]);
    }

    public function deleteProduct($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
?>