<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$inactive = 1800; // 30 mins
$absolute = 28800; // 8 hours

if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $inactive) {
    session_unset(); session_destroy(); header("Location: login.php?timeout=1"); exit;
}
if (isset($_SESSION['created_at']) && time() - $_SESSION['created_at'] > $absolute) {
    session_unset(); session_destroy(); header("Location: login.php?timeout=1"); exit;
}
$_SESSION['last_activity'] = time();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
}
?>