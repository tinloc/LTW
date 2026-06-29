</div><!-- /.page-wrapper -->

<footer>
  <div class="container">
    <div class="row">
      <div class="col-md-4">
        <h6>📰 TinViet</h6>
        <p style="font-size:13px">Trang báo điện tử cung cấp tin tức nhanh, chính xác và đáng tin cậy 24/7.</p>
      </div>
      <div class="col-md-4">
        <h6>Danh mục</h6>
        <?php
        if(!isset($cats)) {
          require_once $_SERVER['DOCUMENT_ROOT'] . '/tinviet/config/db.php';
          $cats = $pdo->query("SELECT * FROM categories")->fetchAll();
        }
        foreach($cats as $cat):
        ?>
          <a href="http://localhost/tinviet/index.php?cat=<?= $cat['id'] ?>">› <?= $cat['name'] ?></a>
        <?php endforeach; ?>
      </div>
      <div class="col-md-4">
        <h6>Liên hệ</h6>
        <p style="font-size:13px">
          📧 contact@tinviet.vn<br>
          📞 0123 456 789<br>
          📍 TP. Hồ Chí Minh
        </p>
      </div>
    </div>
    <div class="footer-bottom">
      © 2024 TinViet. Bản quyền thuộc về nhóm phát triển.
    </div>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>