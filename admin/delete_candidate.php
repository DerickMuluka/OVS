<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit;
}

$error_message = '';
$success_message = '';

$id = $_GET['id'];

// Fetch candidate to delete their profile picture if it exists
$sql_get_pic = "SELECT profile_pic FROM candidates WHERE id='$id'";
$result_get_pic = $conn->query($sql_get_pic);
$candidate = $result_get_pic->fetch_assoc();
if ($candidate && $candidate['profile_pic']) {
    $file_path = '../uploads/' . $candidate['profile_pic'];
    if (file_exists($file_path)) {
        unlink($file_path); // Delete the existing profile picture
    }
}

$sql = "DELETE FROM candidates WHERE id='$id'";
if ($conn->query($sql) === TRUE) {
    $success_message = "Candidate deleted successfully!";
} else {
    $error_message = "Error: " . $sql . "<br>" . $conn->error;
}

// Redirect to manage_candidates.php with success or error message
header("Location: manage_candidates.php?success_message=$success_message&error_message=$error_message");
exit;
?>
