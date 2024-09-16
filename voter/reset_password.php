<?php
include('../includes/db.php');

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $registration_number = $_POST['registration_number'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $sql_update = "UPDATE voters SET password='$new_password' WHERE registration_number='$registration_number'";
    if ($conn->query($sql_update) === TRUE) {
        $success_message = "Your password has been reset successfully!";
    } else {
        $error_message = "Error: " . $sql_update . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <!-- FontAwesome for the eye icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        .password-container input[type="password"], 
        .password-container input[type="text"] {
            padding-right: 40px;
        }
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
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
        <h2>Reset Password</h2>
        <?php if ($error_message): ?>
            <div class="message error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="message success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="registration_number" value="<?php echo $_GET['registration_number']; ?>">
            <div class="password-container">
                <input type="password" name="new_password" id="new_password" placeholder="New Password" class="form-input" required>
                <!-- Use FontAwesome for eye icon -->
                <span class="password-toggle" onclick="togglePasswordVisibility()">
                    <i class="fas fa-eye" id="toggleIcon"></i>
                </span>
            </div><br>
            <input type="submit" value="Reset Password" class="form-input"><br>
            <a href="login.php">Go back to voter login</a>
        </form>
    </div>
    <?php include('../includes/footer.php'); ?>
    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("new_password");
            var toggleIcon = document.getElementById("toggleIcon");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleIcon.classList.remove("fa-eye");
                toggleIcon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                toggleIcon.classList.remove("fa-eye-slash");
                toggleIcon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>
