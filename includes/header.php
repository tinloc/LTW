<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/tinviet/config/db.php';
$cats = $pdo->query("SELECT * FROM categories")->fetchAll();
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
        <a href="http://localhost/tinviet/login.php" class="text-white">🔐 Đăng nhập</a>
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