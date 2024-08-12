<?php
include('../includes/db.php');
include('../includes/positions.php'); // Include positions file
session_start();

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit;
}

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $election_id = $_POST['election_id'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    
    // Check if election_id already exists
    $sql_check = "SELECT * FROM candidates WHERE election_id='$election_id'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        $error_message = "Election ID already exists.";
    } else {
        // Handle file upload
        $profile_pic = "";
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/';
            $upload_file = $upload_dir . basename($_FILES['profile_pic']['name']);
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_file)) {
                $profile_pic = basename($_FILES['profile_pic']['name']);
            }
        }

        $sql = "INSERT INTO candidates (name, profile_pic, position, election_id) VALUES ('$name', '$profile_pic', '$position', '$election_id')";
        if ($conn->query($sql) === TRUE) {
            $success_message = "Candidate added successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Candidate</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <div class="container">
        <h2>Add New Candidate</h2>
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="election_id" placeholder="Election ID" required><br>
            <input type="text" name="name" placeholder="Name" required><br>

            <!-- Position Dropdown -->
            <label for="position">Select Position:</label>
            <select name="position" id="position" required>
                <option value="">Select Position</option>
                <?php foreach ($positions as $position => $rank): ?>
                    <option value="<?php echo $position; ?>"><?php echo $position; ?></option>
                <?php endforeach; ?>
            </select><br>

            <input type="file" name="profile_pic" accept="image/*"><br>
            <input type="submit" value="Add Candidate">
        </form>
    </div>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
