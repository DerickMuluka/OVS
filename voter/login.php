<?php
include('../includes/db.php');
session_start();

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $registration_number = $_POST['registration_number'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM voters WHERE registration_number='$registration_number'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['voter_id'] = $row['id'];
            header("location: vote.php");
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No voter found with this registration number.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Voter Login</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <style>
        .form-input {
            margin-bottom: 15px;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }

        .password-container {
            position: relative;
        }
        .password-container input[type="password"] {
            padding-right: 40px;
        }
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 37%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 1.2em;
            color: #999;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <div class="container">
        <h2>Voter Login</h2>
        <?php if ($error_message): ?>
            <div class="message error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="registration_number" placeholder="Registration Number" class="form-input" required><br>
            <div class="password-container">
                <input type="password" name="password" id="password" placeholder="Password" class="form-input" required>
                <span class="password-toggle" onclick="togglePasswordVisibility()">👁️</span>
            </div><br>
            <input type="submit" value="Login" class="form-input"><br>
            <a href="forgot_password.php" style="display:block; margin-top: 10px; text-align: center; font-size: 14px; color: blue;">Forgot Password?</a>
        </form>
    </div>
    <?php include('../includes/footer.php'); ?>
    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("password");
            var passwordToggle = document.querySelector(".password-toggle");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordToggle.textContent = "🙈";
            } else {
                passwordInput.type = "password";
                passwordToggle.textContent = "👁️";
            }
        }
    </script>
</body>
</html>
