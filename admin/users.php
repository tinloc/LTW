<?php
require '../config/db.php';
require '../includes/auth.php';

// Xóa user
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: users.php");
    exit;
}

// Thêm hoặc sửa user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $role = $_POST['role'];

    if (!empty($_POST['id'])) {
        // Sửa — đổi password nếu có nhập
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username=?, password=?, role=? WHERE id=?");
            $stmt->execute([$username, $password, $role, (int)$_POST['id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username=?, role=? WHERE id=?");
            $stmt->execute([$username, $role, (int)$_POST['id']]);
        }
    } else {
        // Thêm mới
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?,?,?)");
        $stmt->execute([$username, $password, $role]);
    }
    header("Location: users.php");
    exit;
}

$editUser = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editUser = $stmt->fetch();
}

$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quản lý Users - TinViet</title>
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
        <a href="categories.php" class="list-group-item list-group-item-action">📁 Danh mục</a>
        <a href="users.php" class="list-group-item list-group-item-action active">👤 Users</a>
      </div>
    </div>

    <div class="col-md-10">
      <h4 class="mb-4">👤 Quản lý Users</h4>

      <div class="card mb-4">
        <div class="card-header bg-danger text-white">
          <?= $editUser ? '✏️ Sửa user' : '➕ Thêm user mới' ?>
        </div>
        <div class="card-body">
          <form method="POST">
            <?php if($editUser): ?>
              <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
            <?php endif; ?>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Tên đăng nhập</label>
                <input type="text" name="username" class="form-control" required
                       value="<?= $editUser['username'] ?? '' ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label">
                  Mật khẩu <?= $editUser ? '<small class="text-muted">(để trống nếu không đổi)</small>' : '' ?>
                </label>
                <input type="password" name="password" class="form-control"
                       <?= !$editUser ? 'required' : '' ?>>
              </div>
              <div class="col-md-2">
                <label class="form-label">Vai trò</label>
                <select name="role" class="form-select">
                  <option value="user" <?= ($editUser && $editUser['role'] == 'user') ? 'selected' : '' ?>>User</option>
                  <option value="admin" <?= ($editUser && $editUser['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                </select>
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-danger w-100">
                  <?= $editUser ? '💾 Cập nhật' : '➕ Thêm' ?>
                </button>
              </div>
            </div>
            <?php if($editUser): ?>
              <a href="users.php" class="btn btn-secondary mt-2">Hủy</a>
            <?php endif; ?>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header">Danh sách users (<?= count($users) ?>)</div>
        <div class="card-body p-0">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Tên đăng nhập</th>
                <th>Vai trò</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($users as $user): ?>
              <tr>
                <td><?= $user['id'] ?></td>
                <td><?= $user['username'] ?></td>
                <td>
                  <?php if($user['role'] == 'admin'): ?>
                    <span class="badge bg-danger">Admin</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">User</span>
                  <?php endif; ?>
                </td>
                <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                <td>
                  <a href="users.php?edit=<?= $user['id'] ?>" class="btn btn-sm btn-warning">✏️ Sửa</a>
                  <?php if($user['id'] != $_SESSION['user_id']): ?>
                    <a href="users.php?delete=<?= $user['id'] ?>" class="btn btn-sm btn-danger"
                       onclick="return confirm('Xóa user này?')">🗑️ Xóa</a>
                  <?php endif; ?>
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