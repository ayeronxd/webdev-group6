<?php
session_start();
require_once 'config.php';

$closed = $conn->query("SELECT * FROM elections WHERE status='closed'");
$election_id = isset($_GET['election_id']) ? intval($_GET['election_id']) : 0;

$selectedElection = null;
if ($election_id) {
    $selectedElection = $conn->query("SELECT * FROM elections WHERE id = $election_id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Election Results</title>
  <style>
    body {
      background: #f4f6fa;
      font-family: 'Segoe UI', Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 700px;
      margin: 40px auto;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
      padding: 32px 36px 24px 36px;
    }
    h2 {
      text-align: center;
      color: #2d3e50;
      margin-bottom: 32px;
      letter-spacing: 1px;
    }
    form {
      display: flex;
      align-items: center;
      gap: 12px;
      justify-content: center;
      margin-bottom: 28px;
    }
    select {
      padding: 7px 14px;
      border-radius: 6px;
      border: 1px solid #bfc9d1;
      font-size: 1rem;
      background: #f8fafc;
      transition: border 0.2s;
    }
    select:focus {
      border: 1.5px solid #3b82f6;
      outline: none;
    }
    .election {
      margin-top: 18px;
      padding: 18px 22px;
      background: #f8fafc;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(60,72,88,0.05);
    }
    .election h3 {
      color: #2563eb;
      margin-bottom: 18px;
      text-align: center;
      letter-spacing: 0.5px;
    }
    .positions {
      margin-bottom: 24px;
      padding: 16px 18px;
      background: #fff;
      border-radius: 7px;
      box-shadow: 0 1px 4px rgba(60,72,88,0.04);
      border-left: 4px solid #3b82f6;
    }
    .positions h4 {
      color: #374151;
      margin-bottom: 10px;
      font-size: 1.1rem;
      letter-spacing: 0.2px;
    }
    ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    li {
      padding: 8px 0;
      border-bottom: 1px solid #e5e7eb;
      font-size: 1rem;
      color: #374151;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    li:last-child {
      border-bottom: none;
    }
    .winner {
      font-weight: bold;
      color: #16a34a;
      background: #e0fbe6;
      border-radius: 4px;
      padding: 7px 10px;
      margin-left: -10px;
      margin-right: -10px;
      box-shadow: 0 1px 2px rgba(22,163,74,0.07);
    }
    .no-elections, .not-found {
      text-align: center;
      color: #b91c1c;
      background: #fef2f2;
      border-radius: 6px;
      padding: 14px;
      margin: 24px 0;
      font-size: 1.08rem;
    }
    .back-links {
      text-align: center;
      margin-top: 28px;
    }
    .back-links a {
      color: #2563eb;
      text-decoration: none;
      margin: 0 10px;
      font-weight: 500;
      transition: color 0.2s;
    }
    .back-links a:hover {
      color: #1e40af;
      text-decoration: underline;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Election Results</h2>

  <?php if ($closed->num_rows === 0): ?>
    <div class="no-elections">No closed elections yet.</div>
  <?php else: ?>
    <form method="GET">
      <label for="election_id"><strong>Select Election:</strong></label>
      <select name="election_id" id="election_id" onchange="this.form.submit()">
        <option value="">-- Choose an election --</option>
        <?php while ($row = $closed->fetch_assoc()): ?>
          <option value="<?= $row['id'] ?>" <?= ($row['id'] == $election_id ? 'selected' : '') ?>>
            <?= htmlspecialchars($row['title']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </form>

    <?php if ($selectedElection): ?>
      <div class="election">
        <h3><?= htmlspecialchars($selectedElection['title']) ?></h3>
        <?php
        $positions = $conn->query("SELECT * FROM positions WHERE election_id = {$selectedElection['id']}");
        while ($p = $positions->fetch_assoc()):
        ?>
          <div class="positions">
            <h4><?= htmlspecialchars($p['title']) ?></h4>
            <ul>
              <?php
              $candidates = $conn->query("SELECT c.name, COUNT(v.id) AS votes 
                                          FROM candidates c 
                                          LEFT JOIN votes v ON c.id = v.candidate_id 
                                          WHERE c.position_id = {$p['id']} 
                                          GROUP BY c.id 
                                          ORDER BY votes DESC");
              $first = true;
              while ($c = $candidates->fetch_assoc()):
              ?>
                <li<?= $first && $c['votes'] > 0 ? ' class="winner"' : '' ?>>
                  <span><?= htmlspecialchars($c['name']) ?></span>
                  <span><?= $c['votes'] ?> vote(s)</span>
                </li>
                <?php $first = false; ?>
              <?php endwhile; ?>
            </ul>
          </div>
        <?php endwhile; ?>
      </div>
    <?php elseif ($election_id): ?>
      <div class="not-found">Election not found.</div>
    <?php endif; ?>
  <?php endif; ?>

  <div class="back-links">
    <a href="admin.php">Back to Admin</a> | <a href="admin_logout.php">Logout</a>
  </div>
</div>
</body>
</html>