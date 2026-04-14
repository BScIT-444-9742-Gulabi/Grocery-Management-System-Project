<?php
include '../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";
$success = "";

/* REGISTER */
if (isset($_POST['register'])) {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $mobile   = trim($_POST['mobile']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {

        // Check existing email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $check = $stmt->get_result();

        if ($check->num_rows > 0) {
            $error = "Email already exists!";
        } else {

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $mobile, $hashed);

            if ($stmt->execute()) {
                $success = "✅ Registration successful!";
                $_POST = [];
            } else {
                $error = "Registration failed!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - FreshGrocer</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .register-container {
            width: 380px;
            margin: 60px auto;
            background: #fff;
            padding: 30px 25px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .register-container:hover {
            transform: translateY(-5px);
        }

        .register-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            outline: none;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: #6c63ff;
            box-shadow: 0 0 5px rgba(108, 99, 255, 0.3);
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: none;
            border-radius: 6px;
            background: #6c63ff;
            color: #fff;
            font-size: 15px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #5548d9;
        }

        .error {
            background: #ffe5e5;
            color: #d8000c;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .success {
            background: #e6ffed;
            color: #2e7d32;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .register-container p {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .register-container a {
            color: #6c63ff;
            text-decoration: none;
        }

        .register-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="register-container">
    <h2>Create Account</h2>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="name" required>
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Mobile Number:</label>
            <input type="tel" name="mobile" pattern="[0-9]{10}" required>
        </div>

        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required minlength="6">
        </div>

        <div class="form-group">
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required>
        </div>

        <button type="submit" name="register">Register</button>

        <p>Already have an account? <a href="login.php">Login here</a></p>

    </form>
</div>

</body>
</html>

<script>
    function validateForm() {
        let name = document.getElementById("name").value;
        let regex = /^[a-zA-Z ]+$/;
        let errorBox = document.getElementById("errorMsg");

        if (!regex.test(name)) {
            errorBox.innerHTML = "Only alphabets are allowed in the name!";

            setTimeout(function() {
                errorBox.innerHTML = "";
            }, 3000);

            return false;
        } else {
            errorBox.innerHTML = "";
        }

        return true;
    }
</script> 