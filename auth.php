<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';

if (isset($_POST['register_admin'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, 1)");
    $stmt->bind_param("ss", $username, $hash);

    if ($stmt->execute()) {
        header("Location: admin_login.php");
    } else {
        echo "Admin registration failed.";
    }
    exit;
}

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, 0)");
    $stmt->bind_param("ss", $username, $hash);

    if ($stmt->execute()) {
        header("Location: login.php");
    } else {
        echo "User registration failed.";
    }
    exit;
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role === 'admin') {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND is_admin = 1");
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND is_admin = 0");
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $role === 'admin';
            if ($role === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: vote.php");
            }
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo ucfirst($role) . " account not found.";
    }
    exit;
}