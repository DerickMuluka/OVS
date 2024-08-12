<?php
include('../includes/db.php');

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $registration_number = $_POST['registration_number'];
    $email = $_POST['email'];

    $sql = "SELECT * FROM voters WHERE registration_number='$registration_number' AND email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        header("location: reset_password.php?registration_number=$registration_number");
    } else {
        $error_message = "No voter found with this registration number and email combination.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
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
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <div class="container">
        <h2>Forgot Password</h2>
        <?php if ($error_message): ?>
            <div class="message error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="registration_number" placeholder="Registration Number" class="form-input" required><br>
            <input type="email" name="email" placeholder="Email Address" class="form-input" required><br>
            <input type="submit" value="Proceed" class="form-input">
        </form>
    </div>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
