<?php
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']);

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    } 
    else {
        // Check email existence based on role
        $stmt = $conn->prepare("SELECT email FROM USERS WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows !== 0) {
            $errors['email'] = 'Email existed.';
        } 
        $stmt->close();
    }

    // Password validation
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{6,}$/', $password)) {
        $errors['password'] = 'Password must be at least 6 characters and include a letter, a number, and a special character.';
    }
    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    // Terms agreement
    if (!$terms) {
        $errors['terms'] = 'You must agree to the terms and conditions.';
    }

    // Check errors and add to database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO USERS (full_name, hash_password, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $fullname, hash("sha256", $password), $email);


        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location.href = '/minimuji/login';</script>";
            exit;
        } 
        else {
            $errors['general'] = 'Failed to register user. Please try again.';
        }
        $stmt->close();
    } 
    else {
        $errorMessages = implode("\\n", array_values($errors));
        echo "<script>alert('Registration failed:\\n$errorMessages');</script>";
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/form.css">
    <link rel = "stylesheet" href = "assets/css/style.css">
</head>

<body>
    <?php include 'src/components/header.php';?>

    <main>
        <div class = 'form'>
            <h1>Register</h1>

            <p>Already have an account? <a href="/minimuji/login" class="text-decoration-underline text-black">Login</a></p>

            <form method = "POST" id = "form">
                <!-- Full Name -->
                <div class = "form-group">
                    <label for = "compname">Full name*</label>
                    <input type = "text" id = "fullname" name = "fullname" placeholder = "Nguyen Van A" required>
                </div>
                <!-- Email -->
                <div class = "form-group">
                    <label for = "email">E-mail*</label>
                    <input type = "email" id = "email" name = "email" placeholder = "example@gmail.com" required>
                </div>
                <!-- Password -->
                <div class = "form-group">
                    <label for = "password">Password*</label>
                    <input type = "password" id = "password" name = "password" required>
                </div>
                <div class = "form-group">
                    <label for = "confirm_password">Confirm password*</label>
                    <input type = "password" id = "confirm_password" name = "confirm_password" required>
                </div>
                <!-- Terms agree -->
                <div class="form-group checkbox-group d-flex justify-content-center align-items-center">
                    <input type="checkbox" id="terms" name="terms" class="d-inline" required>
                    <label for="terms" class="d-inline px-2">
                        I agree to the 
                        <a href="#" class="text-decoration-underline text-black">terms and conditions</a>.
                    </label>
                </div>
                <!-- Register button -->
                <div class = "form-group">
                    <button type = "submit" class="btn btn-dark">Register</button>
                </div>
            </form>
        </div>
    </main>

    <?php include 'src/components/footer.php';?> 
</body>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const terms = document.getElementById('terms');

    // Regex pattern to check for at least 6 chars, 1 number, 1 letter, 1 special char
    const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{6,}$/;

    form.addEventListener('submit', function (e) {
        // Clear previous errors
        email.setCustomValidity('');
        password.setCustomValidity('');
        confirmPassword.setCustomValidity('');
        terms.setCustomValidity('');

        // Email validation
        if (!email.value.includes('@') || !email.value.includes('.')) {
            email.setCustomValidity('Please enter a valid email address.');
        }

        // Password validation
        if (!passwordPattern.test(password.value)) {
            password.setCustomValidity(
                'Password must be at least 6 characters and include a letter, a number, and a special character.'
            );
        }

        // Confirm password validation
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Passwords do not match.');
        }

        // Terms agreement validation
        if (!terms.checked) {
            terms.setCustomValidity('You must agree to the terms.');
        }

        // If the form is invalid, prevent submission and show errors
        if (!form.checkValidity()) {
            e.preventDefault();
            form.reportValidity();
        }
    });

    // Clear validation errors on input
    [email, password, confirmPassword].forEach(input => {
        input.addEventListener('input', () => {
            input.setCustomValidity('');
        });
    });

    terms.addEventListener('change', () => {
        terms.setCustomValidity('');
    });
});
</script>