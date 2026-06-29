<?php
require 'config/db.php';
if(session_status() === PHP_SESSION_NONE) session_start();

$redirect_back = $_POST['redirect_back'] ?? 'index.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->execute([$username]);
  $user = $stmt->fetch();

  if($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    header("Location: " . ($user['role'] == 'admin' ? 'admin/index.php' : $redirect_back));
    exit;
  } else {
    // Đăng nhập sai -> quay lại trang trước, tự mở lại modal kèm thông báo lỗi
    $separator = strpos($redirect_back, '?') !== false ? '&' : '?';
    header("Location: " . $redirect_back . $separator . "login_error=1");
    exit;
  }
}

header("Location: index.php");
exit;