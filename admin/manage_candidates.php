<?php
include('../includes/db.php');

// Attempt to include the positions file and handle errors if the file is missing
if (file_exists('../includes/positions.php')) {
    include('../includes/positions.php');
} else {
    die('Positions file not found.');
}

session_start();

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit;
}

$success_message = isset($_GET['success_message']) ? $_GET['success_message'] : '';
$error_message = isset($_GET['error_message']) ? $_GET['error_message'] : '';

// Check if positions array is defined
if (!isset($positions) || !is_array($positions)) {
    die('Positions array is not defined or is not an array.');
}

// Create a SQL CASE statement for sorting
$case_statement = '';
foreach ($positions as $position => $rank) {
    $case_statement .= "WHEN position = '$position' THEN $rank ";
}

$sql = "SELECT * FROM candidates
        ORDER BY CASE $case_statement
        ELSE 9999
        END, name";

$result = $conn->query($sql);

if ($result === FALSE) {
    die("SQL Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Candidates</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <div class="container">
        <h2>Manage Candidates</h2>
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <a href="add_candidate.php">Add New Candidate</a>
        <table>
            <tr>
                <th>Election ID</th>
                <th>Name</th>
                <th>Position</th>
                <th>Profile Picture</th> <!-- New header for profile picture -->
                <th>Actions</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['election_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['position']); ?></td>
                    <td>
                        <?php if ($row['profile_pic']): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($row['profile_pic']); ?>" alt="Profile Picture" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                            No Picture
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_candidate.php?id=<?php echo $row['id']; ?>">Edit</a> |
                        <a href="delete_candidate.php?id=<?php echo $row['id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
