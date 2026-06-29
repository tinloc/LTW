<?php
$pageTitle = 'TinViet - Trang Chủ';
require 'config/db.php';
require 'includes/header.php';
?>

<!-- Breaking news -->
<div class="breaking-bar">
  <div class="container">
    <span class="label">🔴 NÓNG</span>
    Chào mừng đến với TinViet — Tin tức nhanh, chính xác, đáng tin cậy!
  </div>
</div>

<div class="container my-4">
  <?php
  $where = isset($_GET['cat']) ? "WHERE p.category_id = " . (int)$_GET['cat'] : "";
  $posts = $pdo->query("
    SELECT p.*, c.name as cat_name 
    FROM posts p 
    LEFT JOIN categories c ON p.category_id = c.id 
    $where
    ORDER BY p.created_at DESC
  ")->fetchAll();
  ?>

  <?php if(count($posts) > 0): ?>
  <div class="row g-4">

    <!-- Bài viết lớn đầu tiên -->
    <div class="col-md-8">
      <?php $first = $posts[0]; ?>
      <div class="card">
        <img src="<?= $first['image'] ? 'uploads/posts/'.$first['image'] : 'https://picsum.photos/800/400?random='.$first['id'] ?>"
             class="featured-img" alt="">
        <div class="card-body">
          <span class="badge-cat"><?= $first['cat_name'] ?></span>
          <div class="featured-title mt-2">
            <a href="post.php?id=<?= $first['id'] ?>"><?= $first['title'] ?></a>
          </div>
          <p class="text-muted mt-2"><?= substr(strip_tags($first['content']), 0, 150) ?>...</p>
          <small class="text-muted">📅 <?= date('d/m/Y H:i', strtotime($first['created_at'])) ?></small>
        </div>
      </div>
    </div>

    <!-- Sidebar bài nhỏ -->
    <div class="col-md-4">
      <div class="section-title">Tin mới nhất</div>
      <?php foreach(array_slice($posts, 1, 4) as $post): ?>
      <div class="card mb-3">
        <div class="row g-0">
          <div class="col-4">
            <img src="<?= $post['image'] ? 'uploads/posts/'.$post['image'] : 'https://picsum.photos/200/120?random='.$post['id'] ?>"
                 class="img-fluid rounded-start" style="height:80px;object-fit:cover;width:100%" alt="">
          </div>
          <div class="col-8">
            <div class="card-body p-2">
              <span class="badge-cat" style="font-size:10px"><?= $post['cat_name'] ?></span>
              <p class="card-title mb-0 mt-1" style="font-size:13px">
                <a href="post.php?id=<?= $post['id'] ?>" style="color:#222;text-decoration:none">
                  <?= $post['title'] ?>
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

  </div>

  <!-- Các bài còn lại -->
  <?php if(count($posts) > 5): ?>
  <div class="section-title mt-4">Xem thêm</div>
  <div class="row g-4">
    <?php foreach(array_slice($posts, 5) as $post): ?>
    <div class="col-md-4">
      <div class="card h-100">
        <img src="<?= $post['image'] ? 'uploads/posts/'.$post['image'] : 'https://picsum.photos/400/200?random='.$post['id'] ?>"
             class="card-img-top" alt="">
        <div class="card-body">
          <span class="badge-cat"><?= $post['cat_name'] ?></span>
          <h6 class="card-title mt-2">
            <a href="post.php?id=<?= $post['id'] ?>"><?= $post['title'] ?></a>
          </h6>
          <small class="text-muted"><?= date('d/m/Y', strtotime($post['created_at'])) ?></small>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php else: ?>
    <div class="alert alert-info">Chưa có bài viết nào.</div>
  <?php endif; ?>

</div>

<?php require 'includes/footer.php'; ?>