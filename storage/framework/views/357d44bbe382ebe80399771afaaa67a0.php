
<div class="modal" id="<?php echo e($id); ?>" hidden>
  <div class="modal-kotak" role="dialog" aria-modal="true" aria-labelledby="<?php echo e($id); ?>-judul">
    <div class="modal-kepala">
      <h2 id="<?php echo e($id); ?>-judul" data-modal-judul>Form</h2>
      <button type="button" class="modal-tutup" data-tutup-modal aria-label="Tutup">&times;</button>
    </div>
    <form class="modal-isi" onsubmit="return false" autocomplete="off" novalidate>
      <p class="petunjuk">Mode tampilan: isian ini belum dapat disimpan (tahap CRUD).</p>
      <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $fid = $id.'-'.$f['name']; $tipe = $f['type'] ?? 'text'; ?>
        <div class="field">
          <label for="<?php echo e($fid); ?>"><?php echo e($f['label']); ?><?php if(!empty($f['wajib'])): ?><span class="wajib" aria-hidden="true"> *</span><?php endif; ?></label>
          <?php if($tipe === 'select'): ?>
            <select id="<?php echo e($fid); ?>" name="<?php echo e($f['name']); ?>" <?php if(!empty($f['wajib'])): ?> required <?php endif; ?>>
              <option value="">— Pilih —</option>
              <?php $__currentLoopData = $f['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nilai => $teks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($nilai); ?>"><?php echo e($teks); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          <?php else: ?>
            <input id="<?php echo e($fid); ?>" name="<?php echo e($f['name']); ?>" type="<?php echo e($tipe); ?>" <?php if(isset($f['min'])): ?> min="<?php echo e($f['min']); ?>" <?php endif; ?> <?php if(!empty($f['wajib'])): ?> required <?php endif; ?>>
          <?php endif; ?>
          <?php if(!empty($f['hint'])): ?><small class="hint"><?php echo e($f['hint']); ?></small><?php endif; ?>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <div class="modal-kaki">
        <button type="button" class="tombol sekunder" data-tutup-modal>Batal</button>
        <button type="submit" class="tombol utama" disabled>Simpan</button>
      </div>
    </form>
  </div>
</div>
<?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/partials/modal-form.blade.php ENDPATH**/ ?>