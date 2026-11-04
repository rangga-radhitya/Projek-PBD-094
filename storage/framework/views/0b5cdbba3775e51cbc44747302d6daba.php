
<div class="halaman-bar">
  <span class="halaman-info">
    <?php if($paginator->total() > 0): ?>
      Menampilkan <?php echo e($paginator->firstItem()); ?>–<?php echo e($paginator->lastItem()); ?> dari <?php echo e($paginator->total()); ?> data
    <?php else: ?>
      0 data
    <?php endif; ?>
  </span>
  <?php if($paginator->hasPages()): ?>
    <nav class="halaman" aria-label="Pagination">
      <?php if($paginator->onFirstPage()): ?>
        <span class="tombol sekunder kecil nonaktif" aria-disabled="true">&laquo; Sebelumnya</span>
      <?php else: ?>
        <a class="tombol sekunder kecil" href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev">&laquo; Sebelumnya</a>
      <?php endif; ?>
      <span class="halaman-posisi">Halaman <?php echo e($paginator->currentPage()); ?> / <?php echo e($paginator->lastPage()); ?></span>
      <?php if($paginator->hasMorePages()): ?>
        <a class="tombol sekunder kecil" href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next">Berikutnya &raquo;</a>
      <?php else: ?>
        <span class="tombol sekunder kecil nonaktif" aria-disabled="true">Berikutnya &raquo;</span>
      <?php endif; ?>
    </nav>
  <?php endif; ?>
</div>
<?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/components/pagination.blade.php ENDPATH**/ ?>