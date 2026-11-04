<?php $__env->startSection('judul', 'Data Barang'); ?>
<?php $__env->startSection('grup', 'Tabel Master'); ?>
<?php $__env->startSection('deskripsi', 'Katalog produk dan harga jual.'); ?>

<?php $__env->startSection('aksi'); ?>
  <button type="button" class="tombol" data-buka-form="m-form" data-judul="Tambah Barang"
          data-aksi="<?php echo e(route('barang.store')); ?>" data-metode="POST">+ Tambah Barang</button>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('isi'); ?>
  <?php if (isset($component)) { $__componentOriginale9f22847d79d6273acb27aff60f1f678 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale9f22847d79d6273acb27aff60f1f678 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-bar','data' => ['reset' => route('barang')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['reset' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('barang'))]); ?>
    <div class="grup-filter">
      <label for="q">Cari nama barang</label>
      <input type="search" id="q" name="q" value="<?php echo e($q); ?>" placeholder="Nama barang">
    </div>
    <div class="grup-filter">
      <label for="jenis">Jenis</label>
      <select id="jenis" name="jenis">
        <option value="">Semua jenis</option>
        <option value="B" <?php if($jenis === 'B'): echo 'selected'; endif; ?>>Bread</option>
        <option value="D" <?php if($jenis === 'D'): echo 'selected'; endif; ?>>Drink</option>
        <option value="C" <?php if($jenis === 'C'): echo 'selected'; endif; ?>>Cake</option>
        <option value="P" <?php if($jenis === 'P'): echo 'selected'; endif; ?>>Pastry</option>
      </select>
    </div>
    <div class="grup-filter">
      <label for="idsatuan">Satuan</label>
      <select id="idsatuan" name="idsatuan">
        <option value="">Semua satuan</option>
        <?php $__currentLoopData = $daftarSatuan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($s->idsatuan); ?>" <?php if($idsatuan === (string) $s->idsatuan): echo 'selected'; endif; ?>><?php echo e($s->nama_satuan); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
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
      <tr><th class="angka">ID</th><th>Nama barang</th><th>Jenis</th><th>Satuan</th><th class="angka">Harga</th><th>Status</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $daftar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td class="angka"><?php echo e($r->idbarang); ?></td>
        <td><?php echo e($r->nama); ?></td>
        <td><?php echo e($r->jenis_teks); ?></td>
        <td><?php echo e($r->nama_satuan ?? '-'); ?></td>
        <td class="angka"><?php if (isset($component)) { $__componentOriginalb4e3971ce842b126bc6c4e68494cd6a3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb4e3971ce842b126bc6c4e68494cd6a3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rp','data' => ['nilai' => $r->harga]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rp'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nilai' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($r->harga)]); ?>
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
        <td class="aksi">
          <button type="button" class="tombol sekunder kecil" data-buka-form="m-form" data-judul="Edit Barang"
                  data-aksi="<?php echo e(route('barang.update', $r->idbarang)); ?>" data-metode="PUT"
                  data-nilai="<?php echo e(json_encode(['nama' => $r->nama, 'jenis' => $r->jenis, 'idsatuan' => $r->idsatuan, 'harga' => $r->harga, 'status' => $r->status])); ?>">Edit</button>
          <form method="POST" action="<?php echo e(route('barang.status', $r->idbarang)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <button type="button" class="tombol sekunder kecil"
                    data-konfirmasi="<?php echo e($r->status == 1 ? 'Nonaktifkan' : 'Aktifkan'); ?> barang &quot;<?php echo e($r->nama); ?>&quot;?"><?php echo e($r->status == 1 ? 'Nonaktifkan' : 'Aktifkan'); ?></button>
          </form>
        </td>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="7" class="kosong"><?php echo e(($q !== '' || $jenis !== '' || $idsatuan !== '' || $status !== '') ? 'Tidak ada barang yang cocok dengan pencarian/filter.' : 'Belum ada data barang.'); ?></td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
  <?php echo e($daftar->links('components.pagination')); ?>


  <?php $__env->startPush('modal'); ?>
    <?php echo $__env->make('partials.modal-form', ['id' => 'm-form', 'aksi' => route('barang.store'), 'judul' => 'Tambah Barang', 'fields' => [
      ['label' => 'Nama barang', 'name' => 'nama', 'wajib' => true, 'hint' => 'Maksimal 45 karakter.'],
      ['label' => 'Jenis', 'name' => 'jenis', 'type' => 'select', 'wajib' => true, 'options' => ['B' => 'Bread', 'D' => 'Drink', 'C' => 'Cake', 'P' => 'Pastry']],
      ['label' => 'Satuan', 'name' => 'idsatuan', 'type' => 'select', 'wajib' => true, 'options' => $opsiSatuan],
      ['label' => 'Harga jual (Rp)', 'name' => 'harga', 'type' => 'number', 'min' => 0, 'wajib' => true],
      ['label' => 'Status', 'name' => 'status', 'type' => 'select', 'wajib' => true, 'options' => ['1' => 'Aktif', '0' => 'Nonaktif']],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/barang/index.blade.php ENDPATH**/ ?>