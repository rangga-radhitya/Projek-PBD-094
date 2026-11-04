<?php $__env->startSection('judul', 'Data Margin Penjualan'); ?>

<?php $__env->startSection('isi'); ?>
  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="angka">ID</th><th class="angka">Persen</th><th>Status</th><th>Dibuat oleh</th><th>Dibuat</th><th>Diubah</th></tr>
    </thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $daftar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td class="angka"><?php echo e($r->idmargin_penjualan); ?></td>
        <td class="angka"><?php echo e(rtrim(rtrim(number_format($r->persen, 2, ',', '.'), '0'), ',')); ?>%</td>
        <td><span class="lencana <?php echo e($r->status == 1 ? 'aktif' : 'nonaktif'); ?>"><?php echo e($r->status == 1 ? 'Aktif' : 'Nonaktif'); ?></span></td>
        <td><?php echo e($r->username); ?></td>
        <td><?php echo e($r->created_at?->format('d/m/Y H:i') ?? '-'); ?></td>
        <td><?php echo e($r->updated_at?->format('d/m/Y H:i') ?? '-'); ?></td>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="6" class="kosong">Belum ada data margin penjualan.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/margin_penjualan/index.blade.php ENDPATH**/ ?>