<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#100D0B">
  <meta name="color-scheme" content="dark">
  <title><?php echo $__env->yieldContent('judul'); ?> - <?php echo e(config('app.name', 'Daily Bun')); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
  <script src="<?php echo e(asset('js/app.js')); ?>" defer></script>
</head>
<body>
<?php
  // Menu dibaca dari config/menu.php (satu sumber untuk sidebar).
  $menu = config('menu', []);
  $tautan = fn (array $m) => route($m['route'], $m['params'] ?? []);
  $menuAktif = fn (array $m) => request()->is(...($m['match'] ?? []));
?>
<a class="lewati" href="#konten">Lewati ke konten</a>
<div class="aplikasi">

  <aside class="sisi" id="sisi" aria-label="Navigasi utama">
    <div class="sisi-merek">
      <a href="<?php echo e(route('dashboard')); ?>">
        <span class="merek-ikon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 15c0-4.4 3.6-8 8-8s8 3.6 8 8"/>
            <path d="M4 15h16"/>
            <path d="M7 15c.5-2 1.6-3.6 3-4.6M17 15c-.5-2-1.6-3.6-3-4.6"/>
            <path d="M9 4.5c0 .8-.7 1.5-1.5 1.5M15 4.5c0 .8-.7 1.5-1.5 1.5"/>
          </svg>
        </span>
        <span class="merek-teks">
          <strong><?php echo e(config('app.name', 'Daily Bun')); ?></strong>
          <small>Manajemen Toko</small>
        </span>
      </a>
    </div>

    <nav>
      <?php $__currentLoopData = $menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(isset($entri['items'])): ?>
          <div class="grup">
            <p class="grup-judul"><?php echo e($entri['label']); ?></p>
            <ul>
              <?php $__currentLoopData = $entri['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                  <a href="<?php echo e($tautan($item)); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['aktif' => $menuAktif($item)]); ?>" <?php if($menuAktif($item)): ?> aria-current="page" <?php endif; ?>>
                    <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => $item['ikon'] ?? 'kotak','ukuran' => 18]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['ikon'] ?? 'kotak'),'ukuran' => 18]); ?>
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
                    <span><?php echo e($item['label']); ?></span>
                  </a>
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </div>
        <?php else: ?>
          <a class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sisi-tunggal', 'aktif' => $menuAktif($entri)]); ?>" href="<?php echo e($tautan($entri)); ?>" <?php if($menuAktif($entri)): ?> aria-current="page" <?php endif; ?>>
            <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => $entri['ikon'] ?? 'kotak','ukuran' => 18]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($entri['ikon'] ?? 'kotak'),'ukuran' => 18]); ?>
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
            <span><?php echo e($entri['label']); ?></span>
          </a>
        <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>

    <div class="sisi-kaki">
      <span class="sisi-kaki-tanda" aria-hidden="true"></span>
      <span>Daily Bun</span>
    </div>
  </aside>
  <div class="sisi-latar" data-tutup-sisi hidden></div>

  <div class="badan">
    <header class="bar-atas">
      <button type="button" class="tombol-menu" data-buka-sisi aria-controls="sisi" aria-expanded="false" aria-label="Buka menu">
        <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'menu','ukuran' => 20]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'menu','ukuran' => 20]); ?>
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
      </button>

      <a class="bar-merek" href="<?php echo e(route('dashboard')); ?>"><?php echo e(config('app.name', 'Daily Bun')); ?></a>

      <nav class="remah" aria-label="Breadcrumb">
        <ol>
          <li><a href="<?php echo e(route('dashboard')); ?>">Beranda</a></li>
          <?php if (! empty(trim($__env->yieldContent('breadcrumb')))): ?>
            <?php echo $__env->yieldContent('breadcrumb'); ?>
          <?php else: ?>
            <?php if (! empty(trim($__env->yieldContent('grup')))): ?><li><?php echo $__env->yieldContent('grup'); ?></li><?php endif; ?>
            <li aria-current="page"><?php echo $__env->yieldContent('judul'); ?></li>
          <?php endif; ?>
        </ol>
      </nav>

      <div class="bar-kanan">
        <div class="pengguna" title="Transaksi dicatat atas nama manajer01">
          <span class="pengguna-avatar" aria-hidden="true">M</span>
          <span class="pengguna-teks">
            <strong>manajer01</strong>
          </span>
        </div>
      </div>
    </header>

    <main id="konten">
      <div class="kepala-halaman">
        <div class="kepala-teks">
          <h1><?php echo $__env->yieldContent('judul'); ?></h1>
          <?php if (! empty(trim($__env->yieldContent('deskripsi')))): ?><p class="deskripsi"><?php echo $__env->yieldContent('deskripsi'); ?></p><?php endif; ?>
        </div>
        <div class="kepala-aksi"><?php echo $__env->yieldContent('aksi'); ?></div>
      </div>

      <?php if(session('sukses')): ?><div class="peringatan sukses" role="status"><?php echo e(session('sukses')); ?></div><?php endif; ?>
      <?php if(session('gagal')): ?><div class="peringatan gagal" role="alert"><?php echo e(session('gagal')); ?></div><?php endif; ?>

      <?php echo $__env->yieldContent('isi'); ?>
    </main>
  </div>
</div>

<?php echo $__env->make('partials.modal-konfirmasi', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->yieldPushContent('modal'); ?>
<?php echo $__env->yieldPushContent('skrip'); ?>
</body>
</html>
<?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/layouts/app.blade.php ENDPATH**/ ?>