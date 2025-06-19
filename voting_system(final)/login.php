<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['is_admin']) {
        header("Location: admin.php");
    } else {
        header("Location: vote.php");
    }
    exit;
}

require_once 'config.php';
require_once 'auth.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>User Login</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .container {
      max-width: 480px; 
      margin: 60px auto 0 auto;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.13);
      padding: 32px 28px 28px 28px;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>User Login</h2>
  <form method="post" action="auth.php" id="auth-forms">
    <input type="text" name="username" placeholder="Enter username" required>
    <input type="password" name="password" placeholder="Enter password" required>
    <input type="hidden" name="role" value="user">
    <button type="submit" name="login">Login</button>
  </form>
  <p style="text-align: center; margin-top: 15px;">
    Don't have an account? <a href="register.php">Register here</a>
  </p>
  <p style="text-align: center; margin-top: 15px;">
    Don't have an account? <a href="admin_login.php">admin</a>
  </p>
</div>
</body>
</html>