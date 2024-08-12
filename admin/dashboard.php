<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['user_role'])) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <div class="container">
        <h2>Admin Dashboard</h2>
        <p>Welcome, <?php echo ucfirst($_SESSION['user_role']); ?>!</p>
        <div class="dashboard-links">
            <a href="manage_candidates.php">Manage Candidates</a>
            <a href="view_results.php">View Results</a>
            <!-- Add more links as needed -->
        </div>
    </div>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
