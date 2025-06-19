<?php
session_start();
require_once 'config.php';
require_once 'auth_check.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $title = trim($_POST['title']);
        $stmt = $conn->prepare("INSERT INTO elections (title, status) VALUES (?, 'open')");
        $stmt->bind_param("s", $title);
        $stmt->execute();
    } elseif (isset($_POST['close'])) {
        $id = (int) $_POST['id'];
        $stmt = $conn->prepare("UPDATE elections SET status='closed' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } elseif (isset($_POST['delete'])) {
        $id = (int) $_POST['delete_id'];
        $stmt = $conn->prepare("DELETE FROM elections WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

$elections = $conn->query("SELECT * FROM elections");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Manage Elections</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="container election">
  <h2>Manage Elections</h2>
  <form method="post" class="form-row">
    <input type="text" name="title" placeholder="Election Title" required>
    <button type="submit" name="add">Add</button>
  </form>
  <ul>
    <?php while ($election = $elections->fetch_assoc()): ?>
      <li>
        <span class="item-label"><?= $election['title'] ?> - <?= $election['status'] ?></span>
        <div class="item-actions">
          <?php if ($election['status'] == 'open'): ?>
            <form method="post" style="display:inline;">
              <input type="hidden" name="id" value="<?= $election['id'] ?>">
              <button type="submit" name="close" class="end-election-btn">Close</button>
            </form>
          <?php endif; ?>
          <form method="post" style="display:inline;">
            <input type="hidden" name="delete_id" value="<?= $election['id'] ?>">
            <button type="submit" name="delete" class="remove-btn">Delete</button>
          </form>
        </div>
      </li>
    <?php endwhile; ?>
  </ul>
  <a href="admin.php"><button>Back</button></a>
</div>
</body>
</html>