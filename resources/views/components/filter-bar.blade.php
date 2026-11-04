@props(['reset'])
{{-- Bar pencarian/filter (GET). Isi field lewat slot; tombol Cari & Reset otomatis. --}}
<form class="filter" method="get" role="search">
  {{ $slot }}
  <button type="submit" class="tombol utama">Cari</button>
  <a class="tombol sekunder" href="{{ $reset }}">Reset</a>
</form>
