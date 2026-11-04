
<?php $adaGalat = $errors->any(); ?>
<div class="modal" id="<?php echo e($id); ?>" <?php if(! $adaGalat): ?> hidden <?php endif; ?>>
  <div class="modal-kotak" role="dialog" aria-modal="true" aria-labelledby="<?php echo e($id); ?>-judul">
    <div class="modal-kepala">
      <h2 id="<?php echo e($id); ?>-judul" data-modal-judul><?php echo e($judul ?? 'Form'); ?></h2>
      <button type="button" class="modal-tutup" data-tutup-modal aria-label="Tutup">
        <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'tutup','ukuran' => 18]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'tutup','ukuran' => 18]); ?>
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
    </div>
    <form class="modal-isi" method="POST" action="<?php echo e($aksi ?? ''); ?>" autocomplete="off" data-form-crud>
      <?php echo csrf_field(); ?>
      <input type="hidden" name="_method" value="<?php echo e($metode ?? 'POST'); ?>" data-metode>

      <?php if($adaGalat): ?>
        <div class="peringatan gagal" role="alert">
          <div>
            <strong>Periksa kembali isian berikut.</strong>
            <ul class="daftar-galat">
              <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $galat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($galat); ?></li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </div>
        </div>
      <?php endif; ?>

      <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
          $fid = $id.'-'.$f['name'];
          $tipe = $f['type'] ?? 'text';
          $nilaiLama = old($f['name']);
          $adaHint = ! empty($f['hint']);
        ?>
        <div class="field">
          <label for="<?php echo e($fid); ?>"><?php echo e($f['label']); ?><?php if(!empty($f['wajib'])): ?><span class="wajib" aria-hidden="true"> *</span><?php endif; ?></label>
          <?php if($tipe === 'select'): ?>
            <select id="<?php echo e($fid); ?>" name="<?php echo e($f['name']); ?>" <?php if(!empty($f['wajib'])): ?> required <?php endif; ?> <?php if($adaHint): ?> aria-describedby="<?php echo e($fid); ?>-hint" <?php endif; ?>>
              <option value="">— Pilih —</option>
              <?php $__currentLoopData = $f['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nilai => $teks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($nilai); ?>" <?php if((string) $nilaiLama === (string) $nilai): echo 'selected'; endif; ?>><?php echo e($teks); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          <?php else: ?>
            <input id="<?php echo e($fid); ?>" name="<?php echo e($f['name']); ?>" type="<?php echo e($tipe); ?>"
                   value="<?php echo e($nilaiLama); ?>"
                   <?php if(isset($f['min'])): ?> min="<?php echo e($f['min']); ?>" <?php endif; ?>
                   <?php if(isset($f['step'])): ?> step="<?php echo e($f['step']); ?>" <?php endif; ?>
                   <?php if(!empty($f['wajib'])): ?> required <?php endif; ?>
                   <?php if($adaHint): ?> aria-describedby="<?php echo e($fid); ?>-hint" <?php endif; ?>>
          <?php endif; ?>
          <?php if($adaHint): ?><small class="hint" id="<?php echo e($fid); ?>-hint"><?php echo e($f['hint']); ?></small><?php endif; ?>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

      <div class="modal-kaki">
        <button type="button" class="tombol sekunder" data-tutup-modal>
          <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'tutup','ukuran' => 15]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'tutup','ukuran' => 15]); ?>
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
          Batal
        </button>
        <button type="submit" class="tombol utama">
          <?php if (isset($component)) { $__componentOriginal8d917d15c7538e1421c51d0f23f3fc09 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d917d15c7538e1421c51d0f23f3fc09 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ikon','data' => ['nama' => 'centang','ukuran' => 15]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ikon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['nama' => 'centang','ukuran' => 15]); ?>
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
          Simpan
        </button>
      </div>
    </form>
  </div>
</div>
<?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/partials/modal-form.blade.php ENDPATH**/ ?>