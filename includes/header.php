<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/tinviet/config/db.php';
$cats = $pdo->query("SELECT * FROM categories")->fetchAll();

// Lấy URL hiện tại để biết quay lại trang nào sau khi đăng nhập
$current_url = strtok($_SERVER['REQUEST_URI'], '?');
$login_error = isset($_GET['login_error']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $pageTitle ?? 'TinViet - Báo điện tử' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="http://localhost/tinviet/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="page-wrapper">

<!-- Top bar -->
<div class="navbar-top">
  <div class="container d-flex justify-content-between align-items-center">
    <span>📅 <?= date('l, d/m/Y') ?></span>
    <div>
      <?php if(isset($_SESSION['user_id'])): ?>
        <span class="me-3">👤 <?= $_SESSION['username'] ?></span>
        <?php if($_SESSION['role'] == 'admin'): ?>
          <a href="http://localhost/tinviet/admin/index.php" class="text-white me-3">⚙️ Admin</a>
        <?php endif; ?>
        <a href="http://localhost/tinviet/logout.php" class="text-white">Đăng xuất</a>
      <?php else: ?>
        <a href="javascript:void(0)" onclick="openLoginModal()" class="text-white">🔐 Đăng nhập</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Logo -->
<div class="navbar-main">
  <div class="container d-flex align-items-center justify-content-between">
    <a href="http://localhost/tinviet/index.php" class="navbar-brand">
      Tin<span>Viet</span>
    </a>
    <div class="text-white-50 fst-italic" style="font-size:13px">
      Tin tức nhanh — Chính xác — Đáng tin cậy
    </div>
  </div>
</div>

<!-- Nav menu danh mục -->
<div class="nav-menu">
  <div class="container">
    <a href="http://localhost/tinviet/index.php"
       class="<?= !isset($_GET['cat']) ? 'active' : '' ?>">🏠 Trang chủ</a>
    <?php foreach($cats as $cat): ?>
      <a href="http://localhost/tinviet/index.php?cat=<?= $cat['id'] ?>"
         class="<?= (isset($_GET['cat']) && $_GET['cat'] == $cat['id']) ? 'active' : '' ?>">
        <?= $cat['name'] ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<!-- ===== LOGIN MODAL (popup) ===== -->
<?php if(!isset($_SESSION['user_id'])): ?>
<div class="login-overlay" id="loginOverlay">
  <div class="login-modal-popup">
    <div class="login-modal-header">
      <span class="login-logo">Tin<span class="text-danger">Viet</span></span>
      <button type="button" class="login-close" onclick="closeLoginModal()">&times;</button>
    </div>

    <div class="login-modal-body">
      <h4 class="fw-bold mb-4">Đăng nhập / Tạo tài khoản</h4>

      <div class="alert alert-danger py-2" id="loginErrorBox" style="<?= $login_error ? '' : 'display:none' ?>">
        Sai tên đăng nhập hoặc mật khẩu!
      </div>

      <form method="POST" action="http://localhost/tinviet/login_process.php">
        <input type="hidden" name="redirect_back" value="<?= htmlspecialchars($current_url . (isset($_GET['cat']) ? '?cat='.(int)$_GET['cat'] : '')) ?>">

        <div class="mb-3">
          <label class="form-label fw-semibold">Tên đăng nhập</label>
          <input type="text" name="username" class="form-control login-input" placeholder="Nhập tên đăng nhập của bạn" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Mật khẩu</label>
          <input type="password" name="password" class="form-control login-input" placeholder="Nhập mật khẩu của bạn" required>
        </div>
        <button type="submit" class="btn btn-login w-100">Đăng nhập</button>
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
    </div>
  </div>
</div>

<script>
function openLoginModal() {
  document.getElementById('loginOverlay').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}
function closeLoginModal() {
  document.getElementById('loginOverlay').style.display = 'none';
  document.body.style.overflow = '';
}
// Click ra ngoài modal để đóng
document.getElementById('loginOverlay').addEventListener('click', function(e) {
  if (e.target === this) closeLoginModal();
});
// Nếu đăng nhập sai -> tự mở lại modal kèm lỗi
<?php if($login_error): ?>
window.addEventListener('DOMContentLoaded', function() {
  openLoginModal();
});
<?php endif; ?>
</script>
<?php endif; ?>