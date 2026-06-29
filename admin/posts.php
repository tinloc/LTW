<?php
require '../config/db.php';
require '../includes/auth.php';
 
// Xóa bài viết
if (isset($_GET['delete'])) {
    // Lấy ảnh để xóa file vật lý (nếu có)
    $stmt = $pdo->prepare("SELECT image FROM posts WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    $old = $stmt->fetch();
    if ($old && $old['image'] && file_exists('../uploads/posts/' . $old['image'])) {
        unlink('../uploads/posts/' . $old['image']);
    }
 
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    header("Location: posts.php");
    exit;
}
 
// Thêm hoặc sửa bài viết
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = (int)$_POST['category_id'];
    $status = $_POST['status'];
 
    // Xử lý upload ảnh
    $image_name = $_POST['old_image'] ?? null; // giữ ảnh cũ nếu không chọn ảnh mới
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
 
        if (in_array($ext, $allowed)) {
            // Xóa ảnh cũ nếu đang sửa bài viết và có ảnh mới thay thế
            if (!empty($_POST['old_image']) && file_exists('../uploads/posts/' . $_POST['old_image'])) {
                unlink('../uploads/posts/' . $_POST['old_image']);
            }
 
            $image_name = time() . '_' . uniqid() . '.' . $ext;
            $target = '../uploads/posts/' . $image_name;
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
        }
    }
 
    if (!empty($_POST['id'])) {
        $stmt = $pdo->prepare("UPDATE posts SET title=?, content=?, category_id=?, status=?, image=? WHERE id=?");
        $stmt->execute([$title, $content, $category_id, $status, $image_name, (int)$_POST['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO posts (title, content, category_id, user_id, status, image) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$title, $content, $category_id, $_SESSION['user_id'], $status, $image_name]);
    }
    header("Location: posts.php");
    exit;
}
 
// Lấy bài viết cần sửa
$editPost = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editPost = $stmt->fetch();
}
 
$posts = $pdo->query("SELECT p.*, c.name as cat_name FROM posts p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC")->fetchAll();
$cats = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quản lý bài viết - TinViet</title>
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
        <a href="index.php" class="list-group-item list-group-item-action">📊 Dashboard</a>
        <a href="posts.php" class="list-group-item list-group-item-action active">📝 Bài viết</a>
        <a href="categories.php" class="list-group-item list-group-item-action">📁 Danh mục</a>
        <a href="users.php" class="list-group-item list-group-item-action">👤 Users</a>
      </div>
    </div>
 
    <!-- Content -->
    <div class="col-md-10">
      <h4 class="mb-4">📝 Quản lý bài viết</h4>
 
      <!-- Form thêm/sửa -->
      <div class="card mb-4">
        <div class="card-header bg-danger text-white">
          <?= $editPost ? '✏️ Sửa bài viết' : '➕ Thêm bài viết mới' ?>
        </div>
        <div class="card-body">
          <form method="POST" enctype="multipart/form-data">
            <?php if($editPost): ?>
              <input type="hidden" name="id" value="<?= $editPost['id'] ?>">
              <input type="hidden" name="old_image" value="<?= $editPost['image'] ?? '' ?>">
            <?php endif; ?>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Tiêu đề</label>
                <input type="text" name="title" class="form-control" required
                       value="<?= $editPost['title'] ?? '' ?>">
              </div>
              <div class="col-md-2">
                <label class="form-label">Ảnh đại diện</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                <?php if($editPost && $editPost['image']): ?>
                  <img src="../uploads/posts/<?= $editPost['image'] ?>" width="60" class="mt-1 rounded d-block">
                <?php endif; ?>
              </div>
              <div class="col-md-2">
                <label class="form-label">Danh mục</label>
                <select name="category_id" class="form-select" required>
                  <?php foreach($cats as $cat): ?>
                    <option value="<?= $cat['id'] ?>"
                      <?= ($editPost && $editPost['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                      <?= $cat['name'] ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                  <option value="published" <?= ($editPost && $editPost['status'] == 'published') ? 'selected' : '' ?>>Công khai</option>
                  <option value="draft" <?= ($editPost && $editPost['status'] == 'draft') ? 'selected' : '' ?>>Nháp</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Nội dung</label>
                <textarea name="content" class="form-control" rows="5" required><?= $editPost['content'] ?? '' ?></textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-danger">
                  <?= $editPost ? '💾 Cập nhật' : '➕ Thêm bài viết' ?>
                </button>
                <?php if($editPost): ?>
                  <a href="posts.php" class="btn btn-secondary ms-2">Hủy</a>
                <?php endif; ?>
              </div>
            </div>
          </form>
        </div>
      </div>
 
      <!-- Danh sách bài viết -->
      <div class="card">
        <div class="card-header">Danh sách bài viết (<?= count($posts) ?>)</div>
        <div class="card-body p-0">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Ảnh</th>
                <th>Tiêu đề</th>
                <th>Danh mục</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($posts as $post): ?>
              <tr>
                <td><?= $post['id'] ?></td>
                <td>
                  <?php if($post['image']): ?>
                    <img src="../uploads/posts/<?= $post['image'] ?>" width="50" class="rounded">
                  <?php else: ?>
                    <span class="text-muted small">Không có</span>
                  <?php endif; ?>
                </td>
                <td><?= $post['title'] ?></td>
                <td><?= $post['cat_name'] ?></td>
                <td>
                  <?php if($post['status'] == 'published'): ?>
                    <span class="badge bg-success">Công khai</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Nháp</span>
                  <?php endif; ?>
                </td>
                <td><?= date('d/m/Y', strtotime($post['created_at'])) ?></td>
                <td>
                  <a href="posts.php?edit=<?= $post['id'] ?>" class="btn btn-sm btn-warning">✏️ Sửa</a>
                  <a href="posts.php?delete=<?= $post['id'] ?>" class="btn btn-sm btn-danger"
                     onclick="return confirm('Xóa bài viết này?')">🗑️ Xóa</a>
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