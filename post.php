<?php
require 'config/db.php';

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT p.*, c.name as cat_name FROM posts p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if(!$post) { header("Location: index.php"); exit; }

$pageTitle = $post['title'] . ' - TinViet';
require 'includes/header.php';
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm p-4">
        <span class="badge bg-danger mb-3" style="width:fit-content"><?= $post['cat_name'] ?></span>
        <h1 class="fw-bold mb-3"><?= $post['title'] ?></h1>
        <p class="text-muted mb-4">📅 <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></p>
        <img src="<?= $post['image'] ?: 'https://picsum.photos/800/400?random='.$post['id'] ?>" 
             class="img-fluid rounded mb-4" alt="">
        <div class="post-content">
          <?= nl2br($post['content']) ?>
        </div>
        <hr>
        <a href="index.php" class="btn btn-outline-danger">← Quay lại trang chủ</a>
      </div>
    </div>
  </div>
</div>

<?php require 'includes/footer.php'; ?>