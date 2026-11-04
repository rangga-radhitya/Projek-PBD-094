<?php $__env->startSection('judul', 'Data Vendor'); ?>
<?php $__env->startSection('grup', 'Tabel Master'); ?>
<?php $__env->startSection('deskripsi', 'Pemasok barang.'); ?>

<?php $__env->startSection('aksi'); ?>
  <button type="button" class="tombol" data-buka-form="m-form" data-judul="Tambah Vendor"
          data-aksi="<?php echo e(route('vendor.store')); ?>" data-metode="POST">+ Tambah Vendor</button>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('isi'); ?>
  <?php if (isset($component)) { $__componentOriginale9f22847d79d6273acb27aff60f1f678 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale9f22847d79d6273acb27aff60f1f678 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-bar','data' => ['reset' => route('vendor')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['reset' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('vendor'))]); ?>
    <div class="grup-filter">
      <label for="q">Cari nama vendor</label>
      <input type="search" id="q" name="q" value="<?php echo e($q); ?>" placeholder="Nama vendor">
    </div>
    <div class="grup-filter">
      <label for="badan_hukum">Badan hukum</label>
      <select id="badan_hukum" name="badan_hukum">
        <option value="">Semua</option>
        <option value="P" <?php if($badan === 'P'): echo 'selected'; endif; ?>>PT</option>
        <option value="C" <?php if($badan === 'C'): echo 'selected'; endif; ?>>CV</option>
        <option value="U" <?php if($badan === 'U'): echo 'selected'; endif; ?>>UD</option>
      </select>
    </div>
    <div class="grup-filter">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="">Semua status</option>
        <option value="A" <?php if($status === 'A'): echo 'selected'; endif; ?>>Aktif</option>
        <option value="N" <?php if($status === 'N'): echo 'selected'; endif; ?>>Nonaktif</option>
      </select>
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
      <tr><th class="angka">ID</th><th>Nama vendor</th><th>Badan hukum</th><th>Status</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $daftar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td class="angka"><?php echo e($r->idvendor); ?></td>
        <td><?php echo e($r->nama_vendor); ?></td>
        <td><?php echo e($r->badan_hukum_teks); ?></td>
        <td><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['aktif' => $r->status === 'A','teks' => $r->status_teks]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['aktif' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->status === 'A'),'teks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->status_teks)]); ?>
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
        <td class="aksi">
          <button type="button" class="tombol sekunder kecil" data-buka-form="m-form" data-judul="Edit Vendor"
                  data-aksi="<?php echo e(route('vendor.update', $r->idvendor)); ?>" data-metode="PUT"
                  data-nilai="<?php echo e(json_encode(['nama_vendor' => $r->nama_vendor, 'badan_hukum' => $r->badan_hukum, 'status' => $r->status])); ?>">Edit</button>
          <form method="POST" action="<?php echo e(route('vendor.status', $r->idvendor)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <button type="button" class="tombol sekunder kecil"
                    data-konfirmasi="<?php echo e($r->status === 'A' ? 'Nonaktifkan' : 'Aktifkan'); ?> vendor &quot;<?php echo e($r->nama_vendor); ?>&quot;?"><?php echo e($r->status === 'A' ? 'Nonaktifkan' : 'Aktifkan'); ?></button>
          </form>
        </td>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="5" class="kosong"><?php echo e(($q !== '' || $badan !== '' || $status !== '') ? 'Tidak ada vendor yang cocok dengan pencarian/filter.' : 'Belum ada data vendor.'); ?></td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
  <?php echo e($daftar->links('components.pagination')); ?>


  <?php $__env->startPush('modal'); ?>
    <?php echo $__env->make('partials.modal-form', ['id' => 'm-form', 'aksi' => route('vendor.store'), 'judul' => 'Tambah Vendor', 'fields' => [
      ['label' => 'Nama vendor', 'name' => 'nama_vendor', 'wajib' => true, 'hint' => 'Maksimal 100 karakter.'],
      ['label' => 'Badan hukum', 'name' => 'badan_hukum', 'type' => 'select', 'wajib' => true, 'options' => ['P' => 'PT', 'C' => 'CV', 'U' => 'UD']],
      ['label' => 'Status', 'name' => 'status', 'type' => 'select', 'wajib' => true, 'options' => ['A' => 'Aktif', 'N' => 'Nonaktif']],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/vendor/index.blade.php ENDPATH**/ ?>