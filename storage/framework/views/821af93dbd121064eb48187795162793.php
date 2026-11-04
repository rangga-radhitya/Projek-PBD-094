<?php $__env->startSection('judul', 'Data Satuan'); ?>
<?php $__env->startSection('grup', 'Master Data'); ?>
<?php $__env->startSection('deskripsi', 'Satuan barang (Pcs, Cup, Box, dst.). Data dibaca dari view v_satuan.'); ?>

<?php $__env->startSection('aksi'); ?>
  <button type="button" class="tombol" data-buka-form="m-form" data-judul="Tambah Satuan">+ Tambah Satuan</button>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('isi'); ?>
  <?php if (isset($component)) { $__componentOriginale9f22847d79d6273acb27aff60f1f678 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale9f22847d79d6273acb27aff60f1f678 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-bar','data' => ['reset' => route('satuan')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['reset' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('satuan'))]); ?>
    <div class="grup-filter">
      <label for="q">Cari nama satuan</label>
      <input type="search" id="q" name="q" value="<?php echo e($q); ?>" placeholder="Nama satuan">
    </div>
    <div class="grup-filter">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="">Semua status</option>
        <option value="1" <?php if($status === '1'): echo 'selected'; endif; ?>>Aktif</option>
        <option value="0" <?php if($status === '0'): echo 'selected'; endif; ?>>Nonaktif</option>
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
      <tr><th class="angka">ID</th><th>Nama satuan</th><th>Status</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $daftar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td class="angka"><?php echo e($r->idsatuan); ?></td>
        <td><?php echo e($r->nama_satuan); ?></td>
        <td><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['aktif' => $r->status == 1,'teks' => $r->status_teks]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['aktif' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->status == 1),'teks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->status_teks)]); ?>
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
          <button type="button" class="tombol sekunder kecil" data-buka-form="m-form" data-judul="Edit Satuan" data-nilai="<?php echo e(json_encode(['nama_satuan' => $r->nama_satuan, 'status' => $r->status])); ?>">Edit</button>
          <button type="button" class="tombol sekunder kecil" data-konfirmasi="<?php echo e($r->status == 1 ? 'Nonaktifkan' : 'Aktifkan'); ?> satuan &quot;<?php echo e($r->nama_satuan); ?>&quot;?"><?php echo e($r->status == 1 ? 'Nonaktifkan' : 'Aktifkan'); ?></button>
        </td>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="4" class="kosong"><?php echo e(($q !== '' || $status !== '') ? 'Tidak ada satuan yang cocok dengan pencarian/filter.' : 'Belum ada data satuan.'); ?></td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
  <?php echo e($daftar->links('components.pagination')); ?>


  <?php $__env->startPush('modal'); ?>
    <?php echo $__env->make('partials.modal-form', ['id' => 'm-form', 'fields' => [
      ['label' => 'Nama satuan', 'name' => 'nama_satuan', 'wajib' => true, 'hint' => 'Maksimal 45 karakter.'],
      ['label' => 'Status', 'name' => 'status', 'type' => 'select', 'wajib' => true, 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/satuan/index.blade.php ENDPATH**/ ?>