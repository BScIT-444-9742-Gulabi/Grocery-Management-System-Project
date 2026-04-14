<?php
$conn = new mysqli("localhost", "root", "", "grocery_store");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";
$redirect = false;

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    if (isset($_POST['update'])) {

        $newpass = $_POST['newpass'];
        $confirmpass = $_POST['confirmpass'];

        if ($newpass !== $confirmpass) {
            $message = "Password does not match!";
        } else {

            // Hash the password before storing
            $hashedPassword = password_hash($newpass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
            $stmt->bind_param("ss", $hashedPassword, $email);
            $stmt->execute();

            $message = "Password updated successfully!";
            $redirect = true;
        }
    }
} else {
    die("No email provided.");
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        input[type=text],
        input[type=password] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            outline: none;
        }

        button {
            width: 95%;
            padding: 10px;
            background: #4CAF50;
            border: none;
            color: #fff;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #45a049;
        }

        p.message {
            color: green;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>

    <?php if ($redirect): ?>
        <meta http-equiv="refresh" content="3;url=login.php">
    <?php endif; ?>
</head>

<body>
    <div class="container">
        <h2>Reset Password</h2>

        <?php if ($message): ?>
            <p class="message" style="color: <?php echo ($message == 'Password does not match!') ? 'red' : 'green'; ?>">
                <?php echo $message; ?>
            </p>
            <?php if ($redirect): ?>
                <p>You will be redirected to the login page...</p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!$redirect): ?>
            <form method="POST" onsubmit="return validatePassword()">
                <input type="text" name="newpass" placeholder="Enter new password" required>

                <input type="password" name="confirmpass" id="confirmpass" placeholder="Confirm password" required>

                <button name="update">Update Password</button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        function validatePassword() {
            var pass = document.querySelector('input[name="newpass"]').value;
            var confirm = document.getElementById('confirmpass').value;

            if (pass !== confirm) {
                alert("Passwords do not match!");
                return false;
            }
            return true;
        }
    </script>

</body>

</html>