<?php
require '../config/db.php';
require '../includes/auth.php';

$totalPosts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$totalCats = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - TinViet</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="http://localhost/tinviet/assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold">⚙️ TinViet Admin</a>
    <div class="ms-auto">
      <a href="http://localhost/tinviet/index.php" class="btn btn-outline-light btn-sm me-2">🌐 Xem trang</a>
      <a href="http://localhost/tinviet/logout.php" class="btn btn-outline-light btn-sm">Đăng xuất</a>
    </div>
  </div>
</nav>

<div class="container-fluid mt-4">
  <div class="row">

    <!-- Sidebar -->
    <div class="col-md-2">
      <div class="list-group">
        <a href="index.php" class="list-group-item list-group-item-action active">📊 Dashboard</a>
        <a href="posts.php" class="list-group-item list-group-item-action">📝 Bài viết</a>
        <a href="categories.php" class="list-group-item list-group-item-action">📁 Danh mục</a>
        <a href="users.php" class="list-group-item list-group-item-action">👤 Users</a>
      </div>
    </div>

    <!-- Content -->
    <div class="col-md-10">
      <h4 class="mb-4">Dashboard</h4>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card text-white bg-danger">
            <div class="card-body text-center">
              <h2><?= $totalPosts ?></h2>
              <p class="mb-0">📝 Bài viết</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-white bg-dark">
            <div class="card-body text-center">
              <h2><?= $totalCats ?></h2>
              <p class="mb-0">📁 Danh mục</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-white bg-secondary">
            <div class="card-body text-center">
              <h2><?= $totalUsers ?></h2>
              <p class="mb-0">👤 Users</p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>