<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Results</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <style>
.print-button {
    position: fixed;
    bottom: 80px;
    right: 20px;
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.container {
    font-family: Arial, sans-serif;
}
.result-section {
    margin-bottom: 30px;
}
.result-section h3 {
    color: #2E8B57;
}
.result-section p {
    color: #333;
    font-size: 16px;
}
.winner {
    color: #FF6347;
    font-weight: bold;
}
.tie {
    color: #FF8C00;
    font-weight: bold;
}
.total-votes, .spoiled-votes, .voter-turnout, .percentage-votes {
    color: #4682B4;
}
.general-report p {
    color: #4B0082;
    font-weight: bold;
}
.verification {
    margin-top: 50px;
}
.verification p {
    color: #000;
}
.table-header {
    background-color: #f2f2f2;
    font-weight: bold;
}
.table-cell {
    padding: 10px;
}
.table-cell:nth-child(odd) {
    background-color: #f9f9f9;
}
.table-cell:nth-child(even) {
    background-color: #e9e9e9;
}
.voter-stats {
    color: #0000FF;
    font-weight: bold;
    background-color: #D3D3D3;
    padding: 10px;
    border-radius: 5px;
}
</style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <div class="container">
        <h2>View Results</h2>
        <?php
        // Fetch positions from the positions file
        include('../includes/positions.php');

        foreach ($positions as $position => $rank) {
            echo "<div class='result-section'>";
            echo "<h3>" . htmlspecialchars($position) . "</h3>";
            $sql = "SELECT candidates.election_id, candidates.name, COUNT(votes.candidate_id) as vote_count 
                    FROM candidates 
                    LEFT JOIN votes ON candidates.id = votes.candidate_id 
                    WHERE candidates.position = '$position' 
                    GROUP BY candidates.election_id, candidates.id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<tr class='table-header'><th>Election ID</th><th>Candidate</th><th>Votes</th><th>Percentage</th></tr>";
                $total_votes_position = 0;
                $candidates_data = [];

                while ($row = $result->fetch_assoc()) {
                    $total_votes_position += $row['vote_count'];
                    $candidates_data[] = $row;
                }

                foreach ($candidates_data as $data) {
                    $percentage = ($total_votes_position > 0) ? ($data['vote_count'] / $total_votes_position) * 100 : 0;
                    echo "<tr>";
                    echo "<td class='table-cell'>" . htmlspecialchars($data['election_id']) . "</td>";
                    echo "<td class='table-cell'>" . htmlspecialchars($data['name']) . "</td>";
                    echo "<td class='table-cell'>" . $data['vote_count'] . "</td>";
                    echo "<td class='table-cell'>" . number_format($percentage, 2) . "%</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No candidates found for " . htmlspecialchars($position) . ".</p>";
            }

            // Determine the winner or if there's a tie
            $sql_winner = "SELECT candidates.election_id, candidates.name, COUNT(votes.candidate_id) as vote_count 
                           FROM candidates 
                           LEFT JOIN votes ON candidates.id = votes.candidate_id 
                           WHERE candidates.position = '$position' 
                           GROUP BY candidates.election_id, candidates.id 
                           ORDER BY vote_count DESC";
            $result_winner = $conn->query($sql_winner);

            if ($result_winner->num_rows > 0) {
                $top_candidates = [];
                $max_votes = 0;

                while ($row = $result_winner->fetch_assoc()) {
                    if ($max_votes == 0) {
                        $max_votes = $row['vote_count'];
                    }

                    if ($row['vote_count'] == $max_votes) {
                        $top_candidates[] = $row['name'];
                    } else {
                        break;
                    }
                }

                if (count($top_candidates) > 1) {
                    echo "<p class='tie'>There is a tie between " . implode(" and ", array_map('htmlspecialchars', $top_candidates)) . " with $max_votes votes each.</p>";
                } else {
                    echo "<p class='winner'>Winner: " . htmlspecialchars($top_candidates[0]) . " with $max_votes votes</p>";
                }
            }

            // Total votes cast for the position
            $sql_total_votes = "SELECT COUNT(*) as total_votes FROM votes WHERE position = '$position'";
            $result_total_votes = $conn->query($sql_total_votes);
            $total_votes = $result_total_votes->fetch_assoc();

            // Display only if there are votes cast
            if ($total_votes['total_votes'] > 0) {
                echo "<p class='total-votes'>Total votes cast: " . $total_votes['total_votes'] . "</p>";

                // Total spoiled votes
                // Assuming spoiled votes are the ones with invalid candidate_id (e.g., candidate_id = 0)
                $sql_spoiled_votes = "SELECT COUNT(*) as spoiled_votes FROM votes WHERE position = '$position' AND candidate_id = 0";
                $result_spoiled_votes = $conn->query($sql_spoiled_votes);
                $spoiled_votes = $result_spoiled_votes->fetch_assoc();
                echo "<p class='spoiled-votes'>Total spoiled votes: " . $spoiled_votes['spoiled_votes'] . "</p>";
            } else {
                echo "<p>No votes have been cast for this position yet.</p>";
            }

            echo "</div>";
        }

        // Total number of registered voters
        $sql_total_voters = "SELECT COUNT(*) as total_voters FROM voters";
        $result_total_voters = $conn->query($sql_total_voters);
        $total_voters = $result_total_voters->fetch_assoc();
        echo "<h3>Total number of registered voters: " . $total_voters['total_voters'] . "</h3>";

        // Total number of voters who have voted
        $sql_voters_turned_out = "SELECT COUNT(DISTINCT voter_id) as voted FROM votes";
        $result_voters_turned_out = $conn->query($sql_voters_turned_out);
        $voted_data = $result_voters_turned_out->fetch_assoc();
        $voted_count = $voted_data['voted'];
        $turnout_percentage = ($total_voters['total_voters'] > 0) ? ($voted_count / $total_voters['total_voters']) * 100 : 0;

        echo "<div class='voter-stats'>";
        echo "<p>Voters Turnout: " . $voted_count . " (" . number_format($turnout_percentage, 2) . "%)</p>";
        echo "</div>";

        // General report for the entire election
        echo "<div class='general-report'><h2>General Report</h2>";
        foreach ($positions as $position => $rank) {
            $sql_winner = "SELECT candidates.election_id, candidates.name, COUNT(votes.candidate_id) as vote_count 
                           FROM candidates 
                           LEFT JOIN votes ON candidates.id = votes.candidate_id 
                           WHERE candidates.position = '$position' 
                           GROUP BY candidates.election_id, candidates.id 
                           ORDER BY vote_count DESC";
            $result_winner = $conn->query($sql_winner);

            if ($result_winner->num_rows > 0) {
                $top_candidates = [];
                $max_votes = 0;

                while ($row = $result_winner->fetch_assoc()) {
                    if ($max_votes == 0) {
                        $max_votes = $row['vote_count'];
                    }

                    if ($row['vote_count'] == $max_votes) {
                        $top_candidates[] = $row['name'];
                    } else {
                        break;
                    }
                }

                if (count($top_candidates) > 1) {
                    echo "<p>There is a tie for " . htmlspecialchars($position) . " between " . implode(" and ", array_map('htmlspecialchars', $top_candidates)) . " with $max_votes votes each.</p>";
                } else {
                    echo "<p>Winner for " . htmlspecialchars($position) . ": " . htmlspecialchars($top_candidates[0]) . " with $max_votes votes</p>";
                }
            }
        }
        echo "</div>";

        // Area for officials to sign
        echo "<div class='verification'><h3>Verification</h3>";
        echo "<p>Signature of Returning Officer: ____________________________</p>";
        echo "<p>Signature of Election Supervisor: ____________________________</p>";
        echo "<p>Date: ____________________________</p>";
        echo "</div>";
        ?>
    </div>
    <button class="print-button" onclick="window.print()">Print Report</button>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
