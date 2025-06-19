<?php
session_start();
require 'config.php';

if (isset($_SESSION['user_id']) && $_SESSION['is_admin']) {
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Login</title>
  <link rel="stylesheet" href="admin.css">
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
    <h2>Admin Login</h2>
    <form action="auth.php" method="POST" id="auth-forms">
      <input type="text" name="username" placeholder="Enter admin username" required>
      <input type="password" name="password" placeholder="Enter password" required>
      <input type="hidden" name="role" value="admin">
      <button type="submit" name="login">Login</button>
    </form>
    <p id="admin-reg">
      Don't have an account? <a href="admin_register.php">Register here</a>
    </p>
  </div>
</body>
</html>