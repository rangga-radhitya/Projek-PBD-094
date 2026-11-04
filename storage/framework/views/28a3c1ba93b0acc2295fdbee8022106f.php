

<?php $__env->startSection('judul', 'Kartu Stok'); ?>
<?php $__env->startSection('grup', 'Persediaan'); ?>
<?php $__env->startSection('deskripsi', 'Riwayat pergerakan stok barang (masuk, keluar, saldo). Data dibaca dari tabel kartu_stok.'); ?>

<?php $__env->startSection('isi'); ?>
  <?php if (isset($component)) { $__componentOriginal3a3743169da1b02a6fcfe13e09b73f16 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3a3743169da1b02a6fcfe13e09b73f16 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catatan','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catatan'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>Kode <strong>jenis transaksi</strong>: <strong>M</strong> = stok masuk (penerimaan atau pembatalan retur), <strong>K</strong> = stok keluar/koreksi (pembatalan penerimaan), <strong>R</strong> = retur ke vendor. Saldo (<code>stock</code>) adalah saldo berjalan per barang. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3a3743169da1b02a6fcfe13e09b73f16)): ?>
<?php $attributes = $__attributesOriginal3a3743169da1b02a6fcfe13e09b73f16; ?>
<?php unset($__attributesOriginal3a3743169da1b02a6fcfe13e09b73f16); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3a3743169da1b02a6fcfe13e09b73f16)): ?>
<?php $component = $__componentOriginal3a3743169da1b02a6fcfe13e09b73f16; ?>
<?php unset($__componentOriginal3a3743169da1b02a6fcfe13e09b73f16); ?>
<?php endif; ?>

  <?php if (isset($component)) { $__componentOriginale9f22847d79d6273acb27aff60f1f678 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale9f22847d79d6273acb27aff60f1f678 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-bar','data' => ['reset' => route('kartu_stok')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['reset' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('kartu_stok'))]); ?>
    <div class="grup-filter">
      <label for="idbarang">Barang</label>
      <select id="idbarang" name="idbarang">
        <option value="">Semua barang</option>
        <?php $__currentLoopData = $daftarBarang; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($b->idbarang); ?>" <?php if($idbarang === (string) $b->idbarang): echo 'selected'; endif; ?>><?php echo e($b->nama); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="grup-filter">
      <label for="jenis">Jenis transaksi</label>
      <select id="jenis" name="jenis">
        <option value="">Semua jenis</option>
        <?php $__currentLoopData = $daftarJenis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($j); ?>" <?php if($jenis === (string) $j): echo 'selected'; endif; ?>><?php echo e($j); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="grup-filter">
      <label for="q">ID transaksi</label>
      <input type="search" id="q" name="q" value="<?php echo e($q); ?>" placeholder="ID transaksi">
    </div>
    <div class="grup-filter">
      <label for="dari">Dari tanggal</label>
      <input type="date" id="dari" name="dari" value="<?php echo e($dari); ?>">
    </div>
    <div class="grup-filter">
      <label for="sampai">Sampai tanggal</label>
      <input type="date" id="sampai" name="sampai" value="<?php echo e($sampai); ?>">
    </div>
   <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale9f22847d79d6273acb27aff60f1f678)): ?>
<?php $attributes = $__attributesOriginale9f22847d79d6273acb27aff60f1f678; ?>
<?php unset($__attributesOriginale9f22847d79d6273acb27aff60f1f678); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale9f22847d79d6273acb27aff60f1f678)): ?>
<?php $component = $__componentOriginale9f22847d79d6273acb27aff60f1f678; ?>
<?php unset($__componentOriginale9f22847d79d6273acb27aff60f1f678); ?>
<?php endif; ?>

  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th>Tanggal</th><th>Barang</th><th>Jenis transaksi</th><th class="angka">Masuk</th><th class="angka">Keluar</th><th class="angka">Saldo (stock)</th><th class="angka">ID transaksi</th></tr>
    </thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $daftar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td><?php echo e($r->tanggal ?? '-'); ?></td>
        <td><?php echo e($r->nama_barang ?? ($r->idbarang ? 'Barang #'.$r->idbarang : '-')); ?></td>
        <td><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['teks' => $r->jenis_transaksi ?? '-']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['teks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->jenis_transaksi ?? '-')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?></td>
        <td class="angka"><?php echo e($r->masuk ?? 0); ?></td>
        <td class="angka"><?php echo e($r->keluar ?? 0); ?></td>
        <td class="angka"><?php echo e($r->stock ?? '-'); ?></td>
        <td class="angka"><?php echo e($r->idtransaksi ?? '-'); ?></td>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="7" class="kosong"><?php echo e(request()->hasAny(['q','idbarang','jenis','dari','sampai']) ? 'Tidak ada riwayat stok yang cocok dengan pencarian/filter.' : 'Belum ada riwayat stok (tabel kartu_stok masih kosong).'); ?></td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
  <?php echo e($daftar->links('components.pagination')); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/kartu_stok/index.blade.php ENDPATH**/ ?>