<?php $__env->startSection('judul', 'Data Vendor'); ?>

<?php $__env->startSection('isi'); ?>
  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="angka">ID</th><th>Nama vendor</th><th>Badan hukum</th><th>Status</th></tr>
    </thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $daftar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td class="angka"><?php echo e($r->idvendor); ?></td>
        <td><?php echo e($r->nama_vendor); ?></td>
        <td><?php echo e($r->badan_hukum_teks); ?></td>
        <td><span class="lencana <?php echo e($r->status === 'A' ? 'aktif' : 'nonaktif'); ?>"><?php echo e($r->status_teks); ?></span></td>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="4" class="kosong">Belum ada data vendor.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/vendor/index.blade.php ENDPATH**/ ?>