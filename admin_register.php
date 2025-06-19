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
  <title>Admin Registration</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="container">
    <h2>Admin Registration</h2>
    <form action="auth.php" method="POST" id="auth-forms">
      <input type="text" name="username" placeholder="Create admin username" required>
      <input type="password" name="password" placeholder="Create password" required>
      <button type="submit" name="register_admin">Register</button>
    </form>
    <p id= "admin-login">
      Already have an account? <a href="admin_login.php">Login here</a>
    </p>
  </div>
</body>
</html>