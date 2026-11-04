<?php $__env->startSection('judul', 'Dashboard'); ?>

<?php $__env->startSection('isi'); ?>
  <div class="hero-grid">
    <section class="hero-penjualan">
      <div class="hero-label">
        <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'penjualan','ukuran' => 15]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'penjualan','ukuran' => 15]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $attributes = $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $component = $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?>
        Penjualan hari ini
      </div>
      <div class="hero-nilai"><?php if (isset($component)) { $__componentOriginalb4e3971ce842b126bc6c4e68494cd6a3 = $component; } ?>
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
<?php endif; ?></div>
      <div class="hero-meta">
        <span><?php echo e($ringkas['penjualan_hari_ini']); ?> transaksi</span>
        <span aria-hidden="true">·</span>
        <span><?php echo e(\Carbon\Carbon::parse($hari)->format('d/m/Y')); ?></span>
        <span aria-hidden="true">·</span>
        <span title="Zona waktu aplikasi menggunakan UTC">UTC</span>
      </div>
      <a class="hero-tautan" href="<?php echo e(route('penjualan.index')); ?>">
        Lihat penjualan
        <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'panah','ukuran' => 14]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'panah','ukuran' => 14]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $attributes = $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $component = $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?>
      </a>
    </section>

    <div class="hero-sisi">
      <section class="kartu kartu-sempit">
        <div class="hero-label">
          <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'margin','ukuran' => 15]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'margin','ukuran' => 15]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $attributes = $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $component = $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?>
          Margin aktif
        </div>
        <div class="nilai-kecil">
          <?php if(! empty($marginAktif)): ?>
            <?php $__currentLoopData = $marginAktif; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php if (isset($component)) { $__componentOriginal50d9ef21f2f1e58eed9598823a32f3d7 = $component; } ?>
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
<?php endif; ?><?php if(! $loop->last): ?>, <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php else: ?>
            -
          <?php endif; ?>
        </div>
        <div class="ket">
          Berlaku untuk transaksi penjualan.
          <a href="<?php echo e(route('margin_penjualan')); ?>">Kelola margin</a>
        </div>
      </section>
    </div>
  </div>

  <section class="kartu kpi-strip" aria-label="Ringkasan operasional">
    <a class="kpi" href="<?php echo e(route('pengadaan.index')); ?>">
      <span class="kpi-label">Pengadaan</span>
      <span class="kpi-nilai"><?php echo e($ringkas['pengadaan']); ?></span>
      <span class="kpi-tautan">Data pengadaan <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'panah','ukuran' => 13]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'panah','ukuran' => 13]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $attributes = $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $component = $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?></span>
    </a>
    <a class="kpi" href="<?php echo e(route('penerimaan.index')); ?>">
      <span class="kpi-label">Penerimaan</span>
      <span class="kpi-nilai"><?php echo e($ringkas['penerimaan']); ?></span>
      <span class="kpi-tautan">Data penerimaan <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'panah','ukuran' => 13]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'panah','ukuran' => 13]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $attributes = $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $component = $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?></span>
    </a>
    <a class="kpi" href="<?php echo e(route('penjualan.index')); ?>">
      <span class="kpi-label">Penjualan</span>
      <span class="kpi-nilai"><?php echo e($ringkas['penjualan']); ?></span>
      <span class="kpi-tautan">Data penjualan <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'panah','ukuran' => 13]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'panah','ukuran' => 13]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $attributes = $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $component = $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?></span>
    </a>
    <a class="kpi" href="<?php echo e(route('retur.index')); ?>">
      <span class="kpi-label">Retur</span>
      <span class="kpi-nilai"><?php echo e($ringkas['retur']); ?></span>
      <span class="kpi-tautan">Data retur <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'panah','ukuran' => 13]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'panah','ukuran' => 13]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $attributes = $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $component = $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?></span>
    </a>
    <a class="kpi" href="<?php echo e(route('kartu_stok')); ?>">
      <span class="kpi-label">Kartu stok</span>
      <span class="kpi-nilai"><?php echo e($ringkas['kartu_stok']); ?></span>
      <span class="kpi-tautan">Riwayat stok <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'panah','ukuran' => 13]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'panah','ukuran' => 13]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $attributes = $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $component = $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?></span>
    </a>
    <a class="kpi" href="<?php echo e(route('barang')); ?>">
      <span class="kpi-label">Barang</span>
      <span class="kpi-nilai"><?php echo e($ringkas['barang_aktif']); ?></span>
      <span class="kpi-tautan">Data barang <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'panah','ukuran' => 13]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'panah','ukuran' => 13]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $attributes = $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $component = $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?></span>
    </a>
    <a class="kpi" href="<?php echo e(route('vendor')); ?>">
      <span class="kpi-label">Vendor</span>
      <span class="kpi-nilai"><?php echo e($ringkas['vendor_aktif']); ?></span>
      <span class="kpi-tautan">Data vendor <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'panah','ukuran' => 13]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'panah','ukuran' => 13]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $attributes = $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $component = $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?></span>
    </a>
    <a class="kpi" href="<?php echo e(route('user')); ?>">
      <span class="kpi-label">User</span>
      <span class="kpi-nilai"><?php echo e($ringkas['user']); ?></span>
      <span class="kpi-tautan">Data user <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'panah','ukuran' => 13]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'panah','ukuran' => 13]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $attributes = $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09)): ?>
<?php $component = $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09; ?>
<?php unset($__componentOriginal8d917d15c7538e1421c51d0f23f3fc09); ?>
<?php endif; ?></span>
    </a>
  </section>

  <div class="dua-kolom">
    <section class="kartu">
      <div class="panel-kepala">
        <h2>Penjualan terakhir</h2>
        <a class="panel-meta" href="<?php echo e(route('penjualan.index')); ?>">Lihat semua</a>
      </div>
      <div class="tabel-bungkus">
        <table>
          <thead><tr><th class="kode">ID</th><th>Tanggal</th><th>Kasir</th><th class="angka">Total</th></tr></thead>
          <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $penjualanTerakhir; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td class="kode"><a href="<?php echo e(route('penjualan.show', $p->idpenjualan)); ?>">#<?php echo e($p->idpenjualan); ?></a></td>
              <td><?php echo e($p->tanggal ?? '-'); ?></td>
              <td><?php echo e($p->username ?? '-'); ?></td>
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
      <div class="panel-kepala">
        <h2>Pengadaan terakhir</h2>
        <a class="panel-meta" href="<?php echo e(route('pengadaan.index')); ?>">Lihat semua</a>
      </div>
      <div class="tabel-bungkus">
        <table>
          <thead><tr><th class="kode">ID</th><th>Tanggal</th><th>Vendor</th><th class="angka">Total</th></tr></thead>
          <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $pengadaanTerakhir; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td class="kode"><a href="<?php echo e(route('pengadaan.show', $p->idpengadaan)); ?>">#<?php echo e($p->idpengadaan); ?></a></td>
              <td><?php echo e($p->tanggal ?? '-'); ?></td>
              <td><?php echo e($p->nama_vendor ?? '-'); ?></td>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catatan','data' => ['ringkas' => true,'judul' => 'Catatan data']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catatan'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['ringkas' => true,'judul' => 'Catatan data']); ?>
    Stok berjalan per barang dapat dilihat di halaman <a href="<?php echo e(route('kartu_stok')); ?>">Kartu Stok</a>.
    Ringkasan stok rendah belum dihitung pada dashboard.
   <?php echo $__env->renderComponent(); ?>
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