<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv; // Add this line

require '../vendor/autoload.php';
include('../includes/db.php');

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$error_message = '';
$success_message = '';

function isValidEmailDomain($email) {
    // Extract domain from the email address
    $domain = substr(strrchr($email, "@"), 1);

    // Check if the domain has MX records
    if (checkdnsrr($domain, 'MX')) {
        return true;
    } else {
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $registration_number = $_POST['registration_number'];
    $student_name = $_POST['student_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if registration number or email already exists
    $sql_check = "SELECT * FROM voters WHERE registration_number='$registration_number' OR email='$email'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        $error_message = "Registration number or email already exists.";
    } elseif (!isValidEmailDomain($email)) {
        $error_message = "The email address domain is not valid or does not exist.";
    } else {
        // Send confirmation email
        $subject = "Registration Successful";
        $message = "Dear $student_name,<br>Your registration for the Online Voting System has been successful. You can now log in and participate in the voting process.";

        $mail = new PHPMailer(true);

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USERNAME']; // Your Gmail address
            $mail->Password   = $_ENV['SMTP_PASSWORD']; // Your Gmail password or app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $_ENV['SMTP_PORT'];

            // Email content
            $mail->setFrom($_ENV['SMTP_USER'], 'Administrator');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->AltBody = 'Your registration for the Online Voting System has been successful.';

            // Attempt to send the email
            $mail->send();

            // If email is sent successfully, register the voter
            $sql_register = "INSERT INTO voters (registration_number, student_name, email, password) 
                             VALUES ('$registration_number', '$student_name', '$email', '$password')";
            
            if ($conn->query($sql_register) === TRUE) {
                $success_message = "Registration successful! A confirmation email has been sent to your email address.";
            } else {
                $error_message = "Error: " . $sql_register . "<br>" . $conn->error;
            }

        } catch (Exception $e) {
            $error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}

// Fetch total number of registered voters
$sql_total_voters = "SELECT COUNT(*) as total_voters FROM voters";
$result_total_voters = $conn->query($sql_total_voters);
$total_voters = $result_total_voters->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Voter Registration</title>
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
        <h2>Voter Registration</h2>
        <?php if ($error_message): ?>
            <div class="message error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="message success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="registration_number" placeholder="Registration Number" class="form-input" required><br>
            <input type="text" name="student_name" placeholder="Student Name" class="form-input" required><br>
            <input type="email" name="email" placeholder="Email Address" class="form-input" required><br>
            <div class="password-container">
                <input type="password" name="password" id="password" placeholder="Password" class="form-input" required>
                <span class="password-toggle" onclick="togglePasswordVisibility()">👁️</span>
            </div><br>
            <input type="submit" value="Register" class="form-input">
        </form>
        <h3>Total Registered Voters: <?php echo $total_voters['total_voters']; ?></h3>
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
