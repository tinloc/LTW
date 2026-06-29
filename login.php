<?php
require 'config/db.php';
if(session_status() === PHP_SESSION_NONE) session_start();
if(isset($_SESSION['user_id'])) header("Location: index.php");

$error = '';
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
    header("Location: " . ($user['role'] == 'admin' ? 'admin/index.php' : 'index.php'));
    exit;
  } else {
    $error = "Sai tài khoản hoặc mật khẩu!";
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Đăng nhập - TinViet</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/tinviet/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="login-wrapper">
  <div class="login-modal shadow">

    <!-- Header logo -->
    <div class="login-modal-header">
      <span class="login-logo">Tin<span class="text-danger">Viet</span></span>
    </div>

    <div class="login-modal-body">
      <h4 class="text-center fw-bold mb-4">Đăng nhập / Tạo tài khoản</h4>

      <?php if($error): ?>
        <div class="alert alert-danger py-2"><?= $error ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label fw-semibold">Tên đăng nhập</label>
          <input type="text" name="username" class="form-control login-input" placeholder="Nhập tên đăng nhập của bạn" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Mật khẩu</label>
          <input type="password" name="password" class="form-control login-input" placeholder="Nhập mật khẩu của bạn" required>
        </div>
        <button type="submit" class="btn btn-login w-100">Tiếp tục</button>
      </form>

      <div class="login-divider"><span>Hoặc</span></div>

      <div class="row g-2">
        <div class="col-4">
          <button type="button" class="btn btn-social w-100" disabled title="Tính năng đang phát triển">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" width="22" alt="Google"><br>
            <span>Google</span>
          </button>
        </div>
        <div class="col-4">
          <button type="button" class="btn btn-social w-100" disabled title="Tính năng đang phát triển">
            <span class="social-icon fb">f</span><br>
            <span>Facebook</span>
          </button>
        </div>
        <div class="col-4">
          <button type="button" class="btn btn-social w-100" disabled title="Tính năng đang phát triển">
            <span class="social-icon apple"></span><br>
            <span>Apple</span>
          </button>
        </div>
      </div>

      <p class="login-terms mt-3 mb-0">
        Tiếp tục là đồng ý với <a href="#">điều khoản sử dụng</a> và <a href="#">chính sách bảo mật</a> của TinViet.
      </p>

      <p class="text-center mt-3 mb-0">
        <a href="index.php" class="text-muted">← Về trang chủ</a>
      </p>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>