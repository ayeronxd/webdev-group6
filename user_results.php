<?php
require 'auth_check.php';
require 'config.php';

$user_id = $_SESSION['user_id'];
$elections = $conn->query("SELECT * FROM elections ORDER BY status DESC, id DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Election Results</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      background: #f4f6fa;
      font-family: 'Segoe UI', Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    .results-card {
      background: #fff;
      max-width: 1200px; 
      margin: 48px auto 0 auto;
      border-radius: 16px;
      box-shadow: 0 6px 32px rgba(0,0,0,0.10);
      padding: 38px 36px 32px 36px;
      position: relative;
    }
    .card-title {
      text-align: center;
      font-size: 2em;
      font-weight: bold;
      margin-bottom: 28px;
      color: #2563eb;
      letter-spacing: 1.2px;
    }
    .elections-flex {
      display: flex;
      flex-wrap: wrap;
      gap: 32px;
      justify-content: flex-start;
      align-items: stretch;
      margin-bottom: 24px;
    }
    .section {
      flex: 1 1 350px;
      min-width: 350px;
      max-width: 420px;
      margin-bottom: 0;
      background: #f8fafc;
      border-radius: 12px;
      padding: 22px 18px 18px 18px;
      box-shadow: 0 2px 8px rgba(60,72,88,0.06);
      border-left: 5px solid #2563eb;
      display: flex;
      flex-direction: column;
      margin-top: 0;
    }
    .section:not(:last-child) {
      margin-right: 0;
    }
    .section h3 {
      margin: 0 0 12px 0;
      font-size: 1.18em;
      color: #1a2547;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .position-title {
      font-weight: bold;
      margin-bottom: 8px;
      color: #374151;
      font-size: 1.07em;
      letter-spacing: 0.2px;
      margin-top: 14px;
    }
    .candidate-result {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #fff;
      border-radius: 7px;
      padding: 10px 14px;
      margin-bottom: 7px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.04);
      transition: background 0.18s;
    }
    .candidate-result:hover {
      background: #e0e7ff;
    }
    .candidate-name {
      font-size: 1em;
      color: #222;
      font-weight: 500;
    }
    .votes-count {
      font-size: 1em;
      color: #2563eb;
      font-weight: bold;
      letter-spacing: 0.5px;
    }
    .votes-count[style*="color:#388e3c"] {
      color: #16a34a !important;
      background: #e0fbe6;
      border-radius: 4px;
      padding: 3px 10px;
      font-weight: bold;
    }
    .status-label {
      font-size: 0.97em;
      color: #fff;
      background: #2563eb;
      border-radius: 7px;
      padding: 3px 14px;
      margin-left: 10px;
      font-weight: 500;
      letter-spacing: 0.5px;
      box-shadow: 0 1px 3px rgba(37,99,235,0.07);
    }
    .status-label.closed {
      background: #b91c1c;
    }
    .back-button {
      text-align: center;
      margin-top: 32px;
    }
    .back-button a {
      color: #2563eb;
      text-decoration: none;
      font-weight: bold;
      margin: 0 12px;
      transition: color 0.2s;
      font-size: 1.08em;
    }
    .back-button a:hover {
      color: #b91c1c;
      text-decoration: underline;
    }
    .no-elections, .no-candidates, .not-voted {
      color: #b91c1c;
      background: #fef2f2;
      border-radius: 6px;
      padding: 12px;
      margin: 18px 0;
      text-align: center;
      font-size: 1.05em;
    }
    .no-positions {
      color: #888;
      background: #f3f4f6;
      border-radius: 6px;
      padding: 10px;
      margin-bottom: 10px;
      font-size: 1em;
    }
    @media (max-width: 1100px) {
      .results-card {
        max-width: 98vw;
        padding: 18px 4vw 18px 4vw;
      }
      .elections-flex {
        flex-direction: column;
        gap: 18px;
      }
      .section {
        max-width: 100%;
        min-width: 0;
      }
    }
  </style>
</head>
<body>
<div class="results-card">
  <div class="card-title">Election Results</div>
  <?php if ($elections->num_rows === 0): ?>
    <div class="no-elections">No elections found.</div>
  <?php else: ?>
    <div class="elections-flex">
    <?php while ($election = $elections->fetch_assoc()): ?>
      <div class="section">
        <h3>
          <?= htmlspecialchars($election['title']) ?>
          <span class="status-label <?= $election['status'] === 'closed' ? 'closed' : '' ?>">
            <?= ucfirst($election['status']) ?>
          </span>
        </h3>
        <?php
        $positions = $conn->query("SELECT * FROM positions WHERE election_id = {$election['id']}");
        if ($positions->num_rows === 0) {
          echo "<div class='no-positions'>No positions for this election.</div>";
        } else {
          while ($p = $positions->fetch_assoc()) {
            echo "<div class='position-title'>" . htmlspecialchars($p['title']) . "</div>";
            $candidates = $conn->query("SELECT * FROM candidates WHERE position_id = {$p['id']}");
            if ($candidates->num_rows === 0) {
              echo "<div class='no-candidates'>No candidates for this position.</div>";
            } else {
              if ($election['status'] === 'closed') {
                // Show overall results for closed elections
                while ($c = $candidates->fetch_assoc()) {
                  $votes = $conn->query("SELECT COUNT(*) as cnt FROM votes WHERE candidate_id = {$c['id']}")->fetch_assoc();
                  echo "<div class='candidate-result'>";
                  echo "<span class='candidate-name'>" . htmlspecialchars($c['name']) . "</span>";
                  echo "<span class='votes-count'>" . intval($votes['cnt']) . " vote" . (intval($votes['cnt']) !== 1 ? "s" : "") . "</span>";
                  echo "</div>";
                }
              } else {
                // Show only user's vote for open elections
                $user_vote = $conn->query("SELECT candidate_id FROM votes WHERE user_id = $user_id AND position_id = {$p['id']}")->fetch_assoc();
                $found = false;
                while ($c = $candidates->fetch_assoc()) {
                  if ($user_vote && $user_vote['candidate_id'] == $c['id']) {
                    echo "<div class='candidate-result'>";
                    echo "<span class='candidate-name'>" . htmlspecialchars($c['name']) . "</span>";
                    echo "<span class='votes-count' style='color:#388e3c;'>Your Vote</span>";
                    echo "</div>";
                    $found = true;
                  }
                }
                if (!$found) {
                  echo "<div class='not-voted'>Not yet voted.</div>";
                }
              }
            }
          }
        }
        ?>
      </div>
    <?php endwhile; ?>
    </div>
  <?php endif; ?>
  <div class="back-button">
    <a href="vote.php">Back to Voting</a> |
    <a href="logout.php">Logout</a>
  </div>
</div>
</body>
</html>
