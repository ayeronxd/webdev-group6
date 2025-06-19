<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['title'], $_POST['election_id'])) {
        $title = trim($_POST['title']);
        $election_id = (int)$_POST['election_id'];
        $stmt = $conn->prepare("INSERT INTO positions (title, election_id) VALUES (?, ?)");
        $stmt->bind_param("si", $title, $election_id);
        $stmt->execute();
    } elseif (isset($_POST['delete_position'])) {
        $id = (int) $_POST['position_id'];
        $stmt = $conn->prepare("DELETE FROM positions WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

$positions = $conn->query("SELECT positions.*, elections.title as election_title FROM positions JOIN elections ON elections.id = positions.election_id");
$elections = $conn->query("SELECT * FROM elections");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Manage Positions</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="container positions">
  <h2>Manage Positions</h2>
  <form method="post" class="form-row">
    <input type="text" name="title" placeholder="Position Title" required>
    <select name="election_id" required>
      <option value="">Select Election</option>
      <?php while ($e = $elections->fetch_assoc()): ?>
        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['title']) ?></option>
      <?php endwhile; ?>
    </select>
    <button type="submit">Add</button>
  </form>
  <ul>
    <?php while ($p = $positions->fetch_assoc()): ?>
      <li>
        <span class="item-label"><?= htmlspecialchars($p['title']) ?> (<?= htmlspecialchars($p['election_title']) ?>)</span>
        <form method="post" class="item-actions" style="display:inline;">
          <input type="hidden" name="position_id" value="<?= $p['id'] ?>">
          <button type="submit" name="delete_position" class="remove-btn">Delete</button>
        </form>
      </li>
    <?php endwhile; ?>
  </ul>
  <a href="admin.php"><button>Back</button></a>
</div>
</body>
</html>