<?php
include('../includes/db.php');

if (isset($_POST['position']) && isset($_POST['election_id'])) {
    $position = $conn->real_escape_string($_POST['position']);
    $election_id = $conn->real_escape_string($_POST['election_id']);

    $sql = "SELECT id, name FROM candidates WHERE position='$position' AND election_id='$election_id'";
    $result = $conn->query($sql);

    echo '<option value="">Select Candidate</option>';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . '</option>';
        }
    } else {
        echo '<option value="">No candidates available</option>';
    }
}
?>
