<?php
require '../config/db.php';
require '../includes/auth.php';

// Xóa
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    header("Location: categories.php");
    exit;
}

// Thêm hoặc sửa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $slug = strtolower(trim($_POST['slug']));

    if (!empty($_POST['id'])) {
        $stmt = $pdo->prepare("UPDATE categories SET name=?, slug=? WHERE id=?");
        $stmt->execute([$name, $slug, (int)$_POST['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?,?)");
        $stmt->execute([$name, $slug]);
    }
    header("Location: categories.php");
    exit;
}

$editCat = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editCat = $stmt->fetch();
}

$cats = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quản lý danh mục - TinViet</title>
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

    <div class="col-md-2">
      <div class="list-group">
        <a href="index.php" class="list-group-item list-group-item-action">📊 Dashboard</a>
        <a href="posts.php" class="list-group-item list-group-item-action">📝 Bài viết</a>
        <a href="categories.php" class="list-group-item list-group-item-action active">📁 Danh mục</a>
        <a href="users.php" class="list-group-item list-group-item-action">👤 Users</a>
      </div>
    </div>

    <div class="col-md-10">
      <h4 class="mb-4">📁 Quản lý danh mục</h4>

      <div class="card mb-4">
        <div class="card-header bg-danger text-white">
          <?= $editCat ? '✏️ Sửa danh mục' : '➕ Thêm danh mục mới' ?>
        </div>
        <div class="card-body">
          <form method="POST">
            <?php if($editCat): ?>
              <input type="hidden" name="id" value="<?= $editCat['id'] ?>">
            <?php endif; ?>
            <div class="row g-3">
              <div class="col-md-5">
                <label class="form-label">Tên danh mục</label>
                <input type="text" name="name" class="form-control" required
                       value="<?= $editCat['name'] ?? '' ?>">
              </div>
              <div class="col-md-5">
                <label class="form-label">Slug (URL)</label>
                <input type="text" name="slug" class="form-control" required
                       value="<?= $editCat['slug'] ?? '' ?>" placeholder="vd: cong-nghe">
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-danger w-100">
                  <?= $editCat ? '💾 Cập nhật' : '➕ Thêm' ?>
                </button>
              </div>
            </div>
            <?php if($editCat): ?>
              <a href="categories.php" class="btn btn-secondary mt-2">Hủy</a>
            <?php endif; ?>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header">Danh sách danh mục (<?= count($cats) ?>)</div>
        <div class="card-body p-0">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Tên danh mục</th>
                <th>Slug</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($cats as $cat): ?>
              <tr>
                <td><?= $cat['id'] ?></td>
                <td><?= $cat['name'] ?></td>
                <td><code><?= $cat['slug'] ?></code></td>
                <td>
                  <a href="categories.php?edit=<?= $cat['id'] ?>" class="btn btn-sm btn-warning">✏️ Sửa</a>
                  <a href="categories.php?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-danger"
                     onclick="return confirm('Xóa danh mục này?')">🗑️ Xóa</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>