<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['is_admin']) {
        header("Location: admin/admin.php");
    } else {
        header("Location: vote.php");
    }
    exit;
}

require_once 'config.php';

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Username already taken.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, 0)");
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            $success = "Registration successful. <a href='login.php'>Login here</a>.";
        } else {
            $error = "Registration failed. Please try again.";
        }
        $stmt->close();
    }
    $check->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>User Registration</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>User Registration</h2>
  <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
  <form method="POST" id="auth-forms">
    <input type="text" name="username" placeholder="Enter username" required>
    <input type="password" name="password" placeholder="Enter password" required>
    <button type="submit" name="register">Register</button>
  </form>
  <p style="text-align: center; margin-top: 15px;">
    Already have an account? <a href="login.php">Login here</a>
  </p>
</div>
</body>
</html>