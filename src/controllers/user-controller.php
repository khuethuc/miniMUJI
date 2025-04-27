<?php
require_once "src/models/user-model.php";

class UserController {
    private $model;

    public function __construct() {
        $this->model = new UserModel();
    }

    // Đăng nhập admin
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents("php://input"), true);
            $admin = $this->model->login($input['username'], $input['password']);

            if ($admin) {
                $_SESSION['admin'] = $admin['username'];
                echo json_encode(['status' => 'success', 'message' => 'Đăng nhập thành công']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Sai tài khoản hoặc mật khẩu']);
            }
        }
    }

    // Lấy danh sách users
    public function getAllUsers() {
        $this->authorize();
        $users = $this->model->getAllUsers();
        echo json_encode($users);
    }

    // Duyệt bài đăng
    public function approveJob() {
        $this->authorize();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $jobId = $data['job_id'];
            $this->model->approveJob($jobId);
            echo json_encode(['status' => 'success', 'message' => 'Bài đăng đã được duyệt']);
        }
    }

    // Kiểm tra quyền admin
    private function authorize() {
        if (!isset($_SESSION['admin'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn không có quyền truy cập']);
            exit;
        }
    }

    // Handle POST request to create a company account
    public function createCompanyAccount() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['action'])) {
            $email = $_POST['email'];
            $username = explode('@', $email)[0];
            $password = $_POST['password'];
            $comp_name = $_POST['company-name'];
            $confirm = $_POST['confirm_password'];
            $phone_number = $_POST['phone'];
            $headquarter = $_POST['headquarter'];

    if ($password !== $confirm) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
        exit;
    }
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{6,}$/', $password)) {
        // if(strlen($password) < 8) {
        echo "<script>alert('Password must be at least 8 characters long and include uppercase, lowercase, and a number.');</script>";
        exit;
    }

            try {
                $conn = Database::connect();

                $stmt = $conn->prepare("INSERT INTO Users (username, password_hash, comp_name, headquarter, email, phone_number, comp_description) VALUES (:username, SHA2(:password, 256), :comp_name, :headquarter, :email, :phone_number, '')");
                $stmt->execute([
                    ':username' => $username,
                    ':password' => $password,
                    ':email' => $email,
                    ':comp_name' => $comp_name,
                    ':phone_number' => $phone_number,
                    ':headquarter' => $headquarter
                ]);

                echo "<script>
                        alert('Company account created successfully!');
                        window.location.href='../../?page=home';
                      </script>";
                exit;
            } catch (PDOException $e) {
                echo "<script>alert(\"Error: " . $e->getMessage() . "\"); </script>";
                exit;
            }
        }
    }
}

// Instantiate the controller and handle actions
$controller = new AdminController();

// Handle actions if specified in the URL
if (isset($_GET['action']) && method_exists($controller, $_GET['action'])) {
    $controller->{$_GET['action']}();
} else {
    // If no specific action is defined, call createCompanyAccount
    $controller->createCompanyAccount();
}
?>
