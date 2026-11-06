<?php $__env->startSection('judul', 'Data Satuan'); ?>
<?php $__env->startSection('grup', 'Tabel Master'); ?>

<?php $__env->startSection('aksi'); ?>
  <button type="button" class="tombol" data-buka-form="m-form" data-judul="Tambah Satuan"
          data-aksi="<?php echo e(route('satuan.store')); ?>" data-metode="POST">
    <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'tambah','ukuran' => 16]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'tambah','ukuran' => 16]); ?>
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
    Tambah Satuan
  </button>
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

  <div data-hasil-daftar>
  <div class="tabel-bungkus">
  <table>
    <thead>
      <tr><th class="kode">ID</th><th>Nama satuan</th><th>Status</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $daftar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td class="kode"><?php echo e($r->idsatuan); ?></td>
        <td><?php echo e($r->nama_satuan); ?></td>
        <td><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['jenis' => $r->status == 1 ? 'sukses' : 'netral','teks' => $r->status_teks]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['jenis' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->status == 1 ? 'sukses' : 'netral'),'teks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->status_teks)]); ?>
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
          <button type="button" class="tombol sekunder kecil" data-buka-form="m-form" data-judul="Edit Satuan"
                  data-aksi="<?php echo e(route('satuan.update', $r->idsatuan)); ?>" data-metode="PUT"
                  data-nilai="<?php echo e(json_encode(['nama_satuan' => $r->nama_satuan, 'status' => $r->status])); ?>">
            <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'edit','ukuran' => 14]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'edit','ukuran' => 14]); ?>
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
            Edit
          </button>
          <form method="POST" action="<?php echo e(route('satuan.status', $r->idsatuan)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <button type="button" class="tombol sekunder kecil"
                    data-konfirmasi="<?php echo e($r->status == 1 ? 'Nonaktifkan' : 'Aktifkan'); ?> satuan &quot;<?php echo e($r->nama_satuan); ?>&quot;?">
              <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => $r->status == 1 ? 'tutup' : 'centang','ukuran' => 14]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->status == 1 ? 'tutup' : 'centang'),'ukuran' => 14]); ?>
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
              <?php echo e($r->status == 1 ? 'Nonaktifkan' : 'Aktifkan'); ?>

            </button>
          </form>
        </td>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="4" class="kosong"><?php echo e($q !== '' ? 'Tidak ada satuan yang cocok dengan pencarian.' : 'Belum ada data satuan.'); ?></td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
  <?php echo e($daftar->links('components.pagination')); ?>

  </div>

  <?php $__env->startPush('modal'); ?>
    <?php echo $__env->make('partials.modal-form', ['id' => 'm-form', 'aksi' => route('satuan.store'), 'judul' => 'Tambah Satuan', 'fields' => [
      ['label' => 'Nama satuan', 'name' => 'nama_satuan', 'wajib' => true, 'hint' => 'Maksimal 45 karakter.'],
      ['label' => 'Status', 'name' => 'status', 'type' => 'select', 'wajib' => true, 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/satuan/index.blade.php ENDPATH**/ ?>