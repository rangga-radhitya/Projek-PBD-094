<?php $__env->startSection('judul', 'Dashboard'); ?>
<?php $__env->startSection('deskripsi', 'Ringkasan data aplikasi. Semua angka dihitung langsung dari database.'); ?>

<?php $__env->startSection('isi'); ?>
  <div class="ringkasan">
    <div class="kartu"><div class="judul">Penjualan hari ini</div><div class="nilai"><?php echo e($ringkas['penjualan_hari_ini']); ?></div><div class="ket">transaksi · <?php if (isset($component)) { $__componentOriginalb4e3971ce842b126bc6c4e68494cd6a3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb4e3971ce842b126bc6c4e68494cd6a3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rp','data' => ['nilai' => $ringkas['nilai_hari_ini']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rp'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nilai' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ringkas['nilai_hari_ini'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb4e3971ce842b126bc6c4e68494cd6a3)): ?>
<?php $attributes = $__attributesOriginalb4e3971ce842b126bc6c4e68494cd6a3; ?>
<?php unset($__attributesOriginalb4e3971ce842b126bc6c4e68494cd6a3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb4e3971ce842b126bc6c4e68494cd6a3)): ?>
<?php $component = $__componentOriginalb4e3971ce842b126bc6c4e68494cd6a3; ?>
<?php unset($__componentOriginalb4e3971ce842b126bc6c4e68494cd6a3); ?>
<?php endif; ?> <br>(tanggal <?php echo e(\Carbon\Carbon::parse($hari)->format('d/m/Y')); ?>, zona waktu aplikasi UTC)</div></div>
    <div class="kartu"><div class="judul">Margin aktif</div><div class="nilai"><?php if($marginAktif->isNotEmpty()): ?><?php $__currentLoopData = $marginAktif; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if (isset($component)) { $__componentOriginal50d9ef21f2f1e58eed9598823a32f3d7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal50d9ef21f2f1e58eed9598823a32f3d7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.persen','data' => ['nilai' => $m->persen]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('persen'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nilai' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($m->persen)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal50d9ef21f2f1e58eed9598823a32f3d7)): ?>
<?php $attributes = $__attributesOriginal50d9ef21f2f1e58eed9598823a32f3d7; ?>
<?php unset($__attributesOriginal50d9ef21f2f1e58eed9598823a32f3d7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal50d9ef21f2f1e58eed9598823a32f3d7)): ?>
<?php $component = $__componentOriginal50d9ef21f2f1e58eed9598823a32f3d7; ?>
<?php unset($__componentOriginal50d9ef21f2f1e58eed9598823a32f3d7); ?>
<?php endif; ?><?php if(! $loop->last): ?>, <?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php else: ?> - <?php endif; ?></div><div class="ket"><a href="<?php echo e(route('margin_penjualan')); ?>">Lihat margin penjualan</a></div></div>
    <div class="kartu"><div class="judul">Barang aktif</div><div class="nilai"><?php echo e($ringkas['barang_aktif']); ?></div><div class="ket"><a href="<?php echo e(route('barang')); ?>">Lihat barang</a></div></div>
    <div class="kartu"><div class="judul">Vendor aktif</div><div class="nilai"><?php echo e($ringkas['vendor_aktif']); ?></div><div class="ket"><a href="<?php echo e(route('vendor')); ?>">Lihat vendor</a></div></div>
    <div class="kartu"><div class="judul">User</div><div class="nilai"><?php echo e($ringkas['user']); ?></div><div class="ket"><a href="<?php echo e(route('user')); ?>">Lihat user</a></div></div>
  </div>

  <div class="ringkasan">
    <div class="kartu"><div class="judul">Total pengadaan</div><div class="nilai"><?php echo e($ringkas['pengadaan']); ?></div><div class="ket"><a href="<?php echo e(route('pengadaan.index')); ?>">Data pengadaan</a></div></div>
    <div class="kartu"><div class="judul">Total penerimaan</div><div class="nilai"><?php echo e($ringkas['penerimaan']); ?></div><div class="ket"><a href="<?php echo e(route('penerimaan.index')); ?>">Data penerimaan</a></div></div>
    <div class="kartu"><div class="judul">Total penjualan</div><div class="nilai"><?php echo e($ringkas['penjualan']); ?></div><div class="ket"><a href="<?php echo e(route('penjualan.index')); ?>">Data penjualan</a></div></div>
    <div class="kartu"><div class="judul">Total retur</div><div class="nilai"><?php echo e($ringkas['retur']); ?></div><div class="ket"><a href="<?php echo e(route('retur.index')); ?>">Data retur</a></div></div>
    <div class="kartu"><div class="judul">Catatan kartu stok</div><div class="nilai"><?php echo e($ringkas['kartu_stok']); ?></div><div class="ket"><a href="<?php echo e(route('kartu_stok')); ?>">Kartu stok</a></div></div>
  </div>

  <div class="dua-kolom">
    <section class="kartu">
      <h2>Penjualan terakhir</h2>
      <div class="tabel-bungkus">
      <table>
        <thead><tr><th class="angka">ID</th><th>Tanggal</th><th>Kasir</th><th class="angka">Total</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $penjualanTerakhir; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td class="angka"><a href="<?php echo e(route('penjualan.show', $p->idpenjualan)); ?>">#<?php echo e($p->idpenjualan); ?></a></td>
            <td><?php echo e($p->created_at?->format('d/m/Y H:i') ?? '-'); ?></td>
            <td><?php echo e($p->user?->username ?? '-'); ?></td>
            <td class="angka"><?php if (isset($component)) { $__componentOriginalb4e3971ce842b126bc6c4e68494cd6a3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb4e3971ce842b126bc6c4e68494cd6a3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rp','data' => ['nilai' => $p->total_nilai]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rp'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nilai' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($p->total_nilai)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb4e3971ce842b126bc6c4e68494cd6a3)): ?>
<?php $attributes = $__attributesOriginalb4e3971ce842b126bc6c4e68494cd6a3; ?>
<?php unset($__attributesOriginalb4e3971ce842b126bc6c4e68494cd6a3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb4e3971ce842b126bc6c4e68494cd6a3)): ?>
<?php $component = $__componentOriginalb4e3971ce842b126bc6c4e68494cd6a3; ?>
<?php unset($__componentOriginalb4e3971ce842b126bc6c4e68494cd6a3); ?>
<?php endif; ?></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="4" class="kosong">Belum ada penjualan.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
      </div>
    </section>

    <section class="kartu">
      <h2>Pengadaan terakhir</h2>
      <div class="tabel-bungkus">
      <table>
        <thead><tr><th class="angka">ID</th><th>Tanggal</th><th>Vendor</th><th class="angka">Total</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $pengadaanTerakhir; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td class="angka"><a href="<?php echo e(route('pengadaan.show', $p->idpengadaan)); ?>">#<?php echo e($p->idpengadaan); ?></a></td>
            <td><?php echo e($p->timestamp?->format('d/m/Y H:i') ?? '-'); ?></td>
            <td><?php echo e($p->vendor?->nama_vendor ?? '-'); ?></td>
            <td class="angka"><?php if (isset($component)) { $__componentOriginalb4e3971ce842b126bc6c4e68494cd6a3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb4e3971ce842b126bc6c4e68494cd6a3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rp','data' => ['nilai' => $p->total_nilai]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rp'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nilai' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($p->total_nilai)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb4e3971ce842b126bc6c4e68494cd6a3)): ?>
<?php $attributes = $__attributesOriginalb4e3971ce842b126bc6c4e68494cd6a3; ?>
<?php unset($__attributesOriginalb4e3971ce842b126bc6c4e68494cd6a3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb4e3971ce842b126bc6c4e68494cd6a3)): ?>
<?php $component = $__componentOriginalb4e3971ce842b126bc6c4e68494cd6a3; ?>
<?php unset($__componentOriginalb4e3971ce842b126bc6c4e68494cd6a3); ?>
<?php endif; ?></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="4" class="kosong">Belum ada pengadaan.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
      </div>
    </section>
  </div>

  <?php if (isset($component)) { $__componentOriginal3a3743169da1b02a6fcfe13e09b73f16 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3a3743169da1b02a6fcfe13e09b73f16 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catatan','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catatan'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>Belum tersedia (butuh backend): ringkasan stok per barang / stok rendah dan pengadaan yang menunggu penerimaan, karena arti kode status dan aturan stok belum ditetapkan. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3a3743169da1b02a6fcfe13e09b73f16)): ?>
<?php $attributes = $__attributesOriginal3a3743169da1b02a6fcfe13e09b73f16; ?>
<?php unset($__attributesOriginal3a3743169da1b02a6fcfe13e09b73f16); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3a3743169da1b02a6fcfe13e09b73f16)): ?>
<?php $component = $__componentOriginal3a3743169da1b02a6fcfe13e09b73f16; ?>
<?php unset($__componentOriginal3a3743169da1b02a6fcfe13e09b73f16); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/dashboard/index.blade.php ENDPATH**/ ?>