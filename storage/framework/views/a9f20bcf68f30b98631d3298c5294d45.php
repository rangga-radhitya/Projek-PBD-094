<?php $__env->startSection('judul', 'Data Retur'); ?>
<?php $__env->startSection('grup', 'Retur'); ?>
<?php $__env->startSection('deskripsi', 'Retur barang yang mengacu ke penerimaan (retur ke vendor). Data dibaca dari tabel retur.'); ?>

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
<?php $component->withAttributes([]); ?>Struktur database mencatat retur terhadap <strong>penerimaan</strong> dari vendor. Pencatatan retur baru belum tersedia pada tahap ini. <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-bar','data' => ['reset' => route('retur.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['reset' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('retur.index'))]); ?>
    <div class="grup-filter">
      <label for="q">Cari ID retur / penerimaan</label>
      <input type="search" id="q" name="q" value="<?php echo e($q); ?>" placeholder="ID retur atau ID penerimaan">
    </div>
    <div class="grup-filter">
      <label for="idvendor">Vendor</label>
      <select id="idvendor" name="idvendor">
        <option value="">Semua vendor</option>
        <?php $__currentLoopData = $daftarVendor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($v->idvendor); ?>" <?php if($idvendor === (string) $v->idvendor): echo 'selected'; endif; ?>><?php echo e($v->nama_vendor); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
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
      <tr><th class="angka">ID</th><th>Tanggal</th><th>Penerimaan asal</th><th>Vendor</th><th>Dibuat oleh</th><th class="angka">Jumlah item</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $daftar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td class="angka"><?php echo e($r->idretur); ?></td>
        <td><?php echo e($r->created_at?->format('d/m/Y H:i') ?? '-'); ?></td>
        <td><?php if($r->idpenerimaan): ?><a href="<?php echo e(route('penerimaan.show', $r->idpenerimaan)); ?>">#<?php echo e($r->idpenerimaan); ?></a><?php else: ?> - <?php endif; ?></td>
        <td><?php echo e($r->penerimaan?->pengadaan?->vendor?->nama_vendor ?? '-'); ?></td>
        <td><?php echo e($r->user?->username ?? '-'); ?></td>
        <td class="angka"><?php echo e($r->detail_count); ?></td>
        <td class="aksi"><a class="tombol sekunder kecil" href="<?php echo e(route('retur.show', $r->idretur)); ?>">Detail</a></td>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="7" class="kosong"><?php echo e(request()->hasAny(['q','idvendor','dari','sampai']) ? 'Tidak ada retur yang cocok dengan pencarian/filter.' : 'Belum ada data retur.'); ?></td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
  <?php echo e($daftar->links('components.pagination')); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/retur/index.blade.php ENDPATH**/ ?>