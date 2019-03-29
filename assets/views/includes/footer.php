<footer class="footer bg-light">
  <div class="container">
    <p class="text-muted">
      <a href="#"><?= getenv('APP_NAME'); ?></a> - Copy left.
      <span class="float-right"><i class="fas fa-stopwatch"></i> <?= number_format(microtime(true) - INIT_TIME, 6); ?></span>
    </p>
  </div>
</footer>
