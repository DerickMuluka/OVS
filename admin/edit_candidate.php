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
    $id = $_POST['id'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    
    // Check if election_id already exists
    $sql_check = "SELECT * FROM candidates WHERE election_id='$election_id' AND id != '$id'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        $error_message = "Election ID already exists.";
    } else {
        // Handle file upload
        $profile_pic = $_POST['existing_profile_pic']; // Use existing picture if no new one is uploaded
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/';
            $upload_file = $upload_dir . basename($_FILES['profile_pic']['name']);
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_file)) {
                $profile_pic = basename($_FILES['profile_pic']['name']);
            }
        }

        $sql = "UPDATE candidates SET name='$name', position='$position', profile_pic='$profile_pic', election_id='$election_id' WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            $success_message = "Candidate updated successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$id = $_GET['id'];
$sql = "SELECT * FROM candidates WHERE id='$id'";
$result = $conn->query($sql);
$candidate = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Candidate</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <div class="container">
        <h2>Edit Candidate</h2>
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $candidate['id']; ?>">
            <input type="text" name="election_id" value="<?php echo $candidate['election_id']; ?>" required><br>
            <input type="text" name="name" value="<?php echo $candidate['name']; ?>" required><br>

            <!-- Position Dropdown -->
            <label for="position">Select Position:</label>
            <select name="position" id="position" required>
                <option value="">Select Position</option>
                <?php foreach ($positions as $position => $rank): ?>
                    <option value="<?php echo $position; ?>" <?php if ($candidate['position'] == $position) echo 'selected'; ?>><?php echo $position; ?></option>
                <?php endforeach; ?>
            </select><br>

            <input type="file" name="profile_pic" accept="image/*"><br>
            <?php if ($candidate['profile_pic']): ?>
                <img src="../uploads/<?php echo htmlspecialchars($candidate['profile_pic']); ?>" alt="Current Profile Picture" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;"><br>
            <?php endif; ?>
            <input type="hidden" name="existing_profile_pic" value="<?php echo $candidate['profile_pic']; ?>">
            <input type="submit" value="Update Candidate">
        </form>
    </div>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
