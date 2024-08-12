<?php
include('../includes/db.php');

$position = $conn->real_escape_string($_GET['position']);
$sql = "SELECT id, name FROM candidates WHERE position='$position'";
$result = $conn->query($sql);

if ($result === FALSE) {
    echo "SQL Error: " . $conn->error;
    exit;
}

if ($result->num_rows > 0) {
    echo "<option value=''>Select Candidate</option>";
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
    }
} else {
    echo "<option value=''>No candidates available</option>";
}
?>
