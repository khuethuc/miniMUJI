<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT * FROM USERS WHERE email = ?");
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            if ($user && hash('sha256', $password) === $user['hash_password']) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['fullname'] = $user['fullname'];
                if ($user['role'] === 'user') {
                    echo "<script>window.location.href='/minimuji/home';</script>";
                }
                else{
                    echo "<script>window.location.href='/minimuji/dashboard';</script>";
                }
                exit;
            } 
            else {
                $errors[] = "Incorrect email or password.";
            }
        } 
        else {
            $errors[] = "Database error. Please try again.";
        }

        $stmt->close();
    }
    if (!empty($errors)){
        $msg = implode("\\n", $errors);
        echo "<script>alert('Login failed: $msg');</script>";
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/form.css">
    <link rel = "stylesheet" href = "assets/css/style.css">
</head>

<body>
    <?php include 'src/components/header.php';?>

    <main>
        <div class = 'form'>
            <h1>Login</h1>

            <p>Don't have an account? <a href="/minimuji/register" class = "text-decoration-underline text-black">Register</a></p>
            
            <form action = "#" method = "POST" id = "form">
                <!-- Email -->
                <div class = "form-group">
                    <label for = "email">Email</label>
                    <input type = "email" id = "email" name = "email" required>
                </div>
                <!-- Password -->
                <div class = "form-group">
                    <label for = "password">Password</label>
                    <input type = "password" id = "password" name = "password" required>
                </div>
                <!-- Forget password? -->
                <div class="form-group">
                    <p><a href="#" class="text-decoration-underline text-black">Forget password?</a></p> 
                </div>
                <!-- Remember me -->
                <div class="form-group checkbox-group d-flex justify-content-center align-items-center">
                    <input type="checkbox" id="terms" name="terms" class="d-inline">
                    <label for="terms" class="d-inline px-2">
                        Remember me
                    </label>
                </div>
                <!-- Login button -->
                <div class="form-group">
                    <button type = "submit" class="btn btn-dark">Login</button>
                </div>
                
            </form>
        </div>
    </main>

    <?php include 'src/components/footer.php';?> 
</body>