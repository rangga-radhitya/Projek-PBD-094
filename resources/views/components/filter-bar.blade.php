@props(['reset'])
{{-- Bar pencarian/filter (GET). Isi field lewat slot; tombol Cari & Reset otomatis.
     Pencarian diterapkan otomatis saat mengetik (lihat public/js/app.js). --}}
<form class="filter" method="get" role="search" data-cari-langsung>
  <div class="filter-isi">
    {{ $slot }}
  </div>
  <div class="filter-aksi">
    <button type="submit" class="tombol utama">
      <x-ikon nama="cari" :ukuran="16" />
      Cari
    </button>
    <a class="tombol sekunder" href="{{ $reset }}">Reset</a>
  </div>
</form>
