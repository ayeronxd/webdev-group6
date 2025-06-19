<?php
require 'auth_check.php';
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote'])) {
    $user_id = $_SESSION['user_id'];

    foreach ($_POST['vote'] as $position_id => $candidate_id) {
        $check = $conn->prepare("SELECT id FROM votes WHERE user_id=? AND position_id=?");
        $check->bind_param("ii", $user_id, $position_id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO votes (user_id, candidate_id, position_id) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $candidate_id, $position_id);
            $stmt->execute();
        }
    }
}

header("Location: vote.php");
exit;