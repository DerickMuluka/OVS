<?php
include('../includes/db.php');
session_start();

$error_message = ''; // Initialize the error message variable

// Check if the user is already logged in and has one of the authorized roles
if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'Election Officer', 'Deputy Election Officer', 'Returning Officer', 'Presiding Officer', 'Polling Officer', 'Scrutineers', 'Election Committee Member'])) {
    // User is already authenticated, redirect to the dashboard
    header("location: dashboard.php");
    exit();
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role']; // Capture the selected role
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the SQL query to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Store the user role in session and check if the user has one of the authorized roles
        $allowed_roles = ['admin', 'Election Officer', 'Deputy Election Officer', 'Returning Officer', 'Presiding Officer', 'Polling Officer', 'Scrutineers', 'Election Committee Member'];

        if ($user['role'] == $role && in_array($user['role'], $allowed_roles)) {
            $_SESSION['username'] = $username;
            $_SESSION['user_role'] = $user['role']; // Set the user role in session
            header("location: dashboard.php");
            exit(); // Ensure no further code is executed after redirection
        } else {
            $error_message = "Unauthorized access!";
        }
    } else {
        $error_message = "Invalid Username or Password!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
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
        <h2>Admin Login</h2>
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="POST">
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="admin">Admin</option>
                <option value="Election Officer">Election Officer</option>
                <option value="Deputy Election Officer">Deputy Election Officer</option>
                <option value="Returning Officer">Returning Officer</option>
                <option value="Presiding Officer">Presiding Officer</option>
                <option value="Polling Officer">Polling Officer</option>
                <option value="Scrutineers">Scrutineers</option>
                <option value="Election Committee Member">Election Committee Member</option>
            </select><br>
            <input type="text" name="username" placeholder="Username" required><br>
            <div class="password-container">
                <input type="password" name="password" id="password" placeholder="Password" class="form-input" required>
                <span class="password-toggle" onclick="togglePasswordVisibility()">👁️</span>
            </div><br>
            <input type="submit" value="Login">
        </form>
    </div>
    <?php include('../includes/footer.php'); ?>
    <script src="../js/scripts.js"></script>
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
