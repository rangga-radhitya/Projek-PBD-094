<?php $__env->startSection('judul', 'Data Barang'); ?>

<?php $__env->startSection('isi'); ?>
  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="angka">ID</th><th>Nama barang</th><th>Jenis</th><th>Satuan</th><th class="angka">Harga</th><th>Status</th></tr>
    </thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $daftar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td class="angka"><?php echo e($r->idbarang); ?></td>
        <td><?php echo e($r->nama); ?></td>
        <td><?php echo e($r->jenis_teks); ?></td>
        <td><?php echo e($r->nama_satuan ?? '-'); ?></td>
        <td class="angka">Rp <?php echo e(number_format($r->harga, 0, ',', '.')); ?></td>
        <td><span class="lencana <?php echo e($r->status == 1 ? 'aktif' : 'nonaktif'); ?>"><?php echo e($r->status == 1 ? 'Aktif' : 'Nonaktif'); ?></span></td>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="6" class="kosong">Belum ada data barang.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/barang/index.blade.php ENDPATH**/ ?>