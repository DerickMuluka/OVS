<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['voter_id'])) {
    header("location: login.php");
    exit;
}

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $voter_id = $_SESSION['voter_id'];
    $position = $_POST['position'];
    $candidate_id = $_POST['candidate_id'];

    // Check if the voter has already voted for this position, regardless of election_id
    $sql_check = "SELECT * FROM votes WHERE voter_id='$voter_id' AND position='$position'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        $error_message = "You have already voted for this position.";
    } else {
        // Assuming election_id is still needed for the vote entry
        $election_id = $_POST['election_id'];

        $sql_vote = "INSERT INTO votes (voter_id, candidate_id, position, election_id) VALUES ('$voter_id', '$candidate_id', '$position', '$election_id')";
        if ($conn->query($sql_vote) === TRUE) {
            $success_message = "Your vote has been cast successfully!";
        } else {
            $error_message = "Error: " . $sql_vote . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vote</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <script src="../js/scripts.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function loadPositions(electionId) {
            $.ajax({
                url: 'load_positions.php',
                method: 'POST',
                data: { election_id: electionId },
                success: function(response) {
                    $('#position').html(response);
                    $('#candidate_id').html('<option value="">Select Candidate</option>'); // Reset candidate dropdown
                }
            });
        }

        function loadCandidates(position) {
            var electionId = $('#election_id').val();
            $.ajax({
                url: 'load_candidates.php',
                method: 'POST',
                data: { position: position, election_id: electionId },
                success: function(response) {
                    $('#candidate_id').html(response);
                }
            });
        }

        function loadFromCandidate(candidateId) {
            $.ajax({
                url: 'load_from_candidate.php',
                method: 'POST',
                data: { candidate_id: candidateId },
                success: function(response) {
                    var data = JSON.parse(response);
                    $('#election_id').val(data.election_id);
                    $('#position').val(data.position);
                }
            });
        }
    </script>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <div class="container">
        <h2>Cast Your Vote</h2>
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <label for="election_id">Select Election:</label>
            <select name="election_id" id="election_id" onchange="loadPositions(this.value)">
                <option value="">Select Election</option>
                <!-- Fetch election IDs from the database -->
                <?php
                $sql_elections = "SELECT DISTINCT election_id FROM candidates";
                $result_elections = $conn->query($sql_elections);
                if ($result_elections->num_rows > 0) {
                    while($row = $result_elections->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['election_id']) . "'>" . htmlspecialchars($row['election_id']) . "</option>";
                    }
                }
                ?>
            </select><br>
            <label for="position">Select Position:</label>
            <select name="position" id="position" onchange="loadCandidates(this.value)">
                <option value="">Select Position</option>
                <!-- Positions will be loaded via AJAX -->
            </select><br>
            <label for="candidate_id">Select Candidate:</label>
            <select name="candidate_id" id="candidate_id" onchange="loadFromCandidate(this.value)">
                <option value="">Select Candidate</option>
                <!-- Candidates will be loaded via AJAX -->
            </select><br>
            <input type="submit" value="Vote">
        </form>
    </div>
    <?php include('../includes/footer.php'); ?>
</body>
</html>