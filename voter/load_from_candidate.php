<?php
include('../includes/db.php');

if (isset($_POST['candidate_id'])) {
    $candidate_id = $conn->real_escape_string($_POST['candidate_id']);
    $sql = "SELECT election_id, position FROM candidates WHERE id='$candidate_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode([]);
    }
}
?>
