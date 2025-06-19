<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['name']) && isset($_POST['position_id'])) {
        $name = trim($_POST['name']);
        $position_id = (int) $_POST['position_id'];
        $stmt = $conn->prepare("INSERT INTO candidates (name, position_id) VALUES (?, ?)");
        $stmt->bind_param("si", $name, $position_id);
        $stmt->execute();
    } elseif (isset($_POST['delete_candidate'])) {
        $id = (int) $_POST['candidate_id'];
        $stmt = $conn->prepare("DELETE FROM candidates WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

$candidates = $conn->query("SELECT c.id, c.name, p.title FROM candidates c JOIN positions p ON c.position_id = p.id");
$positions = $conn->query("SELECT * FROM positions");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Manage Candidates</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="container candidates">
  <h2>Manage Candidates</h2>
  <form method="post" class="form-row">
    <input type="text" name="name" placeholder="Candidate Name" required>
    <select name="position_id">
      <?php while ($p = $positions->fetch_assoc()): ?>
        <option value="<?= $p['id'] ?>"><?= $p['title'] ?></option>
      <?php endwhile; ?>
    </select>
    <button type="submit">Add</button>
  </form>
  <ul>
    <?php while ($c = $candidates->fetch_assoc()): ?>
      <li>
        <span class="item-label"><?= $c['name'] ?> - <?= $c['title'] ?></span>
        <form method="post" class="item-actions" style="display:inline;">
          <input type="hidden" name="candidate_id" value="<?= $c['id'] ?>">
          <button type="submit" name="delete_candidate" class="remove-btn">Delete</button>
        </form>
      </li>
    <?php endwhile; ?>
  </ul>
  <a href="admin.php"><button>Back</button></a>
</div>
</body>
</html>