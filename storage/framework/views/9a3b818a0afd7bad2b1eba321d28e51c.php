<?php $__env->startSection('judul', 'Data User'); ?>
<?php $__env->startSection('grup', 'Tabel Master'); ?>
<?php $__env->startSection('deskripsi', 'Akun pengguna aplikasi. Kolom password tidak pernah ditampilkan.'); ?>

<?php $__env->startSection('aksi'); ?>
  <button type="button" class="tombol" data-buka-form="m-form" data-judul="Tambah User"
          data-aksi="<?php echo e(route('user.store')); ?>" data-metode="POST">+ Tambah User</button>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('isi'); ?>
  <?php if (isset($component)) { $__componentOriginale9f22847d79d6273acb27aff60f1f678 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale9f22847d79d6273acb27aff60f1f678 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-bar','data' => ['reset' => route('user')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['reset' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('user'))]); ?>
    <div class="grup-filter">
      <label for="q">Cari username</label>
      <input type="search" id="q" name="q" value="<?php echo e($q); ?>" placeholder="Username">
    </div>
    <div class="grup-filter">
      <label for="idrole">Role</label>
      <select id="idrole" name="idrole">
        <option value="">Semua role</option>
        <?php $__currentLoopData = $daftarRole; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($role->idrole); ?>" <?php if($idrole === (string) $role->idrole): echo 'selected'; endif; ?>><?php echo e($role->nama_role); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
      <tr><th class="angka">ID</th><th>Username</th><th>Role</th><th class="aksi">Aksi</th></tr>
    </thead>
    <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $daftar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td class="angka"><?php echo e($r->iduser); ?></td>
        <td><?php echo e($r->username); ?></td>
        <td><?php echo e($r->nama_role); ?></td>
        <td class="aksi">
          <button type="button" class="tombol sekunder kecil" data-buka-form="m-form" data-judul="Edit User"
                  data-aksi="<?php echo e(route('user.update', $r->iduser)); ?>" data-metode="PUT"
                  data-nilai="<?php echo e(json_encode(['username' => $r->username, 'idrole' => $r->idrole])); ?>">Edit</button>
          <form method="POST" action="<?php echo e(route('user.destroy', $r->iduser)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button type="button" class="tombol sekunder kecil"
                    data-konfirmasi="Hapus user &quot;<?php echo e($r->username); ?>&quot;? User yang sudah dipakai pada transaksi tidak boleh dihapus.">Hapus</button>
          </form>
        </td>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="4" class="kosong"><?php echo e(($q !== '' || $idrole !== '') ? 'Tidak ada user yang cocok dengan pencarian/filter.' : 'Belum ada data user.'); ?></td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
  <?php echo e($daftar->links('components.pagination')); ?>


  <?php
    $opsiRole = [];
    foreach ($daftarRole as $role) {
        $opsiRole[(string) $role->idrole] = $role->nama_role;
    }
  ?>

  <?php $__env->startPush('modal'); ?>
    <?php echo $__env->make('partials.modal-form', ['id' => 'm-form', 'aksi' => route('user.store'), 'judul' => 'Tambah User', 'fields' => [
      ['label' => 'Username', 'name' => 'username', 'wajib' => true, 'hint' => 'Maksimal 45 karakter.'],
      ['label' => 'Password', 'name' => 'password', 'type' => 'password', 'hint' => 'Wajib saat tambah; kosongkan saat edit bila tidak diganti.'],
      ['label' => 'Role', 'name' => 'idrole', 'type' => 'select', 'wajib' => true, 'options' => $opsiRole],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/user/index.blade.php ENDPATH**/ ?>