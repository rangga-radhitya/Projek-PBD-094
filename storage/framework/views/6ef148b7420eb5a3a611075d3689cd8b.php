<?php $__env->startSection('judul', 'Data Role'); ?>
<?php $__env->startSection('grup', 'Tabel Master'); ?>

<?php $__env->startSection('aksi'); ?>
  <button type="button" class="tombol" data-buka-form="m-form" data-judul="Tambah Role"
          data-aksi="<?php echo e(route('role.store')); ?>" data-metode="POST">
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
    Tambah Role
  </button>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('isi'); ?>
  <?php if (isset($component)) { $__componentOriginale9f22847d79d6273acb27aff60f1f678 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale9f22847d79d6273acb27aff60f1f678 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-bar','data' => ['reset' => route('role')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['reset' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('role'))]); ?>
    <div class="grup-filter">
      <label for="q">Cari nama role</label>
      <input type="search" id="q" name="q" value="<?php echo e($q); ?>" placeholder="Nama role">
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
      <tr><th class="kode">ID</th><th>Nama role</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $daftar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td class="kode"><?php echo e($r->idrole); ?></td>
        <td><?php echo e($r->nama_role); ?></td>
        <td class="aksi">
          <button type="button" class="tombol sekunder kecil" data-buka-form="m-form" data-judul="Edit Role"
                  data-aksi="<?php echo e(route('role.update', $r->idrole)); ?>" data-metode="PUT"
                  data-nilai="<?php echo e(json_encode(['nama_role' => $r->nama_role])); ?>">
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
          <form method="POST" action="<?php echo e(route('role.destroy', $r->idrole)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button type="button" class="tombol sekunder kecil"
                    data-konfirmasi="Hapus role &quot;<?php echo e($r->nama_role); ?>&quot;? Role yang masih dipakai user tidak boleh dihapus.">
              <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'hapus','ukuran' => 14]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'hapus','ukuran' => 14]); ?>
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
              Hapus
            </button>
          </form>
        </td>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="3" class="kosong"><?php echo e($q !== '' ? 'Tidak ada role yang cocok dengan pencarian.' : 'Belum ada data role.'); ?></td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
  <?php echo e($daftar->links('components.pagination')); ?>

  </div>

  <?php $__env->startPush('modal'); ?>
    <?php echo $__env->make('partials.modal-form', ['id' => 'm-form', 'aksi' => route('role.store'), 'judul' => 'Tambah Role', 'fields' => [
      ['label' => 'Nama role', 'name' => 'nama_role', 'wajib' => true, 'hint' => 'Maksimal 100 karakter.'],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/role/index.blade.php ENDPATH**/ ?>