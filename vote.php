<?php
require 'auth_check.php';
require 'config.php';

$open_elections = $conn->query("SELECT * FROM elections WHERE status='open'");

$user_id = $_SESSION['user_id'];
$voted = $conn->query("SELECT position_id FROM votes WHERE user_id = $user_id")->fetch_all(MYSQLI_ASSOC);
$voted_ids = array_column($voted, 'position_id');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Vote</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      background: #f2f2f2;
    }
    .voter-card {
      background: #fff;
      max-width: 720px;
      margin: 40px auto 0 auto;
      border-radius: 16px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.13);
      padding: 32px 28px 28px 28px;
      position: relative;
    }
    .voter-profile {
      display: flex;
      align-items: center;
      margin-bottom: 24px;
      border-bottom: 1px solid #e0e0e0;
      padding-bottom: 18px;
    }
    .voter-avatar {
      width: 64px;
      height: 64px;
      border-radius: 50%;
      background: #e3e3e3;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.2em;
      color: #888;
      margin-right: 18px;
      border: 2px solid #d1d1d1;
    }
    .voter-info {
      flex: 1;
    }
    .voter-info h3 {
      margin: 0 0 4px 0;
      font-size: 1.2em;
      color: #222;
    }
    .voter-info span {
      font-size: 0.98em;
      color: #666;
    }
    .card-title {
      text-align: center;
      font-size: 1.5em;
      font-weight: bold;
      margin-bottom: 18px;
      color: #2a3d6c;
      letter-spacing: 1px;
    }
    .section {
      margin-bottom: 22px;
      background: #f8faff;
      border-radius: 10px;
      padding: 16px 14px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .form-row {
      margin-bottom: 18px;
      padding: 8px 0;
    }
    .position-title {
      font-weight: bold;
      margin-bottom: 8px;
      color: #1a2547;
      font-size: 1.08em;
    }
    .candidates-list {
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin-left: 10px;
    }
    .candidate-item {
      display: flex;
      align-items: center;
      background: #fff;
      border-radius: 6px;
      padding: 7px 10px;
      box-shadow: 0 1px 2px rgba(0,0,0,0.04);
      transition: background 0.2s;
    }
    .candidate-item:hover {
      background: #e3e9f7;
    }
    .candidate-radio {
      margin-right: 10px;
      accent-color: #2a3d6c;
    }
    .candidate-name {
      font-size: 1em;
      color: #222;
    }
    .already-voted {
      color: #b71c1c;
      font-size: 1em;
      margin-bottom: 8px;
    }
    button[type="submit"] {
      width: 100%;
      background: #2a3d6c;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 12px 0;
      font-size: 1.1em;
      font-weight: bold;
      cursor: pointer;
      margin-top: 12px;
      transition: background 0.2s;
    }
    button[type="submit"]:hover {
      background: #1a2547;
    }
    .back-button {
      text-align: center;
      margin-top: 22px;
    }
    .back-button a {
      color: #2a3d6c;
      text-decoration: none;
      font-weight: bold;
      margin: 0 8px;
      transition: color 0.2s;
    }
    .back-button a:hover {
      color: #b71c1c;
    }
  </style>
</head>
<body>
<div class="voter-card">
  <div class="voter-profile">
    <div class="voter-avatar">
      <?php
        // Show first letter of username as avatar
        echo strtoupper(substr($_SESSION['username'], 0, 1));
      ?>
    </div>
    <div class="voter-info">
      <h3><?= htmlspecialchars($_SESSION['username']) ?></h3>
      <span>Voter ID: <?= htmlspecialchars($_SESSION['user_id']) ?></span>
    </div>
  </div>
  <div class="card-title">Voter's Card - Cast Your Vote</div>

  <?php if ($open_elections->num_rows === 0): ?>
    <p style="text-align:center;">No open elections at the moment.</p>
  <?php else: ?>
    <form action="voting.php" method="post" id="vote-form">
      <?php
      $has_any_unvoted = false;
      while ($election = $open_elections->fetch_assoc()):
      ?>
        <div class="section">
          <h3><?= htmlspecialchars($election['title']) ?></h3>
          <?php
          $positions = $conn->query("SELECT * FROM positions WHERE election_id = {$election['id']}");
          $has_unvoted = false;
          ob_start();
          while ($p = $positions->fetch_assoc()):
              if (in_array($p['id'], $voted_ids)) {
                  echo "<div class='already-voted'><strong>" . htmlspecialchars($p['title']) . "</strong>: Already voted</div>";
              } else {
                  $has_unvoted = true;
                  $has_any_unvoted = true;
                  echo "<div class='form-row'>";
                  echo "<div class='position-title'>" . htmlspecialchars($p['title']) . "</div>";
                  echo "<div class='candidates-list' data-position-id='{$p['id']}'>";
                  $candidates = $conn->query("SELECT * FROM candidates WHERE position_id = {$p['id']}");
                  while ($c = $candidates->fetch_assoc()) {
                      echo "<label class='candidate-item'>";
                      echo "<input type='radio' class='candidate-radio' name='vote[{$p['id']}]' value='{$c['id']}' required> ";
                      echo "<span class='candidate-name'>" . htmlspecialchars($c['name']) . "</span>";
                      echo "</label>";
                  }
                  echo "</div></div>";
              }
          endwhile;
          $output = ob_get_clean();
          if ($has_unvoted) {
              echo $output;
          } else {
              echo "<p class='already-voted'>You have already voted in all positions under this election.</p>";
          }
          ?>
        </div>
      <?php endwhile; ?>
      <button type="submit" id="submit-vote-btn"<?= !$has_any_unvoted ? ' style="display:none;"' : '' ?>>Submit Votes</button>
    </form>
    <script>
      // Client-side validation to ensure all fields are filled
      document.getElementById('vote-form').addEventListener('submit', function(e) {
        var requiredGroups = document.querySelectorAll('.candidates-list');
        var allFilled = true;
        requiredGroups.forEach(function(group) {
          var radios = group.querySelectorAll('input[type="radio"]');
          var checked = false;
          radios.forEach(function(radio) {
            if (radio.checked) checked = true;
          });
          if (radios.length > 0 && !checked) {
            allFilled = false;
            group.scrollIntoView({behavior: "smooth", block: "center"});
            group.style.boxShadow = "0 0 0 2px #b71c1c";
            setTimeout(function() { group.style.boxShadow = ""; }, 1500);
          }
        });
        if (!allFilled) {
          alert('Please select a candidate for every position before submitting your votes.');
          e.preventDefault();
        } else {
          // Hide submit button after form is submitted
          setTimeout(function() {
            document.getElementById('submit-vote-btn').style.display = 'none';
          }, 10);
        }
      });
    </script>
  <?php endif; ?>

  <div class="back-button">
    <a href="user_results.php">View Results</a> |
    <a href="logout.php">Logout</a>
  </div>
</div>
</body>
</html>