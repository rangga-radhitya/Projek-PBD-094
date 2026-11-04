<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $__env->yieldContent('judul'); ?> - <?php echo e(config('app.name', 'Daily Bun')); ?></title>
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
      <a href="<?php echo e(route('dashboard')); ?>"><?php echo e(config('app.name', 'Daily Bun')); ?></a>
    </div>
    <nav>
      <?php $__currentLoopData = $menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(isset($entri['items'])): ?>
          <?php $terbuka = collect($entri['items'])->contains(fn ($i) => $menuAktif($i)); ?>
          <details class="grup" <?php if($terbuka): ?> open <?php endif; ?>>
            <summary><?php echo e($entri['label']); ?></summary>
            <ul>
              <?php $__currentLoopData = $entri['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                  <a href="<?php echo e($tautan($item)); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['aktif' => $menuAktif($item)]); ?>" <?php if($menuAktif($item)): ?> aria-current="page" <?php endif; ?>><?php echo e($item['label']); ?></a>
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </details>
        <?php else: ?>
          <a class="sisi-tunggal" href="<?php echo e($tautan($entri)); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['aktif' => $menuAktif($entri)]); ?>" <?php if($menuAktif($entri)): ?> aria-current="page" <?php endif; ?>><?php echo e($entri['label']); ?></a>
        <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>
  </aside>
  <div class="sisi-latar" data-tutup-sisi hidden></div>

  <div class="badan">
    <header class="bar-atas">
      <button type="button" class="tombol-menu" data-buka-sisi aria-controls="sisi" aria-expanded="false" aria-label="Buka menu">&#9776;</button>
      <span class="bar-judul"><?php echo e(config('app.name', 'Daily Bun')); ?></span>
      <span class="bar-status" title="Aplikasi masih tahap tampilan: belum ada login dan belum ada penyimpanan data.">Tahap tampilan</span>
    </header>

    <main id="konten">
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

      <div class="kepala-halaman">
        <div>
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
</body>
</html>
<?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/layouts/app.blade.php ENDPATH**/ ?>