<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['reset']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['reset']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<form class="filter" method="get" role="search">
  <?php echo e($slot); ?>

  <button type="submit" class="tombol utama">Cari</button>
  <a class="tombol sekunder" href="<?php echo e($reset); ?>">Reset</a>
</form>
<?php /**PATH C:\Users\user\Documents\D4 Teknik Informatika\Semester 3\PBD\Project_Bakery\resources\views/components/filter-bar.blade.php ENDPATH**/ ?>