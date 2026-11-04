<?php $__env->startSection('judul', 'Data Margin Penjualan'); ?>
<?php $__env->startSection('grup', 'Tabel Master'); ?>
<?php $__env->startSection('deskripsi', 'Persentase margin penjualan. Hanya satu margin yang boleh aktif.'); ?>

<?php $__env->startSection('aksi'); ?>
  <button type="button" class="tombol" data-buka-form="m-form" data-judul="Tambah Margin Penjualan"
          data-aksi="<?php echo e(route('margin_penjualan.store')); ?>" data-metode="POST">+ Tambah Margin</button>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('isi'); ?>
  <?php if(! empty($aktif)): ?>
    <?php if (isset($component)) { $__componentOriginal3a3743169da1b02a6fcfe13e09b73f16 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3a3743169da1b02a6fcfe13e09b73f16 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catatan','data' => ['jenis' => 'sukses']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catatan'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['jenis' => 'sukses']); ?>Margin aktif saat ini: <?php $__currentLoopData = $aktif; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><strong><?php if (isset($component)) { $__componentOriginal50d9ef21f2f1e58eed9598823a32f3d7 = $component; } ?>
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
<?php endif; ?></strong> (ID <?php echo e($m->idmargin_penjualan); ?>)<?php if(! $loop->last): ?>, <?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3a3743169da1b02a6fcfe13e09b73f16)): ?>
<?php $attributes = $__attributesOriginal3a3743169da1b02a6fcfe13e09b73f16; ?>
<?php unset($__attributesOriginal3a3743169da1b02a6fcfe13e09b73f16); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3a3743169da1b02a6fcfe13e09b73f16)): ?>
<?php $component = $__componentOriginal3a3743169da1b02a6fcfe13e09b73f16; ?>
<?php unset($__componentOriginal3a3743169da1b02a6fcfe13e09b73f16); ?>
<?php endif; ?>
  <?php else: ?>
    <?php if (isset($component)) { $__componentOriginal3a3743169da1b02a6fcfe13e09b73f16 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3a3743169da1b02a6fcfe13e09b73f16 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.catatan','data' => ['jenis' => 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('catatan'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['jenis' => 'info']); ?>Tidak ada margin yang berstatus aktif. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3a3743169da1b02a6fcfe13e09b73f16)): ?>
<?php $attributes = $__attributesOriginal3a3743169da1b02a6fcfe13e09b73f16; ?>
<?php unset($__attributesOriginal3a3743169da1b02a6fcfe13e09b73f16); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3a3743169da1b02a6fcfe13e09b73f16)): ?>
<?php $component = $__componentOriginal3a3743169da1b02a6fcfe13e09b73f16; ?>
<?php unset($__componentOriginal3a3743169da1b02a6fcfe13e09b73f16); ?>
<?php endif; ?>
  <?php endif; ?>

  <?php if (isset($component)) { $__componentOriginale9f22847d79d6273acb27aff60f1f678 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale9f22847d79d6273acb27aff60f1f678 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-bar','data' => ['reset' => route('margin_penjualan')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['reset' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('margin_penjualan'))]); ?>
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
      <tr><th class="angka">ID</th><th class="angka">Persen</th><th>Status</th><th>Dibuat oleh</th><th>Dibuat</th><th>Diubah</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $daftar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td class="angka"><?php echo e($r->idmargin_penjualan); ?></td>
        <td class="angka"><?php if (isset($component)) { $__componentOriginal50d9ef21f2f1e58eed9598823a32f3d7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal50d9ef21f2f1e58eed9598823a32f3d7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.persen','data' => ['nilai' => $r->persen]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('persen'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nilai' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->persen)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal50d9ef21f2f1e58eed9598823a32f3d7)): ?>
<?php $attributes = $__attributesOriginal50d9ef21f2f1e58eed9598823a32f3d7; ?>
<?php unset($__attributesOriginal50d9ef21f2f1e58eed9598823a32f3d7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal50d9ef21f2f1e58eed9598823a32f3d7)): ?>
<?php $component = $__componentOriginal50d9ef21f2f1e58eed9598823a32f3d7; ?>
<?php unset($__componentOriginal50d9ef21f2f1e58eed9598823a32f3d7); ?>
<?php endif; ?></td>
        <td><?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['aktif' => $r->status == 1,'teks' => $r->status == 1 ? 'Aktif' : 'Nonaktif']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['aktif' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->status == 1),'teks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->status == 1 ? 'Aktif' : 'Nonaktif')]); ?>
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
        <td><?php echo e($r->username); ?></td>
        <td><?php echo e($r->dibuat ?? '-'); ?></td>
        <td><?php echo e($r->diubah ?? '-'); ?></td>
        <td class="aksi">
          <button type="button" class="tombol sekunder kecil" data-buka-form="m-form" data-judul="Edit Margin Penjualan"
                  data-aksi="<?php echo e(route('margin_penjualan.update', $r->idmargin_penjualan)); ?>" data-metode="PUT"
                  data-nilai="<?php echo e(json_encode(['persen' => $r->persen, 'status' => $r->status])); ?>">Edit</button>
          <?php if($r->status != 1): ?>
            <form method="POST" action="<?php echo e(route('margin_penjualan.aktifkan', $r->idmargin_penjualan)); ?>">
              <?php echo csrf_field(); ?>
              <?php echo method_field('PATCH'); ?>
              <button type="button" class="tombol sekunder kecil"
                      data-konfirmasi="Aktifkan margin <?php echo e(rtrim(rtrim(number_format((float) $r->persen, 2, ',', '.'), '0'), ',')); ?>%? Margin lain akan dinonaktifkan.">Aktifkan</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="7" class="kosong"><?php echo e($status !== '' ? 'Tidak ada margin yang cocok dengan filter.' : 'Belum ada data margin penjualan.'); ?></td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
  <?php echo e($daftar->links('components.pagination')); ?>


  <?php $__env->startPush('modal'); ?>
    <?php echo $__env->make('partials.modal-form', ['id' => 'm-form', 'aksi' => route('margin_penjualan.store'), 'judul' => 'Tambah Margin Penjualan', 'fields' => [
      ['label' => 'Persen margin', 'name' => 'persen', 'type' => 'number', 'min' => 0, 'step' => '0.01', 'wajib' => true, 'hint' => 'Pembuat (user) diisi otomatis (user id 1).'],
      ['label' => 'Status', 'name' => 'status', 'type' => 'select', 'wajib' => true, 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/margin_penjualan/index.blade.php ENDPATH**/ ?>