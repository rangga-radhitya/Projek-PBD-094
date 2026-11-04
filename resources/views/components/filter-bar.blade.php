@props(['reset'])
{{-- Bar pencarian/filter (GET). Isi field lewat slot; tombol Cari & Reset otomatis. --}}
<form class="filter" method="get" role="search">
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
