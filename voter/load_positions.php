<?php
include('../includes/db.php');

if (isset($_POST['election_id'])) {
    $election_id = $conn->real_escape_string($_POST['election_id']);
    $sql = "SELECT DISTINCT position FROM candidates WHERE election_id='$election_id'";
    $result = $conn->query($sql);

    echo '<option value="">Select Position</option>';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($row['position']) . '">' . htmlspecialchars($row['position']) . '</option>';
        }
    }
}
?>
